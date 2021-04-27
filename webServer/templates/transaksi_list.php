<?php
$tglMulai = isset($_GET["tglmulai"])?$_GET["tglmulai"]:date("Y-m-d");
$tglAkhir = isset($_GET["tglakhir"])?$_GET["tglakhir"]:date("Y-m-d");
$sql = "SELECT koin.kode,koin.nama,transaksi.tgl_beli,transaksi.qty,transaksi.harga_beli,transaksi.fee_beli,ifnull(transaksi.tgl_jual,'0000-00-00') AS tgl_jual, ifnull(transaksi.harga_jual,0) AS harga_jual,ifnull(transaksi.fee_jual,0) AS fee_jual ";
$sql.= "FROM koin JOIN transaksi ON koin.id = transaksi.koin_id ";
$sql.= "WHERE transaksi.tgl_beli BETWEEN ? AND ? AND transaksi.status <>4";

$arrTransaksi = $db->query($sql,$tglMulai.' 00:00:00',$tglAkhir.' 23:59:59')->fetchall();
$konten = "<table class=\"table \">";
$konten .= "<tr>";
$konten .= "<td>Kode</td>";
$konten .= "<td>Nama</td>";
$konten .= "<td>Tgl Beli</td>";
$konten .= "<td>Qty</td>";
$konten .= "<td>Harga Beli</td>";
$konten .= "<td>Fee Beli</td>";
$konten .= "<td>Tgl Jual</td>";
$konten .= "<td>Harga Jual</td>";
$konten .= "<td>Fee Jual</td>";
$konten .= "<td>Margin</td>";
$konten .= "</tr>";
foreach ($arrTransaksi as $transaksi) {
    $margin = ($transaksi["qty"]*$transaksi["harga_jual"]) - ($transaksi["qty"]*$transaksi["harga_beli"]);
    $konten .= "<tr>";
    $konten .= "<td>" . $transaksi["kode"] . "</td>";
    $konten .= "<td>" . $transaksi["nama"] . "</td>";
    $konten .= "<td>" . $transaksi["tgl_beli"] . "</td>";
    $konten .= "<td>" . number_format($transaksi["qty"],8,",",".") . "</td>";
    $konten .= "<td>" . number_format($transaksi["harga_beli"],0,",",".") . "</td>";
    $konten .= "<td>" . number_format($transaksi["fee_beli"],0,",",".") . "</td>";
    $konten .= "<td>" . $transaksi["tgl_jual"] . "</td>";
    $konten .= "<td>" . number_format($transaksi["harga_jual"],0,",",".") . "</td>";
    $konten .= "<td>" . number_format($transaksi["fee_jual"],0,",",".") . "</td>";
    $konten .= "<td>" . number_format($margin,0,",",".") . "</td>";
    $konten .= "</tr>";
}
$konten .= "</table>";
?>
<!DOCTYPE html>
<html lang="en">
<?php include "header.php"; ?>

<body>

    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->

        <!-- /#sidebar-wrapper -->
        <?php include "sidebar.php"; ?>
        <!-- Page Content -->
        <div id="page-content-wrapper">
			<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
				<button class="btn btn-primary" id="menu-toggle">Menu</button>
			</nav>
		<form action="/transaksi/list">
		  <label for="tglmulai">Tgl Mulai:</label>
		  <input type="date" id="tglmulai" name="tglmulai" value="<?php echo $tglMulai; ?>">
		  <label for="tglakhir">Tgl Akhir:</label>
		  <input type="date" id="tglakhir" name="tglakhir" value="<?php echo $tglAkhir; ?>">
		  <input type="submit">
		</form>
            <div class="container-fluid">
				<div class="table-responsive">
                <?php echo $konten; ?>
				</div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->

    <!-- Bootstrap core JavaScript -->
    <script src="/templates/vendor/jquery/jquery.min.js"></script>
    <script src="/templates/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Menu Toggle Script -->
    <script>
    $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
    });
  </script>

</body>

</html>