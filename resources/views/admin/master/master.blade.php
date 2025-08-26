<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="{{ $ins_name }}">
	<meta name="robots" content="">
    <meta name="keywords" content="{{ $keyword }}">
	<meta name="description" content="{{ $description }}">
	<meta property="og:title" content="{{ $ins_name }}">
	<meta property="og:description" content="{{ $description }}">
	<meta property="og:image" content="{{ asset('/') }}{{ $logo }}">
    <!-- Title -->
    <title>@yield('title')</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('/') }}{{ $icon }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    
    <link rel="stylesheet" href="{{ asset('/') }}public/admin/assets/css/style.css">
     <link rel="stylesheet" href="{{asset('/')}}public/online/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('/') }}public/parsely.css"/>
    
    <!-- CSS -->
    <link rel="stylesheet" href="{{asset('/')}}public/online/alertify.min.css"/>
    {{-- jQuery UI CSS for Datepicker --}}
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
{{-- Summernote CSS --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>

        .btn-custom-sm {
                       
  font-size: 0.875rem;              /* Same as Bootstrap's .btn-sm */
  padding: 0.25rem 0.5rem;          /* Smaller padding */
  border: none;                     /* Remove default border */
  border-radius: 0.2rem;            /* Slightly rounded corners */
  line-height: 1.5;                 /* Consistent text alignment */
  transition: background-color 0.3s ease;
  cursor: pointer;
}

.btn-custom-sm:hover {
  box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.2);  /* Shadow on hover */
}


        .alertify .ajs-dialog {
            top: 50%;
            transform: translateY(-50%);
            margin: auto;
        }


        .ajs-header{

            color:rgb(255, 28, 28) !important;
            font-weight:bold !important;
            font-size:25px !important;
            text-align: center !important;
            font-style: italic;

        }

        .ajs-content{
            color:#35452e !important;
            font-weight:bold !important;
            font-size:15px !important;

        }

        .ajs-ok{
            background-color:#35452e !important;
            color:white !important;
        }

        .ajs-cancel{
            background-color:rgb(255, 28, 28) !important;
            color:white !important;
        }
        
      

.swal2-confirm{

    margin-left: 20px;
}

#ajax-loaderOne
{
  background: rgba( 255, 255, 255, 0.8 );
  display: none;
  height: 100%;
  position: fixed;
  width: 100%;
  z-index: 9999;
}

#ajax-loaderOne img
{
  left: 50%;
  margin-left: -32px;
  margin-top: -32px;
  position: absolute;
  top: 50%;
}
.dt-column-order{
    display: none;
}
        </style>
        <style>
        th.sortable {
            cursor: pointer;
        }
        th.sortable:hover {
            background-color: #f8f9fa;
        }
    </style>
  <style>
#pagination {
    display: flex;
    justify-content: center;
    gap: 6px;
    list-style: none;
    padding-left: 0;
    margin-top: 1rem;
}

.page-item {
    display: inline-block;
}

.page-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 10px !important;
    font-size: 15px !important;
    color: #0d6efd;
    background-color: transparent;
    border: 1px solid #0d6efd !important;
    border-radius: 0.375rem;
    text-decoration: none;
    transition: background-color 0.2s ease, color 0.2s ease;
}

.page-link:hover {
    background-color: #0d6efd !important;
    color: #fff !important;
    text-decoration: none;
}

/* Disabled button look */
.page-item.disabled .page-link {
    color: #aaa;
    border-color: #ccc;
    cursor: not-allowed;
    pointer-events: none;
    padding: 10px;
    font-size: 15px;
}

