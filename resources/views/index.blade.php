<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ asset('css/shielded.css') }}">
    <title>TEST</title>
    <style>
        .floating-container {
            position: fixed;
            top: 60px;
            /* Adjust based on your navbar height */
            right: 200px;
            width: 300px;
            min-width: 600px;
            /* Minimum width */
            min-height: 200px;
            /* Minimum height */
            resize: both;
            /* Allow both horizontal and vertical resizing */
            overflow: auto;
            /* Ensure content is scrollable if resized smaller */
            display: none;
            z-index: 1050;
            /* Bootstrap modals have a z-index of 1050 */
        }
    </style>
</head>

<div class="container" id="mainDiv">
    <div id="floatingContainer" class="floating-container shielded-card shielded-shadow">
        <div class="shielded-card-body">

            <div class="container" style="color: black; background-color: white; padding: 5px;">
                <div class="col" style="flex: 0 0 55%; margin-left: 10px;">
                    <p style="margin-top: -10px; font-size: 12px;" align="center"> blablablba </p>
                    <ul>
                        <li style="font-weight: bold;"> URL DETEKSI </li>
                        <section id="textURL"></section>
                        <button class="shielded-btn shielded-btn-success" id="analisaURLBTN"> Analisa URL
                        </button>
                        <span class="shielded-spinner-border-sm shielded-text-secondary" style="display: none;"
                            id="spinnerAnalisaURL"></span>
                        <li style="font-weight: bold; margin-top: 10px;"> FILE DETEKSI </li>
                        <section id="textFile"></section>
                        <button class="shielded-btn shielded-btn-success" id="analisaFileBTN"> Analisa File
                        </button>
                        <span class="shielded-spinner-border-sm shielded-text-secondary" style="display: none;"
                            id="spinnerAnalisaFile"></span>
                        <section id="sectionAnalisaFile"> </section>
                        <li style="font-weight: bold; margin-top: 10px;"> DOMAIN </li>
                        <section id="textDomain"></section>
                        <button class="shielded-btn shielded-btn-success" id="analisaDomainBTN"> Analisa
                            Domain </button>
                        <span class="shielded-spinner-border-sm shielded-text-secondary" style="display: none;"
                            id="spinnerAnalisaDomain"></span>
                        <section id="sectionAnalisaDomain"> </section>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<body style="background-color: white">

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Dropdown
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                    </li>
                </ul>
                <button id="toggleButton" class="btn btn-primary me-2">Shielded</button>
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <img src="{{ asset('download.png') }}" style="height: 50%; width: 50%;">

    <hr>

    <div class="container" style="margin-bottom: 50px;">
        <div class="row">
            <div class="col-2">
                <img src="{{ asset('download.png') }}" style="height: 100%; width: 100%;">
                <p style="margin-top: -10px; font-size: 12px;" align="center"> blablablba </p>
            </div>
            <div class="col-10">
                <p style="font-weight: bold;"> RINGKASAN
                <section id="textRingkasan" style="flex: 0 0 50%;" class="typing-effect"></section>
                <button class="shielded-btn shielded-btn-success" id="analisaRingkasanBTN"> Analisa
                    Ringkasan </button>
                <span class="shielded-spinner-border-sm shielded-text-secondary" style="display: none;"
                    id="spinnerAnalisaRingkasan"></span>
            </div>
        </div>
    </div>

    {{-- <div class="shielded-card shielded-rounded shielded-shadow">
        <div class="shielded-card-body" align="center">
            <img src="{{ asset('download.png') }}" style="height: 50%; width: 50%;">
            <p style="margin-top: -10px; font-size: 10px;"> blablablabla </p>
        </div>
    </div>
    <div class="shielded-card shielded-rounded shielded-shadow" style="margin-top: 10px;">
        <div class="shielded-card-body">
            <ul>
                <li style="font-weight: bold;"> Judul </li>
                blablablablalb
                <li style="font-weight: bold; margin-top: 5px;"> Pengirim </li>
                blablablabla
                <section id="sectionIdAnalisa">
                </section>
            </ul>
        </div>
    </div>
    <div class="shielded-card shielded-rounded shielded-shadow" style="margin-top: 10px;">
        <div class="shielded-card-body">
            <p style="font-weight: bold;"> RINGKASAN
            <section id="textRingkasan" style="flex: 0 0 50%;" class="typing-effect"></section>
            <button class="shielded-btn shielded-btn-success" id="analisaRingkasanBTN"> Analisa
                Ringkasan </button>
            <span class="shielded-spinner-border-sm shielded-text-secondary" style="display: none;"
                id="spinnerAnalisaRingkasan"></span>
        </div>
    </div> --}}

    <hr>

    <img src="{{ asset('download.png') }}" style="height: 50%; width: 50%;">

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#toggleButton').on('click', function() {
            var y = $('#floatingContainer').css('display');
            y == 'block' ? $('#floatingContainer').hide() : $('#floatingContainer').show()
        });
        $('#closeButton').on('click', function() {
            $('#floatingContainer').hide();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#floatingContainer, #toggleButton').length) {
                $('#floatingContainer').hide();
            }
        });
    });
</script>

</html>
