<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="{{ $ins_name }}">
	<meta name="robots" content="">
    <meta name="keywords" content="{{ $keyword }}">
	<meta name="description" content="{{ $description }}">
	<meta property="og:title" content="{{ $ins_name }}">
	<meta property="og:description" content="{{ $description }}">
	<meta property="og:image" content="{{ asset('/') }}{{ $front_logo_name }}">
    <!-- Title -->
    <title>Login</title>
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
                <h1 class="branding-title">{{ $front_ins_name }}</h1>
                <p class="branding-subtitle">The future of fashion retail management is here. Streamlined, simple, and powerful.</p>
            </div>

            <div class="auth-form-column">
                <div class="auth-form-header">
                    <img src="{{ asset('/') }}{{$front_black_logo_name}}" alt="Logo" class="auth-logo">
                    <h3 class="auth-title">Welcome Back!</h3>
                    <p class="text-muted">Sign in to continue.</p>
                </div>
                
                       <form method="POST" action="{{ route('login') }}" >

                    @csrf 
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="you@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" id="password" placeholder="••••••••" required>
                            <span class="input-group-text" id="togglePassword"><i class="fas fa-eye"></i></span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div></div>
                        <a href="{{ route('showLinkRequestForm') }}" class="auth-link">Forgot Password?</a>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary auth-btn">Login</button>
                    </div>
                </form>

                {{-- <div class="text-center mt-4">
                    <p class="text-muted">Don't have an account? <a href="#" class="auth-link">Sign Up</a></p>
                </div> --}}
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>

