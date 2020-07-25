<div class="container">
    <?php if ($this->session->flashdata('bentrok')): ?>
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
    <?php elseif ($this->session->flashdata('semuaSama')): ?>
        <div class="row mt-3">
            <div class="col-md-10">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('semuaSama'); ?>.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="row mt-3">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    Form Tambah Jadwal Kolokium
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="nim">NIM Mahasiswa</label>

                            <input type="text" name="nim" class="form-control" id="nim" value="<?= $mahasiswa['nim'] ?>" readonly>

                        </div>
                        <div class="form-group">
                            <label for="nama">Nama Mahasiswa</label>

                            <input type="text" name="nama" class="form-control" id="nama" value="<?= $mahasiswa['nama'] ?>" readonly>

                        </div>
                        <div class="form-group">
                            <label for="judul">Dosen Pembimbing 1</label>
                            <select class="form-control" id="dosen1" name="dosen1">  
                                <option value="">-</option>
                                <?php foreach ($dosen as $ds): ?>
                                    <option value="<?= $ds['nama']; ?>"><?= $ds['nama']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-danger"><?= form_error('dosen1'); ?></small>
                        </div>
                        <div class="form-group">
                            <label for="judul">Dosen Pembimbing 2</label>
                            <select class="form-control" id="dosen2" name="dosen2">
                                <option value="">-</option>
                                <?php foreach ($dosen as $ds): ?>
                                    <option value="<?= $ds['nama']; ?>"><?= $ds['nama']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="judul">Judul Tugas Akhir</label>
                            <textarea class="form-control" name="judul" id="judul" rows="3"></textarea>
                            <small class="form-text text-danger"><?= form_error('judul'); ?></small>
                        </div>
                        <div class="form-group">
                            <label for="reviewer">Reviewer</label>
                            <select class="form-control" id="reviewer" name="reviewer">     
                                <option value="">-</option>
                                <?php foreach ($dosen as $ds): ?>
                                    <option value="<?= $ds['nama']; ?>"><?= $ds['nama']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-danger"><?= form_error('reviewer'); ?></small>
                        </div>
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" id="tanggal">                        
                            <small class="form-text text-danger"><?= form_error('tanggal'); ?></small>
                        </div>
                        <div class="form-group">
                            <label for="durasi">Jam</label>
                            <select class="form-control" id="durasi" name="durasi">
                                <?php foreach ($jam as $j): ?>
                                    <option value="<?= $j; ?>"><?= $j; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ruang">Ruang</label>
                            <select class="form-control" id="ruang" name="ruang">
                                <?php foreach ($ruang as $r): ?>
                                    <option value="<?= $r['nama']; ?>"><?= $r['nama']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" rows="3"></textarea>
                        </div>
                        <button type="submit" name="tambah" class="btn btn-primary float-right">Tambah Data</button>
                    </form>
                </div>   
            </div>
        </div>
    </div>
</div>