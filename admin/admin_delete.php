<?php
include('header.php');
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
        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."admins` ";

        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `admin_id` = '" . $value . "'";
            }
            else
            {
                $sql.= " OR `admin_id` = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);

        mysqli_query($mysqli,$sql);

        transfer($config,'admin_view.php','Admin Deleted');
        exit;
    }
}

if(isset($_GET['id']))
{
	$_POST['list'][] = $_GET['id'];
}
?>
    <!-- Page Content -->
    <div id="page-wrapper">
    <div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Admin Users Delete</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="index.php">Dashboard</a></li>
                <li class="active">Delete Admin User</li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">
                <form action="" method="post" name="f1" id="f1">
                    <h3 class="box-title">Delete Admin User</h3>
                    <hr>
                    <div class="panel-body" style="width:70%; margin-left:15%">
                        <div class="alert alert-danger">
                            <?php
                            if(is_int(array_search($_SESSION['admin']['id'], $_POST['list'])))
                            {
                                echo 'Sorry, you cannot delete the main admin. <br><Br><a href="admin_view.php">Click here</a> to go back';
                            }
                            else
                            {
                            ?>
                            <i class="fa fa-bolt"></i> Are you sure you want to delete the following admins?
                        </div>
                        <div class="hr-line-dashed"></div>
                        <ul>
                            <?php
                            $count = 0;
                            $sql = "SELECT admin_id,username FROM ".$config['db']['pre']."admins ";

                            foreach ($_POST['list'] as $value)
                            {
                                if($count == 0)
                                {
                                    $sql.= "WHERE admin_id='" . $value . "'";
                                }
                                else
                                {
                                    $sql.= " OR admin_id='" . $value . "'";
                                }

                                $count++;
                            }
                            $sql.= " LIMIT " . count($_POST['list']);

                            $query_result = mysqli_query($mysqli,$sql);
                            while ($info = @mysqli_fetch_array($query_result))
                            {
                                if($_SESSION['admin']['id'] != $info['admin_id'])
                                {
                                    echo "<li><h3 style=\"color:#FF0000\">" . $info['username'] . "<h3></li>";
                                    echo "<input type=\"hidden\" name=\"list[]\" id=\"list[]\" value=\"" . $info['admin_id'] . "\">";
                                }
                            }
                            ?>
                        </ul>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group" align="center">
                            <div class="col-sm-8 col-sm-offset-2">
                                <a href="admin_view.php" class="btn btn-default">Cancel</a>
                                <input name="Submit" type="submit" class="btn btn-danger" value="Yes I'm Sure">

                            </div>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </form>
            </div>
        </div>
    </div>



<?php include('footer.php'); ?>