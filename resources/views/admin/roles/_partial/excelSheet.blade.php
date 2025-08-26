<table>
    <thead>
        <tr>
            <th >ID</th>
            <th>Role Name</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($roleListall as $key=>$branchLists)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $branchLists->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>