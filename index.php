<?php
require_once 'php/includes/db_connect.php';
require_once 'php/includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "login.html";</script>';
}
else{
    $user = $_SESSION['userDetail'];
    $userID = $user->getId();
    $role = $user->getRole();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>SneakerCube</title>

  <link rel="icon" href="images/favicon.png" type="image"/>
  <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- IonIcons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- Google Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,700;1,400&display=swap" rel="stylesheet">
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar navbar-light" style="background-color:#E3E3E3; border-color:#1888CA">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars" style="color:#1888CA"></i></a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color:#000000;">
        <!-- Brand Logo-->
        <a href="https://sneakercube.io/" class="brand-link logo-switch">
            <img src="images/logo-cube-white.png" alt="Sneakercube Logo" class="brand-image-xl logo-xs">
            <img src="images/logo-horizontal-white.png" alt="Sneakercube Logo" class="brand-image-xl logo-xl">
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image" style="align-self: center;">
                    <img src="images/user-avatar.png" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info" style="white-space: nowrap;">
                    <p style="font-size:0.75rem; color:#E3E3E3; margin-bottom:0rem; color:#1888CA">Welcome</p>
                    <a href="#myprofile" data-file="myProfile.php" id="goToProfile" class="d-block"><?=$user->getName() ?></a>
                </div>
            </div>
    
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" id="sideMenu" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                    <li class="nav-item">
                        <a href="#dashboard" data-file="dashboard.php" class="nav-link link">
                            <i class="nav-icon fas fa-th"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                  
                    <?php 
                        if($role == "PRIADMIN"){
                            echo '<li class="nav-item"><a href="#members" data-file="members.php" class="nav-link link"><i class="nav-icon fas fa-user"></i><p>Members</p></a></li>';
                            echo '<li class="nav-item"><a href="#manageRole" data-file="roleManagement.php" class="nav-link link"><i class="nav-icon fas fa-user"></i><p>Manage Roles</p></a></li>';
                            echo '<li class="nav-item"><a href="#entryResult" data-file="entryResult.php" class="nav-link link"><i class="nav-icon fas fa-book"></i><p>Entry Result</p></a></li>';
                        }
                    ?>
                  
                    <li class="nav-item">
                        <a href="#shoes" data-file="shoes.php" class="nav-link link">
                            <i class="nav-icon fas fa-shoe-prints"></i>
                            <p>Shoes</p>
                        </a>
                    </li>
                  
                    <li class="nav-item has-treeview">
        			    <a href="#" class="nav-link">
        				    <i class="nav-icon fas fa-cogs"></i>
        				    <p>Settings<i class="fas fa-angle-left right"></i></p>
        			    </a>
        			
        			    <ul class="nav nav-treeview">
        				    <li class="nav-item">
        					    <a href="#myprofile" data-file="myProfile.php" class="nav-link link">
        						    <i class="nav-icon fas fa-id-badge"></i>
        						    <p>Profile</p>
        					    </a>
        				    </li>
        				
        				    <li class="nav-item">
        					    <a href="#changepassword" data-file="changePassword.php" class="nav-link link">
        						    <i class="nav-icon fas fa-key"></i>
        						    <p>Change Password</p>
        					    </a>
        				    </li>
        			    </ul>
        		    </li>
        		
        		    <li class="nav-item">
        			    <a href="php/logout.php" class="nav-link">
        				    <i class="nav-icon fas fa-sign-out-alt"></i>
        				    <p>Logout</p>
        			    </a>
        		    </li>
                </ul>
            </nav>
          <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" id="mainContents">
        
    </div><!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark"></aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2020 sneakercube.io</a> - </strong>All rights reserved.
        <div class="float-right d-none d-sm-inline-block"><b>Version</b> 1.0.0</div>
    </footer>
</div><!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="plugins/select2/js/select2.full.min.js"></script>
<!-- Bootstrap -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="dist/js/adminlte.js"></script>
<!-- OPTIONAL SCRIPTS -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/toastr/toastr.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>

<!-- page script -->
<script>
$(function () {
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    
    $('#sideMenu').on('click', '.link', function(){
        var files = $(this).attr('data-file');
        $('#sideMenu').find('.active').removeClass('active');
        $(this).addClass('active');
        
        $.get(files, function(data) {
            $('#mainContents').html(data);
        });
    });
    
    $('#goToProfile').on('click', function(){
        var files = $(this).attr('data-file');
        $('#sideMenu').find('.active').removeClass('active');
        $(this).addClass('active');
        
        $.get(files, function(data) {
            $('#mainContents').html(data);
        });
    });
    
    $("a[href='#dashboard']").click();
});
</script>
</body>
</html>
