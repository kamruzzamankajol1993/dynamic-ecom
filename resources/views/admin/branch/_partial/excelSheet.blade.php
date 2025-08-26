<table>
    <thead>
        <tr>
            <th >ID</th>
            <th >Branch/Agent Organization Name</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($branchList as $key=>$branchLists)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $branchLists->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>