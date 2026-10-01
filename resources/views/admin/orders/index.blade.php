@extends('admin.layout')

@section('title', 'Data Order')

@section('content')
    <style>
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-primary { background: rgba(255, 107, 0, 0.1); color: var(--accent-primary); }
        .order-code {
            font-weight: 700;
            color: var(--accent-primary);
            background: rgba(255, 107, 0, 0.05);
            padding: 6px 10px;
            border-radius: 8px;
            letter-spacing: 0.5px;
        }
    </style>

        <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
            <p style="margin: 0; color: var(--text-muted); font-size: 15px;">
                <i class="fa-solid fa-circle-info" style="margin-right: 6px; color: var(--accent-primary);"></i>
                Data order ini dipakai untuk tracking lokasi user, mekanik, dan bengkel.
            </p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Kode Order</th>
                        <th>Customer</th>
                        <th>Bengkel</th>
                        <th>Mekanik</th>
                        <th>Status</th>
                        <th>Problem</th>
                        <th>Total</th>
                        <th>ETA</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            // Mengambil koordinat langsung di blade karena controller tidak boleh diubah
                            if (!isset($order->user_latitude)) {
                                $coords = \Illuminate\Support\Facades\DB::table('orders')
                                    ->leftJoin('mechanics', 'orders.mechanic_id', '=', 'mechanics.id')
                                    ->leftJoin('workshops', 'orders.workshop_id', '=', 'workshops.id')
                                    ->where('orders.id', $order->id)
                                    ->selectRaw("
                                        ST_Y(orders.user_location::geometry) AS user_latitude,
                                        ST_X(orders.user_location::geometry) AS user_longitude,
                                        ST_Y(mechanics.current_position::geometry) AS mechanic_latitude,
                                        ST_X(mechanics.current_position::geometry) AS mechanic_longitude,
                                        ST_Y(workshops.geom::geometry) AS workshop_latitude,
                                        ST_X(workshops.geom::geometry) AS workshop_longitude
                                    ")
                                    ->first();

                                if ($coords) {
                                    $order->user_latitude = $coords->user_latitude;
                                    $order->user_longitude = $coords->user_longitude;
                                    $order->mechanic_latitude = $coords->mechanic_latitude;
                                    $order->mechanic_longitude = $coords->mechanic_longitude;
                                    $order->workshop_latitude = $coords->workshop_latitude;
                                    $order->workshop_longitude = $coords->workshop_longitude;
                                }
                            }
                        @endphp
                        <tr>
                            <td><span class="order-code">{{ $order->order_code }}</span></td>
                            <td style="font-weight: 600;">{{ $order->customer_name }}</td>
                            <td>{{ $order->workshop_name ?? '-' }}</td>
                            <td>{{ $order->mechanic_name ?? '-' }}</td>
                            <td>
                                <span class="badge badge-primary">{{ str_replace('_', ' ', $order->status) }}</span>
                            </td>
                            <td style="text-transform: capitalize;">{{ $order->problem ?? '-' }}</td>
                            <td style="font-weight: 700;">Rp {{ number_format($order->total_cost, 0, ',', '.') }}</td>
                            <td class="eta-cell"
                                data-status="{{ strtolower($order->status) }}"
                                data-mode="{{ $order->service_mode ?? 'onsite' }}"
                                data-u-lat="{{ $order->user_latitude ?? '' }}"
                                data-u-lng="{{ $order->user_longitude ?? '' }}"
                                data-m-lat="{{ $order->mechanic_latitude ?? '' }}"
                                data-m-lng="{{ $order->mechanic_longitude ?? '' }}"
                                data-w-lat="{{ $order->workshop_latitude ?? '' }}"
                                data-w-lng="{{ $order->workshop_longitude ?? '' }}">
                                @if($order->eta)
                                    <b style="color: var(--accent-primary);">{{ $order->eta }} menit</b>
                                @else
                                    <span style="color: #718096; font-size: 13px; font-style: italic;">Menghitung...</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-primary btn-sm" style="padding: 8px 16px;">
                                    <i class="fa-solid fa-location-crosshairs"></i> Tracking
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 32px; color: var(--text-muted);">Belum ada order.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const etaCells = document.querySelectorAll('.eta-cell');

            const delay = ms => new Promise(res => setTimeout(res, ms));
            let requestQueueCount = 0;

            etaCells.forEach(async (cell) => {
                if (!cell.innerText.includes('Menghitung')) return;

                const mode = cell.getAttribute('data-mode');
                const uLat = cell.getAttribute('data-u-lat');
                const uLng = cell.getAttribute('data-u-lng');
                const mLat = cell.getAttribute('data-m-lat');
                const mLng = cell.getAttribute('data-m-lng');
                const wLat = cell.getAttribute('data-w-lat');
                const wLng = cell.getAttribute('data-w-lng');

                let fromLat, fromLng, toLat, toLng;

                if (mode === 'onsite') {
                    fromLat = mLat; fromLng = mLng;
                    toLat = uLat; toLng = uLng;
                } else {
                    fromLat = uLat; fromLng = uLng;
                    toLat = wLat; toLng = wLng;
                }

                if (!fromLat || !fromLng || !toLat || !toLng) {
                    cell.innerHTML = '-';
                    return;
                }

                // Add delay to prevent rate limiting from OSRM API (max 1-2 requests per second)
                requestQueueCount++;
                await delay(requestQueueCount * 1000);

                try {
                    const url = `https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${toLng},${toLat}?overview=false`;
                    const response = await fetch(url);
                    const data = await response.json();

                    if (data.routes && data.routes.length > 0) {
                        const durationSeconds = data.routes[0].duration;
                        const minutes = Math.round(durationSeconds / 60);

                        if (minutes < 60) {
                            cell.innerHTML = `<b style="color: var(--accent-primary);">${minutes} menit</b>`;
                        } else {
                            const hours = Math.floor(minutes / 60);
                            const restMinutes = minutes % 60;
                            cell.innerHTML = `<b style="color: var(--accent-primary);">${hours} jam ${restMinutes} mnt</b>`;
                        }
                    } else {
                        cell.innerHTML = '-';
                    }
                } catch (e) {
                    cell.innerHTML = '-';
                }
            });
        });
    </script>
@endsection
