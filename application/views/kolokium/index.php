<div class="container">
    <?php if ($this->session->flashdata('flash')): ?>
        <div class="row mt-3">
            <div class="col-md-10">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Data Jadwal Kolokium <strong>berhasil</strong> <?= $this->session->flashdata('flash'); ?>.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    <?php elseif ($this->session->flashdata('terdaftar')): ?>
        <div class="row mt-3">
            <div class="col-md-10">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('terdaftar'); ?>.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    <?php elseif ($this->session->flashdata('bentrok')): ?>
        <div class="row mt-3">
            <div class="col-md-10">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('bentrok'); ?>.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    <?php elseif ($this->session->flashdata('dosen1Sama')): ?>
        <div class="row mt-3">
            <div class="col-md-10">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('dosen1Sama'); ?>.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    <?php elseif ($this->session->flashdata('dosenReviewerSama')): ?>
        <div class="row mt-3">
            <div class="col-md-10">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('dosenReviewerSama'); ?>.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    <?php elseif ($this->session->flashdata('dosen2Sama')): ?>
        <div class="row mt-3">
            <div class="col-md-10">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('dosen2Sama'); ?>.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <h3 class="mt-3">Jadwal Kolokium</h3>

    <div class="row mt-3">
        <div class="col-md-10">
            <a href="<?= base_url(); ?>kolokium/inputNim" class="btn btn-primary">Tambah Jadwal Kolokium</a>
            <a href="<?= base_url(); ?>kolokium/report" class="btn btn-success"><i class="fas fa-file-alt"></i> Report</a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <form action="<?= base_url('kolokium'); ?>" method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Cari mahasiswa.." name="keyword" autocomplete="off" autofocus="">
                    <div class="input-group-append">
                        <input class="btn btn-primary" type="submit" name="submit">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-16">
            <h7>Jumlah Data : <?= $total_rows; ?></h7>

            <table class="table" border="1">
                <thead class="thead-dark">
                    <tr style="text-align:center">
                        <th scope="col">No.</th>
                        <th scope="col">NIM</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Jam</th>
                        <th scope="col">Ruangan</th>
                        <th scope="col">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kolokium)) : ?>
                        <tr>
                            <td colspan="4">
                                <div class="alert alert-danger" role="alert">
                                    Data tidak ditemukan!
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($kolokium as $kol) : ?>
                        <tr style="text-align:center">
                            <th><?= ++$start; ?></th>
                            <td><?= $kol['nim']; ?></td>
                            <td style="text-align:left"><?= $kol['nama']; ?></td>
                            <td><?= $kol['tanggal']; ?></td>
                            <td><?= $kol['durasi']; ?></td>
                            <td><?= $kol['ruang']; ?></td>
                            <td>
                                <a href="<?= base_url(); ?>kolokium/detail/<?= $kol['id']; ?>" class="badge badge-primary" >Detail</a> 
                                <a href="<?= base_url(); ?>kolokium/edit/<?= $kol['id']; ?>" class="badge badge-success" >Edit</a> 
                                <a href="<?= base_url(); ?>kolokium/hapus/<?= $kol['id']; ?>" class="badge badge-danger" onclick="return confirm('Apakah anda yakin menghapus data ini?');">Hapus</a>                                       
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?= $this->pagination->create_links(); ?>

        </div>
    </div>

</div>