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
                <li class="active">Users DATA EXPORT</li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <!-- /row -->
    <div class="row">

        <!-- /.row -->
        <div class="col-sm-12">
            <div class="white-box">
                <h3 class="box-title m-b-0">Data Export</h3>
                <p class="text-muted m-b-30">Export data to Copy, CSV, Excel, PDF & Print</p>
                <div class="table-responsive">
                    <table id="example23" class="display nowrap" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Sex</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = "SELECT * FROM `".$config['db']['pre'].$MySQLi_user_table_name."` order by $MySQLi_userid_field ASC";
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
                                $status = '<span class="label label-danger">BANNED</span>';
                            }

                            ?>
                            <tr>
                                <td><?php echo $id ?></td>
                                <td><img src="../storage/user_image/<?php echo $picname; ?>" alt="<?php echo $username ?>" class="img-circle bg-theme" width="40"></td>
                                <td><?php echo $row[$MySQLi_fullname_field] ?></td>
                                <td><?php echo $username ?></td>
                                <td><?php echo $row[$MySQLi_email_field] ?></td>
                                <td><?php echo $row[$MySQLi_sex_field] ?></td>
                                <td><?php echo $row[$MySQLi_country_field] ?></td>
                                <td><?php echo $status ?></td>
                                <td><?php echo date('M dS', strtotime($row[$MySQLi_joined_field])); ?></td>
                            </tr>
                        <?php }?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->


<?php include("footer.php"); ?>