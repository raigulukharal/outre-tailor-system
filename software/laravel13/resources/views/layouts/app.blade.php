{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OUTRE Tailor System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .toast { transition: all 0.3s ease; }
        .sidebar { transition: transform 0.3s ease; }
        @media print {
            .no-print, .sidebar, .header, .print-buttons { display: none !important; }
            .print-area { margin: 0; padding: 0; }
        }
        .loading-spinner { border: 3px solid #f3f3f3; border-top: 3px solid #4f46e5; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        /* Logo Overlap Styles */
        .logo-parent {
            position: relative;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .logo-absolute {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 50;
        }
        
        .logo-absolute:hover {
            transform: translateX(-50%) scale(1.1);
        }
        
        .logo-image {
            margin:5px;
            width: 70px;
            height: 70px;
            /*border-radius: 50%;*/
            object-fit: cover;
            background: transparent;
            /*border: 3px solid #10B981;*/
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .logo-image:hover {
            border-color: #34D399;
            box-shadow: 0 20px 30px -10px rgba(16, 185, 129, 0.4);
            transform: scale(1.05);
        }
        
        /* Sidebar top padding adjustment for overlapping logo */
        .sidebar-top-padding {
            padding-top: 3rem;
        }
        
        /* Brand text styling */
        .brand-text {
            text-align: center;
            margin-top: 0.5rem;
        }
        .brand-title {
            margin:10px;
            font-size: 1.25rem;
            font-weight: bold;
            letter-spacing: 0.05em;
        }
        .brand-subtitle {
            font-size: 0.7rem;
            color: #a5b4fc;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
    
    @auth
    {{-- Only show sidebar and header if user is logged in --}}
    <div class="flex h-screen">
        {{-- Sidebar --}}
        <aside class="sidebar fixed inset-y-0 left-0 z-30 w-64 bg-gradient-to-b from-indigo-900 to-indigo-800 text-white transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out overflow-y-auto">
            
            {{-- Logo Section with Overlap --}}
            <div class="logo-parent">
                <div class="logo-absolute">
                    @php
                        $logoPath = public_path('images/outre-logo.19');
                        $hasCustomLogo = file_exists($logoPath);
                    @endphp
                    
                    @if($hasCustomLogo)
                        <img src="{{ asset('images/outre-logo.19') }}" 
                             alt="OUTRE Logo" 
                             class="logo-image"
                             style="background: transparent; object-fit: cover;">
                    @else
                        <div class="logo-image bg-gradient-to-br from-emerald-500 to-indigo-600 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- Brand Text - Centered below logo --}}
            <div class="sidebar-top-padding">
                <div class="brand-text">
                    <div class="brand-title">
                        OUTRE <span class="text-emerald-400">Tailor</span>
                    </div>
                    <div class="brand-subtitle">
                        Management System
                    </div>
                </div>
            </div>
            
            <nav class="mt-6">
                <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 text-indigo-100 hover:bg-indigo-800 hover:text-white transition group">
                    <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('orders.create') }}" class="flex items-center px-6 py-3 text-indigo-100 hover:bg-indigo-800 hover:text-white transition group">
                    <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Order
                </a>
                <a href="{{ route('reminders') }}" class="flex items-center px-6 py-3 text-indigo-100 hover:bg-indigo-800 hover:text-white transition group">
                    <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Reminders
                </a>
                <a href="{{ route('completed-orders') }}" class="flex items-center px-6 py-3 text-indigo-100 hover:bg-indigo-800 hover:text-white transition group">
                    <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Completed Orders
                </a>
            </nav>
            
            {{-- Footer in sidebar --}}
            <div class="absolute bottom-0 left-0 right-0 p-4 text-center text-indigo-400 text-xs border-t border-indigo-800">
                <p>© {{ date('Y') }} OUTRE Tailor</p>
                <p class="mt-1">Version 1.0</p>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Header --}}
            <header class="header bg-white shadow-sm py-4 px-6 flex justify-between items-center">
                <button id="sidebarToggle" class="md:hidden text-gray-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <span class="text-gray-700">{{ Auth::user()->name ?? 'User' }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium transition hover:scale-105">Logout</button>
                    </form>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>
    @else
        {{-- For guest users (login page) - show only content without sidebar --}}
        @yield('content')
    @endauth

    <script>
        // Sidebar toggle for mobile
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        if(toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
            });
        }

        // Toast notification function
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast px-6 py-3 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-emerald-600' : 'bg-red-600'} transform transition-all duration-300`;
            toast.innerHTML = message;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        };

        // Set axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    </script>
    @stack('scripts')
</body>
</html>