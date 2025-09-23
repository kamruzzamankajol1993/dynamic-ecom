@php
     $usr = Auth::user();
 @endphp

 <nav id="sidebar">
            <div class="sidebar-header">
                <img src="{{asset('/')}}public/logo.png" alt="{{ $ins_name }} Logo" class="img-fluid">
            </div>
            <ul class="nav flex-column" id="sidebar-menu">
                 @if ($usr->can('dashboardView'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('home')  ? 'active' : '' }}" href="{{route('home')}}">
                        <i data-feather="grid"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @endif
                
                <li class="sidebar-title">
                    <span>Invoice & Product</span>
                </li>
                @if($usr->can('invoiceView') || $usr->can('invoiceAdd') || $usr->can('invoiceDelete') || $usr->can('invoiceUpdate'))
                <li class="nav-item">
                     <a class="nav-link" href="#ordersSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="ordersSubmenu">
                        <i data-feather="shopping-bag"></i>
                        <span>Invoice</span>
                        <i data-feather="chevron-down" class="ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled {{Route::is('order.create') || Route::is('order.edit') || Route::is('order.index') || Route::is('order.show') ? 'show' : ''}}" id="ordersSubmenu" data-bs-parent="#sidebar-menu">
                          @if($usr->can('invoiceAdd'))
                        <li><a class="nav-link {{Route::is('order.create') ? 'active' : ''}}" href="{{route('order.create')}}">Create Invoice</a></li>
                        @endif
                        @if($usr->can('invoiceView') || $usr->can('invoiceDelete') || $usr->can('invoiceUpdate'))  
                        <li><a class="nav-link {{Route::is('order.edit') || Route::is('order.index') || Route::is('order.show') ? 'active' : ''}}" href="{{route('order.index')}}">Invoice List</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                  @if ($usr->can('animationCategoryAdd') || $usr->can('animationCategoryView') ||  $usr->can('animationCategoryDelete') ||  $usr->can('animationCategoryUpdate') || $usr->can('brandAdd') || $usr->can('brandView') ||  $usr->can('brandDelete') ||  $usr->can('brandUpdate') || $usr->can('categoryAdd') || $usr->can('categoryView') ||  $usr->can('categoryDelete') ||  $usr->can('categoryUpdate') || $usr->can('productAdd') || $usr->can('productView') ||  $usr->can('productDelete') ||  $usr->can('productUpdate'))
                <li class="nav-item">
                    <a class="nav-link" href="#productsSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="productsSubmenu">
                        <i data-feather="tag"></i>
                        <span>Products</span>
                        <i data-feather="chevron-down" class="ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled {{Route::is('barcode.index')|| Route::is('product.index') || Route::is('size-chart.index') || Route::is('unit.index') || Route::is('size.index') || Route::is('color.index') ||  Route::is('fabric.index') || Route::is('sub-subcategory.index') ||  Route::is('subcategory.index') || Route::is('animationCategory.index') || Route::is('animationCategory.edit') || Route::is('animationCategory.create') || Route::is('brand.show') || Route::is('brand.index') || Route::is('brand.edit') || Route::is('brand.create') || Route::is('category.index') || Route::is('category.edit') || Route::is('category.create') || Route::is('product.index') || Route::is('product.edit')|| Route::is('product.show') || Route::is('product.create') ? 'show' : '' }}" id="productsSubmenu" data-bs-parent="#sidebar-menu">
                           @if ($usr->can('productView') ||  $usr->can('productDelete') ||  $usr->can('productUpdate'))
                        <li><a class="nav-link {{ Route::is('product.index') || Route::is('product.edit') || Route::is('product.show') ? 'active' : '' }}" href="{{route('product.index')}}">Product List</a></li>
                        @endif
                      @if ($usr->can('productAdd'))
                        <li><a class="nav-link {{Route::is('product.create') ? 'active' : ''}}" href="{{route('product.create')}}">Add Product</a></li>
                        @endif
                         @if ($usr->can('brandAdd') || $usr->can('brandView') ||  $usr->can('brandDelete') ||  $usr->can('brandUpdate'))

                        <a class="nav-link {{ Route::is('brand.index') || Route::is('brand.edit') || Route::is('brand.create') ? 'active' : '' }}" href="{{ route('brand.index') }}">Brand</a>

                    @endif


                          @if ($usr->can('categoryAdd') || $usr->can('categoryView') ||  $usr->can('categoryDelete') ||  $usr->can('categoryUpdate'))

                        <a class="nav-link {{ Route::is('category.index') || Route::is('category.edit') || Route::is('category.create') ? 'active' : '' }}" href="{{ route('category.index') }}">Category</a>

                    @endif

                     @if ($usr->can('subcategoryAdd') || $usr->can('subcategoryView') ||  $usr->can('subcategoryDelete') ||  $usr->can('subcategoryUpdate'))

                        <a class="nav-link {{ Route::is('subcategory.index') || Route::is('subcategory.edit') || Route::is('subcategory.create') ? 'active' : '' }}" href="{{ route('subcategory.index') }}">Sub Category</a>

                    @endif

                                    @if ($usr->can('sub-subcategoryAdd') || $usr->can('sub-subcategoryView') ||  $usr->can('sub-subcategoryDelete') ||  $usr->can('sub-subcategoryUpdate'))

                        <a class="nav-link {{ Route::is('sub-subcategory.index') || Route::is('sub-subcategory.edit') || Route::is('sub-subcategory.create') ? 'active' : '' }}" href="{{ route('sub-subcategory.index') }}">Sub-subcategory</a>

                    @endif

                    @if ($usr->can('animationCategoryAdd') || $usr->can('animationCategoryView') ||  $usr->can('animationCategoryDelete') ||  $usr->can('animationCategoryUpdate'))

                        <a class="nav-link {{ Route::is('animationCategory.index') || Route::is('animationCategory.edit') || Route::is('animationCategory.create') ? 'active' : '' }}" href="{{ route('animationCategory.index') }}">Animation Category</a>

                    @endif

                                  @if ($usr->can('fabricAdd') || $usr->can('fabricView') ||  $usr->can('fabricDelete') ||  $usr->can('fabricUpdate'))

                        <a class="nav-link {{ Route::is('fabric.index') || Route::is('fabric.edit') || Route::is('fabric.create') ? 'active' : '' }}" href="{{ route('fabric.index') }}">Material</a>

                    @endif

                     @if ($usr->can('colorAdd') || $usr->can('colorView') ||  $usr->can('colorDelete') ||  $usr->can('colorUpdate'))

                        <a class="nav-link {{ Route::is('color.index') || Route::is('color.edit') || Route::is('color.create') ? 'active' : '' }}" href="{{ route('color.index') }}">Color</a>

                    @endif


                      @if ($usr->can('unitAdd') || $usr->can('unitView') ||  $usr->can('unitDelete') ||  $usr->can('unitUpdate'))

                        <a class="nav-link {{ Route::is('unit.index') || Route::is('unit.edit') || Route::is('unit.create') ? 'active' : '' }}" href="{{ route('unit.index') }}">Unit</a>

                    @endif

                     @if ($usr->can('sizeAdd') || $usr->can('sizeView') ||  $usr->can('sizeDelete') ||  $usr->can('sizeUpdate'))

                        <a class="nav-link {{ Route::is('size.index') || Route::is('size.edit') || Route::is('size.create') ? 'active' : '' }}" href="{{ route('size.index') }}">Size</a>

                    @endif

                    @if ($usr->can('sizeChartAdd') || $usr->can('sizeChartView') ||  $usr->can('sizeChartDelete') ||  $usr->can('sizeChartUpdate'))

                        <a class="nav-link {{ Route::is('size-chart.index') || Route::is('size-chart.edit') || Route::is('size-chart.create') ? 'active' : '' }}" href="{{ route('size-chart.index') }}">Size Chart</a>

                    @endif

                      @if ($usr->can('barcodeView'))

                        <a class="nav-link {{ Route::is('barcode.index')  ? 'active' : '' }}" href="{{ route('barcode.index') }}">Print Barcode</a>

                    @endif

                    </ul>
                </li>
                @endif
               @if ($usr->can('customerAdd') || $usr->can('customerView') ||  $usr->can('customerDelete') ||  $usr->can('customerUpdate'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('customer.index') || Route::is('customer.edit') || Route::is('customer.create') ? 'active' : '' }}" href="{{ route('customer.index') }}">
                        <i data-feather="users"></i>
                        <span>Customers</span>
                    </a>
                </li>
