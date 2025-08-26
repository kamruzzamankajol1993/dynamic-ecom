<header class="pos-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <h4 class="text-white mb-0 me-4 fw-bold">{{$ins_name}}</h4>
                <div class="btn-group">
                    <a href="{{route('product.index')}}" class="btn"><i class="fa-solid fa-tag me-2"></i><span class="d-none d-lg-inline">Products</span></a>
                    <a href="{{route('home')}}" class="btn"><i class="fa-solid fa-border-all me-2"></i><span class="d-none d-lg-inline">Dashboard</span></a>
                    <a href="{{route('order.index')}}" class="btn"><i class="fa-solid fa-cart-shopping me-2"></i><span class="d-none d-lg-inline">Orders</span></a>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="btn-group">
                    <button class="btn" data-bs-toggle="modal" data-bs-target="#calculatorModal"><i class="fa-solid fa-calculator me-1"></i><span class="d-none d-lg-inline"> Calculator</span></button>
                    <button class="btn" data-bs-toggle="modal" data-bs-target="#expiredProductsModal"><i class="fa-solid fa-calendar-times me-1"></i><span class="d-none d-lg-inline"> Expired</span></button>
                    <a href="{{route('systemInformation.index')}}" class="btn"><i class="fa-solid fa-gear"></i></a>
                    <a href="{{route('users.index')}}" class="btn"><i class="fa-solid fa-users me-1"></i><span class="d-none d-lg-inline"> Users</span></a>
                    <a href="{{url('/clear')}}" class="btn"><i class="fa-solid fa-broom me-1"></i><span class="d-none d-lg-inline"> Clear Cache</span></a>
                    <a class="btn" href="{{ route('logout') }}"  onclick="event.preventDefault();
                      document.getElementById('admin-logout-form').submit();"><i class="fa-solid fa-right-from-bracket me-1"></i><span class="d-none d-lg-inline"> Logout</span></a>
                       <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                </div>
            </div>
        </header>