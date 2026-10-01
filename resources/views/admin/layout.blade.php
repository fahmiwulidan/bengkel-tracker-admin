<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bengkel Track Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-color: #f4f6f9;
            --sidebar-bg: #161A2B;
            --card-bg: #ffffff;
            --text-main: #2d3748;
            --text-muted: #718096;
            --accent-primary: #FF6B00;
            --accent-gradient: linear-gradient(135deg, #FF6B00 0%, #FF8533 100%);
            --accent-hover: #E66000;
            --border-color: rgba(0, 0, 0, 0.06);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: none;
            position: fixed;
            left: 0;
            top: 0;
            padding: 32px 24px;
            display: flex;
            flex-direction: column;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0,0,0,0.05);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 48px;
            padding: 0 12px;
        }

        .sidebar-brand i {
            font-size: 28px;
            color: var(--accent-primary);
        }

        .sidebar h2 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 16px;
            color: #8f9bb3;
            text-decoration: none;
            padding: 14px 16px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .sidebar a i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .sidebar a:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(5px);
        }

        .sidebar a.active {
            color: #ffffff;
            background: var(--accent-primary);
            box-shadow: 0 4px 15px rgba(255, 107, 0, 0.3);
        }

        .sidebar a.active:hover {
            transform: none;
            background: var(--accent-hover);
        }

        /* Content Area */
        .content-wrapper {
            margin-left: 280px;
            flex: 1;
            padding: 40px;
            transition: all 0.3s ease;
        }

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            gap: 16px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--text-main);
            cursor: pointer;
            padding: 4px;
        }

        .top-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a202c;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 16px;
            background: var(--card-bg);
            padding: 8px 16px 8px 8px;
            border-radius: 30px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--sidebar-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        /* Glass Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        }

        /* Grid */
        .grid-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
            background: rgba(255, 107, 0, 0.1);
            color: var(--accent-primary);
        }

        .stat-card p {
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: #1a202c;
            line-height: 1;
        }

        /* Tables */
        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: transparent;
            min-width: 900px;
        }

        th {
            background: #f8fafc;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            border-top: 1px solid var(--border-color);
            white-space: nowrap;
        }

        th:first-child { border-top-left-radius: 12px; border-left: 1px solid var(--border-color); }
        th:last-child { border-top-right-radius: 12px; border-right: 1px solid var(--border-color); }

        td {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            font-size: 15px;
            color: var(--text-main);
            vertical-align: middle;
        }

        td:first-child { border-left: 1px solid var(--border-color); }
        td:last-child { border-right: 1px solid var(--border-color); }

        tr:hover td {
            background: #f8fafc;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input, select, textarea {
            width: 100%;
            padding: 14px 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 4px rgba(255, 107, 0, 0.15);
            background: #ffffff;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--accent-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 0, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 0, 0.4);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: var(--text-main);
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .btn-danger:hover {
            background: #fee2e2;
            color: #b91c1c;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 13px;
            border-radius: 8px;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        /* Alerts */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            animation: slideIn 0.5s ease;
            width: fit-content;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #059669;
            padding: 11px 20px;
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .content-wrapper {
                margin-left: 0;
                padding: 20px;
            }
            .top-header {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 24px;
            }
            .page-actions-container {
                flex-direction: column;
                align-items: stretch !important;
                gap: 16px;
            }
            .alerts-container {
                margin-right: 0 !important;
                width: 100%;
            }
            .page-actions {
                width: 100%;
            }
            .page-actions .btn {
                width: 100%;
                justify-content: center;
            }
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 99;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.active {
            display: block;
        }
        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(22, 26, 43, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            padding: 32px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            transform: translateY(20px) scale(0.95);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            max-height: 90vh;
            overflow-y: auto;
            scrollbar-width: none;
        }

        .modal-content::-webkit-scrollbar {
            display: none;
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0) scale(1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            color: var(--text-main);
        }

        .modal-header h2 {
            font-size: 24px;
            margin: 0;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 24px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .modal-close:hover {
            color: #dc2626;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border-color);
        }
        /* Collapsed Sidebar Styles */
        body.sidebar-collapsed .sidebar {
            width: 88px;
            padding: 24px 12px;
        }
        body.sidebar-collapsed .sidebar-brand {
            justify-content: center;
            padding: 0;
            margin-bottom: 48px;
        }
        body.sidebar-collapsed .sidebar-brand h2 {
            display: none;
        }
        body.sidebar-collapsed .sidebar a {
            justify-content: center;
            padding: 14px 0;
        }
        body.sidebar-collapsed .sidebar a span {
            display: none;
        }
        body.sidebar-collapsed .sidebar a i {
            margin-right: 0;
            font-size: 22px;
        }
        body.sidebar-collapsed .content-wrapper {
            margin-left: 88px;
        }
    </style>
</head>
<body>
    <script>
        if (localStorage.getItem('sidebarState') === 'collapsed' && window.innerWidth > 768) {
            document.body.classList.add('sidebar-collapsed');
        }
    </script>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand" id="sidebarToggle" style="cursor: pointer;">
            <i class="fa-solid fa-screwdriver-wrench"></i>
            <h2>BengkelTrack</h2>
        </div>
        <div class="nav-links">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.workshops.index') }}" class="{{ request()->routeIs('admin.workshops.*') ? 'active' : '' }}">
                <i class="fa-solid fa-warehouse"></i>
                <span>Daftar Bengkel</span>
            </a>
            <a href="{{ route('admin.mechanics.index') }}" class="{{ request()->routeIs('admin.mechanics.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear"></i>
                <span>Daftar Mekanik</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-shield"></i>
                <span>Daftar User</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-list"></i>
                <span>Daftar Order</span>
            </a>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="top-header">
            <div class="header-left">
                <button class="mobile-menu-btn" onclick="toggleSidebar()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1>@yield('title', 'Admin Panel')</h1>
            </div>
            <div class="user-profile">
                <div class="user-avatar">A</div>
                <span style="font-weight: 500;">Admin User</span>
            </div>
        </div>

        <div class="page-actions-container" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; min-height: 44px;">
            <div class="alerts-container" style="flex: 1; margin-right: 24px;">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <div>
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            <div class="page-actions" style="flex-shrink: 0;">
                @yield('actions')
            </div>
        </div>

        @yield('content')
    </div>

    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            if (window.innerWidth > 768) {
                document.body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarState', document.body.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded');
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        }

        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // Close when clicking outside modal content
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.classList.remove('active');
            }
        }

        // Auto-hide alerts after 3 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);
    </script>
</body>
</html>
