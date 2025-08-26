<table>
    <thead>
        <tr>
            <th>Sl</th>
            <th>Group Name</th>
            <th>Permission Name</th>
        </tr>
    </thead>
    <tbody>
         @foreach($permissionListall as $key=>$allPermissionGroup)
                    <tr>
                        
                        <td>{{$key+1}} </td>
                        <td>{{ $allPermissionGroup->group_name }}</td>
                        <td>
                            <?php
        
                            $permissionList = DB::table('permissions')->where('group_name',$allPermissionGroup->group_name)
                            ->select('name')->get();
                            
                                                                    ?>
                            
                                                               @foreach($permissionList as $allPermissionList)
                            
                                                             {{ $allPermissionList->name }},
                            
                                                               @endforeach
                        </td>
                       
              
                    </tr>
                    @endforeach
    </tbody>
</table>