@endif
                    @if ($usr->can('offerDetailAdd') || $usr->can('offerDetailView') ||  $usr->can('offerDetailDelete') || $usr->can('offerDetailUpdate') || $usr->can('bundleofferAdd') || $usr->can('bundleofferView') ||  $usr->can('bundleofferDelete') ||  $usr->can('bundleofferUpdate'))
                <li class="sidebar-title">
                    <span>Offer & Coupon</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#offerSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="offerSubmenu">
                        <i data-feather="file-text"></i>
                        <span>Offer List</span>
                        <i data-feather="chevron-down" class="ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled {{Route::is('offer-product.create') || Route::is('offer-product.edit') || Route::is('offer-product.index') || Route::is('offer-product.show') || Route::is('bundle-offer.show') || Route::is('bundle-offer.create') || Route::is('bundle-offer.edit') || Route::is('bundle-offer.index')  ? 'show' : '' }}" id="offerSubmenu" data-bs-parent="#sidebar-menu">
                                           @if ( $usr->can('bundleofferAdd') || $usr->can('bundleofferView') ||  $usr->can('bundleofferDelete') ||  $usr->can('bundleofferUpdate'))
                        <li><a class="nav-link {{ Route::is('bundle-offer.show') || Route::is('bundle-offer.create') || Route::is('bundle-offer.edit') || Route::is('bundle-offer.index')  ? 'active' : '' }}" href="{{route('bundle-offer.index')}}">Offer Name</a></li>
                        @endif
                                            @if ($usr->can('offerDetailAdd') || $usr->can('offerDetailView') ||  $usr->can('offerDetailDelete') || $usr->can('offerDetailUpdate'))
                         <li><a class="nav-link {{Route::is('offer-product.index') || Route::is('offer-product.edit') || Route::is('offer-product.create') || Route::is('offer-product.show')   ? 'active' : '' }}" href="{{route('offer-product.index')}}">Offer Product</a></li>
                         @endif
                    </ul>
                </li>
                @endif
                    @if ($usr->can('couponAdd') || $usr->can('couponView') ||  $usr->can('couponDelete') ||  $usr->can('couponUpdate'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('coupon.index') || Route::is('coupon.edit') || Route::is('coupon.create') ? 'active' : '' }}" href="{{ route('coupon.index') }}">
                        <i data-feather="bar-chart-2"></i>
                        <span>Coupons</span>
                    </a>
                </li>
                @endif
                 {{-- UPDATED REWARD POINT MENU --}}
                 @if ($usr->can('rewardPointView'))
                <li class="nav-item">
                    <a class="nav-link" href="#rewardSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="rewardSubmenu">
                        <i data-feather="gift"></i>
                        <span>Reward Point</span>
                        <i data-feather="chevron-down" class="ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled {{ Route::is('reward.settings') || Route::is('reward.history') || Route::is('reward.customer.history') ? 'show' : '' }}" id="rewardSubmenu" data-bs-parent="#sidebar-menu">
                        {{-- You can add permission checks here later if you want --}}
                         @if ($usr->can('rewardPointView'))
                        <li><a class="nav-link {{ Route::is('reward.settings') ? 'active' : '' }}" href="{{ route('reward.settings') }}">Settings</a></li>
                        @endif
                        @if ($usr->can('rewardPointView'))
                        <li><a class="nav-link {{ Route::is('reward.history') || Route::is('reward.customer.history') ? 'active' : '' }}" href="{{ route('reward.history') }}">History</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                 @if ($usr->can('sliderSectionView') || $usr->can('offerSectionControlView') || $usr->can('sideBarView') || $usr->can('headerAdd') || $usr->can('headerView') ||  $usr->can('headerDelete') ||  $usr->can('headerUpdate'))
                <li class="sidebar-title">
                    <span>CMS</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#cmsSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="cmsSubmenu">
                        <i data-feather="file-text"></i>
                        <span>Website CMS</span>
                        <i data-feather="chevron-down" class="ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled {{Route::is('homepage-section.index') || Route::is('featured-category.index') || Route::is('highlight-product.index') || Route::is('offer-section.control.index') ||  Route::is('slider.control.index') ||  Route::is('sidebar-menu.control.index') ||  Route::is('frontend.control.index')  ? 'show' : '' }}" id="cmsSubmenu" data-bs-parent="#sidebar-menu">
                       
                       
                         <li><a class="nav-link {{Route::is('featured-category.index') ? 'active':''}}" href="{{route('featured-category.index')}}"> Home Page First Section</a></li>
                         <li><a class="nav-link {{Route::is('homepage-section.index') ? 'active':''}}" href="{{route('homepage-section.index')}}"> Home Page Second Section</a></li>
                       
                       
                        @if ($usr->can('headerAdd') || $usr->can('headerView') ||  $usr->can('headerDelete') ||  $usr->can('headerUpdate'))
                        <li><a class="nav-link {{ Route::is('frontend.control.index')  ? 'active' : '' }}" href="{{route('frontend.control.index')}}"> Header</a></li>
                        @endif
                        @if ($usr->can('sidebarMenuView'))
                         <li><a class="nav-link {{ Route::is('sidebar-menu.control.index')  ? 'active' : '' }}" href="{{route('sidebar-menu.control.index')}}">Side Bar</a></li>
                          @endif
                          @if ($usr->can('offerSectionControlView'))
                          <li><a class="nav-link {{ Route::is('offer-section.control.index')  ? 'active' : '' }}" href="{{route('offer-section.control.index')}}">Offer Section</a></li>
                          @endif
                          @if ($usr->can('sliderSectionView'))
                          <li><a class="nav-link {{ Route::is('slider.control.index')  ? 'active' : '' }}" href="{{route('slider.control.index')}}">Slider Section</a></li>
                          @endif
                           <li><a class="nav-link {{ Route::is('highlight-product.index') ? 'active' : '' }}" href="{{ route('highlight-product.index') }}">Highlight Product</a></li>
                    </ul>
                </li>
                @endif
                @if ($usr->can('anlyticSettingView'))
