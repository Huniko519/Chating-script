<?php
include("header.php");

if(isset($_GET['page']))
{
    $pageno = $_GET['page'];
}
else
{
    $page = 1;
}

if(!isset($_GET['sortby']))
{
    $_GET['sortby']='admin_id';
}
if(!isset($_GET['direction']))
{
    $_GET['direction']='DESC';
}

?>

    <!-- Page Content -->
    <div id="page-wrapper">
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Users</h4>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                <ol class="breadcrumb">
                    <li><a href="index.php">Dashboard</a></li>
                    <li class="active">Users</li>
                </ol>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">
                    <form action="users_delete.php" method="post" name="f1" id="f1">
                        <div>
                            <div class="pull-left"><h3 class="box-title">Users Data</h3></div>
                            <div class="pull-right">
                                <p class="text-muted">
                                    <a href="users_add.php" class="btn btn-success waves-effect waves-light m-r-10">Add Users</a>
                                    <button type="submit" name="submit" class="btn btn-danger waves-effect waves-light m-r-10"><i class="fa fa-trash-o"></i> Delete Marked</button>
                                </p>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <hr>

                    <div class="table-responsive">

                        <table id="myTable" class="table table-striped">
                            <thead>
                            <tr>
                                <th class="sortingNone"><input type="checkbox" name="selall" value="checkbox" onClick="checkBox(this)" style="display: block"></th>
                                <th>#ID</th>
                                <th class="sortingNone">Image</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Sex</th>
                                <th>Country</th>
                                <th>Status</th>
                                <th class="sortingNone">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                           $query = "SELECT * FROM `".$config['db']['pre'].$MySQLi_user_table_name."` ORDER BY $MySQLi_userid_field DESC";
                            $result = $mysqli->query($query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id = $row[$MySQLi_userid_field];
                                $username = $row[$MySQLi_username_field];
                                $picname = $row[$MySQLi_photo_field];
                                $status = $row[$MySQLi_status_field];
                                if ($picname == "")
                                    $picname = "avatar_default.png";
                                else {
                                    $picname = "small" . $picname;
                                }

                                if ($status == "0"){
                                    $status = '<span class="label label-info">ACTIVE</span>';
                                }
                                elseif($status == "1")
                                {
                                    $status = '<span class="label label-success">CONFIRM</span>';
                                }
                                else{
                                    $status = '<span class="label label-warning">BANNED</span>';
                                }

                                ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="titles[]" id="titles[]" value="<?php echo $username;?>">
                                        <input type="checkbox" name="list[]" id="list[]" value="<?php echo $id;?>" style="display: block">
                                    </td>
                                    <td><?php echo $id ?></td>
                                    <td><img src="../storage/user_image/<?php echo $picname; ?>" alt="<?php echo $username ?>" class="img-circle bg-theme" width="40"></td>


                                    <td><?php echo $row[$MySQLi_fullname_field] ?></td>
                                    <td><?php echo $username ?></td>
                                    <td><?php echo $row[$MySQLi_email_field] ?></td>
                                    <td><?php echo $row[$MySQLi_sex_field] ?></td>
                                    <td><?php echo $row[$MySQLi_country_field] ?></td>
                                    <td><?php echo $status ?></td>
                                    <td class="text-nowrap">
                                        <a href="user_profile.php?id=<?php echo $id; ?>" data-toggle="tooltip" data-original-title="View <?php echo $username ?> Profile"> <i class="fa fa-eye text-success m-r-5"></i></a>
                                        <a href="users_edit.php?id=<?php echo $id;?>" data-toggle="tooltip" data-original-title="Edit <?php echo $username ?>"> <i class="fa fa-pencil text-success m-r-5"></i> </a>
                                        <a href="users_delete.php?id=<?php echo $id;?>" data-toggle="tooltip" data-original-title="Delete <?php echo $username ?>"> <i class="fa fa-close text-danger m-r-5"></i> </a>
                                        <?php if($status != 2) { ?>
                                            <a href="users_ban.php?id=<?php echo $id; ?>" data-toggle="tooltip"
                                               data-original-title="Ban <?php echo $username ?>"> <i
                                                    class="fa fa-user-times text-warning"></i> </a>
                                        <?php
                                        }
                                        if($status == 2) { ?>
                                            <a href="users_active.php?id=<?php echo $id; ?>" data-toggle="tooltip"
                                               data-original-title="Activate <?php echo $username ?>"> <i
                                                    class="fa fa-user-times text-info"></i> </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php }?>

                            </tbody>
                        </table>

                    </div>
                    </form>
                </div>
            </div>

        </div>
        <!-- /.row -->


<?php include("footer.php"); ?>