/* Active page button */
.page-item.active .page-link {
    background-color: #0d6efd !important;
    color: white !important;
    pointer-events: none;
    padding: 10px;
    font-size: 15px;
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.css" integrity="sha512-f8gN/IhfI+0E9Fc/LKtjVq4ywfhYAVeMGKsECzDUHcFJ5teVwvKTqizm+5a84FINhfrgdvjX8hEJbem2io1iTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @yield('css')
</head>
<body>

  <div class="wrapper">
        <!-- Sidebar -->
        @include('admin.include.sidebar')

        <!-- Page Content -->
        <div id="content">
            <!-- Header Navbar -->
            @include('admin.include.header')

            <!-- Main Content Area -->
           @yield('body')

           
        </div>
    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Set current date in header
        const dateElement = document.getElementById('currentDate');
        if (dateElement) {
            const today = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            dateElement.textContent = today.toLocaleDateString('en-US', options);
        }

        // Initialize Feather Icons
        feather.replace();

        // Sidebar Toggle Functionality
        document.getElementById('sidebarToggleBtn').addEventListener('click', function () {
            document.querySelector('.wrapper').classList.toggle('sidebar-collapsed');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="{{ asset('/')}}public/parsely1.js"></script>
    <script>
        $(document).ready(function() {
            $('#summernote').summernote();
        });
    </script>

<!-- end script from elenga--->

<script src="{{asset('/')}}public/online/toastr.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    function activeTag(id) {
        swal({
            title: 'Are You Sure',
            text: "You can't bring it back",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Active it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger',
            buttonsStyling: false,
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                event.preventDefault();
                document.getElementById('adelete-form-'+id).submit();
            } else if (
                // Read more about handling dismissals
                result.dismiss === swal.DismissReason.cancel
            ) {
                swal(
                    'Cancelled',
                    'Your data is safe :)',
                    'error'
                )
            }
        })
    }
</script>
<script type="text/javascript">
    function inactiveTag(id) {
        swal({
            title: 'Are You Sure',
            text: "You can't bring it back",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, inActive it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger',
            buttonsStyling: false,
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                event.preventDefault();
                document.getElementById('adelete-form-'+id).submit();
            } else if (
                // Read more about handling dismissals
                result.dismiss === swal.DismissReason.cancel
            ) {
                swal(
                    'Cancelled',
                    'Your data is safe :)',
                    'error'
                )
            }
        })
    }
</script>
<script type="text/javascript">
    function deleteTag(id) {
        swal({
            title: 'Are You Sure',
            text: "You can't bring it back",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger',
            buttonsStyling: false,
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                event.preventDefault();
                document.getElementById('delete-form-'+id).submit();
            } else if (
                // Read more about handling dismissals
                result.dismiss === swal.DismissReason.cancel
            ) {
                swal(
                    'Cancelled',
                    'Your data is safe :)',
                    'error'
                )
            }
        })
    }
</script>
{!! Toastr::message() !!}
<script>
    @if($errors->any())
        @foreach($errors->all() as $error)
              toastr.error('{{ $error }}','Error',{
                  closeButton:true,
                  progressBar:true,
               });
        @endforeach
    @endif
</script>
<script>
    setTimeout(function(){
  $('#divID').remove();
}, 3000);
</script>

<script src="{{asset('/')}}public/online/alertify.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
    $(function(){
       $(".datepicker23").datepicker({
           dateFormat: "yy-mm-dd",
           changeMonth: true,
           changeYear: true
       });
   });
     </script>

<script>
    $(function(){
       $(".datepicker233").datepicker({
           dateFormat: "dd-mm-yy",
           changeMonth: true,
           changeYear: true,
           calendarWeeks: true,
    todayHighlight: true,
    autoclose: true
       });
   });
     </script>
<script>
    $(document).ready(function() {
        // Find the sidebar container
        var sidebar = $('#sidebar');
        
        // Find the currently active nav-link within the sidebar
        var activeLink = sidebar.find('.nav-link.active');

        // Check if an active link exists
        if (activeLink.length > 0) {
            // Use the scrollIntoView() method to scroll the sidebar
            // 'block: "center"' attempts to place the element in the middle of the scrollable area
            activeLink[0].scrollIntoView({
                behavior: 'smooth', // Optional: for a smooth scrolling effect
                block: 'center'
            });
        }
    });
</script>
    @yield('script')
</body>
</html>
