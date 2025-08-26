<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Permission List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            color: #333;
            margin: 20px;
        }

        header {
            border-bottom: 2px solid #007BFF;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        header h1 {
            margin: 0;
            color: #007BFF;
        }

        .info {
            margin-bottom: 20px;
        }

        .info p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px 12px;
            text-align: left;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        tfoot td {
            font-weight: bold;
            background-color: #f7f7f7;
        }

        footer {
            text-align: center;
            font-size: 10pt;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            position: fixed;
            bottom: 20px;
            width: 90%;
        }
    </style>
</head>
<body>



<table>
    <thead>
        <tr>
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


</body>
</html>
