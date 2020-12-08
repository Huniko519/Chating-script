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
            <h4 class="page-title">Admins</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="index.php">Dashboard</a></li>
                <li class="active">Admins</li>
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
                        <div class="pull-left"><h3 class="box-title">All Admins List</h3></div>
                        <div class="pull-right">
                            <p class="text-muted">
                                <a href="admin_add.php" class="btn btn-success waves-effect waves-light m-r-10">Add Admin</a>
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
                                <th class="sortingNone">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $query = "SELECT * FROM `".$config['db']['pre']."admins` ORDER BY id DESC";
                            $result = $mysqli->query($query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id = $row['id'];
                                $username = $row['username'];
                                $picname = $row['picname'];
                                $status = $row['status'];
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
                                    <td>
                                        <input type="hidden" name="titles[]" id="titles[]" value="<?php echo $username;?>">
                                        <input type="checkbox" name="list[]" id="list[]" value="<?php echo $id;?>" style="display: block">
                                    </td>
                                    <td><?php echo $row['id'] ?></td>
                                    <td><img src="../storage/user_image/<?php echo $picname; ?>" alt="<?php echo $username ?>" class="img-circle bg-theme" width="40"></td>


                                    <td><?php echo $row['name'] ?></td>
                                    <td><?php echo $username ?></td>
                                    <td><?php echo $row['email'] ?></td>
                                    <td><?php echo $row['sex'] ?></td>
                                    <td><?php echo $row['country'] ?></td>
                                    <td class="text-nowrap">
                                        <a href="admin_edit.php?id=<?php echo $id;?>" data-toggle="tooltip" data-original-title="Edit <?php echo $username ?>"> <i class="fa fa-pencil text-inverse m-r-10"></i> </a>
                                        <a href="admin_delete.php?id=<?php echo $id;?>" data-toggle="tooltip" data-original-title="Delete <?php echo $username ?>"> <i class="fa fa-close text-danger"></i> </a>
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