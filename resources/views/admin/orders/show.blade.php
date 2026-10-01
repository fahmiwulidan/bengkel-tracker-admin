@extends('admin.layout')

@section('content')
    <style>
        .tracking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .info-box {
            background: rgba(0,0,0,0.02);
            padding: 16px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }
        .info-label {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-main);
        }
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-primary { background: rgba(217, 119, 6, 0.1); color: var(--accent-primary); }
        
        .map-legend {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
        }
        .legend-color {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid rgba(0,0,0,0.1);
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 24px;
        }
        .detail-card {
            padding: 24px;
        }
        .detail-card h3 {
            font-size: 16px;
            margin-bottom: 20px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed var(--border-color);
        }
        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .detail-row span:first-child {
            color: var(--text-muted);
            font-weight: 500;
        }
        .detail-row span:last-child {
            font-weight: 600;
            color: var(--text-main);
            text-align: right;
            max-width: 60%;
            word-wrap: break-word;
        }
    </style>

    <div class="tracking-header">
        <h1 style="font-size: 28px; font-weight: 700;">Tracking Order</h1>
        <div class="actions">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="order-info-grid">
            <div class="info-box">
                <div class="info-label">Kode Order</div>
                <div class="info-value">{{ $order->order_code }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="badge badge-primary">{{ str_replace('_', ' ', $order->status) }}</span>
                </div>
            </div>
            <div class="info-box">
                <div class="info-label">Layanan</div>
                <div class="info-value" style="text-transform: capitalize;">{{ $order->service_type ?? '' }} ({{ $order->service_mode ?? 'onsite' }})</div>
            </div>
            <div class="info-box" style="background: rgba(217, 119, 6, 0.05); border-color: rgba(217, 119, 6, 0.2);">
                <div class="info-label" style="color: var(--accent-primary);" id="activeRouteTitle">Estimasi Jarak</div>
                <div class="info-value">
                    <span id="activeRouteDistance" style="color: var(--accent-primary); font-size: 22px;">-</span>
                </div>
            </div>
            <div class="info-box" style="background: rgba(217, 119, 6, 0.05); border-color: rgba(217, 119, 6, 0.2);">
                <div class="info-label" style="color: var(--accent-primary);">Estimasi Waktu</div>
                <div class="info-value">
                    <span id="activeRouteDuration" style="color: var(--accent-primary); font-size: 22px;">-</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 24px; padding: 24px;">
        <div class="map-legend">
            <div class="legend-item">
                <div class="legend-color" style="background: #2563eb;"></div> User
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #f97316;"></div> Mekanik
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #16a34a;"></div> Bengkel
            </div>
        </div>
        <div id="trackingMap" style="height: 460px; border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color);"></div>
    </div>

    <div class="details-grid">
        <div class="card detail-card">
            <h3><i class="fa-solid fa-user"></i> Detail Lokasi User</h3>
            <div class="detail-row"><span>Nama</span><span>{{ $order->customer_name }}</span></div>
            <div class="detail-row"><span>No HP</span><span>{{ $order->customer_phone ?? '-' }}</span></div>
            <div class="detail-row"><span>Latitude</span><span>{{ $order->user_latitude ?? '-' }}</span></div>
            <div class="detail-row"><span>Longitude</span><span>{{ $order->user_longitude ?? '-' }}</span></div>
        </div>

        <div class="card detail-card">
            <h3><i class="fa-solid fa-wrench"></i> Detail Mekanik</h3>
            <div class="detail-row"><span>Nama</span><span>{{ $order->mechanic_name ?? '-' }}</span></div>
            <div class="detail-row"><span>No HP</span><span>{{ $order->mechanic_phone ?? '-' }}</span></div>
            <div class="detail-row"><span>Latitude</span><span>{{ $order->mechanic_latitude ?? '-' }}</span></div>
            <div class="detail-row"><span>Longitude</span><span>{{ $order->mechanic_longitude ?? '-' }}</span></div>
        </div>

        <div class="card detail-card">
            <h3><i class="fa-solid fa-warehouse"></i> Detail Bengkel</h3>
            <div class="detail-row"><span>Nama</span><span>{{ $order->workshop_name ?? '-' }}</span></div>
            <div class="detail-row"><span>Alamat</span><span>{{ $order->workshop_address ?? '-' }}</span></div>
            <div class="detail-row"><span>Latitude</span><span>{{ $order->workshop_latitude ?? '-' }}</span></div>
            <div class="detail-row"><span>Longitude</span><span>{{ $order->workshop_longitude ?? '-' }}</span></div>
        </div>

        <div class="card detail-card">
            <h3><i class="fa-solid fa-receipt"></i> Rincian Biaya</h3>
            <div class="detail-row"><span>Problem</span><span style="text-transform: capitalize;">{{ $order->problem ?? '-' }}</span></div>
            <div class="detail-row"><span>Biaya Dasar</span><span>Rp {{ number_format($order->basic_cost, 0, ',', '.') }}</span></div>
            <div class="detail-row" style="border-top: 2px solid var(--border-color); margin-top: 8px; padding-top: 16px;">
                <span style="font-weight: 700; color: var(--text-main);">Total Biaya</span>
                <span style="font-size: 20px; color: var(--accent-primary); font-weight: 800;">Rp {{ number_format($order->total_cost, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        function parseCoord(value) {
            if (value === null || value === undefined || value === '') {
                return null;
            }

            const number = Number(value);

            if (Number.isNaN(number)) {
                return null;
            }

            return number;
        }

        function isValidPoint(point) {
            return point.lat !== null && point.lng !== null;
        }

        function formatDistance(meter) {
            if (meter === null || meter === undefined) {
                return '-';
            }

            if (meter < 1000) {
                return Math.round(meter) + ' m';
            }

            return (meter / 1000).toFixed(2) + ' km';
        }

        function formatDuration(seconds) {
            if (seconds === null || seconds === undefined) {
                return '-';
            }

            const minutes = Math.round(seconds / 60);

            if (minutes < 60) {
                return minutes + ' menit';
            }

            const hours = Math.floor(minutes / 60);
            const restMinutes = minutes % 60;

            return hours + ' jam ' + restMinutes + ' menit';
        }

        function calculateStraightDistanceMeter(pointA, pointB) {
            if (!isValidPoint(pointA) || !isValidPoint(pointB)) {
                return null;
            }

            const earthRadiusMeter = 6371000;

            const lat1 = pointA.lat * Math.PI / 180;
            const lat2 = pointB.lat * Math.PI / 180;
            const deltaLat = (pointB.lat - pointA.lat) * Math.PI / 180;
            const deltaLng = (pointB.lng - pointA.lng) * Math.PI / 180;

            const a =
                Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2) +
                Math.cos(lat1) * Math.cos(lat2) *
                Math.sin(deltaLng / 2) * Math.sin(deltaLng / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return earthRadiusMeter * c;
        }

        function makeIcon(label, color) {
            return L.divIcon({
                className: '',
                html: `
                    <div style="
                        background:${color};
                        color:white;
                        width:38px;
                        height:38px;
                        border-radius:50%;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        border:4px solid white;
                        box-shadow:0 3px 10px #0005;
                        font-weight:bold;
                    ">${label}</div>
                `,
                iconSize: [38, 38],
                iconAnchor: [19, 19],
            });
        }

        const serviceType = @json($order->service_type ?? '-');
        const serviceMode = @json($order->service_mode ?? 'onsite');

        const userLocation = {
            lat: parseCoord(@json($order->user_latitude ?? null)),
            lng: parseCoord(@json($order->user_longitude ?? null)),
            name: @json($order->customer_name),
            phone: @json($order->customer_phone ?? '-'),
        };

        const mechanicLocation = {
            lat: parseCoord(@json($order->mechanic_latitude ?? null)),
            lng: parseCoord(@json($order->mechanic_longitude ?? null)),
            name: @json($order->mechanic_name ?? 'Mekanik'),
            phone: @json($order->mechanic_phone ?? '-'),
        };

        const workshopLocation = {
            lat: parseCoord(@json($order->workshop_latitude ?? null)),
            lng: parseCoord(@json($order->workshop_longitude ?? null)),
            name: @json($order->workshop_name ?? 'Bengkel'),
            address: @json($order->workshop_address ?? '-'),
        };

        const defaultCenter = isValidPoint(userLocation)
            ? [userLocation.lat, userLocation.lng]
            : [-7.1167, 112.4167];

        const map = L.map('trackingMap').setView(defaultCenter, 15);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const userIcon = makeIcon('U', '#2563eb');
        const mechanicIcon = makeIcon('M', '#f97316');
        const workshopIcon = makeIcon('B', '#16a34a');

        const bounds = [];

        if (isValidPoint(userLocation)) {
            L.marker([userLocation.lat, userLocation.lng], { icon: userIcon })
                .addTo(map)
                .bindPopup(`
                    <b>Lokasi User</b><br>
                    Nama: ${userLocation.name}<br>
                    No HP: ${userLocation.phone}<br>
                    Koordinat: ${userLocation.lat}, ${userLocation.lng}
                `);

            bounds.push([userLocation.lat, userLocation.lng]);
        }

        if (isValidPoint(mechanicLocation)) {
            L.marker([mechanicLocation.lat, mechanicLocation.lng], { icon: mechanicIcon })
                .addTo(map)
                .bindPopup(`
                    <b>Lokasi Mekanik</b><br>
                    Nama: ${mechanicLocation.name}<br>
                    No HP: ${mechanicLocation.phone}<br>
                    Koordinat: ${mechanicLocation.lat}, ${mechanicLocation.lng}
                `);

            bounds.push([mechanicLocation.lat, mechanicLocation.lng]);
        }

        if (isValidPoint(workshopLocation)) {
            L.marker([workshopLocation.lat, workshopLocation.lng], { icon: workshopIcon })
                .addTo(map)
                .bindPopup(`
                    <b>Lokasi Bengkel</b><br>
                    Nama: ${workshopLocation.name}<br>
                    Alamat: ${workshopLocation.address}<br>
                    Koordinat: ${workshopLocation.lat}, ${workshopLocation.lng}
                `);

            bounds.push([workshopLocation.lat, workshopLocation.lng]);
        }

        if (bounds.length > 1) {
            map.fitBounds(bounds, {
                padding: [60, 60],
            });
        }

        async function drawOsrmRoute(from, to, color, label, distanceElementId, durationElementId) {
            if (!isValidPoint(from) || !isValidPoint(to)) {
                document.getElementById(distanceElementId).innerText = '-';
                document.getElementById(durationElementId).innerText = '-';
                return;
            }

            const osrmUrl =
                `https://router.project-osrm.org/route/v1/driving/` +
                `${from.lng},${from.lat};${to.lng},${to.lat}` +
                `?overview=full&geometries=geojson`;

            try {
                const response = await fetch(osrmUrl);
                const data = await response.json();

                if (!data.routes || data.routes.length === 0) {
                    throw new Error('Rute tidak ditemukan');
                }

                const route = data.routes[0];

                const routeCoordinates = route.geometry.coordinates.map((coordinate) => {
                    return [coordinate[1], coordinate[0]];
                });

                L.polyline(routeCoordinates, {
                    color: color,
                    weight: 5,
                    opacity: 0.85,
                })
                .addTo(map)
                .bindPopup(`
                    <b>${label}</b><br>
                    Jarak jalan: ${formatDistance(route.distance)}<br>
                    Estimasi waktu: ${formatDuration(route.duration)}
                `);

                document.getElementById(distanceElementId).innerText =
                    formatDistance(route.distance);

                document.getElementById(durationElementId).innerText =
                    formatDuration(route.duration);

            } catch (error) {
                console.error(error);

                L.polyline(
                    [
                        [from.lat, from.lng],
                        [to.lat, to.lng],
                    ],
                    {
                        color: color,
                        weight: 4,
                        opacity: 0.7,
                        dashArray: '8, 8',
                    }
                )
                .addTo(map)
                .bindPopup(`
                    <b>${label}</b><br>
                    Rute jalan gagal dimuat. Menampilkan garis lurus.
                `);

                const straightDistance = calculateStraightDistanceMeter(from, to);

                document.getElementById(distanceElementId).innerText =
                    formatDistance(straightDistance);

                document.getElementById(durationElementId).innerText =
                    'estimasi rute gagal';
            }
        }

        function setActiveRouteTitle(title) {
            document.getElementById('activeRouteTitle').innerText = title;
        }

        if (serviceMode === 'onsite') {
            setActiveRouteTitle('Rute Mekanik → User');

            drawOsrmRoute(
                mechanicLocation,
                userLocation,
                '#f97316',
                'Rute Mekanik ke User',
                'activeRouteDistance',
                'activeRouteDuration'
            );
        } else if (serviceMode === 'workshop') {
            setActiveRouteTitle('Rute User → Bengkel');

            drawOsrmRoute(
                userLocation,
                workshopLocation,
                '#16a34a',
                'Rute User ke Bengkel',
                'activeRouteDistance',
                'activeRouteDuration'
            );
        } else {
            setActiveRouteTitle('Rute Tidak Diketahui');

            document.getElementById('activeRouteDistance').innerText = '-';
            document.getElementById('activeRouteDuration').innerText = '-';
        }
    </script>
@endsection