<li class="sidebar-title">
                    <span>Marketing</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::is('settings.analytics.index') ? 'active' : '' }}" href="{{ route('settings.analytics.index') }}">
                        <i data-feather="bar-chart-2"></i>
                        <span>Facebook & Google</span>
                    </a>
                </li>
                @endif

@if ($usr->can('stockView') || $usr->can('supplierAdd') || $usr->can('supplierView') ||  $usr->can('supplierDelete') ||  $usr->can('supplierUpdate') ||$usr->can('purchaseAdd') || $usr->can('purchaseView') ||  $usr->can('purchaseDelete') ||  $usr->can('purchaseUpdate'))
                 <li class="sidebar-title">
                    <span>Inventory & Purchase</span>
                </li>
                {{-- NEW PURCHASE MENU --}}
                @if ($usr->can('purchaseAdd') || $usr->can('purchaseView') ||  $usr->can('purchaseDelete') ||  $usr->can('purchaseUpdate'))
                <li class="nav-item">
                    <a class="nav-link" href="#purchaseSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="purchaseSubmenu">
                        <i data-feather="truck"></i>
                        <span>Purchase</span>
                        <i data-feather="chevron-down" class="ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled {{ Route::is('purchase.create')|| Route::is('purchase.index') || Route::is('purchase.show') || Route::is('purchase.edit') ? 'show' : '' }}" id="purchaseSubmenu" data-bs-parent="#sidebar-menu">
