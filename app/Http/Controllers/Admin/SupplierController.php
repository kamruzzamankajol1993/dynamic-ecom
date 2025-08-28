<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use App\Exports\SupplierExport;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function index()
    {
        return view('admin.supplier.index');
    }

    public function data(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $query->where('company_name', 'like', $request->search . '%')
                  ->orWhere('contact_person', 'like', $request->search . '%')
                  ->orWhere('email', 'like', $request->search . '%')
                  ->orWhere('phone', 'like', $request->search . '%');
        }

        $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
        $suppliers = $query->paginate(10);

        return response()->json([
            'data' => $suppliers->items(),
            'total' => $suppliers->total(),
            'current_page' => $suppliers->currentPage(),
            'last_page' => $suppliers->lastPage(),
        ]);
    }

    public function create()
    {
        return view('admin.supplier.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:suppliers'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:suppliers'],
            'address' => ['nullable', 'string'],
            'vat_number' => ['nullable', 'string', 'max:50'],
        ]);

        Supplier::create($request->all());

        return redirect()->route('supplier.index')->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        // You can add purchase statistics here later
        return view('admin.supplier.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:suppliers,phone,' . $supplier->id],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:suppliers,email,' . $supplier->id],
            'address' => ['nullable', 'string'],
            'vat_number' => ['nullable', 'string', 'max:50'],
        ]);

        $supplier->update($request->all());

        return redirect()->route('supplier.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return response()->json(['message' => 'Supplier deleted successfully.']);
    }
    
    public function exportPdf()
    {
        $suppliers = Supplier::latest()->get();
        $html = view('admin.supplier._partial.pdfSheet', compact('suppliers'))->render();
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        return $mpdf->Output('supplier-list.pdf', 'D');
    }

    public function exportExcel()
    {
        return Excel::download(new SupplierExport, 'suppliers.xlsx');
    }
}