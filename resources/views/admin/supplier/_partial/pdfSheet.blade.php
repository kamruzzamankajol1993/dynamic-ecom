<!DOCTYPE html>
<html>
<head>
    <title>Supplier List PDF</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Supplier List</h1>

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
                <td>{{ $supplier->email ?? 'N/A' }}</td>
                <td>{{ $supplier->status ? 'Active' : 'Inactive' }}</td>
                <td>{{ $supplier->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>