@if($usr->can('purchaseAdd'))
                        <li><a class="nav-link {{ Route::is('purchase.create') ? 'active' : '' }}" href="{{ route('purchase.create') }}">Create Purchase</a></li>
@endif
@if($usr->can('purchaseView'))
                        <li><a class="nav-link {{ Route::is('purchase.index') || Route::is('purchase.show') || Route::is('purchase.edit') ? 'active' : '' }}" href="{{ route('purchase.index') }}">Purchase List</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                {{-- NEW SUPPLIER MENU --}}
                @if($usr->can('supplierAdd') || $usr->can('supplierView') ||  $usr->can('supplierDelete') ||  $usr->can('supplierUpdate'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('supplier.index') || Route::is('supplier.create') || Route::is('supplier.edit') || Route::is('supplier.show') ? 'active' : '' }}" href="{{ route('supplier.index') }}">
                        <i data-feather="briefcase"></i>
                        <span>Supplier</span>
                    </a>
                </li>
                @endif
                @if ($usr->can('stockView'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('stock.index') ? 'active' : '' }}" href="{{ route('stock.index') }}">
                        <i data-feather="archive"></i>
                        <span>Stock</span>
                    </a>
                </li>
                @endif
                @endif
                {{-- <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i data-feather="alert-triangle"></i>
                        <span>Damage Product</span>
                    </a>
                </li> --}}
   @if ($usr->can('expenseAdd') || $usr->can('expenseView') ||  $usr->can('expenseDelete') ||  $usr->can('expenseUpdate') || $usr->can('expense-categoryAdd') || $usr->can('expense-categoryView') ||  $usr->can('expense-categoryDelete') ||  $usr->can('expense-categoryUpdate'))
                 {{-- NEW EXPENSE MENU --}}
                <li class="sidebar-title">
                    <span>Finance</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#expenseSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="expenseSubmenu">
                        <i data-feather="dollar-sign"></i>
                        <span>Expense</span>
                        <i data-feather="chevron-down" class="ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled {{ Route::is('expense-category.index') || Route::is('expense.index') ? 'show' : '' }}" id="expenseSubmenu" data-bs-parent="#sidebar-menu">
                         @if ( $usr->can('expense-categoryAdd') || $usr->can('expense-categoryView') ||  $usr->can('expense-categoryDelete') ||  $usr->can('expense-categoryUpdate'))
                        <li><a class="nav-link {{ Route::is('expense-category.index') ? 'active' : '' }}" href="{{ route('expense-category.index') }}">Expense Category</a></li>
                        @endif
                         @if ($usr->can('expenseAdd') || $usr->can('expenseView') ||  $usr->can('expenseDelete') ||  $usr->can('expenseUpdate'))
                        <li><a class="nav-link {{ Route::is('expense.index') ? 'active' : '' }}" href="{{ route('expense.index') }}">Expense List</a></li>
                        @endif
                    </ul>
                </li>
