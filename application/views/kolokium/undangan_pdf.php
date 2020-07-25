<html><head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <title></title>
    </head><body>
    <center>
        <h3>
            UNDANGAN UJIAN KOLOKIUM
        </h3>
    </center>
    <?php if ($kolokium['dosen2'] != NULL): ?>
        <table>
            <tr>
                <td>Yth.</td>
                <td><?= $kolokium['dosen1']; ?></td>
            </tr>
            <tr>
                <td></td>
                <td><?= $kolokium['dosen2']; ?></td>
            </tr>
            <tr>
                <td></td>
                <td><?= $kolokium['reviewer']; ?></td>
            </tr>
        </table>
    <br><br>
        Terkait penetapan jadwal ujian kolokium, atas nama mahasiswa :<br>
        <table>
            <tr>
                <td style="color: white">Yth.</td>                
                <td>NIM</td>
                <td>:</td>
                <td><?= $kolokium['nim']; ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Nama</td>
                <td>:</td>
                <td><?= $kolokium['judul']; ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Judul</td>
                <td>:</td>
                <td><?= $kolokium['nim']; ?></td>
            </tr>
        </table>
        <br><br>
        Jadwal ujian kolokium akan dilaksanakan pada:<br>
        <table>
            <tr>
                <td style="color: white">Yth.</td>  
                <td>Tanggal</td>
                <td>:</td>
                <td><?= $kolokium['tanggal']; ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Jam</td>
                <td>:</td>
                <td><?= $kolokium['durasi']; ?> WIB</td>
            </tr>
            <tr>
                <td></td>
                <td>Dosen Pembimbing 1</td>
                <td>:</td>
                <td><?= $kolokium['dosen1']; ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Dosen Pembimbing 2</td>
                <td>:</td>
                <td><?= $kolokium['dosen2']; ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Dosen Penguji</td>
                <td>:</td>
                <td><?= $kolokium['reviewer']; ?></td>
            </tr>
        </table>
        <br><br>

        <p>Saya ingin meminta konfirmasi Bapak/Ibu, apakah bisa dilaksanakan sesuai jadwal diatas?<br>
            Atas perhatian dan kerjasama Bapak/Ibu saya ucapkan terima kasih.
        </p>
    <?php else: ?>
        <table>
            <tr>
                <td>Yth.</td>
                <td><?= $kolokium['dosen1']; ?></td>
            </tr>
            <tr>
                <td></td>
                <td><?= $kolokium['reviewer']; ?></td>
            </tr>
        </table>
    <br><br>
        Terkait penetapan jadwal ujian kolokium, atas nama mahasiswa :<br>
        <table>
            <tr>
                <td style="color: white">Yth.</td>                
                <td>NIM</td>
                <td>:</td>
                <td><?= $kolokium['nim']; ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Nama</td>
                <td>:</td>
                <td><?= $kolokium['judul']; ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Judul</td>
                <td>:</td>
                <td><?= $kolokium['nim']; ?></td>
            </tr>
        </table>
        <br><br>
        Jadwal ujian kolokium akan dilaksanakan pada:<br>
        <table>
            <tr>
                <td style="color: white">Yth.</td>  
                <td>Tanggal</td>
                <td>:</td>
                <td><?= $kolokium['tanggal']; ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Jam</td>
                <td>:</td>
                <td><?= $kolokium['durasi']; ?> WIB</td>
            </tr>
            <tr>
                <td></td>
                <td>Dosen Pembimbing 1</td>
                <td>:</td>
                <td><?= $kolokium['dosen1']; ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Dosen Penguji</td>
                <td>:</td>
                <td><?= $kolokium['reviewer']; ?></td>
            </tr>
        </table>
        <br><br>

        <p>Saya ingin meminta konfirmasi Bapak/Ibu, apakah bisa dilaksanakan sesuai jadwal diatas?<br>
            Atas perhatian dan kerjasama Bapak/Ibu saya ucapkan terima kasih.
        </p>
    <?php endif; ?>

</body>
</html>