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
            <h4 class="page-title">Messages</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="index.php">Dashboard</a></li>
                <li class="active">Messages</li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">
                <form action="message_delete.php" method="post" name="f1" id="f1">
                    <div>
                        <div class="pull-left"><h3 class="box-title">All Messages List</h3></div>
                        <div class="pull-right">
                            <p class="text-muted">
                                <button type="submit" name="submit" class="btn btn-danger waves-effect waves-light m-r-10"><i class="fa fa-trash-o"></i> Delete Marked</button>
                            </p>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <hr>

                    <div class="table-responsive">

                        <table id="message" class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="sortingNone"><input type="checkbox" name="selall" value="checkbox" onClick="checkBox(this)" style="display: block"></th>
                                    <th class="sortingNone">#ID</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Message</th>
                                    <th>Time</th>
                                    <th>Received</th>
                                    <th class="sortingNone">Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th class="sortingNone">None</th>
                                    <th class="sortingNone">#ID</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Message</th>
                                    <th>date</th>
                                    <th class="sortingNone">Received</th>
                                    <th class="sortingNone">Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                            <?php
                            if(isset($_GET['from']) && isset($_GET['to']) ){
                                $query = "SELECT * FROM `".$config['db']['pre']."messages` where ((to_uname = '".mysqli_real_escape_string($mysqli, $_GET['from'])."' AND from_uname = '".mysqli_real_escape_string($mysqli,$_GET['to'])."' ) OR (to_uname = '".mysqli_real_escape_string($mysqli,$_GET['to'])."' AND from_uname = '".mysqli_real_escape_string($mysqli,$_GET['from'])."' )) ORDER BY message_id DESC";
                            }
                            else{
                                $query = "SELECT * FROM `".$config['db']['pre']."messages` where message_type = 'text' ORDER BY message_id DESC";
                            }

                            $result = $mysqli->query($query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id = $row['message_id'];
                                $fromuname = $row['from_uname'];
                                $touname = $row['to_uname'];

                                $msgdate = $row['message_date'];
                                $msgcontent = $row['message_content'];
                                $recd = $row['recd'];
                                $msgtype = $row['message_type'];

                                $picname = "";
                                $picname2 = "";

                                $query1 = "SELECT $MySQLi_photo_field FROM `".$config['db']['pre'].$MySQLi_user_table_name."` WHERE $MySQLi_username_field='" .mysqli_real_escape_string($mysqli,$row['from_uname']). "' LIMIT 1";
                                $query_result = mysqli_query ($mysqli, $query1);
                                while ($info = mysqli_fetch_array($query_result))
                                {
                                    $picname = "small".$info[$MySQLi_photo_field];
                                }

                                $query4 = "SELECT $MySQLi_photo_field FROM `".$config['db']['pre'].$MySQLi_user_table_name."` WHERE $MySQLi_username_field='" .mysqli_real_escape_string($mysqli,$row['to_uname']). "' LIMIT 1";
                                $query_result4 = mysqli_query ($mysqli, $query4);
                                while ($info4 = mysqli_fetch_array($query_result4))
                                {
                                    $picname2 = "small".$info4[$MySQLi_photo_field];
                                }

                                if($picname == "small")
                                    $picname = "avatar_default.png";

                                if($picname2 == "small")
                                    $picname2 = "avatar_default.png";

                                if ($recd == "0"){
                                    $recd = '<span class="label label-info">Unread</span>';
                                }
                                elseif($recd == "1")
                                {
                                    $recd = '<span class="label label-success">Read</span>';
                                }

                                ?>
                                <tr>

                                    <td>
                                        <input type="hidden" name="titles[]" id="titles[]" value="<?php echo $id;?>">
                                        <input type="checkbox" name="list[]" id="list[]" value="<?php echo $id;?>" style="display: block">
                                    </td>
                                    <td><?php echo $id ?></td>
                                    <!--<td><img src="../storage/user_image/<?php /*echo $picname; */?>" alt="<?php /*echo $username */?>" class="img-circle bg-theme" width="40"></td>-->


                                    <td><img src="../storage/user_image/<?php echo $picname; ?>" alt="<?php echo $row['from_uname'] ?>" class="img-circle bg-theme" width="30"> <?php echo $row['from_uname'] ?></td>
                                    <td><img src="../storage/user_image/<?php echo $picname2; ?>" alt="<?php echo $row['to_uname'] ?>" class="img-circle bg-theme" width="30"> <?php echo $row['to_uname'] ?></td>
                                    <td width="20%" style="max-width: 100px;word-break: break-all;"><?php echo $msgcontent ?></td>
                                    <td><?php echo date('M dS g:iA', strtotime($msgdate)); ?></td>
                                    <td><?php echo $recd ?></td>
                                    <td class="text-nowrap">
                                        <a href="messages.php?from=<?php echo $row['from_uname'] ?>&to=<?php echo $row['to_uname'] ?>" data-toggle="tooltip" data-original-title="Filter <?php echo $row['from_uname'] ?> and <?php echo $row['to_uname'] ?> Conversation"> <i class="ti-eye m-r-10 font-bold"></i></a>
                                        <a href="message_delete.php?id=<?php echo $id;?>" data-toggle="tooltip" data-original-title="Delete Message"> <i class="ti-close text-danger"></i> </a>
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