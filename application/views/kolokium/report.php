<div class="container">
    <?php if ($this->session->flashdata('reportKolokium')): ?>
        <div class="row mt-3">
            <div class="col-md-10">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('reportKolokium'); ?>.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($kolokium == null): ?>
        <div class="row mt-3">
            <div class="col">
                <h3 class="mt-3">Report Jadwal Kolokium</h3>

                <form action="" method="post">
                    <table cellpadding="20">
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label for="bulan">Bulan</label>
                                    <select class="form-control" id="bulan" name="bulan">
                                        <option value="">-</option>
                                        <?php foreach ($bulan as $b): ?>
                                            <option value="<?= $b; ?>"><?= $b; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="dosen1">Dosen Pembimbing 1</label>
                                    <select class="form-control" id="dosen1" name="dosen1">
                                        <option value="">-</option>
                                        <?php foreach ($dosen as $ds): ?>
                                            <option value="<?= $ds['nama']; ?>"><?= $ds['nama']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="dosen2">Dosen Pembimbing 2</label>
                                    <select class="form-control" id="dosen2" name="dosen2">
                                        <option value="">-</option>
                                        <?php foreach ($dosen as $ds): ?>
                                            <option value="<?= $ds['nama']; ?>"><?= $ds['nama']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="reviewer">Reviewer</label>
                                    <select class="form-control" id="reviewer" name="reviewer">
                                        <option value="">-</option>
                                        <?php foreach ($dosen as $ds): ?>
                                            <option value="<?= $ds['nama']; ?>"><?= $ds['nama']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="jam">Jam</label>
                                    <select class="form-control" id="jam" name="jam">
                                        <option value="">-</option>
                                        <?php foreach ($jam as $ds): ?>
                                            <option value="<?= $ds; ?>"><?= $ds; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="ruang">Ruangan</label>
                                    <select class="form-control" id="ruang" name="ruang">
                                        <option value="">-</option>
                                        <?php foreach ($ruang as $ds): ?>
                                            <option value="<?= $ds['nama']; ?>"><?= $ds['nama']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <a href="<?= base_url() ?>kolokium" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Kembali</a>
                    <button type="submit" name="cari" class="btn btn-warning"><i class="fas fa-search"></i> Cari</button>
                </form>
            </div>
        </div>
    <?php else: ?>

        <?php $postData = $this->input->post(); ?>

        <div class="row mt-3">
            <div class="col">
                <h3 class="mt-3">Report Jadwal Kolokium</h3>

                <form action="" method="post">
                    <table cellpadding="20">
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label for="bulan">Bulan</label>
                                    <select class="form-control" id="bulan" name="bulan">
                                        <option value="">-</option>                            
                                        <?php foreach ($bulan as $ds): ?>
                                            <?php if ($ds == $postData['bulan']): ?>
                                                <option value="<?= $ds; ?>"selected><?= $ds ?></option>
                                            <?php else: ?>
                                                <option value="<?= $ds ?>"><?= $ds; ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="dosen1">Dosen Pembimbing 1</label>
                                    <select class="form-control" id="dosen1" name="dosen1">
                                        <option value="">-</option>
                                        <?php foreach ($dosen as $ds): ?>
                                            <?php if ($ds['nama'] == $postData['dosen1']): ?>
                                                <option value="<?= $ds['nama']; ?>"selected><?= $ds['nama'] ?></option>
                                            <?php else: ?>
                                                <option value="<?= $ds['nama'] ?>"><?= $ds['nama']; ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="dosen2">Dosen Pembimbing 2</label>
                                    <select class="form-control" id="dosen2" name="dosen2">
                                        <option value="">-</option>
                                        <?php foreach ($dosen as $ds): ?>
                                            <?php if ($ds['nama'] == $postData['dosen2']): ?>
                                                <option value="<?= $ds['nama']; ?>"selected><?= $ds['nama'] ?></option>
                                            <?php else: ?>
                                                <option value="<?= $ds['nama'] ?>"><?= $ds['nama']; ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="reviewer">Reviewer</label>
                                    <select class="form-control" id="reviewer" name="reviewer">
                                        <option value="">-</option>
                                        <?php foreach ($dosen as $ds): ?>
                                            <?php if ($ds['nama'] == $postData['reviewer']): ?>
                                                <option value="<?= $ds['nama']; ?>"selected><?= $ds['nama'] ?></option>
                                            <?php else: ?>
                                                <option value="<?= $ds['nama']; ?>"><?= $ds['nama']; ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="jam">Jam</label>
                                    <select class="form-control" id="jam" name="jam">
                                        <option value="">-</option>
                                        <?php foreach ($jam as $ds): ?>
                                            <?php if ($ds == $postData['durasi']): ?>
                                                <option value="<?= $ds; ?>"selected><?= $ds ?></option>
                                            <?php else: ?>
                                                <option value="<?= $ds; ?>"><?= $ds; ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="ruang">Ruangan</label>
                                    <select class="form-control" id="ruang" name="ruang">
                                        <option value="">-</option>
                                        <?php foreach ($ruang as $ds): ?>
                                            <?php if ($ds['nama'] == $postData['reviewer']): ?>
                                                <option value="<?= $ds['nama']; ?>"selected><?= $ds['nama'] ?></option>
                                            <?php else: ?>
                                                <option value="<?= $ds['nama'] ?>"><?= $ds['nama']; ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <a href="<?= base_url() ?>kolokium" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Kembali</a>
                    <button type="submit" name="cari" class="btn btn-warning"><i class="fas fa-search"></i> Cari</button>   
                    <a href="<?= base_url(); ?>kolokium/excel" class="btn btn-success"><i class="fas fa-file-excel"></i> Cetak Exel</a>
                </form>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-16">

                <h7>Jumlah Data : <?= $jumlahData; ?></h7>

                <table class="table" border="1">
                    <thead class="thead-dark">
                        <tr style="text-align:center">
                            <th scope="col">No.</th>
                            <th scope="col">NIM</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Jam</th>
                            <th scope="col">Ruangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $start = 0; ?>
                        <?php foreach ($kolokium as $kol) : ?>
                            <tr style="text-align:center">
                                <th><?= ++$start; ?></th>
                                <td><?= $kol['nim']; ?></td>
                                <td style="text-align:left"><?= $kol['nama']; ?></td>
                                <td><?= $kol['tanggal']; ?></td>
                                <td><?= $kol['durasi']; ?></td>
                                <td><?= $kol['ruang']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
