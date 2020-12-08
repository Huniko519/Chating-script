<?php
include("header.php");

$error = "";

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
        if($_FILES['file']['name'] != "")
        {
            $uploaddir = '../storage/user_image/';
            $original_filename = $_FILES['file']['name'];
            $random1 = rand(9999,100000);
            $random2 = rand(9999,200000);
            $random3 = $random1.$random2;
            $image_name=$title.'_'.$random1.$random2.'.'.$imageFileType;
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
                $query = "insert into `".$config['db']['pre'].$MySQLi_user_table_name."` set
            $MySQLi_username_field='" . $_POST['username'] . "',
            $MySQLi_password_field='" . md5($_POST['password']) . "',
            $MySQLi_fullname_field='" . $_POST['name'] . "',
            $MySQLi_email_field='" . $_POST['email'] . "',
            $MySQLi_about_field='" . $_POST['about'] . "',
            $MySQLi_sex_field='" . $_POST['sex'] . "',
            $MySQLi_dob_field='" . $_POST['dob'] . "',
            $MySQLi_country_field='" . $_POST['country'] . "',
            $MySQLi_joined_field = NOW(),
            $MySQLi_photo_field='$uniqueName' ";
                $query_result = $mysqli->query($query);
                $last_id = $mysqli->insert_id;
                $success = "User Profile Created Successfully";
                transfer($config,'user_profile.php?id='.$last_id,$success);
                exit;
            }
        }
        else{
            $error = "Profile Picture Required";
        }
    }
}

?>

    <!-- Page Content -->
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row bg-title">
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                    <h4 class="page-title">Add User</h4>
                </div>
                <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                    <ol class="breadcrumb">
                        <li><a href="index.php">Dashboard</a></li>
                        <li class="active">Add User</li>
                    </ol>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <span style="color:#df6c6e;">
                    <?php
                    if(!empty($error)){
                        echo '<div class="byMsg byMsgError">! '.$error.'</div>';
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
                                                    <label class="control-label">Profile Picture<code>*Required</code></label>
                                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                                        <div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
            <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file</span> <span class="fileinput-exists">Change</span>
            <input type="file" name="file" required="">
            </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a> </div>
                                                    <span class="help-block">Valid <code>jpg</code> image only</span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">About Us (Optional)</label>
                                                    <textarea name="about" class="form-control" placeholder="Write something about yourself."></textarea>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputfullname">Full Name<code>*Required</code></label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><i class="ti-user"></i></div>
                                                        <input type="text" class="form-control" id="exampleInputfullname" placeholder="Full Name" name="name" required="">
                                                        <span class="help-block"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Gender<code>*Required</code></label>
                                                    <select class="form-control" name="sex" required="">
                                                        <option value="male">Male</option>
                                                        <option value="female">Female</option>
                                                    </select>
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <!--/span-->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputDOB">Date of Birth (Optional)</label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><i data-icon="b" class="linea-icon linea-basic"></i></div>
                                                        <input type="text" class="form-control" id="exampleInputDOB" name="dob" placeholder="Format : 02-April-1992">
                                                        <span class="help-block"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Country<code>*Required</code></label>
                                                    <select class="form-control" name="country" required="">
                                                        <?php $country = get_country_list($config,$mysqli);

                                                        foreach ($country as $value){
                                                            echo '<option value="'.$value['title'].'">'.$value['title'].'</option>';
                                                        }

                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>


                                        <h3 class="box-title m-t-40">User Login Details</h3>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputuname">User Name<code>*Required</code></label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><i class="ti-user"></i></div>
                                                        <input type="text" class="form-control" id="exampleInputuname" placeholder="Username" name="username" required="">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Email address<code>*Required</code></label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><i class="ti-email"></i></div>
                                                        <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email" name="email" required="">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputpwd1">Password<code>*Required</code></label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><i class="ti-lock"></i></div>
                                                        <input type="password" class="form-control" id="exampleInputpwd1" placeholder="Enter Password" name="password" required="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" name="Submit" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
                                        <a href="index.php" class="btn btn-default">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


<?php include("footer.php"); ?>