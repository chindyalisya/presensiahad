@extends('layouts.presensi')
@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Presensi</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
     <style>
        .webcam-capture,
        .webcam-capture video {
            display: inline-block;
            width: 100% !important;
            margin: auto;
            height: auto !important;
            border-radius: 15px;
            transform: scaleX(1);
        }

        #map {
             height: 200px;
        }

</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection
@section('content')
<div class="row" style="margin-top: 70px">
    <div class="col">
        <input type="hidden" id="lokasi">
        <div class="webcam-capture"></div>
    </div>
 </div>
 <div class="row">
    <div class="col">
        <button id="takeabsen" class="btn btn-primary btn-block">
            <ion-icon name="camera-outline"></ion-icon>
            Absen Datang
        </button>
    </div>
</div>
<div class="row mt-2">
    <div class="col">
        <div id="map"></div>
    </div>
</div>

<audio id="notifikasi_in">
        <source src="{{ asset('assets/sound/notifikasi_in.mp3') }}" type="audio/mpeg">
</audio>
@endsection

@push('myscript')
<script>
    var  notifikasi_in = document.getElementById('notifikasi_in');
    Webcam.set({
    height: 480,
    width: 640,
    image_format: 'jpeg',
    jpeg_quality: 80,
    flip_horiz: false // Pastikan ini diset ke false
});

Webcam.attach('.webcam-capture');

var webcamVideo = document.querySelector('.webcam-capture video');
if (webcamVideo) {
    // Memastikan tidak ada efek mirror
    webcamVideo.style.transform = 'scaleX(1)';
}


    var lokasi = document.getElementById('lokasi');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    }

    function successCallback(position) {
        lokasi.value = position.coords.latitude + "," + position.coords.longitude;
        var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
            , attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        var marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
        var circle = L.circle([-7.1132129, 110.2790159], {
                color: 'red'
                , fillColor: '#f03'
                , fillOpacity: 0.5
                , radius: 20
            }).addTo(map);
    }

    function errorCallback(){

    }

    $("#takeabsen").click(function(e) {
    Webcam.snap(function(uri) {
        image = uri;
    });
    var lokasi = $("#lokasi").val();
    $.ajax({
        type: 'POST',
        url: '/presensi/store',
        data: {
            _token: "{{ csrf_token() }}",
            image: image,
            lokasi: lokasi
        },
        cache: false,
        success: function(respond) {
            if (respond.status == "success") {
                if (respond.type == "in") {
                    // Pastikan audio dimulai setelah klik
                    notifikasi_in.play().catch(function(error) {
                        console.log("Gagal memutar suara: ", error);
                    });
                }
                
                Swal.fire({
                    title: 'Berhasil !',
                    text: 'Terimakasih, Selamat Melanjutkan Aktivitas Anda',
                    icon: 'success'
                });
                setTimeout(function() {
                    location.href = '/dashboard';
                }, 3000);
            } else {
                Swal.fire({
                    title: 'Error !',
                    text: respond.message,
                    icon: 'error'
                });
            }
        },
        error: function(xhr, status, error) {
            // Handling error jika AJAX request gagal
            Swal.fire({
                title: 'Error !',
                text: 'Terjadi kesalahan saat menghubungi server.',
                icon: 'error'
            });
        }
    });
});


    </script>
    @endpush