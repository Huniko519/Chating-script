<?php
require_once('../config.php');
require_once('../includes/setting.php');
require_once('functions/func.admin.php');

$mysqli = db_connect($config);

if(!isset($_GET['page']))
{
    $_GET['page'] = 1;
}
session_start();
checkloggedadmin();

$query1 = "SELECT * FROM `".$config['db']['pre']."admins` where id = '".$_SESSION['admin']['id']."'";
$result1 = $mysqli->query($query1);
$row1 = mysqli_fetch_assoc($result1);
$string = $row1['username'];
$sesuserpic = $row1['picname'];

if($sesuserpic == "")
    $sesuserpic = "avatar_default.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="../plugins/images/favicon.png">
    <title>Wchat Admin - Fully Responsive PHP/AJAX Chat</title>
    <!-- Bootstrap Core CSS -->
    <link href="bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Data Table CSS -->
    <link href="../plugins/bower_components/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <!-- Menu CSS -->
    <link href="../plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <!--alerts CSS -->
    <link href="../plugins/bower_components/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
    <!-- morris CSS -->
    <link href="../plugins/bower_components/morrisjs/morris.css" rel="stylesheet">
    <!-- animation CSS -->
    <link href="css/animate.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/<?php echo $config['tpl_name'] ?>.css" rel="stylesheet">
    <!-- color CSS -->
    <?php
    if($config['tpl_name'] == 'style-dark'){
        //$color = $config['tpl_color'].'-'."dark";
        $color = $config['tpl_color'];
    }
    else{
        $color = $config['tpl_color'];
    }
    ?>
    <link href="assets/css/colors/<?php echo $color ?>.css" id="theme"  rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="fix-header fix-sidebar content-wrapper">
<!-- Preloader -->
<div class="preloader">
    <div class="cssload-speeding-wheel"></div>
</div>
<div id="wrapper">
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top m-b-0">
        <div class="navbar-header"> <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse"><i class="ti-menu"></i></a>
            <div class="top-left-part"><a class="logo" href="index.php"><b><img src="../plugins/images/eliteadmin-logo.png" alt="home" /></b><span class="hidden-xs"><img src="../plugins/images/eliteadmin-text.png" alt="home" /></span></a></div>
            <ul class="nav navbar-top-links navbar-left hidden-xs">
                <li><a href="javascript:void(0)" class="open-close hidden-xs waves-effect waves-light"><i class="icon-arrow-left-circle ti-menu"></i></a></li>

            </ul>
            <ul class="nav navbar-top-links navbar-right pull-right">


                <!-- /.dropdown -->
                <li class="dropdown"> <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#"> <img src="../storage/user_image/<?php echo $sesuserpic;?>" alt="<?php echo $row1['name'];?>" width="36" class="img-circle"><b class="hidden-xs"><?php echo $row1['username'];?></b><i class="icon-options-vertical"></i> </a>
                    <ul class="dropdown-menu dropdown-user animated flipInY">
                        <li><a href="../index.php" target="_blank"><i class="ti-comments-smiley"></i> Frontend</a></li>
                        <li><a href="configuration.php"><i class="ti-settings"></i> Wchat Setting</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="logout.php"><i class="fa fa-power-off"></i> Logout</a></li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.Megamenu -->
                <li class="right-side-toggle"> <a class="waves-effect waves-light" href="javascript:void(0)"><i class="ti-settings"></i></a></li>
                <!-- /.dropdown -->
            </ul>
        </div>
        <!-- /.navbar-header -->
        <!-- /.navbar-top-links -->
        <!-- /.navbar-static-side -->
    </nav>
    <!-- Left navbar-header -->
    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse slimscrollsidebar">
            <ul class="nav" id="side-menu">

                <li class="user-pro"> <a href="#" class="waves-effect"><img src="../storage/user_image/<?php echo $sesuserpic;?>" alt="<?php echo $row1['name'];?>"  class="img-circle"> <span class="hide-menu"><?php echo $row1['name'];?><span class="fa arrow"></span></span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="admin_edit.php"><i class="ti-user"></i> Edit Profile</a></li>
                        <li><a href="logout.php"><i class="fa fa-power-off"></i> Logout</a></li>
                    </ul>
                </li>
                <li class="nav-small-cap m-t-10">--- Main Menu</li>
                <li><a href="index.php" class="waves-effect"><i class="linea-icon linea-basic fa-fw" data-icon="v"></i> <span class="hide-menu">Dashboard</span></a></li>
                <li> <a href="#" class="waves-effect"><i data-icon="/" class="icon-user  fa-fw"></i> <span class="hide-menu">Users<span class="fa arrow"></span> </span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="users_add.php">Add Users</a></li>
                        <li><a href="users.php">All Users</a></li>
                        <li><a href="users-export.php">Export Users Data</a></li>
                    </ul>
                </li>
                <li><a href="messages.php" class="waves-effect"><i class="icon-envelope fa-fw"></i> <span class="hide-menu">Messages</span></a></li>
                <li> <a href="#" class="waves-effect"><i data-icon="/" class="icon-people fa-fw"></i> <span class="hide-menu">Admin<span class="fa arrow"></span> </span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="admin_add.php">Add Admin</a></li>
                        <li><a href="admin_view.php">All Admin</a></li>
                    </ul>
                </li>
                <li><a href="configuration.php" class="waves-effect"><i class="ti-settings fa-fw"></i> <span class="hide-menu">Configuration</span></a></li>
                <li><a href="logout.php" class="waves-effect"><i class="icon-logout fa-fw"></i> <span class="hide-menu">Log out</span></a></li>
                <li class="nav-small-cap">--- Support</li>
                <li><a href="../../documentation/documentation.html" target="_blank" class="waves-effect"><i class="fa fa-circle-o text-danger"></i> <span class="hide-menu">Documentation</span></a></li>

            </ul>
        </div>
    </div>
    <!-- Left navbar-header end -->