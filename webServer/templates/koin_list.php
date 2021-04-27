<?php
$sql = "SELECT id, kode, nama, ifnull(harga_maksimum,0) as harga_maksimum, status ";
$sql .= "FROM koin";
$arrKoin = $db->query($sql)->fetchall();
// var_dump($arrKoin);
$konten = "<table class=\"table \">";
$konten .= "<tr>";
$konten .= "<td>Kode</td>";
$konten .= "<td>Nama</td>";
$konten .= "<td>Harga Maksimum</td>";
$konten .= "<td>Status</td>";
$konten .= "<td>&nbsp;</td>";
$konten .= "</tr>";
foreach ($arrKoin as $koin) {
    if ($koin["id"] != 0) {
        $descStatus = "Non Aktif";
        if ($koin["status"] == 1) {
            $descStatus = "Aktif";
        }
        $konten .= "<tr>";
        $konten .= "<td>" . $koin["kode"] . "</td>";
        $konten .= "<td>" . $koin["nama"] . "</td>";
        $konten .= "<td>" . $koin["harga_maksimum"] . "</td>";
        $konten .= "<td>" . $descStatus . "</td>";
        $konten .= "<td><a href=/koin/edit?id=" . $koin["id"] . ">Edit</></td>";
        $konten .= "</tr>";
    }
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
            <a href="/koin/tambah" class="button">Tambah Koin</a>
            <div class="container-fluid">
                <?php echo $konten; ?>
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