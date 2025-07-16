<?php
$page = "Rekap Absensi";
require_once("./header.php");

// Tangani input dari form filter
$bulan_bulanan = $_POST['bulan'] ?? date('Y-m');
$jabatan_id_filter = $_POST['jabatan_id'] ?? 'semua';

// Pecah nilai bulan dan tahun
$pecah = explode("-", $bulan_bulanan);
$tahun = isset($pecah[0]) ? (int)$pecah[0] : (int)date('Y');
$bulan = isset($pecah[1]) ? (int)$pecah[1] : (int)date('m');
$jumlah_tanggal_pada_bulan = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// Siapkan kondisi WHERE untuk jabatan
$where_jabatan_sql = '';
$bind_params_jabatan = [];
if ($jabatan_id_filter !== 'semua') {
    $where_jabatan_sql = ' AND p.jabatan_id = ?';
    $bind_params_jabatan[] = &$jabatan_id_filter;
}

// OPTIMASI 1: Ambil semua data rekap harian untuk bulan ini
$rekap_bulanan = [];
$sql_rekap = "SELECT r.pegawai_id, DAY(r.rekap_tanggal) as tanggal, r.rekap_keterangan FROM rekap r JOIN pegawai p ON r.pegawai_id = p.pegawai_id WHERE MONTH(r.rekap_tanggal) = ? AND YEAR(r.rekap_tanggal) = ? " . $where_jabatan_sql;
$stmt_rekap = $koneksi->prepare($sql_rekap);
$bind_types = 'ii';
$bind_params = [&$bulan, &$tahun];
if ($jabatan_id_filter !== 'semua') {
    $bind_types .= 'i';
    $bind_params[] = &$jabatan_id_filter;
}
$stmt_rekap->bind_param($bind_types, ...$bind_params);
$stmt_rekap->execute();
$result_rekap = $stmt_rekap->get_result();
while ($row = $result_rekap->fetch_assoc()) {
    $rekap_bulanan[$row['pegawai_id']][$row['tanggal']] = $row;
}
$stmt_rekap->close();

// OPTIMASI 2: Ambil ringkasan total per status
$ringkasan_per_pegawai = [];
$sql_ringkasan = "SELECT p.pegawai_id,
                    COUNT(CASE WHEN r.rekap_keterangan = 'Hadir Masuk' THEN 1 END) as total_hm,
                    COUNT(CASE WHEN r.rekap_keterangan = 'Hadir Terlambat' THEN 1 END) as total_ht,
                    COUNT(CASE WHEN r.rekap_keterangan = 'Hadir Pulang' THEN 1 END) as total_hp
                FROM rekap r JOIN pegawai p ON r.pegawai_id = p.pegawai_id WHERE MONTH(r.rekap_tanggal) = ? AND YEAR(r.rekap_tanggal) = ? " . $where_jabatan_sql . " GROUP BY p.pegawai_id";
$stmt_ringkasan = $koneksi->prepare($sql_ringkasan);
$stmt_ringkasan->bind_param($bind_types, ...$bind_params);
$stmt_ringkasan->execute();
$result_ringkasan = $stmt_ringkasan->get_result();
while ($row = $result_ringkasan->fetch_assoc()) {
    $ringkasan_per_pegawai[$row['pegawai_id']] = $row;
}
$stmt_ringkasan->close();
?>
<style>
    .pdf-export-table { font-size: 7px; line-height: 1.2; }
    .pdf-export-table th, .pdf-export-table td { padding: 2px !important; white-space: nowrap; }