@endif
   @if ($usr->can('accountSettingAdd') || $usr->can('accountSettingView') ||$usr->can('accountSettingDelete') || $usr->can('accountSettingUpdate') || $usr->can('opening-balancesAdd') || $usr->can('opening-balancesView') ||$usr->can('opening-balancesDelete') || $usr->can('opening-balancesUpdate') || $usr->can('coaAdd') || $usr->can('coaView') ||$usr->can('coaDelete') || $usr->can('coaUpdate') || $usr->can('bankAdd') || $usr->can('bankView') ||$usr->can('bankDelete') || $usr->can('bankUpdate') || $usr->can('shareholderAdd') || $usr->can('shareholderView') ||  $usr->can('shareholderDelete') ||  $usr->can('shareholderUpdate'))
     {{-- NEW EXPENSE MENU --}}
                <li class="sidebar-title">
                    <span>Accounting</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#shareholderSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="shareholderSubmenu">
                        <i data-feather="user-plus"></i>
                        <span>Shareholders</span>
                        <i data-feather="chevron-down" class="ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled {{Route::is('shareholder-withdraws.edit') || Route::is('shareholder-withdraws.create') || Route::is('shareholder-withdraws.index')  || Route::is('shareholder-deposits.edit') || Route::is('shareholder-deposits.create') || Route::is('shareholder-deposits.index') || Route::is('shareholders.index') || Route::is('shareholders.index') ? 'show' : '' }}" id="shareholderSubmenu" data-bs-parent="#sidebar-menu">
                         @if ( $usr->can('shareholderAdd') || $usr->can('shareholderView') ||  $usr->can('shareholderDelete') ||  $usr->can('shareholderUpdate'))
                        <li><a class="nav-link {{ Route::is('shareholders.index') ? 'active' : '' }}" href="{{ route('shareholders.index') }}">Shareholder List</a></li>
                        <li><a class="nav-link {{Route::is('shareholder-deposits.edit') || Route::is('shareholder-deposits.create') || Route::is('shareholder-deposits.index') ? 'active' : '' }}" href="{{ route('shareholder-deposits.index') }}"> Deposit List</a></li>
                        <li><a class="nav-link {{Route::is('shareholder-withdraws.edit') || Route::is('shareholder-withdraws.create') || Route::is('shareholder-withdraws.index') ? 'active' : '' }}" href="{{ route('shareholder-withdraws.index') }}"> Withdrawal List</a></li>
                        @endif
                        
                    </ul>
                </li>
                @if ($usr->can('bankAdd') || $usr->can('bankView') ||$usr->can('bankDelete') || $usr->can('bankUpdate'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('banks.index')  || Route::is('banks.create') || Route::is('banks.edit') ? 'active' : '' }}" href="{{ route('banks.index') }}">
                        <i data-feather="folder"></i>
                        <span>Bank List</span>
                    </a>
                </li>
                @endif
                     @if ($usr->can('coaAdd') || $usr->can('coaView') ||$usr->can('coaDelete') || $usr->can('coaUpdate'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('accounts.index')  || Route::is('accounts.create') || Route::is('accounts.edit') ? 'active' : '' }}" href="{{ route('accounts.index') }}">
                        <i data-feather="folder"></i>
                        <span>COA</span>
                    </a>
                </li>
                @endif
                 @if ($usr->can('accountSettingAdd') || $usr->can('accountSettingView') ||$usr->can('accountSettingDelete') || $usr->can('accountSettingUpdate'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('accounting-settings.index')  || Route::is('accounting-settings.create') || Route::is('accounting-settings.edit') ? 'active' : '' }}" href="{{ route('accounting-settings.index') }}">
                        <i data-feather="folder"></i>
                        <span>Accounting Settings</span>
                    </a>
                </li>
                @endif
                 @if ($usr->can('opening-balancesAdd') || $usr->can('opening-balancesView') ||$usr->can('opening-balancesDelete') || $usr->can('opening-balancesUpdate'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('opening-balances.index')  || Route::is('opening-balances.create') || Route::is('opening-balances.edit') ? 'active' : '' }}" href="{{ route('opening-balances.index') }}">
                        <i data-feather="folder"></i>
                        <span>Opening Balances</span>
                    </a>
                </li>
                @endif
