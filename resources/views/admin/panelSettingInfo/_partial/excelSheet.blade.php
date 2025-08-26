<table>
    <thead>
       
            <tr>
                    <th >Sl</th>
                 
                   <th >Branch Name</th>
            <th>Icon</th>
            <th  >Logo</th>
            <th >Name</th>
            <th >Phone</th>
            <th >Email</th>
            <th >Address</th>
            
                </tr>
        
    </thead>
    <tbody>
          @foreach($systemInformation as $key=>$panelSettingInfos)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ \App\Models\Branch::where('id',$panelSettingInfos->branch_id)->value('name') }}</td>
                                        <td><img src="{{ asset('/') }}{{ $panelSettingInfos->icon }}" style="height:20px;"/></td>
                                        <td><img src="{{ asset('/') }}{{ $panelSettingInfos->logo }}" style="height:20px;"/></td>
                                        <td>{{ $panelSettingInfos->ins_name }}</td>
                                        <td>{{ $panelSettingInfos->phone }}</td>
                                        <td>{{ $panelSettingInfos->email }}</td>
                                        <td>{{ $panelSettingInfos->address }}</td>
                                     
                                    
                                    </tr>
                                    @endforeach
    </tbody>
</table>