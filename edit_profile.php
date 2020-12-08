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

if(!isset($GLOBALS['sesId'])) {
    header("Location: login.php");
    exit;
}

$TNMuser       = $GLOBALS['MySQLi_user_table_name'];
$TFuserid      = $GLOBALS['MySQLi_userid_field'];
$TFusername    = $GLOBALS['MySQLi_username_field'];
$TFemail       = $GLOBALS['MySQLi_email_field'];
$TFfullname    = $GLOBALS['MySQLi_fullname_field'];
$TFabout       = $GLOBALS['MySQLi_about_field'];
$TFsex         = $GLOBALS['MySQLi_sex_field'];
$TFdob         = $GLOBALS['MySQLi_dob_field'];
$TFPicname     = $GLOBALS['MySQLi_photo_field'];

$query1 = "SELECT * FROM `".$config['db']['pre'].$TNMuser."` where $TFuserid = '".$GLOBALS['sesId']."'";
$result1 = $con->query($query1);
$row1 = mysqli_fetch_assoc($result1);
$sesUname       = $GLOBALS['sesUsername'];
$sesemail       = $row1[$TFemail];
$sesfullname    = $row1[$TFfullname];
$sesabout       = $row1[$TFabout];
$sessex         = $row1[$TFsex];
$sesdob         = $row1[$TFdob];
$sesuserpic     = $row1[$TFPicname];

if($sesuserpic == "")
    $sesuserpic = "avatar_default.png";

$error = "";

if(isset($_POST['Submit']))
{
    if($_FILES['file']['name'] != "")
    {
        $uploaddir = 'storage/user_image/';
        $original_filename = $_FILES['file']['name'];
        $random1 = rand(9999,100000);
        $random2 = rand(9999,200000);
        $random3 = $random1.$random2;
        $extensions = explode(".", $original_filename);
        $extension = $extensions[count($extensions) - 1];
        $uniqueName =  $random3 . "." . $extension;
        $uploadfile = $uploaddir . $uniqueName;

        $file_type = "file";

        if ($extension == "jpg" || $extension == "jpeg" || $extension == "gif" || $extension == "png") {
            $file_type = "image";

            $size = filesize($_FILES['file']['tmp_name']);

            $image = $_FILES["file"]["name"];
            $uploadedfile = $_FILES['file']['tmp_name'];

            if ($image) {
                if ($extension == "jpg" || $extension == "jpeg") {
                    $uploadedfile = $_FILES['file']['tmp_name'];
                    $src = imagecreatefromjpeg($uploadedfile);
                } else if ($extension == "png") {
                    $uploadedfile = $_FILES['file']['tmp_name'];
                    $src = imagecreatefrompng($uploadedfile);
                } else {
                    $src = imagecreatefromgif($uploadedfile);
                }

                list($width, $height) = getimagesize($uploadedfile);

                $newwidth = 225;
                $newheight = 225;
                //$newheight = ($height / $width) * $newwidth;
                $tmp = imagecreatetruecolor($newwidth, $newheight);

                imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

                $filename = $uploaddir . "small" . $uniqueName;

                imagejpeg($tmp, $filename, 100);

                imagedestroy($src);
                imagedestroy($tmp);
            }


        }
        //else if it's not bigger then 0, then it's available '
        //and we send 1 to the ajax request
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            //$time = date('Y-m-d H:i:s', time());
            $query = "Update `".$config['db']['pre'].$TNMuser."` set $TFfullname='" . $_POST['name'] . "', $TFemail='" . $_POST['email'] . "', $TFabout='" . $_POST['about'] . "', $TFsex='" . $_POST['sex'] . "', $TFdob='" . $_POST['dob'] . "', $TFPicname='$uniqueName' WHERE $TFuserid = {$GLOBALS['sesId']} ";
            $query_result = $con->query($query);

            header("Location: index.php");
            exit;
        }
    }
    else{
        //$time = date('Y-m-d H:i:s', time());
        $query = "Update `".$config['db']['pre'].$TNMuser."` set $TFfullname='" . $_POST['name'] . "', $TFemail='" . $_POST['email'] . "', $TFabout='". addslashes($_POST['about'])."', $TFsex='" . $_POST['sex'] . "', $TFdob='" . $_POST['dob'] . "' WHERE $TFuserid = {$GLOBALS['sesId']}";
        $query_result = $con->query($query);

        header("Location: index.php");
        exit;
    }

}


