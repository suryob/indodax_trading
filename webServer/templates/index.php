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
      <div class="container-fluid">
        <?php echo $konten; ?>
      </div>
    </div>
    <!-- /#page-content-wrapper -->
  </div>
  <!-- /#wrapper -->

  <!-- Bootstrap core JavaScript -->
  <script src="templates/vendor/jquery/jquery.min.js"></script>
  <script src="templates/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Menu Toggle Script -->
  <script>
    $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
    });
  </script>

</body>

</html>