@endif
  @if ($usr->can('reportAdd'))
                <li class="sidebar-title">
                    <span>Report</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#reportSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="reportSubmenu">
                        <i data-feather="file-text"></i>
                        <span>Report</span>
                        <i data-feather="chevron-down" class="ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled {{Route::is('profit_and_loss.index') || Route::is('trail_balance.index') || Route::is('balance_sheet.index') || Route::is('general_ledger.index') || Route::is('reports.bank_book.index') || Route::is('reports.cash_book.index') || Route::is('report.profit_loss') || Route::is('report.income') || Route::is('report.sales') || Route::is('report.customer') || Route::is('report.category') ? 'show' : '' }}" id="reportSubmenu" data-bs-parent="#sidebar-menu">
                         
                       @if ($usr->can('cashbookReportView'))
                         <li><a class="nav-link {{ Route::is('reports.cash_book.index') ? 'active' : '' }}" href="{{ route('reports.cash_book.index') }}">Cash Book</a></li>
@endif
          @if ($usr->can('bankbookReportView'))
                         <li><a class="nav-link {{ Route::is('reports.bank_book.index') ? 'active' : '' }}" href="{{ route('reports.bank_book.index') }}">Bank Book</a></li>
@endif
  @if ($usr->can('generalLedgerView'))
                         <li><a class="nav-link {{ Route::is('general_ledger.index') ? 'active' : '' }}" href="{{ route('general_ledger.index') }}">General Ledger</a></li>
@endif
  @if ($usr->can('balanceSheetView'))
                         <li><a class="nav-link {{ Route::is('balance_sheet.index') ? 'active' : '' }}" href="{{ route('balance_sheet.index') }}">Balance Sheet</a></li>
@endif
  @if ($usr->can('trailBalanceView'))
                         <li><a class="nav-link {{ Route::is('trail_balance.index') ? 'active' : '' }}" href="{{ route('trial_balance.index') }}">Trail Balance</a></li>
@endif
  @if ($usr->can('profitlossView'))
                         <li><a class="nav-link {{ Route::is('profit_and_loss.index') ? 'active' : '' }}" href="{{ route('profit_and_loss.index') }}">Profit & Loss(Accounting)</a></li>
