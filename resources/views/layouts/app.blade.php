<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="../resources/sass/app.scss">
        <script src="https://kit.fontawesome.com/6a68df703a.js" crossorigin="anonymous"></script>
        {{-- <script
            src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
            integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8="
            crossorigin="anonymous"></script>
             --}}
        <script
            src="https://code.jquery.com/jquery-3.4.1.js"
            integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
            crossorigin="anonymous"></script>
        <script type="text/javascript" src="../resources/js/app.js"></script>
    </head>

    <body>
        <div class="header">
            <h3 class="app-header">PRINTS GENERATOR</h3>
            <img src="http://d2d11z2jyoa884.cloudfront.net/logo/Inkbox_Logo_v3.svg" class="inkbox-logo">
        </div>
        <div class="container">
            <div class="back-button">
                <a href="../public" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i></a>            
            </div>
            <div class="content">
                @yield('content')
            </div>
        </div>
    </body>
</html>