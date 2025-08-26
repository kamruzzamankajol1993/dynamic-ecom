@extends('pos.master.master')

@section('title', 'POS - Sales')
@section('styles')
  
@endsection

@section('body')
   <div class="row g-2">
                <div class="col-lg-5">
                    <div class="card left-panel p-2">
                            <div class="cart-top-inputs">
                            <div class="position-relative">
                                <div class="input-group mb-2">
                                    <input type="text" id="customer-search" class="form-control" placeholder="Search or select customer...">
                                    <input type="hidden" id="selected-customer-id">
                                    <button class="btn btn-light border" data-bs-toggle="modal" data-bs-target="#addCustomerModal" type="button"><i class="fa-solid fa-plus text-success"></i></button>
                                </div>
                                <div id="customer-results" class="list-group position-absolute w-100" style="top: 100%; z-index: 1050; display: none; max-height: 200px; overflow-y: auto;"></div>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" placeholder="Scan barcode or type the number then hit enter">
                                <button class="btn btn-light border" type="button"><i class="fa-solid fa-check text-primary"></i></button>
                            </div>
                        </div>

                        <div class="cart-table-wrapper">
                             <div class="cart-table-header d-flex justify-content-end">
                                 <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can me-1"></i> Delete Selected</button>
                             </div>
                             <div class="cart-table-scroll">
                                 <table class="table table-hover cart-table">
                                     <thead>
                                         <tr>
                                             <th scope="col" class="text-center"><input class="form-check-input" type="checkbox"></th>
                                             <th scope="col">#</th>
                                             <th scope="col">Item</th>
                                             <th scope="col" class="text-center">Qty</th>
                                             <th scope="col" class="text-end">Price</th>
                                             <th scope="col"></th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                         <tr>
                                             <td class="text-center"><input class="form-check-input" type="checkbox"></td>
                                             <td>1</td><td>FLUCONAZOLE</td><td><div class="input-group input-group-sm qty-control"><button class="btn btn-light border" type="button">-</button><input type="text" class="form-control text-center" value="1"><button class="btn btn-light border" type="button">+</button></div></td><td class="text-end">৳800</td><td class="text-center"><button class="btn btn-sm btn-link text-danger"><i class="fa-solid fa-xmark"></i></button></td>
                                         </tr>
                                         <tr>
                                             <td class="text-center"><input class="form-check-input" type="checkbox"></td>
                                             <td>2</td><td>FUROSEMIDE</td><td><div class="input-group input-group-sm qty-control"><button class="btn btn-light border" type="button">-</button><input type="text" class="form-control text-center" value="3"><button class="btn btn-light border" type="button">+</button></div></td><td class="text-end">৳4,500</td><td class="text-center"><button class="btn btn-sm btn-link text-danger"><i class="fa-solid fa-xmark"></i></button></td>
                                         </tr>
                                         <tr>
                                             <td class="text-center"><input class="form-check-input" type="checkbox"></td>
                                             <td>3</td><td>METHOCARBAMOL</td><td><div class="input-group input-group-sm qty-control"><button class="btn btn-light border" type="button">-</button><input type="text" class="form-control text-center" value="2"><button class="btn btn-light border" type="button">+</button></div></td><td class="text-end">৳3,600</td><td class="text-center"><button class="btn btn-sm btn-link text-danger"><i class="fa-solid fa-xmark"></i></button></td>
                                         </tr>
                                         <tr>
                                             <td class="text-center"><input class="form-check-input" type="checkbox"></td>
                                             <td>4</td><td>TACROLIMUS</td><td><div class="input-group input-group-sm qty-control"><button class="btn btn-light border" type="button">-</button><input type="text" class="form-control text-center" value="1"><button class="btn btn-light border" type="button">+</button></div></td><td class="text-end">৳1,000</td><td class="text-center"><button class="btn btn-sm btn-link text-danger"><i class="fa-solid fa-xmark"></i></button></td>
                                         </tr>
                                         <tr>
                                             <td class="text-center"><input class="form-check-input" type="checkbox"></td>
                                             <td>5</td><td>HYDROGEN PEROXIDE</td><td><div class="input-group input-group-sm qty-control"><button class="btn btn-light border" type="button">-</button><input type="text" class="form-control text-center" value="4"><button class="btn btn-light border" type="button">+</button></div></td><td class="text-end">৳7,200</td><td class="text-center"><button class="btn btn-sm btn-link text-danger"><i class="fa-solid fa-xmark"></i></button></td>
                                         </tr>
                                         <tr>
                                             <td class="text-center"><input class="form-check-input" type="checkbox"></td>
                                             <td>6</td><td>ASPIRIN</td><td><div class="input-group input-group-sm qty-control"><button class="btn btn-light border" type="button">-</button><input type="text" class="form-control text-center" value="10"><button class="btn btn-light border" type="button">+</button></div></td><td class="text-end">৳500</td><td class="text-center"><button class="btn btn-sm btn-link text-danger"><i class="fa-solid fa-xmark"></i></button></td>
                                         </tr>
                                         <tr>
                                             <td class="text-center"><input class="form-check-input" type="checkbox"></td>
                                             <td>7</td><td>VITAMIN C</td><td><div class="input-group input-group-sm qty-control"><button class="btn btn-light border" type="button">-</button><input type="text" class="form-control text-center" value="2"><button class="btn btn-light border" type="button">+</button></div></td><td class="text-end">৳1,200</td><td class="text-center"><button class="btn btn-sm btn-link text-danger"><i class="fa-solid fa-xmark"></i></button></td>
                                         </tr>
                                     </tbody>
                                 </table>
                             </div>
                        </div>
                        
                        <div class="cart-summary">
                            <div class="d-flex justify-content-between"><p class="mb-1">Subtotal</p><p class="mb-1 fw-bold">৳17,100</p></div>
                            <div class="d-flex justify-content-between align-items-center"><p class="mb-1">Discount Type</p><div><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="discountType" id="fixed" value="fixed" checked><label class="form-check-label" for="fixed">Fixed</label></div><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="discountType" id="percentage" value="percentage"><label class="form-check-label" for="percentage">%</label></div></div></div>
                            <div class="d-flex justify-content-between align-items-center mb-2"><p class="mb-1">Discount Amount</p><input type="text" class="form-control form-control-sm" style="max-width: 120px;" value="0.00"></div>
                            <hr class="my-1">
                            <div class="d-flex justify-content-between align-items-center"><h6 class="mb-0 fw-bold">Total Payable</h6><h6 class="mb-0 fw-bold grand-total">৳17,100</h6></div>
                            <hr class="my-1">
                            <div class="d-flex justify-content-between align-items-center mb-2"><p class="mb-1">Payment Type</p><select class="form-select form-select-sm" style="max-width: 140px;"><option selected>Cash</option><option value="1">Card</option><option value="2">Mobile Money</option></select></div>
                            <div class="d-flex justify-content-between align-items-center mb-2"><p class="mb-1">Total Pay</p><input type="text" class="form-control form-control-sm" style="max-width: 140px;" value="0.00"></div>
                            <div class="d-flex justify-content-between align-items-center"><p class="mb-1">Due</p><p class="mb-1 fw-bold text-danger">৳17,100</p></div>
                            <div class="d-flex gap-2 mt-2">
                                <button class="btn btn-lock w-100"><i class="fa-solid fa-lock"></i></button>
                                <button class="btn btn-cancel w-100">Cancel</button>
                                <button class="btn btn-hold w-100">Hold</button>
                                <button class="btn btn-pay w-100">Pay</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card right-panel p-2 main-container">
                        <div class="row g-2 mb-2">
                            <div class="col-sm-8"><input type="search" class="form-control" placeholder="Search product by name or sku"></div>
                            <div class="col-sm-4"><select class="form-select"><option selected>Select Category</option><option value="1">Painkillers</option><option value="2">Antibiotics</option><option value="3">Vitamins</option></select></div>
                        </div>
                        
                        <div class="action-buttons-bar">
                            <button class="action-btn">animation category</button>
                            <button class="action-btn">bundle offer</button>
                        </div>

                        <div class="product-grid " >
                            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-2">
                                <div class="col"><div class="card product-card"><img src="https://i.imgur.com/k2p82My.png" class="card-img-top" alt="AMOXICIL"><div class="card-body"><h6 class="card-title">Tablet.AMOXICIL.Antibiotic</h6><div><p class="product-stock">STOCK 92</p><p class="product-price">৳1,000/per unit</p></div></div></div></div>
                                <div class="col"><div class="card product-card"><div class="img-placeholder"><i class="fa-solid fa-camera"></i> NO IMAGE</div><div class="card-body"><h6 class="card-title">Capsule.CEPHALEX.Antibiotic</h6><div><p class="product-stock">STOCK 75</p><p class="product-price">৳1,200/per unit</p></div></div></div></div>
                                <div class="col"><div class="card product-card"><div class="img-placeholder"><i class="fa-solid fa-camera"></i> NO IMAGE</div><div class="card-body"><h6 class="card-title">Tablet.ACETAMIN.Analgesic</h6><div><p class="product-stock">STOCK 11</p><p class="product-price">৳800/per unit</p></div></div></div></div>
                                <div class="col"><div class="card product-card"><div class="img-placeholder"><i class="fa-solid fa-camera"></i> NO IMAGE</div><div class="card-body"><h6 class="card-title">Tablet.IBUPROFE.Painkiller</h6><div><p class="product-stock">STOCK 50</p><p class="product-price">৳1,500/per unit</p></div></div></div></div>
                                <div class="col"><div class="card product-card"><div class="img-placeholder"><i class="fa-solid fa-camera"></i> NO IMAGE</div><div class="card-body"><h6 class="card-title">Tablet.SERTRALIN.Antidepressant</h6><div><p class="product-stock">STOCK 45</p><p class="product-price">৳1,800/per unit</p></div></div></div></div>
                                <div class="col"><div class="card product-card"><div class="img-placeholder"><i class="fa-solid fa-camera"></i> NO IMAGE</div><div class="card-body"><h6 class="card-title">Syrup.ONDANSET.Antiemetic</h6><div><p class="product-stock">STOCK 40</p><p class="product-price">৳1,000/per unit</p></div></div></div></div>
                                <div class="col"><div class="card product-card"><div class="img-placeholder"><i class="fa-solid fa-camera"></i> NO IMAGE</div><div class="card-body"><h6 class="card-title">Tablet.CETIRIZINE.Antihistamine</h6><div><p class="product-stock">STOCK 55</p><p class="product-price">৳1,200/per unit</p></div></div></div></div>
                                <div class="col"><div class="card product-card"><div class="img-placeholder"><i class="fa-solid fa-camera"></i> NO IMAGE</div><div class="card-body"><h6 class="card-title">Capsule.FLUCONAZ.Antifungal</h6><div><p class="product-stock">STOCK 61</p><p class="product-price">৳800/per unit</p></div></div></div></div>
                                <div class="col"><div class="card product-card"><div class="img-placeholder"><i class="fa-solid fa-camera"></i> NO IMAGE</div><div class="card-body"><h6 class="card-title">Cream.ACYCLOVIR.Antiviral</h6><div><p class="product-stock">STOCK 59</p><p class="product-price">৳1,500/per unit</p></div></div></div></div>
                                <div class="col"><div class="card product-card"><div class="img-placeholder"><i class="fa-solid fa-camera"></i> NO IMAGE</div><div class="card-body"><h6 class="card-title">Liquid.HYDROGE.Antiseptic</h6><div><p class="product-stock">STOCK 64</p><p class="product-price">৳1,800/per unit</p></div></div></div></div>
                                <div class="col"><div class="card product-card"><div class="img-placeholder"><i class="fa-solid fa-camera"></i> NO IMAGE</div><div class="card-body"><h6 class="card-title">Inhaler.ALBUTERC.Bronchodilator</h6><div><p class="product-stock">STOCK 86</p><p class="product-price">৳1,000/per unit</p></div></div></div></div>
                                <div class="col"><div class="card product-card"><div class="img-placeholder"><i class="fa-solid fa-camera"></i> NO IMAGE</div><div class="card-body"><h6 class="card-title">Patch.ESTRADIOL.Hormone</h6><div><p class="product-stock">STOCK 80</p><p class="product-price">৳1,200/per unit</p></div></div></div></div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
@section('script')
 
@endsection