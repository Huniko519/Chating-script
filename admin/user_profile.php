<?php
include("header.php");

if(!isset($_GET['id']))
{
    echo '<script>window.location="404.php"</script>';
}

$error = array();
$errorNo = 0;
if(isset($_POST['Submit']))
{
    if(!check_allow()){
        ?>
        <script src="../plugins/bower_components/jquery/dist/jquery.min.js"></script>
        <script>
            $(document).ready(function(){
                $('#sa-title').trigger('click');
            });
        </script>
    <?php

    }
    else{
        if($_POST["username"] == ""){
            $error[] = "Username can't be blank.";
            $errorNo = 1;
        }
        if($_POST["email"] == ""){
            $error[] = "Email can't be blank.";
            $errorNo = 2;
        }
        if($errorNo==0) {
            if ($_FILES['file']['name'] != "") {
                $uploaddir = '../storage/user_image/';
                $original_filename = $_FILES['file']['name'];

                $extensions = explode(".", $original_filename);
                $extension = $extensions[count($extensions) - 1];
                $uniqueName = $string . "." . $extension;
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
                        $newheight = ($height / $width) * $newwidth;
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
                    echo $uniqueName;
                    $query = "Update `".$config['db']['pre'].$MySQLi_user_table_name."` set
            $MySQLi_username_field='" . mysqli_real_escape_string($mysqli, $_POST["username"]) . "',
            $MySQLi_email_field='" . mysqli_real_escape_string($mysqli, $_POST["email"]) . "',
            $MySQLi_fullname_field='" . mysqli_real_escape_string($mysqli, $_POST['name']) . "',
            $MySQLi_about_field='" . mysqli_real_escape_string($mysqli, $_POST['about']) . "',
            $MySQLi_sex_field='" . mysqli_real_escape_string($mysqli, $_POST['sex']) . "',
            $MySQLi_dob_field='" . mysqli_real_escape_string($mysqli, $_POST['dob']) . "',
            $MySQLi_country_field='" . mysqli_real_escape_string($mysqli, $_POST['country']) . "',
                $MySQLi_photo_field='$uniqueName'
                WHERE $MySQLi_userid_field = '".$_GET['id']."' LIMIT 1";
                    $query_result = $mysqli->query($query) or mysqli_error($mysqli);

                    $success = "Profile Updated Successfully";
                }
            } else {
                //$time = date('Y-m-d H:i:s', time());
                $query = "Update `".$config['db']['pre'].$MySQLi_user_table_name."` set
            $MySQLi_username_field='" . mysqli_real_escape_string($mysqli, $_POST["username"]) . "',
            $MySQLi_email_field='" . mysqli_real_escape_string($mysqli, $_POST["email"]) . "',
            $MySQLi_fullname_field='" . mysqli_real_escape_string($mysqli, $_POST['name']) . "',
            $MySQLi_about_field='" . mysqli_real_escape_string($mysqli, $_POST['about']) . "',
            $MySQLi_sex_field='" . mysqli_real_escape_string($mysqli, $_POST['sex']) . "',
            $MySQLi_dob_field='" . mysqli_real_escape_string($mysqli, $_POST['dob']) . "',
            $MySQLi_country_field='" . mysqli_real_escape_string($mysqli, $_POST['country']) . "'
            WHERE $MySQLi_userid_field = '".$_GET['id']."' LIMIT 1";
                $query_result = $mysqli->query($query);

                $success = "Profile Updated Successfully";
            }
        }
    }

}


$user = "SELECT * FROM `".$config['db']['pre'].$MySQLi_user_table_name."` where $MySQLi_userid_field = '".$_GET['id']."'";
$userresult = $mysqli->query($user);
$fetchuser = mysqli_fetch_assoc($userresult);
$fetchusername  = $fetchuser[$MySQLi_username_field];
$fetchuserpic     = $fetchuser[$MySQLi_photo_field];

