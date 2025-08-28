<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Company Name</th>
            <th>Contact Person</th>
            <th>Phone</th>
            <th>Email Address</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($suppliers as $supplier)
        <tr>
            <td>{{ $supplier->id }}</td>
            <td>{{ $supplier->company_name }}</td>
            <td>{{ $supplier->contact_person }}</td>
            <td>{{ $supplier->phone }}</td>
            <td>{{ $supplier->email }}</td>
            <td>{{ $supplier->status ? 'Active' : 'Inactive' }}</td>
            <td>{{ $supplier->created_at->format('Y-m-d') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>