@endif
                        <li><a class="nav-link {{ Route::is('report.sales') ? 'active' : '' }}" href="{{ route('report.sales') }}">Sales Report</a></li>
                        <li><a class="nav-link {{ Route::is('report.customer') ? 'active' : '' }}" href="{{ route('report.customer') }}">Customer Report</a></li>
                        <li><a class="nav-link {{ Route::is('report.category') ? 'active' : '' }}" href="{{ route('report.category') }}">Category Wise Report</a></li>
                        <li><a class="nav-link {{ Route::is('report.income') ? 'active' : '' }}" href="{{ route('report.income') }}">Income Report</a></li>
                        <li><a class="nav-link {{ Route::is('report.profit_loss') ? 'active' : '' }}" href="{{ route('report.profit_loss') }}">Profit & Loss Report</a></li>
                    </ul>
                </li>
                @endif

                <li class="sidebar-title">
                    <span>Website Content</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#contentSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="contentSubmenu">
                        <i data-feather="file-text"></i>
                        <span>Content</span>
                        <i data-feather="chevron-down" class="ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled {{Route::is('review.index') || Route::is('review.edit') || Route::is('review.show')||Route::is('extraPage.index') ||  Route::is('message.index') ||  Route::is('aboutUs.index') ||  Route::is('socialLink.index')  ? 'show' : '' }}" id="contentSubmenu" data-bs-parent="#sidebar-menu">
                      

                        @if ($usr->can('reviewAdd') || $usr->can('reviewView') ||  $usr->can('reviewDelete') ||  $usr->can('reviewUpdate'))
                    <li>
                        <a class="nav-link {{ Route::is('review.index') || Route::is('review.edit') || Route::is('review.show') ? 'active' : '' }}" href="{{ route('review.index') }}">Review</a>
                    </li>
                    @endif
                          @if ($usr->can('socialLinkAdd') || $usr->can('socialLinkView') ||  $usr->can('socialLinkDelete') ||  $usr->can('socialLinkUpdate'))
                    <li>
                        <a class="nav-link {{ Route::is('socialLink.index') || Route::is('socialLink.edit') || Route::is('socialLink.create') ? 'active' : '' }}" href="{{ route('socialLink.index') }}">Social Link</a>
                    </li>
                    @endif

 
                    @if ($usr->can('extraPageAdd') || $usr->can('extraPageView') ||  $usr->can('extraPageDelete') ||  $usr->can('extraPageUpdate'))
                    <li >
                        <a class="nav-link {{ Route::is('extraPage.index') || Route::is('extraPage.edit') || Route::is('extraPage.create') ? 'active' : '' }}" href="{{ route('extraPage.index') }}">Extra Page</a>
                    </li>
                    @endif

                        @if ($usr->can('messageAdd') || $usr->can('messageView') ||  $usr->can('messageDelete') ||  $usr->can('messageUpdate'))
                    <li >
                        <a class="nav-link {{ Route::is('message.index') || Route::is('message.edit') || Route::is('message.create') ? 'active' : '' }}" href="{{ route('message.index') }}">Message</a>
                    </li>
                    @endif

                    @if ($usr->can('aboutUsAdd') || $usr->can('aboutUsView') ||  $usr->can('aboutUsDelete') ||  $usr->can('aboutUsUpdate'))
                    <li>
                        <a class="nav-link {{ Route::is('aboutUs.index') || Route::is('aboutUs.edit') || Route::is('aboutUs.create') ? 'active' : '' }}" href="{{ route('aboutUs.index') }}">About Us</a>
                    </li>
                    @endif
                    </ul>
                </li>


                 @if ($usr->can('sliderSectionView') || $usr->can('offerSectionControlView') || $usr->can('sideBarView') || $usr->can('headerAdd') || $usr->can('headerView') ||  $usr->can('headerDelete') ||  $usr->can('headerUpdate'))
                <li class="sidebar-title">
                    <span>CMS</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#cmsSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="cmsSubmenu">
                        <i data-feather="file-text"></i>
                        <span>Website CMS</span>
                        <i data-feather="chevron-down" class="ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled {{Route::is('highlight-product.index') || Route::is('offer-section.control.index') ||  Route::is('slider.control.index') ||  Route::is('sidebar-menu.control.index') ||  Route::is('frontend.control.index')  ? 'show' : '' }}" id="cmsSubmenu" data-bs-parent="#sidebar-menu">
                                         @if ($usr->can('headerAdd') || $usr->can('headerView') ||  $usr->can('headerDelete') ||  $usr->can('headerUpdate'))
                        <li><a class="nav-link {{ Route::is('frontend.control.index')  ? 'active' : '' }}" href="{{route('frontend.control.index')}}"> Header</a></li>
                        @endif
                        @if ($usr->can('sidebarMenuView'))
                         <li><a class="nav-link {{ Route::is('sidebar-menu.control.index')  ? 'active' : '' }}" href="{{route('sidebar-menu.control.index')}}">Side Bar</a></li>
                          @endif
                          @if ($usr->can('offerSectionControlView'))
                          <li><a class="nav-link {{ Route::is('offer-section.control.index')  ? 'active' : '' }}" href="{{route('offer-section.control.index')}}">Offer Section</a></li>
                          @endif
                          @if ($usr->can('sliderSectionView'))
                          <li><a class="nav-link {{ Route::is('slider.control.index')  ? 'active' : '' }}" href="{{route('slider.control.index')}}">Slider Section</a></li>
                          @endif
                           <li><a class="nav-link {{ Route::is('highlight-product.index') ? 'active' : '' }}" href="{{ route('highlight-product.index') }}">Highlight Product</a></li>
                    </ul>
                </li>
                @endif
                @if ( $usr->can('userAdd') || $usr->can('userView') ||  $usr->can('userDelete') ||  $usr->can('userUpdate') || $usr->can('designationAdd') || $usr->can('designationView') ||  $usr->can('designationDelete') ||  $usr->can('designationUpdate') || $usr->can('branchAdd') || $usr->can('branchView') ||  $usr->can('branchDelete') ||  $usr->can('branchUpdate'))
                <li class="sidebar-title">
                    <span>Settings</span>
                </li>

                 <li class="nav-item">
                    <a class="nav-link {{ Route::is('setting.sms.index')  ? 'active' : '' }}" href="{{ route('setting.sms.index') }}">
                        <i data-feather="folder"></i>
                        <span>SMS Setting</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#accountSettingsSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="accountSettingsSubmenu">
                        <i data-feather="user"></i>
                        <span>Account</span>
                        <i data-feather="chevron-down" class="ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled {{Route::is('users.show') || Route::is('users.index') || Route::is('users.edit') || Route::is('users.create') || Route::is('branch.index') || Route::is('branch.edit') || Route::is('branch.create') || Route::is('designation.index') || Route::is('designation.edit') || Route::is('designation.create') ? 'show' : '' }}" id="accountSettingsSubmenu" data-bs-parent="#sidebar-menu">

                          @if ($usr->can('designationAdd') || $usr->can('designationView') ||  $usr->can('designationDelete') ||  $usr->can('designationUpdate'))

                        <a class="nav-link {{ Route::is('designation.index') || Route::is('designation.edit') || Route::is('designation.create') ? 'active' : '' }}" href="{{ route('designation.index') }}">Designation</a>
                   
                    @endif


                          @if ($usr->can('branchAdd') || $usr->can('branchView') ||  $usr->can('branchDelete') ||  $usr->can('branchUpdate'))

                        <a class="nav-link {{ Route::is('branch.index') || Route::is('branch.edit') || Route::is('branch.create') ? 'active' : '' }}" href="{{ route('branch.index') }}">Branch</a>

                    @endif


                    @if ($usr->can('userAdd') || $usr->can('userView') ||  $usr->can('userDelete') ||  $usr->can('userUpdate'))

                        <a class="nav-link {{ Route::is('users.show') || Route::is('users.index') || Route::is('users.edit') || Route::is('users.create') ? 'active' : '' }}" href="{{ route('users.index') }}">User</a>

                    @endif
                       
                    </ul>
                </li>
                @endif
                   @if ($usr->can('permissionAdd') || $usr->can('permissionView') ||  $usr->can('permissionDelete') ||  $usr->can('permissionUpdate') || $usr->can('roleAdd') || $usr->can('roleView') ||  $usr->can('roleUpdate') ||  $usr->can('roleDelete') || $usr->can('panelSettingAdd') || $usr->can('panelSettingView') ||  $usr->can('panelSettingDelete') ||  $usr->can('panelSettingUpdate'))
                 <li class="nav-item">
                    <a class="nav-link" href="#generalSettingsSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="generalSettingsSubmenu">
                        <i data-feather="settings"></i>
                        <span>General</span>
                        <i data-feather="chevron-down" class="ms-auto"></i>
                    </a>
                    <ul class="collapse list-unstyled {{Route::is('roles.show') || Route::is('permissions.index') || Route::is('permissions.edit') || Route::is('permissions.create') || Route::is('roles.index') || Route::is('roles.edit') || Route::is('roles.create') || Route::is('systemInformation.index') || Route::is('systemInformation.edit') || Route::is('systemInformation.create') ? 'show' : '' }}" id="generalSettingsSubmenu" data-bs-parent="#sidebar-menu">

                            @if ($usr->can('panelSettingAdd') || $usr->can('panelSettingView') ||  $usr->can('panelSettingDelete') ||  $usr->can('panelSettingUpdate'))
                   
                        <a class="nav-link {{ Route::is('systemInformation.index') || Route::is('systemInformation.edit') || Route::is('systemInformation.create') ? 'active' : '' }}" href="{{ route('systemInformation.index') }}">Panel Settings</a>
                    
                    @endif

                    @if ($usr->can('roleAdd') || $usr->can('roleView') ||  $usr->can('roleEdit') ||  $usr->can('roleDelete'))
                   
                        <a class="nav-link {{Route::is('roles.show') || Route::is('roles.index') || Route::is('roles.edit') || Route::is('roles.create') ? 'active' : '' }}" href="{{ route('roles.index') }}">Role Management</a>
                    
                    @endif

                    @if ($usr->can('permissionAdd') || $usr->can('permissionView') ||  $usr->can('permissionDelete') ||  $usr->can('permissionUpdate'))
                   
                        <a class="nav-link {{ Route::is('permissions.index') || Route::is('permissions.edit') || Route::is('permissions.create') ? 'active' : '' }}" href="{{ route('permissions.index') }}">Permission Management</a>
                    
                    @endif

                    </ul>
                </li>
                @endif
            </ul>
        </nav>

 