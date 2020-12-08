<?php
require_once('config.php');
require_once('includes/dbcon.php');
require_once('includes/setting.php');
require_once('includes/function.php');
checkinstall($config);


if(isset($_GET['lang'])){
    $_SESSION['lang'] = $_GET['lang'];
}
if(isset($_SESSION['id'])) {
    echo '<script type="text/javascript"> window.location = "index.php" </script>';
    exit;
}
$config['lang'] = check_user_lang($config);
require_once('includes/lang/lang_'.$config['lang'].'.php');

$error = "";

/*$countryIP = getLocationInfoByIp();
$countrycode = $countryIP['code'];
$countryname = $countryIP['country'];*/

$countryname = "IN";
$errors = array();

if(isset($_POST['guestlogin']))
{

    $errors['username'] = validStrLen($_POST['username'], 4, 16, $con, $config);

    if($errors['username'] == 1)
    {

            /*$time = date('Y-m-d H:i:s', time());*/
            $query = "insert into `".$config['db']['pre'].$MySQLi_user_table_name."` set $MySQLi_fullname_field='" . $_POST['name'] . "', $MySQLi_email_field='" . $_POST['email'] . "', $MySQLi_username_field='" . $_POST['username'] . "', $MySQLi_password_field='" . $_POST['password'] . "', $MySQLi_joined_field = '$timenow', $MySQLi_country_field='$countryname' ";
            $query_result = $con->query($query);

            $user_id = $con->insert_id;
            $username = $_POST['username'];
            if (isset($user_id)) {
                $_SESSION['id'] = $user_id;
                $_SESSION['username'] = $username;
                echo '<script type="text/javascript"> window.location = "index.php" </script>';
                exit;
            } else {
                $error = $lang['USERNOTFOUND'];
            }

    }

}
if(isset($_POST['login']))
{

     $query = "SELECT $MySQLi_userid_field,$MySQLi_username_field,$MySQLi_password_field,$MySQLi_status_field FROM `".$config['db']['pre'].$MySQLi_user_table_name."` WHERE $MySQLi_username_field='" . $_POST['username'] . "' AND $MySQLi_password_field='" . md5($_POST['password']) . "' LIMIT 1";
    $query_result = mysqli_query($con,$query);
     $row_count = mysqli_num_rows($query_result);
    if($row_count>0){
        $info = mysqli_fetch_array($query_result);
        $user_id = $info[$MySQLi_userid_field];
        $username = $info[$MySQLi_username_field];
        $status = $info[$MySQLi_status_field];
        if($status != 2)
        {
            $_SESSION['id'] = $user_id;
            $_SESSION['username'] = $username;

            echo '<script type="text/javascript"> window.location = "index.php" </script>';
            exit;
        }
        else
        {
            if($info['status'] == 2)
                $error = $lang['ACCOUNTBAN'];
            else
                $error = $lang['USERNOTFOUND'];
        }
    }
    else
    {
        $error = $lang['USERNOTFOUND'];
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <title>Wchat - Fully Responsive PHP/AJAX Chat - Login</title>
    <!-- Bootstrap Core CSS -->
    <link href="assets/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Menu CSS -->
    <link href="plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <!-- Animation CSS -->
    <link href="assets/css/animate.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style-light.css" rel="stylesheet">
    <!-- color CSS you can use different color css from css/colors folder -->
    <!-- We have chosen the skin-blue (blue.css) for this starter
              page. However, you can choose any other skin from folder css / colors .
    -->
    <link href="assets/css/colors/blue.css" id="theme"  rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!-- Preloader -->
<div class="preloader">
    <div class="cssload-speeding-wheel"></div>
</div>
<section id="wrapper" class="front-login-register">
    <div class="login-box">
        <div class="white-box">
            <form class="form-horizontal form-material" id="loginform" method="post" action="#">
                <h3 class="box-title m-b-20"><?php echo $lang['SIGNIN']; ?></h3>
                <span style="color:#df6c6e;">
                    <?php
                    if(!empty($error)){
                        echo '<div class="byMsg byMsgError">! '.$error.'</div>';
                    }
                    ?>
                </span>
                <div class="form-group ">
                    <div class="col-xs-12">
                        <input class="form-control" type="text" required="" placeholder="<?php echo $lang['USERNAME']; ?>" name="username">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control" type="password" required="" placeholder="<?php echo $lang['PASSWORD']; ?>" name="password">
                    </div>
                </div>

                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" name="login" type="submit"><?php echo $lang['LOGIN']; ?></button>
                    </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="row">
                        <div class="col-xs-1">&nbsp;</div>
                        <div class="col-xs-5">
                            <button onclick="fblogin()" class="btn btn-rounded waves-effect waves-light btn-facebook" type="button"><span class="btn-label"><i class="fa fa-facebook"></i></span>Facebook</button>
                        </div>
                        <div class="col-xs-5">
                            <a onclick="gmlogin()" class="btn btn-rounded waves-effect waves-light btn-googleplus"><span class="btn-label"><i class="fa fa-google-plus"></i></span>Google+</a>
                        </div>
                    </div>

                </div>

                <div class="form-group m-b-0">
                    <div class="col-sm-12 text-center">
                        <p><?php echo $lang['DONTHAVACCOUNT']; ?>? <a href="register.php" class="text-primary m-l-5"><b><?php echo $lang['SIGNUP']; ?></b></a></p>
                    </div>
                </div>
            </form>
            <form class="form-horizontal" id="recoverform" action="#" method="post">
                <div class="form-group ">
                    <div class="col-xs-12">
                        <h3><?php echo $lang['LOGINGUEST']; ?></h3>
                    </div>
                </div>
                <div class="form-group ">
                    <div class="col-xs-12">
                        <input class="form-control" type="text" required="" placeholder="<?php echo $lang['GUESTUNAME']; ?>">
                    </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" type="submit" name="guestlogin"><?php echo $lang['LOGINGUEST']; ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- jQuery -->
<script src="plugins/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="assets/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Menu Plugin JavaScript -->
<!--<script src="../plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js"></script>-->
<!--slimscroll JavaScript -->
<script src="assets/js/jquery.slimscroll.js"></script>
<!--Wave Effects -->
<!--<script src="js/waves.js"></script>-->
<!-- Custom Theme JavaScript -->
<script src="assets/js/custom.js"></script>
<!--Style Switcher -->
<script src="plugins/bower_components/styleswitcher/jQuery.style.switcher.js"></script>
<!--Style Switcher -->


<script type="text/javascript">
    var w=640;
    var h=500;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    function fblogin()
    {
        var newWin = window.open("social_login/facebook/index.php", "fblogin", 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no,display=popup, width='+w+', height='+h+', top='+top+', left='+left);
    }

    function gmlogin()
    {
        var newWin = window.open("social_login/google/index.php", "gmlogin", 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
    }
    $(document).ready(function() {
        $('#button').click(function(e) { // Button which will activate our modal
            $('.modal').reveal({ // The item which will be opened with reveal
                animation: 'fade',                   // fade, fadeAndPop, none
                animationspeed: 600,                       // how fast animtions are
                closeonbackgroundclick: true,              // if you click background will modal close?
                dismissmodalclass: 'close'    // the class of a button or element that will close an open modal
            });
            return false;
        });
    });
</script>

</body>
</html>
