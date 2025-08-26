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
                       <th class="h6 text-gray-300">View Password</th>
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