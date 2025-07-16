<?php
$page = "Data Rekap";
require_once("./header.php");
?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-4">Data Rekap</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Data Rekap</li>
            </ol>
            <!-- START MESSAGE -->
            <?php
            if (isset($_GET['msg']) && isset($_SERVER['HTTP_REFERER'])) {
                if ($_GET['msg'] == 1 && $_SERVER['HTTP_REFERER']) {
            ?>
            <div class="alert alert-success alert-dismissible fade show text-center h4" role="alert">
                <strong>Berhasil Menghapus Data Rekap!</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
                } else if ($_GET['msg'] == 2 && $_SERVER['HTTP_REFERER']) {
            ?>
            <div class="alert alert-success alert-dismissible fade show text-center h4" role="alert">
                <strong>Gagal Menghapus Data Rekap!</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
                }
            }
            ?>
            <!-- END MESSAGE -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <i class="fas fa-table mr-1"></i>
                        Data Rekap
                    </div>
                    <div>
                        <a href="./Rekap.php">
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-file mr-1"></i> Rekapitulasi
                            </button>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Pegawai Foto</th>
                                    <th>Pegawai Nama</th>
                                    <th>Pegawai NIP</th>
                                    <th>Rekap Tanggal</th>
                                    <th>Pegawai Masuk</th>
                                    <th>Pegawai Foto Masuk</th>
                                    <th>Pegawai Keluar</th>
                                    <th>Pegawai Foto Keluar</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Pegawai Foto</th>
                                    <th>Pegawai Nama</th>
                                    <th>Pegawai NIP</th>
                                    <th>Rekap Tanggal</th>
                                    <th>Pegawai Masuk</th>
                                    <th>Pegawai Foto Masuk</th>
                                    <th>Pegawai Keluar</th>
                                    <th>Pegawai Foto Keluar</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php
                            $sql = "SELECT `rekap`.`rekap_id`, `rekap`.`rekap_tanggal`, `pegawai`.`pegawai_foto`, `pegawai`.`pegawai_nama`, `pegawai`.`pegawai_nip`, `rekap`.`rekap_masuk`, `rekap`.`rekap_keluar`, `rekap`.`rekap_photomasuk`, `rekap`.`rekap_photokeluar`, `rekap`.`rekap_keterangan` 
                            FROM `rekap`
                            INNER JOIN `pegawai` ON `rekap`.`pegawai_id` = `pegawai`.`pegawai_id`";
                            $result = $koneksi->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                $rekap_id = $row['rekap_id'];
                                $pegawa_foto = $row['pegawai_foto'];
                                $pegawai_nama = $row['pegawai_nama'];
                                $pegawai_nip = $row['pegawai_nip'];
                                $rekap_tanggal = $row['rekap_tanggal'];
                                $rekap_masuk = $row['rekap_masuk'];
                                $rekap_keluar = $row['rekap_keluar'];
                                $rekap_photomasuk = $row['rekap_photomasuk'];
                                $rekap_photokeluar = $row['rekap_photokeluar'];
                                $rekap_keterangan = $row['rekap_keterangan'];
                            ?>
                                <tr>
                                    <td><img src="./image/<?php echo $pegawai_foto ?>" class="rounded-circle"
                                            alt="Foto <?php echo $pegawai_nama ?>" width="80" height="80"></td>
                                    <td><?php echo $pegawai_nama; ?></td>
                                    <td><?php echo $pegawai_nip; ?></td>
                                    <td><?php echo $rekap_tanggal; ?></td>
                                    <td><?php echo $rekap_masuk; ?></td>
                                    <td><img src="./image/<?php echo $rekap_photomasuk ?>" class="rounded-circle"
                                            alt="Foto Masuk <?php echo $pegawai_nama ?>" width="80" height="80"></td>
                                    <td><?php echo $rekap_keluar; ?></td>
                                    <td><img src="./image/<?php echo $rekap_photokeluar ?>" class="rounded-circle"
                                            alt="Foto Keluar <?php echo $pegawai_nama ?>" width="80" height="80"></td>
                                    <td><?php echo $rekap_keterangan; ?></td>
                                    <td>
                                        <a href="edit_rekap.php?rekap_id=<?php echo $rekap_id; ?>"
                                            class="btn btn-primary"><i class="fas fa-edit"> </i> Lihat atau Edit</a>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php
require_once("./footer.php");
?>