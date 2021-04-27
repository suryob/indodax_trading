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
if(isset($_POST["nama"])){
    $sql = "INSERT INTO koin(kode,nama,harga_maksimum,status) VALUES(?,?,?,?)";
    $db->query($sql,$_POST["kode"],$_POST["nama"],$_POST["harga_maksimum"],$_POST["status"]);
    echo "<script language=\"javascript\">alert(\"".$_POST["kode"]." berhasil ditambahkan\")</script>";
}else{
    // $id = $_GET["id"];
}

$konten = "<form action=\"/koin/tambah\" method=\"post\">";
$konten .= "<table class=\"table \">";
$konten .= "<tr>";
$konten .= "<td>Kode</td>";
$konten .= "<td>:</td>";
$konten .= "<td><input name=\"kode\" id=\"kode\" class=\"form-control\" id=\"inputField\" type=\"text\" maxlength=\"6\">" . "</td>";
$konten .= "</tr>";
$konten .= "<tr>";
$konten .= "<td>Nama</td>";
$konten .= "<td>:</td>";
$konten .= "<td><input name=\"nama\" id=\"nama\" class=\"form-control\" id=\"inputField\" type=\"text\" maxlength=\"50\"></td>";
$konten .= "</tr>";
$konten .= "<tr>";
$konten .= "<td>Harga Maksimum</td>";
$konten .= "<td>:</td>";
$konten .= "<td><input name=\"harga_maksimum\" id=\"harga_maksimum\" class=\"form-control\" id=\"inputField\" type=\"text\" value=\"0\">" . "</td>";
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
			<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
				<button class="btn btn-primary" id="menu-toggle">Menu</button>
			</nav>
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