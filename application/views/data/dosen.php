<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <nav class="navbar">
                <h1 class="mt-4"><?= $title ?></h1>
                <form method="post" class="form-inline" action="<?php base_url('home/search'); ?>">
                    <input class="form-control mr-sm-2" name="nama" type="text" id="nama" placeholder="Search Name"
                           aria-label="Search">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                </form>
            </nav>
            <div class="card-body">
                <a href="http://" class="btn btn-primary mb-2" data-toggle="modal" data-target="#exampleModal">Import
                    Data Dosen</a>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>NAMA</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($data->result() as $row) : ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?php echo $row->nip; ?></td>
                                    <td><?php echo $row->nama; ?></td>
                                    <td>
                                        <a href="<?php echo base_url(); ?>home/editDsn/<?= $row->nip; ?>"
                                           class="badge badge-success ">Edit</a>
                                        <a href="<?php echo base_url(); ?>home/deleteDsn/<?= $row->nip; ?>"
                                           class="badge badge-danger"
                                           onclick="return confirm('Are you sure you want to delete <?= $row->nip; ?>?');">Delete</a>
                                    </td>
                                </tr>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!--Tampilkan pagination-->
                    <div class="row">
                        <div class="col">
                            <?php echo $pagination; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Menu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?php echo base_url("home/uploadDosen") ?>">
                    <div class="modal-body">
                        <input type="file" class="form-control-file" id="file" name="file[]" multiple>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>