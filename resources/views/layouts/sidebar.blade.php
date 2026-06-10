<div class="d-flex flex-column p-3 text-white" 
     style="width: 260px; min-height: 100vh; background-color: #2c3e50;">

    {{-- Logo --}}
    <a href="/" class="d-flex align-items-center mb-4 text-white text-decoration-none">
        <span class="fs-5 fw-bold">☕ Cafe Management</span>
    </a>

    <hr class="text-white">

    {{-- Menu Admin --}}
    @if(auth()->user()->role === 'admin')
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-1">
            <a href="{{ route('admin.dashboard') }}" 
               class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                📊 Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="{{ route('admin.users.index') }}" 
               class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                👥 Kelola User
            </a>
        </li>
       <li class="nav-item mb-1">
            <a href="{{ route('admin.tables.index') }}"
            class="nav-link text-white {{ request()->routeIs('admin.tables.*') ? 'active' : '' }}">
                🪑 Manajemen Meja
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="#" class="nav-link text-white">
                📈 Laporan
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="{{ route('admin.shifts.index') }}" 
            class="nav-link text-white {{ request()->routeIs('admin.shifts.*') ? 'active' : '' }}">
                🕐 Manajemen Shift
            </a>
        </li>
        {{-- Supplier --}}
        <li class="nav-item mb-1">
            <a href="{{ route('admin.suppliers.index') }}"
            class="nav-link text-white {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                🏭 Supplier
            </a>
        </li>

        {{-- Bahan Baku --}}
        <li class="nav-item mb-1">
            <a href="{{ route('admin.ingredients.index') }}"
            class="nav-link text-white {{ request()->routeIs('admin.ingredients.*') ? 'active' : '' }}">
                🧂 Bahan Baku
            </a>
        </li>

        {{-- Pembelian --}}
        <li class="nav-item mb-1">
            <a href="{{ route('admin.purchases.index') }}"
            class="nav-link text-white {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}">
                🛒 Pembelian
            </a>
        </li>
        {{-- Kategori --}}
        <li class="nav-item mb-1">
            <a href="{{ route('admin.categories.index') }}"
            class="nav-link text-white {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                🗂️ Kategori
            </a>
        </li>

        {{-- Menu --}}
        <li class="nav-item mb-1">
            <a href="{{ route('admin.menus.index') }}"
            class="nav-link text-white {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                🍽️ Menu
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="{{ route('admin.settings.index') }}"
            class="nav-link text-white {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                ⚙️ Pengaturan
            </a>
        </li>
    </ul>
    @endif

    {{-- Menu Kasir --}}
    @if(auth()->user()->role === 'kasir')
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-1">
            <a href="{{ route('kasir.dashboard') }}" 
               class="nav-link text-white {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
                📊 Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="#" class="nav-link text-white">
                🧾 Transaksi
            </a>
        </li>
    </ul>
    @endif

    {{-- Menu Barista --}}
    @if(auth()->user()->role === 'barista')
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-1">
            <a href="{{ route('barista.dashboard') }}" 
               class="nav-link text-white {{ request()->routeIs('barista.dashboard') ? 'active' : '' }}">
                📊 Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="#" class="nav-link text-white">
                ☕ Antrian Pesanan
            </a>
        </li>
    </ul>
    @endif

    {{-- Menu Pelayan --}}
    @if(auth()->user()->role === 'pelayan')
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-1">
            <a href="{{ route('pelayan.dashboard') }}" 
               class="nav-link text-white {{ request()->routeIs('pelayan.dashboard') ? 'active' : '' }}">
                📊 Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="#" class="nav-link text-white">
                📋 Pesanan Meja
            </a>
        </li>
    </ul>
    @endif

    <hr class="text-white">

    {{-- User Info & Logout --}}
    <div class="d-flex align-items-center gap-2">
        <div>
            <div class="fw-bold" style="font-size: 13px;">{{ auth()->user()->name }}</div>
            <div style="font-size: 11px; opacity: 0.7;">{{ ucfirst(auth()->user()->role) }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="ms-auto">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light">Keluar</button>
        </form>
    </div>

    

</div>