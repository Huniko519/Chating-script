<?php
require_once('config.php');
require_once('includes/dbcon.php');
require_once('includes/setting.php');
require_once('includes/function.php');
checkinstall($config);
$config['lang'] = check_user_lang($config);
require_once('includes/lang/lang_'.$config['lang'].'.php');

if(isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

$countryIP = getLocationInfoByIp();
$countryname = $countryIP['country'];

//$countryname = "India";
$errors = array();

if(isset($_POST['signup']))
{

    $errors['username'] = validStrLen($_POST['username'], 4, 10, $con, $config, $lang);

    if($errors['username'] == 1)
    {

            /*$time = date('Y-m-d H:i:s', time());*/
            $query = "insert into `".$config['db']['pre'].$MySQLi_user_table_name."` set $MySQLi_fullname_field='" . $_POST['name'] . "', $MySQLi_email_field='" . $_POST['email'] . "', $MySQLi_username_field='" . $_POST['username'] . "', $MySQLi_password_field='" . md5($_POST['password']) . "', $MySQLi_joined_field = '$timenow', $MySQLi_country_field='$countryname'";
            $query_result = $con->query($query);

            $user_id = $con->insert_id;

            $from = "Wchat";
            $to = $_POST['username'];
            $to_id = $user_id;
            $from_id = 1;
            $message = "Weclome to wchat you can test better if you login with 2 diffrent browser and with other userid. Also Seacrh user and start chat with people.";

            /*$sql = "insert into `".$config['db']['pre']."messages` (from_uname,to_uname,from_id,to_id,message_content,message_type,message_date) values ('$from', '$to','$from_id','$to_id','".addslashes($message)."','text',NOW())";

            $query = $con->query($sql);*/

            $username = $_POST['username'];
            if (isset($user_id)) {
                $_SESSION['id'] = $user_id;
                $_SESSION['username'] = $username;
                header("Location: index.php");
                exit;
            } else {
                $error = $lang['USERNOTFOUND'];
            }

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
    <title>Wchat - Fully Responsive PHP/AJAX Chat - Create Account</title>
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
            <form class="form-horizontal form-material" id="loginform" action="#" method="post" enctype="multipart/form-data">
                <h3 class="box-title m-b-20"><?php echo $lang['SIGNUP']; ?></h3>
                <span style="color:#df6c6e;">
                    <?php
                    if(!empty($errors)){
                        echo '<div class="byMsg byMsgError">! '.$errors['username'].'</div>';
                    }
                    ?>
                </span>
                <div class="form-group ">
                    <div class="col-xs-12">
                        <input class="form-control" type="text" required="" placeholder="<?php echo $lang['FULLNAME']; ?>" name="name">
                    </div>
                </div>
                <div class="form-group ">
                    <div class="col-xs-12">
                        <input class="form-control" type="text" required="" placeholder="<?php echo $lang['USERNAME']; ?>" name="username">
                    </div>
                </div>
                <div class="form-group ">
                    <div class="col-xs-12">
                        <input class="form-control" type="email" required="" placeholder="<?php echo $lang['EMAIL']; ?>" name="email">
                    </div>
                </div>
                <div class="form-group ">
                    <div class="col-xs-12">
                        <input class="form-control" type="password" required="" placeholder="<?php echo $lang['PASSWORD']; ?>" name="password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="checkbox checkbox-primary p-t-0">
                            <input id="checkbox-signup" type="checkbox" required="">
                            <label for="checkbox-signup"> <?php echo $lang['IAGREETOTERM']; ?></label>
                        </div>
                    </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit" name="signup"><?php echo $lang['SIGNUP']; ?></button>
                    </div>
                </div>
                <div class="form-group m-b-0">
                    <div class="col-sm-12 text-center">
                        <p><?php echo $lang['ALREDYHAVACCOUNT']; ?>? <a href="login.php" class="text-primary m-l-5"><b><?php echo $lang['SIGNIN']; ?></b></a></p>
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
</body>
</html>
