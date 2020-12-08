<?php
require_once('config.php');
require_once('includes/dbcon.php');
require_once('includes/setting.php');
require_once('includes/function.php');

if(isset($_GET['lang'])){
    $_SESSION['lang'] = $_GET['lang'];
}
$config['lang'] = check_user_lang($config);
require_once('includes/lang/lang_'.$config['lang'].'.php');

if(!isset($_GET['uname']))
{
    if(!isset($GLOBALS['sesId'])) {
        echo $lang['INVALIDURL'];
        exit();
    }
    else{
        $_GET['uname'] = $GLOBALS['sesUsername'];
    }
}

$query1 = "SELECT * FROM `".$config['db']['pre'].$MySQLi_user_table_name."` where $MySQLi_username_field = '".$_GET['uname']."'";
$result1 = $con->query($query1);
if(mysqli_num_rows($result1)==0){
    //and we send 0 to the ajax request
    echo $lang['UNAMENOTEXIST'];
    exit();
}
$row1 = mysqli_fetch_assoc($result1);
$userpic = $row1[$MySQLi_photo_field];

if($userpic == "")
    $userpic = "avatar_default.png";

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<head>
    <title><?php echo $row1[$MySQLi_fullname_field];?> - Wchat Fully Responsive PHP/AJAX Chat</title>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="php chat script, php ajax Chat,facebook similar chat, php mysql chat, chat script, facebook style chat script, gmail style chat script. fbchat, gmail chat, facebook style message inbox, facebook similar inbox, facebook like chat" />
    <meta name="description"  content="This jQuery chat module easily to integrate Gmail/Facebook style chat into your existing website." />
    <meta name="author" content="Wchat - Codentheme.com">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,300italic,400italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <!-- Global CSS -->
    <link href="assets/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Plugins CSS -->
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
    <!-- Theme CSS -->
    <link id="theme-style" rel="stylesheet" href="assets/css/profile.css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


</head>

<body>


<script language="JavaScript">

    function MM_jumpMenu(targ,selObj,restore){ //v3.0
        eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
        if (restore) selObj.selectedIndex=0;
    }

</script>
<div class="entry-board J_entryBoard col-lg-12">
    <div class="col-lg-12" align="center">
        <div class="pull-left">
            <a href="index.php"><?php echo $lang['CHATPAGE']; ?></a>
        </div>
        <div class="entry pull-left">
            <a href="profile.php"><?php echo $lang['MYPROFILE']; ?></a>
        </div> <div class="entry  pull-left">
            <a href="logout.php"><?php echo $lang['LOGOUT']; ?></a>
        </div>
        <div class="entry  pull-left hidden-xs">
            <?php if($config['userlangsel'] == 1){ ?>
                <select name="lang" id="lang" onChange="MM_jumpMenu('parent',this,0)" class="form-control" style="width:60%">
                    <?php
                    $langs = array();

                    if ($handle = opendir('includes/lang/'))
                    {
                        while (false !== ($file = readdir($handle)))
                        {
                            if ($file != "." && $file != "..")
                            {
                                $lang2 = str_replace('.php','',$file);
                                $lang2 = str_replace('lang_','',$lang2);

                                $langs[] = $lang2;
                            }
                        }
                        closedir($handle);
                    }

                    sort($langs);

                    foreach ($langs as $key => $lang2)
                    {
                        if($config['lang'] == $lang2)
                        {
                            echo '<option value="profile.php?lang='.$lang2.'" selected>'.ucwords($lang2).'</option>';
                        }
                        else
                        {
                            echo '<option value="profile.php?lang='.$lang2.'">'.ucwords($lang2).'</option>';
                        }
                    }
                    ?>
                </select>
            <?php } ?>
        </div>

    </div>
</div>

<!-- ******HEADER****** -->
<header class="header" style="padding-top: 20px">
    <div class="container">
        <div class="col-lg-12 " align="center">
            <div class="profile-picture medium-profile-picture mpp XxGreen mnkLeft">
                <img width="169px" style="min-height:170px;" src="storage/user_image/<?php echo $userpic; ?>" alt="<?php echo $row1[$MySQLi_username_field];?>">
            </div>
            <div class="profile-content pull-left" align="left" >
                <h1 class="name"><?php echo $row1[$MySQLi_fullname_field];?></h1>
                <h2 class="desc">#<?php echo $row1[$MySQLi_username_field];?></h2>
            </div><!--//profile-->
            <?php
            if($_GET['uname'] == $GLOBALS['sesUsername']){ ?>
                <a class="btn btn-cta-primary pull-right" href="edit_profile.php"><i class="fa fa-paper-plane"></i><?php echo $lang['EDITPROFILE'];?></a>
            <?php } ?>
        </div>
    </div><!--//container-->
</header><!--//header-->


<div class="container sections-wrapper">
    <div class="row">
        <div class="primary col-md-8 col-sm-12 col-xs-12">
            <section class="about section">
                <div class="section-inner">
                    <h2 class="heading"><?php echo $lang['ABOUTME']; ?></h2>
                    <div class="content">
                        <?php echo $row1[$MySQLi_about_field];?>

                    </div><!--//content-->
                </div><!--//section-inner-->
            </section><!--//section-->

        </div><!--//primary-->
        <div class="secondary col-md-4 col-sm-12 col-xs-12">
            <aside class="info aside section">
                <div class="section-inner">
                    <h2 class="heading sr-only"><?php echo $lang['BASICINFO']; ?></h2>
                    <div class="content">
                        <ul class="list-unstyled">
                            <?php
                            if(!empty($row1[$MySQLi_sex_field]))
                            {
                                if($row1[$MySQLi_sex_field] == "male")
                                {
                                    ?><li><i class="fa fa-mars"></i><span class="sr-only"><?php echo $lang['GENDER']; ?>:</span><?php echo $lang['MALE']; ?></li><?php
                                }
                                else{
                                    ?><li><i class="fa fa-venus"></i><span class="sr-only"><?php echo $lang['GENDER']; ?>:</span><?php echo $lang['FEMALE']; ?></li><?php
                                }
                            }
                            ?>
                            <li><i class="fa fa-birthday-cake"></i><span class="sr-only"><?php echo $lang['BIRTH']; ?>:</span><?php echo $row1[$MySQLi_dob_field];?></li>
                            <li><i class="fa fa-map-marker"></i><span class="sr-only"><?php echo $lang['LOCATION']; ?>:</span><?php echo $row1[$MySQLi_country_field];?></li>
                            <li><i class="fa fa-envelope-o"></i><span class="sr-only"><?php echo $lang['EMAIL']; ?>:</span><a href="#"><?php echo $row1[$MySQLi_email_field];?></a></li>
                        </ul>
                    </div><!--//content-->
                </div><!--//section-inner-->
            </aside><!--//aside-->



        </div><!--//secondary-->
    </div><!--//row-->
</div><!--//masonry-->

<!-- ******FOOTER****** -->
<footer class="footer">
    <div class="container text-center">
        <small class="copyright"><?php echo $lang['POWEREDBY']; ?></small>
    </div><!--//container-->
</footer><!--//footer-->











</body>
</html>

