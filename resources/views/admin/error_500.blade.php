<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>500 Internal Server Error</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.lordicon.com/lordicon.js"></script>
  <style>
    body {
      background-color: #f8f9fa;
    }

    .error-container {
      opacity: 0;
      transform: scale(0.8);
      transition: all 0.8s ease-out;
    }

    .error-container.animated {
      opacity: 1;
      transform: scale(1);
    }

    .error-container.bounce {
      animation: bounce 1s;
    }

    @keyframes bounce {
      0%   { transform: scale(1); }
      30%  { transform: scale(1.1); }
      50%  { transform: scale(0.95); }
      70%  { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
  </style>
</head>
<body>
  <div class="container vh-100 d-flex align-items-center justify-content-center">
    <div id="errorBox" class="text-center error-container">
      
      <!-- Animated Robot SVG -->
      <lord-icon
        src="https://cdn.lordicon.com/msoeawqm.json"
        trigger="loop"
        delay="100"
        colors="primary:#dc3545,secondary:#545454"
        style="width:120px;height:120px">
      </lord-icon>

      <h1 class="display-1 fw-bold text-danger">500</h1>
      <p class="fs-3"><span class="text-danger">Oops!</span> Internal Server Error.</p>
      <p class="lead">
        Something went wrong on our side. Please try again later.
      </p>
      <a href="{{route('home')}}" class="btn btn-primary">Go Home</a>
    </div>
  </div>

  <script>
    window.addEventListener('DOMContentLoaded', () => {
      const errorBox = document.getElementById('errorBox');
      setTimeout(() => {
        errorBox.classList.add('animated', 'bounce');
      }, 100);
    });
  </script>
</body>
</html>
