<?php
include("header.php");

if(!isset($_GET['id']))
{
    echo '<script>window.location="404.php"</script>';
}
$error = array();
$errorNo = 0;
function check_account_exists($config,$con,$id)
{
    $row = mysqli_num_rows(mysqli_query($con, "select 1 from `".$config['db']['pre'].$GLOBALS['MySQLi_user_table_name']."` where `".$GLOBALS['MySQLi_userid_field']."` = '".$id."'"));
    if($row>0){
        return TRUE;
    }
    return FALSE;
}
$check = check_account_exists($config,$mysqli,$_GET['id']);
if($check != 1)
{
    echo '<script>window.location="404.php"</script>';
}

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
        else{
            $result = mysqli_fetch_array(mysqli_query($mysqli, "select $MySQLi_username_field from `".$config['db']['pre'].$MySQLi_user_table_name."` where id = '".$_GET['id']."'"));
            if($result['username'] != $_POST['username']){
                $chkavailUname = validStrLen($_POST['username'], 4, 10, $mysqli, $config);
                if($chkavailUname != 1){
                    $error[] = $chkavailUname;
                    $errorNo = 2;
                }
            }
        }
        if($_POST["email"] == ""){
            $error[] = "Email can't be blank.";
            $errorNo = 3;
        }
        if($errorNo==0) {
            if ($_FILES['file']['name'] != "") {
                $uploaddir = '../storage/user_image/';
                $original_filename = $_FILES['file']['name'];
                $random1 = rand(9999,100000);
                $random2 = rand(9999,200000);
                $random3 = $random1.$random2;
                $extensions = explode(".", $original_filename);
                $extension = $extensions[count($extensions) - 1];
                $uniqueName = $random3 . "." . $extension;                     //Change Variable string Have error
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

                    //$success = "Profile Updated Successfully";
                    transfer($config,'user_profile.php?id='.$_GET['id'],'Profile Updated Successfully');
                    exit;
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
                transfer($config,'user_profile.php?id='.$_GET['id'],'Profile Updated Successfully');
                exit;
            }
        }
    }
}


$user = "SELECT * FROM `".$config['db']['pre'].$MySQLi_user_table_name."` where $MySQLi_userid_field = '".$_GET['id']."'";
$userresult = $mysqli->query($user);
$fetchuser = mysqli_fetch_assoc($userresult);
$fetchusername  = $fetchuser[$MySQLi_username_field];
$fetchuserpic     = $fetchuser[$MySQLi_photo_field];

if($fetchuserpic == "")
    $fetchuserpic = "avatar_default.png";

$fetchname  = $fetchuser[$MySQLi_fullname_field];
$fetchemail  = $fetchuser[$MySQLi_email_field];
$fetchsex  = $fetchuser[$MySQLi_sex_field];
$fetchdob  = $fetchuser[$MySQLi_dob_field];
$fetchabout  = $fetchuser[$MySQLi_about_field];
$fetchcountry  = $fetchuser[$MySQLi_country_field];
$fetchjoined  = $fetchuser[$MySQLi_joined_field];
?>

    <!-- Page Content -->
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row bg-title">
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                    <h4 class="page-title">Edit <?php echo $fetchusername;?> Profile</h4>
                </div>
                <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                    <ol class="breadcrumb">
                        <li><a href="index.php">Dashboard</a></li>
                        <li class="active">Edit <?php echo $fetchusername;?> Profile</li>
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
            <!-- /row -->
            <div class="row">
                <div class="col-md-12">

                    <div class="panel panel-info">

                        <div class="panel-wrapper collapse in" aria-expanded="true">
                            <div class="panel-body">
                                <form name="form1" method="post" action="#" id="send" enctype="multipart/form-data">
                                    <div class="form-body">
                                        <h3 class="box-title">Person Info</h3>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="col-md-2">
                                                        <img src="../storage/user_image/<?php echo $fetchuserpic;?>" alt="<?php echo $fetchname;?>" style="width: 80px; border-radius: 50%">
                                                    </div>
                                                    <div class="col-md-10">
                                                        <label class="control-label">Profile Picture</label>
                                                        <div class="col-sm-12">
                                                            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                                                <div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                    <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file</span> <span class="fileinput-exists">Change</span>
                    <input type="file" name="file">
                    </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a> </div>
                                                        </div>
                                                        <span class="help-block"> Change Your Photo</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">About Us</label>
                                                    <textarea name="about" class="form-control" ><?php echo $fetchabout;?></textarea>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputfullname">Full Name</label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><i class="ti-user"></i></div>
                                                        <input type="text" class="form-control" id="exampleInputfullname" placeholder="Full Name" name="name" value="<?php echo $fetchname;?>">
                                                        <span class="help-block"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Gender</label>
                                                    <select class="form-control" name="sex">
                                                        <option value="male" <?php if($fetchsex == "male") { echo "selected"; }?>>Male</option>
                                                        <option value="female" <?php if($fetchsex == "female") { echo "selected"; }?>>Female</option>
                                                    </select>
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <!--/span-->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputDOB">Date of Birth</label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><i data-icon="b" class="linea-icon linea-basic"></i></div>
                                                        <input type="text" class="form-control" id="exampleInputDOB" name="dob" placeholder="Format : 02-April-1992" value="<?php echo $fetchdob;?>">
                                                        <span class="help-block"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Country</label>
                                                    <select class="form-control" name="country">
                                                        <?php $country = get_country_list($config,$mysqli,$fetchcountry);

                                                        /*foreach($country as $value){
                                                            foreach($value as $value2){
                                                                echo '<option value="'.$value2.'">'.$value2.'</option>';
                                                            }
                                                        }*/
                                                        foreach ($country as $value){
                                                            echo '<option value="'.$value['title'].'" '.$value['selected'].'>'.$value['title'].'</option>';
                                                        }

                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    <h3 class="box-title m-t-40">Username And Email</h3>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputuname">User Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="ti-user"></i></div>
                                                    <input type="text" class="form-control" id="exampleInputuname" placeholder="Username" name="username" value="<?php echo $fetchusername;?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Email address</label>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="ti-email"></i></div>
                                                    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email" name="email" value="<?php echo $fetchemail;?>">
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" name="Submit" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
                                        <a href="users.php" class="btn btn-default">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


<?php include("footer.php"); ?>