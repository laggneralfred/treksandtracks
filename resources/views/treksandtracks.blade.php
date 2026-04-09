<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v3.8.5">
    <title>Treks and Tracks</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.3/examples/cover/">

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        html, body {
            background: url("{{ asset('asset/img/background_old.jpg') }}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;

            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            color: white;
            height: 100%;
            margin: 0;
        }

        .white {
            color: white;
        }

    </style>
    <!-- Custom styles for this template -->
    <link href="{{ asset('cover.css') }}" rel="stylesheet">
</head>
<body class="text-center">
<div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
    <header class="masthead mb-auto">
        <div class="inner">
            <h3 class="masthead-brand">Treks and Tracks</h3>
            <nav class="nav nav-masthead justify-content-center">
                {{--
                                <a class="nav-link active" href="#">Blog</a>
                --}}
            </nav>
        </div>
    </header>

    <main role="main" class="inner cover">
        <div class="lead">
            <a href="{{ route('poem') }}" class="btn btn-lg white">
                <h1 class="cover-heading">Thank you for joining us on 10 years of outdoor adventures.</h1>
                <div class="lead">Treks and Tracks has officially closed.</div>
                <div>May your trails be winding and weather fair</div>
            </a>
        </div>
        <p class="lead">
            <a href="{{ url('blogs') }}" class="btn btn-lg btn-secondary">Blog</a>
        </p>
    </main>

    <footer class="mastfoot mt-auto">
        <div class="inner">
            <p>Treksandtracks.com</p>
        </div>
    </footer>
</div>
</body>
</html>