?>
<?php if(!empty($error)) {
    echo '<script type="text/javascript">alert("' . $error . '");</script>';
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Edit Profile - Wchat Fully Responsive PHP/AJAX Chat</title>
    <meta name="keywords" content="PHP inbox messaging script, php chat script, php ajax Chat,facebook similar chat, php mysql chat, chat script, facebook style chat script, gmail style chat script. fbchat, gmail chat, facebook style message inbox, facebook similar inbox, facebook like chat" />
    <meta name="description"  content="This jQuery chat module easily to integrate Gmail/Facebook style chat into your existing website." />
    <meta name="author" content="Wchat - Codentheme.com">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,300italic,400italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>


    <!-- Global CSS -->
    <link href="assets/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Plugins CSS -->
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
    <!-- Theme CSS -->
    <link id="theme-style" rel="stylesheet" href="assets/css/profile.css">



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
                            echo '<option value="edit_profile.php?lang='.$lang2.'" selected>'.ucwords($lang2).'</option>';
                        }
                        else
                        {
                            echo '<option value="edit_profile.php?lang='.$lang2.'">'.ucwords($lang2).'</option>';
                        }
                    }
                    ?>
                </select>
            <?php } ?>
        </div>


    </div>
</div>

<!-- ******HEADER****** -->
<header class="header">
    <div class="container">
        <div class="col-lg-12 " align="center">
            <div class="profile-picture medium-profile-picture mpp XxGreen mnkLeft">
                <img width="169px" style="min-height:170px;" src="storage/user_image/small<?php echo $sesuserpic; ?>" alt="<?php echo $GLOBALS['sesUsername'];?>">
            </div>
            <div class="profile-content pull-left" align="left" >
                <h1 class="name"><?php echo $sesfullname;?></h1>
                <h2 class="desc">#<?php echo $sesfullname;?></h2>

            </div><!--//profile-->
        </div>
    </div><!--//container-->
</header><!--//header-->


<div class="middle-container container">
    <div class="middle-dabba col-md-12">
        <h1><?php echo $lang['EDITPROFILE']; ?></h1>
        <div id="post-form" style="padding:10px">

            <form name="form1" method="post" action="" id="send" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="input text">
                        <label for="file"><?php echo $lang['CHANGEPICTURE']; ?> </label>
                        <img class="pull-left" src="storage/user_image/<?php echo $sesuserpic; ?>" alt="<?php echo $GLOBALS['sesUsername'];?>"  style="width: 42px; border-radius: 50%"/>
                        <input type="file" name="file" style="width:70%">
                    </div>

                    <div class="input text">
                        <label for="name"><?php echo $lang['FULLNAME']; ?> </label><input type="text" name="name" value="<?php echo $sesfullname;?>">
                    </div>

                    <div class="input text">
                        <label for="email"><?php echo $lang['EMAIL']; ?></label><input type="text" name="email" value="<?php echo $sesemail;?>">
                    </div>

                    <div class="input text">
                        <label for="dob"><?php echo $lang['BIRTH']; ?> </label><input type="text" name="dob" placeholder="Format : 02-April-1992" value="<?php echo $sesdob;?>">
                    </div>




                </div>


                <div class="col-md-6">


                    <div class="input text">
                        <label for="sex"><?php echo $lang['SEX']; ?></label>
                        <input type="radio" name="sex" value="male" style="width: 10%" <?php if($sessex == "male") { echo "checked"; }?>> <?php echo $lang['MALE']; ?> <br>
                        <input type="radio" name="sex" value="female" style="width: 10%" <?php if($sessex == "female") { echo "checked"; }?>> <?php echo $lang['FEMALE']; ?>

                    </div>
                    <div class="input text">
                        <label for="about"><?php echo $lang['ABOUTME']; ?> </label>
                        <textarea name="about" style="width: 95%;height: 150px"><?php echo $sesabout;?></textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-12" align="center">
                <button class="btn btn-cta-theme" type="submit" name="Submit"><?php echo $lang['SUBMIT']; ?></button>
            </div>

            </form>
        </div>
    </div>
</div>


<!-- ******FOOTER****** -->
<footer class="footer">
    <div class="container text-center">
        <small class="copyright"><?php echo $lang['POWEREDBY']; ?></small>
    </div><!--//container-->
</footer><!--//footer-->



</body>
</html>