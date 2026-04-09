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

    <main role="main" class="inner cover" style="opacity: 1.0;">
        <h3 class="cover-heading">May your trails be crooked, winding, lonesome, dangerous, leading to the most amazing
            view.</h3>
        May your mountains rise into and above the clouds.<br>May your rivers flow without end,.<br> meandering through
        pastoral
        valleys tinkling with bells,.<br>past temples and castles and poets' towers .<br>into a dark primeval forest
        where
        tigers belch and monkeys howl, .<br>through miasmal and mysterious swamps and .<br>down into a desert of red
        rock,
        blue mesas, domes and pinnacles and .<br>grottos of endless stone, and down again into .<br>a deep vast ancient
        unknown chasm .<br>where bars of sunlight blaze on profiled cliffs, .<br>where deer walk across the white sand
        beaches, .<br>where storms come and go as lightning clangs upon the high crags, .<br>where something strange and
        more
        beautiful .<br>and more full of wonder than your deepest dreams waits for you — .<br>beyond that next turning of
        the
        canyon walls.</p>
        <a href="https://en.wikipedia.org/wiki/Edward_Abbey" style="align: right; font-style: italic"><caption>Edward Paul Abbey 1927-1989</caption></a>
    </main>

    <footer class="mastfoot mt-auto">
        <div class="inner">
            <caption>Treksandtracks.com</caption>
        </div>
    </footer>
</div>
</body>
</html>
