<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>A4</title>

  <!-- Normalize or reset CSS with your favorite library -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

  <!-- Load paper.css for happy printing -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

  <!-- Set page size here: A5, A4 or A3 -->
  <!-- Set also "landscape" if you need -->
  <style>
    @page {
            size: A4 
                }

                #title {
                        font-size: 18px;
                        font-weight: bold;
                }

                .tabeldatakaryawan{
                    margin-top 40px;
                }

                .tabeldatakaryawan{
                    padding 5px;
                }

                .tabelpresensi{
                    width: 100%;
                    margin-top: 20px;
                    border-collapse: collapse;
                }
                                                                                                                                                                                                    
                .tabelpresensi tr th{
                    border: 1px solid #131212;
                    padding: 8px;
                    background: #dbdbdb;
                }

                .tabelpresensi tr td{
                    border: 1px solid #131212;
                    padding: 5px;
                    font-size: 12px;
                }

                .foto{
                    width: 40px;
                    height: 30px;
                }

         </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4 landscape">

  <!-- Each sheet element should have the class "sheet" -->
  <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
  <section class="sheet padding-10mm">

   <table style="width: 100%">
        <tr>
            <td style="width: 10%">
                <img src="{{ asset('assets/img/logopku.jpeg') }}" width="80" height="100" alt="">
            </td>
            <td>
                <span id="title">
                    REKAP PRESENSI AHAD PAGI<br>
                    PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}<br>
                    RS PKU MUHAMMADIYAH BOJA
                </span><br>
                <span><i>Jl.Raya Boja Limbangan Ds.Salamsari Kec.Boja Kab.Kendal</i></span>
            </td>
        </tr>
   </table>
    <table class="tabelpresensi">
        <tr>
            <th rowspan="2">Nik</th>
            <th rowspan="2">Nama Karyawan</th>
        </tr>
        <tr>
            @for ($i = 1; $i <= 4; $i++)
            <th>Minggu ke-{{ $i }}</th>
            @endfor
            <th colspan="2">Total Hadir</th>
        </tr>
        @foreach ($rekap as $d)
<tr>
    <td>{{ $d->nik }}</td>
    <td>{{ $d->nama_lengkap }}</td>

    <?php
    $totalhadir = 0;
    for ($i = 1; $i <= 4; $i++) {
        $tgl = "tgl_$i";  // Kolom absensi minggu ke-1, ke-2, ke-3, ke-4
        
        if (empty($d->$tgl)) {
            // Jika tidak ada data absensi (misalnya, kosong), tampilkan spasi kosong
            $hadir = [' '];
            $jam = '';  // Tidak ada jam jika tidak ada absensi
        } else {
            // Pisahkan berdasarkan spasi untuk mendapatkan waktu
            $hadir = explode(" ", $d->$tgl);  // Memisahkan tanggal dan jam
            
            // Ambil waktu yang ada di bagian kedua dari hasil explode (jika ada)
            $jam = isset($hadir[1]) ? trim($hadir[1]) : ''; // $hadir[1] berisi waktu

            // Tambahkan 1 ke totalhadir jika jam tidak kosong
            if (!empty($jam)) {
                $totalhadir += 1;
            }
        }
    ?>

    <td>
        <!-- Menampilkan jam absensi hanya jika ada (kolom minggu yang ada absensi) -->
        <span style="color: {{ isset($jam) && strtotime($jam) > strtotime('07:30:00') ? 'red' : '' }}">
            {{ $jam }}
        </span>
    </td>

    <?php
    }
    ?>

    <td>{{ $totalhadir }}</td>
</tr>
@endforeach


    </table>

    <table width="100%" style="margin-top:100px">
  <tr>
    <td></td>
    <td style="text-align: center;">Kendal, {{ date('d-m-Y') }}</td>
  </tr>
  <tr>  <!-- Baris kedua dimulai di sini -->
    <td style="text-align: center; vertical-align:bottom" height="100px">
      <u>Deni Kurniawan, S.sos</u><br>
      <i><b>Pembina Rohani</b></i>
    </td>
    <td style="text-align: center; vertical-align:bottom">
      <u>dr. Arfa Bima Firizqina, MARS</u><br>
      <i><b>Direktur</b></i>
    </td>
  </tr> <!-- Baris kedua ditutup dengan benar -->
</table>

  </section>
</body>
</html>