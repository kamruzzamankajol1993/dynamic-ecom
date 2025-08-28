<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="{{ $ins_name }}">
	<meta name="robots" content="">
    <meta name="keywords" content="{{ $keyword }}">
	<meta name="description" content="{{ $description }}">
	<meta property="og:title" content="{{ $ins_name }}">
	<meta property="og:description" content="{{ $description }}">
	<meta property="og:image" content="{{ asset('/') }}{{ $logo }}">
    <!-- Title -->
    <title>Forgot Password</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('/') }}{{ $front_icon_name }}">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{asset('/')}}public/admin/assets/css/bootstrap.min.css">
    <!-- file upload -->
    <link rel="stylesheet" href="{{asset('/')}}public/admin/assets/css/file-upload.css">
    <!-- file upload -->
    <link rel="stylesheet" href="{{asset('/')}}public/admin/assets/css/plyr.css">
 
    <!-- full calendar -->
    <link rel="stylesheet" href="{{asset('/')}}public/admin/assets/css/full-calendar.css">
    <!-- jquery Ui -->
    <link rel="stylesheet" href="{{asset('/')}}public/admin/assets/css/jquery-ui.css">
    <!-- editor quill Ui -->
    <link rel="stylesheet" href="{{asset('/')}}public/admin/assets/css/editor-quill.css">
    <!-- apex charts Css -->
    <link rel="stylesheet" href="{{asset('/')}}public/admin/assets/css/apexcharts.css">
    <!-- calendar Css -->
    <link rel="stylesheet" href="{{asset('/')}}public/admin/assets/css/calendar.css">
    <!-- jvector map Css -->
    <link rel="stylesheet" href="{{asset('/')}}public/admin/assets/css/jquery-jvectormap-2.0.5.css">
    <!-- Main css -->
    <link rel="stylesheet" href="{{asset('/')}}public/admin/assets/css/main.css">
</head> 
<body>
    
<!--==================== Preloader Start ====================-->
<div class="preloader">
    <div class="loader"></div>
  </div>
<!--==================== Preloader End ====================-->

<!--==================== Sidebar Overlay End ====================-->
<div class="side-overlay"></div>
<!--==================== Sidebar Overlay End ====================-->

    <section class="auth d-flex">
        <div class="auth-left bg-main-50 flex-center p-24">
            <img src="{{ asset('/') }}public/admin/assets/images/thumbs/auth-img3.png" alt="">
        </div>
        <div class="auth-right py-40 px-24 flex-center flex-column">
            <div class="auth-right__inner mx-auto w-100">
                <a href="{{route('home')}}" class="auth-right__logo">
                    <img src="{{ asset('/') }}{{ asset('/') }}{{$front_black_logo_name}}" alt="">
                </a>
               
                    @include('flash_message')   
                  
            </div>
        </div>
    </section>

        <!-- Jquery js -->
    <script src="{{asset('/')}}public/admin/assets/js/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle Js -->
    <script src="{{asset('/')}}public/admin/assets/js/boostrap.bundle.min.js"></script>
    <!-- Phosphor Js -->
    <script src="{{asset('/')}}public/admin/assets/js/phosphor-icon.js"></script>
    <!-- file upload -->
    <script src="{{asset('/')}}public/admin/assets/js/file-upload.js"></script>
    <!-- file upload -->
    <script src="{{asset('/')}}public/admin/assets/js/plyr.js"></script>
   
    <!-- full calendar -->
    <script src="{{asset('/')}}public/admin/assets/js/full-calendar.js"></script>
    <!-- jQuery UI -->
    <script src="{{asset('/')}}public/admin/assets/js/jquery-ui.js"></script>
    <!-- jQuery UI -->
    <script src="{{asset('/')}}public/admin/assets/js/editor-quill.js"></script>
    <!-- apex charts -->
    <script src="{{asset('/')}}public/admin/assets/js/apexcharts.min.js"></script>
    <!-- jvectormap Js -->
    <script src="{{asset('/')}}public/admin/assets/js/jquery-jvectormap-2.0.5.min.js"></script>
    <!-- jvectormap world Js -->
    <script src="{{asset('/')}}public/admin/assets/js/jquery-jvectormap-world-mill-en.js"></script>
    
    <!-- main js -->
    <script src="{{asset('/')}}public/admin/assets/js/main.js"></script>

    <script>
        $(document).ready(function () {
            $("#mainEmail").keyup(function () {
                var mainId = $(this).val();
                //alert(mainId);
    
                $.ajax({
            url: "{{ route('checkMailForPassword') }}",
            method: 'GET',
            data: {mainId:mainId},
            success: function(data) {
    
                //alert(data);
    
             if(data == 0){
    
                $('#finalValue').attr('disabled','disabled');
    
             }else{
                $('#finalValue').removeAttr('disabled');
    
             }
            }
        });
            });
        });
    </script>

    </body>


</html>

