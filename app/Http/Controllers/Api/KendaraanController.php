<?php

namespace App\Http\Controllers\Api;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KendaraanController extends \App\Http\Controllers\Controller
{
    /**
     * AMBIL SEMUA DATA VEHICLE
     * Potensi Error: 500 jika database bermaslah
     */
    public function index()
    {
        try {
            $vehicles = Kendaraan::all();

            if ($vehicles->isEmpty()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Data kendaraan masih kosong',
                    'data'    => []
                ], 200);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Daftar kendaraan ',
                'data'    => $vehicles
            ], 200);

        }catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data kendaraan',
                'debug'   => $e->getMessage()
            ], 500);
        }

    }

    /**
     * SIMPAN DATA VEHICLE BARU
     */
    public function store(Request $request)
    {
        try {
            // Validasi input data terlebih dahulu
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'type'    => 'required|in:car,motorcycle',
                'brand'   => 'required|string|max:255',
                'color'   => 'required|string|max:255',
            ]);

            // Jika validasi gagal (Error 400 / Bad Request)
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Validasi gagal, periksa kembali inputan Anda',
                    'errors'  => $validator->errors()
                ], 400);
            }

            // Menyimpan ke database jika validasi lolos
            $vehicle = Kendaraan::create($request->all());

            return response()->json([
                'status'  => 'success',
                'message' => 'Data kendaraan berhasil ditambahkan',
                'data'    => $vehicle
            ], 201); // 201 artinya Created

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data kendaraan',
                'debug'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AMBIL SATU DATA VEHICLE BERDASARKAN ID
     * Potensi Error: 404 jika ID tidak ada
     */
    public function show($id)
    {
        try {
            $vehicle = Kendaraan::find($id);

            // MENANGANI ERROR 404 (Not Found)
            if (!$vehicle) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Data kendaraan dengan ID ' . $id . ' tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Detail kendaraan berhasil diambil',
                'data'    => $vehicle
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server',
                'debug'   => $e->getMessage()
            ], 500);
        }
    }
}
