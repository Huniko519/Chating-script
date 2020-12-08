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
        $sql = "UPDATE `".$config['db']['pre'].$MySQLi_user_table_name."` set $MySQLi_status_field='1' ";

        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE $MySQLi_userid_field = '" . $value . "'";
            }
            else
            {
                $sql.= " OR $MySQLi_userid_field = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);

        mysqli_query($mysqli,$sql);

        transfer($config,'users.php','User Activated');
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
                    <h4 class="page-title">User Activate</h4>
                </div>
                <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                    <ol class="breadcrumb">
                        <li><a href="index.php">Dashboard</a></li>
                        <li class="active">Activate User</li>
                    </ol>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /row -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="white-box">
                        <form action="" method="post" name="f1" id="f1">
                            <h3 class="box-title">Activate User</h3>
                            <hr>
                            <div class="panel-body" style="width:70%; margin-left:15%">
                                <div class="hr-line-dashed"></div>
                                <ul>
                                    <?php
                                    $count = 0;
                                    $sql = "SELECT $MySQLi_userid_field,$MySQLi_username_field FROM ".$config['db']['pre'].$MySQLi_user_table_name." ";

                                    foreach ($_POST['list'] as $value)
                                    {
                                        if($count == 0)
                                        {
                                            $sql.= "WHERE $MySQLi_userid_field='" . $value . "'";
                                        }
                                        else
                                        {
                                            $sql.= " OR $MySQLi_userid_field='" . $value . "'";
                                        }

                                        $count++;
                                    }
                                    $sql.= " LIMIT " . count($_POST['list']);

                                    $query_result = mysqli_query($mysqli,$sql);
                                    while ($info = @mysqli_fetch_array($query_result))
                                    {

                                        echo "<li><h3 style=\"color:#FF0000\">" . $info[$MySQLi_username_field] . "<h3></li>";
                                        echo "<input type=\"hidden\" name=\"list[]\" id=\"list[]\" value=\"" . $info[$MySQLi_userid_field] . "\">";

                                    }
                                    ?>
                                </ul>
                                <div class="hr-line-dashed"></div>
                                <div class="form-group" align="center">
                                    <div class="col-sm-8 col-sm-offset-2">
                                        <a href="users.php" class="btn btn-default">Cancel</a>
                                        <input name="Submit" type="submit" class="btn btn-warning" value="Activate">
                                    </div>
                                </div>
                                <?php

                                ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

<?php include('footer.php'); ?>