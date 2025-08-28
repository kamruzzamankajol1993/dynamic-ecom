<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Product; // Import Product model
use App\Models\Size; // Import Size model
use App\Models\AnimationCategory;
use App\Models\BundleOffer; // 1. Import BundleOffer
use App\Models\BundleOfferProduct; // 2. Import BundleOfferProduct
use Carbon\Carbon; // 3. Import Carbon for date comparison
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Mpdf\Mpdf;
class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:posView')->only(['index']);
        $this->middleware('can:posAdd')->only(['create', 'store']);
        $this->middleware('can:posUpdate')->only(['edit', 'update']);
        $this->middleware('can:posDelete')->only(['destroy']);
    }

    public function index()
    {
        $categories = Category::where('status', true)->latest()->get();
        return view('pos.index', compact('categories'));
    }

     public function search(Request $request)
    {
        $query = $request->get('q');
        $customers = Customer::where('name', 'LIKE', $query . '%')
            ->orWhere('phone', 'LIKE', $query . '%')
            ->limit(10)
            ->get();

        return response()->json($customers);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);

        return response()->json($customer);
    }

     /**
     * Fetch products for the POS grid via AJAX.
     */
    public function getProducts(Request $request)
    {
        $query = Product::with('category')
            ->where('status', true)
            ->latest();

        // Handle search query
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('product_code', 'LIKE', "%{$search}%");
            });
        }

        // Handle category filter
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

           // It checks for a boolean flag from the frontend.
        if ($request->boolean('filter_by_all_animation')) {
            // Use whereHas to find products that have AT LEAST ONE assigned category of type 'animation'.
            $query->whereHas('assigns', function ($q) {
                $q->where('type', 'animation');
            });
        }

        $products = $query->paginate(24); // Paginate with 24 products per page

        // Return a rendered view for the product cards
        return response()->json([
            'html' => view('pos.partials.product_grid', compact('products'))->render(),
            'next_page_url' => $products->nextPageUrl()
        ]);
    }

    /**
     * Fetch detailed information for a single product for the modal.
     */
     public function getProductDetails(Product $product)
    {
        // Eager load variants and their associated color.
        // The 'detailed_sizes' accessor on ProductVariant will be automatically
        // appended to the JSON response thanks to the $appends property.
        $product->load('variants.color');

        return response()->json($product);
    }

    public function getBundleOffers()
    {
        $today = Carbon::today();
        
        $offers = BundleOffer::where('status', true)
            ->whereDate('startdate', '<=', $today)
            ->whereDate('enddate', '>=', $today)
            ->with('bundleOfferProducts')
            ->latest()
            ->get();

        // To efficiently get the image for each offer card, we'll fetch them in one go.
        $firstProductIds = [];
        foreach ($offers as $offer) {
            foreach ($offer->bundleOfferProducts as $productSet) {
                if (isset($productSet->product_id[0])) {
                    $firstProductIds[] = $productSet->product_id[0];
                }
            }
        }
        
        $productsWithImages = Product::whereIn('id', array_unique($firstProductIds))->get(['id', 'main_image'])->keyBy('id');

        // Attach the image path to each product set
        foreach ($offers as $offer) {
            foreach ($offer->bundleOfferProducts as $productSet) {
                $firstProductId = $productSet->product_id[0] ?? null;
                if ($firstProductId && isset($productsWithImages[$firstProductId])) {
                    $productSet->image = $productsWithImages[$firstProductId]->main_image[0] ?? null;
                } else {
                    $productSet->image = null;
                }
            }
        }

        return response()->json($offers);
    }

    /**
     * Fetch the details of a specific bundle offer, including all its products with variants.
     */
    public function getBundleOfferDetails(BundleOfferProduct $bundleOfferProduct)
    {
        if (empty($bundleOfferProduct->product_id)) {
            return response()->json(['products' => []]);
        }
        
        // Fetch all products in the bundle, with their variants and colors.
        // The `detailed_sizes` accessor we created earlier will be automatically appended.
        $products = Product::whereIn('id', $bundleOfferProduct->product_id)
            ->with('variants.color')
            ->get();

        return response()->json([
            'bundle' => $bundleOfferProduct,
            'products' => $products
        ]);
    }

         /**
     * Store the POS order.
     */
    public function storeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'cart' => 'required|array|min:1',
            'subtotal' => 'required|numeric',
            'discount' => 'required|numeric',
            'shipping_cost' => 'required|numeric', // Add validation for shipping_cost
            'cod' => 'required|numeric',
            'total_payable' => 'required|numeric',
            'total_pay' => 'required|numeric',
            'due' => 'required|numeric',
            'notes' => 'nullable|string|max:1000', 
            'payment_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Use a database transaction to ensure data integrity
        try {
            DB::beginTransaction();
            $customer = Customer::find($request->customer_id);

            if($request->payment_method === 'Cash') {
                $paymentTerm = 'cod';
            }else{
                $paymentTerm = 'online';
            }

            $order = Order::create([
                'customer_id' => $request->customer_id,
                'invoice_no' => 'INV-' . time() . mt_rand(100, 999),
                'subtotal' => $request->subtotal,
                'shipping_address' => $customer->address ?? 'N/A',
                'billing_address' => $customer->address ?? 'N/A',
                'discount' => $request->discount,
                'total_amount' => $request->total_payable,
                'total_pay' => $request->total_pay,
                'payment_term' => $paymentTerm,
                'shipping_cost' => $request->shipping_cost,
                'cod' => $request->cod,
                'due' => $request->due,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->due > 0 ? 'unpaid' : 'paid',
                'status' => 'pending',
                'order_from' => 'pos',
                'notes' => $request->notes,
            ]);

            foreach ($request->cart as $item) {
                if ($item['type'] === 'product') {
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'product_id' => $item['productId'],
                        'product_variant_id' => $item['variantId'],
                        'color' => $item['colorName'],
                        'size' => $item['sizeName'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);

                    // Decrement stock for single product
                    $variant = ProductVariant::find($item['variantId']);
                    if ($variant) {
                        $sizes = $variant->sizes;
                        foreach ($sizes as $key => $sizeInfo) {
                            if ($sizeInfo['size_id'] == $item['sizeId']) {
                                $newQuantity = $sizeInfo['quantity'] - $item['quantity'];
                                if ($newQuantity < 0) {
                                    throw new \Exception('Not enough stock for ' . $item['productName']);
                                }
                                $sizes[$key]['quantity'] = $newQuantity;
                                break;
                            }
                        }
                        $variant->sizes = $sizes;
                        $variant->save();
                    }
                }
                // ==========================================================
                // ========= UPDATED BUNDLE SAVING LOGIC STARTS HERE ========
                // ==========================================================
                elseif ($item['type'] === 'bundle') {
                    $isFirstBundleItem = true;

                    // Loop through each product WITHIN the bundle
                    foreach ($item['products'] as $bundleProduct) {
                        $priceForThisItem = 0;

                        // Assign the total bundle price to the first item, and 0 to the rest
                        if ($isFirstBundleItem) {
                            $priceForThisItem = $item['price'];
                            $isFirstBundleItem = false;
                        }

                        OrderDetail::create([
                            'order_id' => $order->id,
                            'product_id' => $bundleProduct['productId'],
                            'product_variant_id' => $bundleProduct['variantId'],
                            'color' => $bundleProduct['colorName'],
                            'size' => $bundleProduct['sizeName'],
                            'quantity' => 1, // Quantity for each item in a bundle is 1
                            'unit_price' => $priceForThisItem,
                            'subtotal' => $priceForThisItem,
                        ]);

                        // Decrement stock for each item in the bundle
                        $variant = ProductVariant::find($bundleProduct['variantId']);
                        if ($variant) {
                           $sizes = $variant->sizes;
                           foreach ($sizes as $key => $sizeInfo) {
                               if ($sizeInfo['size_id'] == $bundleProduct['sizeId']) {
                                   $sizes[$key]['quantity'] -= 1; // Quantity is 1 for each bundle item
                                   if ($sizes[$key]['quantity'] < 0) {
                                       throw new \Exception('Not enough stock for bundle item ' . $bundleProduct['productName']);
                                   }
                                   break;
                               }
                           }
                           $variant->sizes = $sizes;
                           $variant->save();
                       }
                    }
                }
                // ========================================================
                // ========= UPDATED BUNDLE SAVING LOGIC ENDS HERE ==========
                // ========================================================
            }
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order created successfully!', 'order_id' => $order->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Order creation failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Fetch and return the HTML for an invoice modal.
     */
    public function showInvoice(Order $order)
    {
        // Eager load the relationships needed for the invoice
        $order->load('customer', 'orderDetails.product');
        return view('pos.partials.invoice_modal_content', compact('order'))->render();
    }

    /**
     * Generate and stream a PDF invoice using mPDF.
     */
    public function printInvoice(Request $request, Order $order)
    {
        $order->load('customer', 'orderDetails.product');
        
        $printType = $request->query('type', 'a4'); // Default to A4
        $viewPath = $printType === 'pos' ? 'pos.print.pos_invoice' : 'pos.print.a4_invoice';
        
        $html = view($viewPath, compact('order'))->render();
        
        // Setup mPDF based on print type
        if ($printType === 'pos') {
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => [80, 297], // 80mm width, height is variable
                'margin_left' => 5,
                'margin_right' => 5,
                'margin_top' => 5,
                'margin_bottom' => 5,
            ]);
        } else { // A4
            $mpdf = new Mpdf();
        }

        $mpdf->WriteHTML($html);
        
        // 'I' outputs the PDF directly to the browser
        return $mpdf->Output('invoice-'.$order->invoice_no.'.pdf', 'I');
    }
}
