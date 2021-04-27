<!DOCTYPE html>
<html lang="en">
<?php include "header.php"; ?>
<?php
function createCbo($selected){
    $cbo = "<select class=\"form-control\" name=\"status\" id=\"status\">";
    if($selected==0){
        $cbo.= '<option value="0" selected>Non Aktif</option>';
        $cbo.= '<option value="1">Aktif</option>';
    }else{
        $cbo.= '<option value="0">Non Aktif</option>';
        $cbo.= '<option value="1"  selected>Aktif</option>';
    }
    $cbo.= "</select>";
    return $cbo;
}

// var_dump($_POST);
if(isset($_POST["id"])){
    $id = $_POST["id"];
    $sql = "UPDATE koin SET ";
    $sql.= "harga_maksimum=?, ";
    $sql.= "status=? ";
    $sql.= "WHERE id =? ";
    $db->query($sql,$_POST["harga_maksimum"],$_POST["status"],$_POST["id"]);
    echo "<script language=\"javascript\">alert(\"".$_POST["kode"]." berhasil diubah\")</script>";
}else{
    $id = $_GET["id"];
}
// var_dump($id);
$sql = "SELECT id, kode, nama, ifnull(harga_maksimum,0) as harga_maksimum, status ";
$sql .= "FROM koin ";
$sql .= "WHERE id=? ";
$arrKoin = $db->query($sql, $id)->fetchArray();
// var_dump($arrKoin);
$konten = "<form action=\"/koin/edit\" method=\"post\">";
$konten .= "<table class=\"table \">";
$konten .= "<tr>";
$konten .= "<td>Kode</td>";
$konten .= "<td>:</td>";
$konten .= "<td>" . $arrKoin["kode"] . "</td>";
$konten .= "</tr>";
$konten .= "<tr>";
$konten .= "<td>Nama</td>";
$konten .= "<td>:</td>";
$konten .= "<td>" . $arrKoin["nama"] . "</td>";
$konten .= "</tr>";
$konten .= "<tr>";
$konten .= "<td>Harga Maksimum</td>";
$konten .= "<td>:</td>";
$konten .= "<td><input name=\"harga_maksimum\" id=\"harga_maksimum\" class=\"form-control\" id=\"inputField\" type=\"text\" value=\"" . $arrKoin["harga_maksimum"] . "\">" . "</td>";
$konten .= "</tr>";
$konten .= "<tr>";
$konten .= "<td>Status</td>";
$konten .= "<td>:</td>";
$konten .= "<td>" . createCbo($arrKoin["status"]) . "</td>";
$konten .= "</tr>";
$konten .= "<tr>";
$konten .= "<td colspan=\"3\">";
$konten .= "<button type=\"submit\" value=\"Submit\">Submit</button>";
$konten .= "</td>";
$konten .= "</tr>";
$konten .= "</table>";
$konten .= "<input type=\"hidden\" id=\"id\" name=\"id\" value=\"".$id."\">";
$konten .= "<input type=\"hidden\" id=\"simpan\" name=\"simpan\" value=\"".$id."\">";
$konten .= "</form>";
?>
<body>

    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->

        <!-- /#sidebar-wrapper -->
        <?php include "sidebar.php"; ?>
        <!-- Page Content -->
        <div id="page-content-wrapper">
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
    <!-- <script>
    $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
    });
  </script> -->
</body>

</html>