</style>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-4">Rekapitulasi Absensi Pegawai</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="./data_rekap.php">Data rekap</a></li>
                <li class="breadcrumb-item active">Rekap Absensi Bulanan</li>
            </ol>

            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-filter mr-1"></i> Pilih Filter</div>
                <div class="card-body">
                    <form action="rekap.php" method="POST">
                        <div class="form-row align-items-end">
                            <div class="col-md-4">
                                <label for="bulan">Periode Bulan & Tahun</label>
                                <input type="month" name="bulan" class="form-control" value="<?php echo $bulan_bulanan; ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="jabatan_id">Jabatan</label>
                                <select name="jabatan_id" id="jabatan_id" class="form-control">
                                    <option value="semua">Semua Jabatan</option>
                                    <?php
                                    $query_jabatan = $koneksi->query("SELECT jabatan_id, jabatan_nama FROM jabatan ORDER BY jabatan_nama ASC");
                                    while ($jabatan = $query_jabatan->fetch_assoc()) {
                                        $selected = ($jabatan_id_filter == $jabatan['jabatan_id']) ? 'selected' : '';
                                        echo "<option value='{$jabatan['jabatan_id']}' $selected>" . htmlspecialchars($jabatan['jabatan_nama']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Tampilkan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-table mr-1"></i>
                    Hasil Rekap Bulanan: <?php echo format_hari_tanggal("$tahun-$bulan-01", true); ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <button type="button" id="export_excel_button" class="btn btn-success btn-sm mb-2"><i class="fas fa-file-excel"></i> Export Excel</button>
                        <button type="button" id="export_pdf_button" class="btn btn-danger btn-sm mb-2"><i class="fas fa-file-pdf"></i> Export PDF</button>

                        <div id="rekap-bulanan-export">
                            <h4 style="text-align: center; display: none;" id="pdf-title">Rekap Absensi Bulan: <?php echo format_hari_tanggal("$tahun-$bulan-01", true); ?></h4>
                            <table id="employee_data" class="table table-bordered" style="width:100%;">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th rowspan="2" class="align-middle">No</th>
                                        <th rowspan="2" class="align-middle">Nama</th>
                                        <th rowspan="2" class="align-middle">NIP</th>
                                        <th colspan="<?php echo $jumlah_tanggal_pada_bulan; ?>">Tanggal</th>
                                        <th colspan="3">Ringkasan</th>
                                    </tr>
                                    <tr>
                                        <?php for ($i = 1; $i <= $jumlah_tanggal_pada_bulan; $i++) echo "<th>$i</th>"; ?>
                                        <th>HM</th><th>HT</th><th>HP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql_pegawai = "SELECT p.pegawai_id, p.pegawai_nama, p.pegawai_nip FROM pegawai p WHERE 1=1 ";
                                    if($jabatan_id_filter !== 'semua') $sql_pegawai .= "AND p.jabatan_id = '$jabatan_id_filter'";
                                    $sql_pegawai .= " ORDER BY p.pegawai_nama ASC";
                                    
                                    $query_pegawai = $koneksi->query($sql_pegawai);
                                    $no = 1;
                                    if($query_pegawai->num_rows > 0):
                                        while ($pegawai = $query_pegawai->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($pegawai['pegawai_nama']); ?></td>
                                            <td><?php echo htmlspecialchars($pegawai['pegawai_nip']); ?></td>
                                            <?php
                                            for ($i = 1; $i <= $jumlah_tanggal_pada_bulan; $i++) {
                                                $status_text = '-';
                                                if (isset($rekap_bulanan[$pegawai['pegawai_id']][$i])) {
                                                    switch ($rekap_bulanan[$pegawai['pegawai_id']][$i]['rekap_keterangan']) {
                                                        case 'Hadir Masuk': $status_text = 'HM'; break;
                                                        case 'Hadir Pulang': $status_text = 'HP'; break;
                                                        case 'Hadir Terlambat': $status_text = 'HT'; break;
                                                    }
                                                }
                                                echo "<td class='text-center'>$status_text</td>";
                                            }
                                            $ringkasan = $ringkasan_per_pegawai[$pegawai['pegawai_id']] ?? ['total_hm' => 0, 'total_ht' => 0, 'total_hp' => 0];
                                            echo "<td class='text-center'><b>{$ringkasan['total_hm']}</b></td>";
                                            echo "<td class='text-center'><b>{$ringkasan['total_ht']}</b></td>";
                                            echo "<td class='text-center'><b>{$ringkasan['total_hp']}</b></td>";
                                            ?>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="<?php echo 4 + $jumlah_tanggal_pada_bulan; ?>" class="text-center">Tidak ada pegawai pada jabatan yang dipilih.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php require_once("./footer.php"); ?>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Logika Export tidak perlu diubah, hanya hapus fungsi toggle
            $('#export_excel_button').on('click', function() {
                var data = document.getElementById('employee_data');
                var file = XLSX.utils.table_to_book(data, { sheet: "sheet1" });
                XLSX.write(file, { bookType: 'xlsx', bookSST: true, type: 'base64' });
                XLSX.writeFile(file, 'Rekap_Bulanan_<?php echo $bulan . '_' . $tahun; ?>.xlsx');
            });

            $('#export_pdf_button').on('click', function() {
                const elementToExport = document.getElementById('rekap-bulanan-export');
                const table = document.getElementById('employee_data');
                const title = document.getElementById('pdf-title');
                title.style.display = 'block';
                table.classList.add('pdf-export-table');
                var opt = { margin: 0.2, filename: 'Rekap_Bulanan_<?php echo $bulan . '_' . $tahun; ?>.pdf', image: { type: 'jpeg', quality: 0.98 }, html2canvas:  { scale: 2, useCORS: true }, jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' } };
                html2pdf().from(elementToExport).set(opt).save().then(function() {
                    title.style.display = 'none';
                    table.classList.remove('pdf-export-table');
                });
            });
        });
    </script>
</div>