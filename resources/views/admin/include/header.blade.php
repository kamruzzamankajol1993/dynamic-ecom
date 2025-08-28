@php
     $usr = Auth::user();
 @endphp

 <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <!-- Left Side: Toggler and Title -->
                    <div class="d-flex align-items-center">
                        <button class="sidebar-toggler-btn me-3" type="button" id="sidebarToggleBtn" title="Toggle Sidebar">
                            <i data-feather="menu"></i>
                        </button>
                        <div class="d-none d-lg-flex align-items-baseline">
                            <h5 class="mb-0 text-primary me-3" style="color: var(--primary-color) !important;">{{$ins_name}}</h5>
                            <small id="currentDate" class="text-muted"></small>
                        </div>
                    </div>

                    <!-- Right Side: Buttons and Profile -->
                    <div class="d-flex align-items-center">
                        <!-- Clear Cache Button -->
                        <a href="{{url('/clear')}}" class="btn btn-sm btn-danger text-white me-3" title="Clear Cache">
                            <i data-feather="refresh-cw" style="width:16px; height:16px;"></i>
                            <span class="d-none d-sm-inline ms-1">Clear Cache</span>
                        </a>
@if ($usr->can('posView'))
                        <a class="btn btn-sm btn-success text-white me-3" href="{{route('pos.index')}}">
                            <i data-feather="monitor" style="width:16px; height:16px;"></i> Pos
                        </a>
                        @endif

                        <!-- Profile Dropdown -->
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                                        @if(empty(Auth::user()->image))
                  
                     <img src="{{asset('/')}}public/No_Image_Available.jpg" alt="Admin" width="40" height="40" class="rounded-circle">
                    @else
                    <img src="{{asset('/')}}{{ Auth::user()->image }}" alt="Admin" width="40" height="40" class="rounded-circle">
                    @endif

                               
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom" aria-labelledby="navbarDropdown">
                               @if ($usr->can('profileView'))
                                <li><a class="dropdown-item" href="{{ route('profileView') }}"><i data-feather="user"></i>Profile</a></li>
                               @endif
                               @if ($usr->can('profileSetting'))
                                <li><a class="dropdown-item" href="{{ route('profileSetting') }}"><i data-feather="settings"></i>Settings</a></li>
                                 @endif

                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="{{ route('logout') }}"  onclick="event.preventDefault();
                      document.getElementById('admin-logout-form').submit();"><i data-feather="log-out"></i>Logout</a></li>
                       <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

 

