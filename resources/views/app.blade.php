<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/webp" href="{{ asset('images/puskesmas2.webp') }}">

@vite(['resources/css/app.css', 'resources/js/app.js'])

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

  @inertiaHead
  @routes
  <script>
    if (typeof Ziggy !== 'undefined') {
      Ziggy.absolute = false; // ← penting: bikin route() output relatif
    }
  </script>

  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
  @inertia

  <!-- Script reCAPTCHA (aman, HTTPS) -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
