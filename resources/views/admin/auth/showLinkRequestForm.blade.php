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
	<meta property="og:image" content="{{ asset('/') }}{{ $front_logo_name }}">
    <!-- Title -->
    <title>Forgot Password</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('/') }}{{ $front_icon_name }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/') }}public/admin/assets/css/auth-style.css">
</head>
<body>

    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-branding-column">
                <h1 class="branding-title">{{$front_ins_name}}</h1>
                <p class="branding-subtitle">The future of fashion retail management is here. Streamlined, simple, and powerful.</p>
            </div>

            <div class="auth-form-column">
                <div class="auth-form-header">
                    <img src="{{ asset('/') }}{{$front_black_logo_name}}" alt="Logo" class="auth-logo">
                    <h3 class="auth-title">Forgot Password?</h3>
                    <p class="text-muted">Enter your email and we'll send instructions to reset your password.</p>
                </div>
                
                 <form id="form" class="theme-form login-form" action="{{route('checkMailPost')}}" method="post">
                    @csrf
                    @include('flash_message')  
                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
                    </div>
                    
                    <div class="d-grid">
                        <button id="finalValue" type="submit" class="btn btn-primary auth-btn">Send Instructions</button>
                    </div>
                </form>

                {{-- <div class="text-center mt-4">
                     <a href="login.html" class="auth-link"><i class="fas fa-arrow-left me-2"></i>Back to Login</a>
                </div> --}}
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
<script>
        $(document).ready(function () {
            $("#email").keyup(function () {
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


