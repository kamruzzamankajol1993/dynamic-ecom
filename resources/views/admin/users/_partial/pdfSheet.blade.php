<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Partner List</title>
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



<table id="assignmentTable" class="table table-striped display">
                <thead>
                    <tr>
                       
                        <th class="h6 text-gray-300">Sl</th>
                        <th class="h6 text-gray-300">Branch Name</th>
                        <th class="h6 text-gray-300">Profile Image</th>
                        <th class="h6 text-gray-300">Name</th>
                        <th class="h6 text-gray-300">Designation Name</th>
                        <th class="h6 text-gray-300">Phone</th>
                        <th class="h6 text-gray-300">Email</th>
                        <th class="h6 text-gray-300">Address</th>
                        <th class="h6 text-gray-300">Roles</th>
                        <th class="h6 text-gray-300">Status</th>
                      <th class="h6 text-gray-300">Password</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($userList as $key=>$user)
                    <tr>
                       
                        <td class="h6 mb-0 fw-medium text-gray-300">{{ $key+1 }}</td>
                        <td class="h6 mb-0 fw-medium text-gray-300">{{ \App\Models\Branch::where('id',$user->branch_id)->value('name') }}</td>
                        <td class="h6 mb-0 fw-medium text-gray-300">

                            @if(empty($user->image))

                            <img src="{{asset('/')}}public/admin/images/profile/user.jpg" style="height:20px;"/>

                            @else
                            
                            <img src="{{ asset('/') }}{{ $user->image }}" style="height:20px;"/>

                            @endif
                        
                        </td>
                        <td class="h6 mb-0 fw-medium text-gray-300">{{ $user->name }}</td>
                        <td class="h6 mb-0 fw-medium text-gray-300">{{ \App\Models\Designation::where('id',$user->designation_id)->value('name') }}</td>
                        <td class="h6 mb-0 fw-medium text-gray-300">{{ $user->phone }}</td>
                        <td class="h6 mb-0 fw-medium text-gray-300">{{ $user->email }}</td>
                        <td class="h6 mb-0 fw-medium text-gray-300">{{ $user->address }}</td>
                        <td class="h6 mb-0 fw-medium text-gray-300">
                            @if(!empty($user->getRoleNames()))
                              @foreach($user->getRoleNames() as $v)
                                 <label class="badge bg-success">{{ $v }}</label>
                              @endforeach
                            @endif
                          </td>
                        <td >
                            
                            @if($user->status == 1)
                            
                            <span class="badge bg-success">Active</span>

                           @else
                           <span class="badge bg-danger">InActive</span>
                           @endif
                        
                        </td> 
                                                <td class="h6 mb-0 fw-medium text-gray-300">{{ $user->viewpassword }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>


</body>
</html>