$fetchname  = $fetchuser[$MySQLi_fullname_field];
$fetchemail  = $fetchuser[$MySQLi_email_field];
$fetchsex  = $fetchuser[$MySQLi_sex_field];
$fetchdob  = $fetchuser[$MySQLi_dob_field];
$fetchabout  = $fetchuser[$MySQLi_about_field];
$fetchcountry  = $fetchuser[$MySQLi_country_field];
$fetchjoined  = $fetchuser[$MySQLi_joined_field];

if($fetchuserpic == "")
    $fetchuserpic = "avatar_default.png";
?>

    <!-- Page Content -->
    <div id="page-wrapper">
    <div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $fetchusername;?> Profile</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="index.php">Dashboard</a></li>
                <li class="active"><?php echo $fetchusername;?> Profile</li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
        <span style="color:#df6c6e;">
                    <?php
                    if($errorNo!=0){
                        foreach($error as $value){
                            echo '<div class="byMsg byMsgError">! '.$value.'</div>';
                        }
                    }

                    ?>
                </span>
            <span style="color:#31df0c;">
                    <?php
                    if(!empty($success)){
                        echo '<div class="byMsg byMsgSuccess">! '.$success.'</div>';
                    }
                    ?>
                </span>
    <!-- .row -->
    <div class="row">
        <div class="col-md-4 col-xs-12">
            <div class="white-box">
                <div class="user-bg">
                    <img width="100%" alt="user" src="../plugins/images/large/img1.jpg">
                    <div class="overlay-box">
                        <div class="user-content"> <a href="javascript:void(0)">
                                <img class="thumb-lg img-circle" src="../storage/user_image/<?php echo $fetchuserpic;?>" alt="<?php echo $fetchname;?>"></a>
                            <h4 class="text-white"><?php echo $fetchusername;?></h4>
                            <h5 class="text-white"><?php echo $fetchemail;?></h5>
                        </div>
                    </div>
                </div>

                <div class="user-btm-box">
                    <!-- .row -->
                    <div class="row text-center m-t-10">
                        <div class="col-md-6 b-r"><strong>Name</strong><p><?php echo $fetchname;?></p></div>
                        <div class="col-md-6"><strong>Gender</strong><p><?php echo $fetchsex;?></p></div>
                    </div>
                    <!-- /.row -->
                    <hr>
                    <!-- .row -->
                    <div class="row text-center m-t-10">
                        <div class="col-md-6 b-r"><strong>Email ID</strong><p style="word-wrap: break-word;"><?php echo $fetchemail;?></p></div>
                        <div class="col-md-6"><strong>Joined</strong><p><?php echo date('dS M g:iA', strtotime($fetchjoined)); ?></p></div>
                    </div>
                    <!-- /.row -->
                    <hr>
                    <!-- .row -->
                    <div class="row text-center m-t-10">
                        <div class="col-md-12"><strong>Country</strong><p><?php echo $fetchcountry;?></p></div>

                    </div>
                    <div class="col-md-1 col-sm-1 text-center">&nbsp;</div>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-xs-12">
            <div class="white-box">
                <!-- .tabs -->
                <ul class="nav nav-tabs tabs customtab">
                    <li class="active tab"><a href="#profile" data-toggle="tab"> <span class="visible-xs"><i class="fa fa-user"></i></span> <span class="hidden-xs">About <?php echo $fetchusername;?></span> </a> </li>
                    <!--<li class="tab"><a href="#settings" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i class="fa fa-cog"></i></span> <span class="hidden-xs">Edit Detail</span> </a> </li>-->
                </ul>
                <!-- /.tabs -->
                <div class="tab-content">
                    <!-- .tabs2 -->
                    <div class="tab-pane active" id="profile">
                        <div class="row">
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Full Name</strong> <br>
                                <p class="text-muted"><?php echo $fetchname;?></p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Joined</strong> <br>
                                <p class="text-muted"><?php echo date('dS M g:iA', strtotime($fetchjoined)); ?></p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Email</strong> <br>
                                <p class="text-muted" style="word-wrap: break-word;"><?php echo $fetchemail;?></p>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Location</strong> <br>
                                <p class="text-muted"><?php echo $fetchcountry;?></p>
                            </div>
                        </div>
                        <hr>
                        <p class="m-t-30"><?php echo $fetchabout;?></p>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->


<?php include("footer.php"); ?>