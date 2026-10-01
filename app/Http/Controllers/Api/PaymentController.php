<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // Penting untuk debugging
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;

class PaymentController extends Controller
{
    public function __construct()
    {
        Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
    }

    public function createInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id'     => 'required|exists:orders,id',
            'amount'       => 'required|integer',
            'method'       => 'required|string',
            'payer_email'  => 'required|email',
            'redirect_url' => 'nullable|string', // 🌟 Menampung URL port dinamis dari Flutter Web
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $external_id = 'pay_' . Str::random(10);

            // LOGIKA 1: PEMETAAN CHANNEL RESMI XENDIT (Atasi RTO GoPay & Double Input)
            $methodFromFlutter = strtolower($request->method);
            $preferredChannels = [];

            if ($methodFromFlutter === 'qris') {
                $preferredChannels = ['QRIS'];
            } elseif (in_array($methodFromFlutter, ['gopay', 'ovo', 'dana'])) {
                // Mengubah otomatis 'gopay' -> 'ID_GOPAY' untuk mencegah RTO
                $preferredChannels = ['ID_' . strtoupper($methodFromFlutter)];
            } elseif (in_array($methodFromFlutter, ['bca', 'mandiri'])) {
                // Mengubah otomatis 'bca' -> 'BCA_VA'
                $preferredChannels = [strtoupper($methodFromFlutter) . '_VA'];
            }

            // 🌟 LOGIKA 2: REDIRECT URL DINAMIS (Atasi Balik Aplikasi Secara Manual)
            // Jika Flutter mengirimkan URL port, gunakan itu. Jika kosong, gunakan default project
            $finalRedirectUrl = $request->input('redirect_url') ?? 'http://localhost:8080/#/payment-success';

            $invoicePayload = [
                'external_id'      => $external_id,
                'amount'           => $request->amount,
                'payer_email'      => $request->payer_email,
                'description'      => 'Pembayaran untuk Order #' . $request->order_id,
                'invoice_duration' => 172800,

                // 🚀 Pengunci Channel: Memaksa Xendit HANYA menampilkan metode pilihan user dari Flutter
                'payment_methods'  => $preferredChannels,

                // 🚀 Pengarah Otomatis: Begitu pembayaran kelar, tab Xendit akan redirect ke port Flutter kamu
                'success_redirect_url' => $finalRedirectUrl,
                'failure_redirect_url' => $finalRedirectUrl,
            ];

            $createInvoice = new CreateInvoiceRequest($invoicePayload);

            $apiInstance = new InvoiceApi();
            $result = $apiInstance->createInvoice($createInvoice);

            // Simpan ke database
            $payment = Payment::create([
                'order_id'     => $request->order_id,
                'user_id'      => $request->user()->id ?? null,
                'xendit_id'    => $result['id'],
                'method'       => $request->method,
                'status'       => 'PENDING',
                'amount'       => $request->amount,
                'checkout_url' => $result['invoice_url'],
            ]);

            return response()->json([
                'message' => 'Invoice berhasil dibuat',
                'data'    => $payment
            ], 201);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function webhook(Request $request)
    {
        // 1. Mengambil xendit_id dari body
        $xenditId = $request->input('xendit_id') ?? $request->input('id');

        if (!$xenditId) {
            return response()->json(['message' => 'xendit_id atau id diperlukan'], 400);
        }

        // 2. Cari pembayaran berdasarkan xendit_id
        $payment = Payment::where('xendit_id', $xenditId)->first();

        if (!$payment) {
            return response()->json(['message' => 'Data transaksi tidak ditemukan'], 404);
        }

        // 3. LOGIKA OTOMATIS:
        // Jika request membawa data status dari Xendit, update statusnya.
        if ($request->has('status')) {
            $newStatus = $request->input('status');

            // Konversi status Xendit ke status internal Anda
            $finalStatus = ($newStatus === 'PAID') ? 'PAID' : 'FAILED';

            $payment->update(['status' => $finalStatus]);

            Log::info("Webhook otomatis: Transaksi {$xenditId} diperbarui ke {$finalStatus}");

            return response()->json(['message' => 'Status berhasil diupdate', 'status' => $finalStatus], 200);
        }

        // 4. LOGIKA MANUAL (Pengecekan via Postman):
        // Jika tidak ada data status di body, cukup tampilkan status saat ini.
        return response()->json([
            'message' => 'Data ditemukan',
            'data' => [
                'xendit_id' => $payment->xendit_id,
                'status'    => $payment->status
            ]
        ], 200);
    }

}

