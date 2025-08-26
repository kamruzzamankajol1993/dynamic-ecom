<table>
    <thead>
        <tr>
            <th >ID</th>
            <th >Designation Name</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($designationList as $key=>$branchLists)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $branchLists->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>