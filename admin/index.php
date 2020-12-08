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

$total_user = mysqli_num_rows(mysqli_query($mysqli,"select 1 from `".$config['db']['pre'].$MySQLi_user_table_name."`"));
$month_user = mysqli_num_rows(mysqli_query($mysqli,"select 1 from `".$config['db']['pre'].$MySQLi_user_table_name."` where $MySQLi_joined_field > DATE_SUB(NOW(), INTERVAL 1 MONTH)"));
$day_user = mysqli_num_rows(mysqli_query($mysqli,"select 1 from `".$config['db']['pre'].$MySQLi_user_table_name."` where $MySQLi_joined_field > DATE_SUB(NOW(), INTERVAL 1 DAY)"));
$banned_user = mysqli_num_rows(mysqli_query($mysqli,"select 1 from `".$config['db']['pre'].$MySQLi_user_table_name."` where $MySQLi_status_field = '2'"));

?>
    <!-- Page Content -->
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row bg-title">
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                    <h4 class="page-title">Dashboard</h4>
                </div>
                <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                    <ol class="breadcrumb">
                        <li><a href="index.php">Dashboard</a></li>
                        <li class="active">Dashboard</li>
                    </ol>
                </div>
                <!-- /.col-lg-12 -->
            </div>

            <!-- .row -->
            <div class="row">
                <div class="col-lg-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-lg-3 col-sm-3 col-xs-12">
                            <div class="white-box">
                                <h3 class="box-title">NEW USERS THIS MONTH</h3>
                                <ul class="list-inline two-part">
                                    <li><i class="icon-people text-info"></i></li>
                                    <li class="text-right"><span class="counter"><?php echo $month_user; ?></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-3 col-xs-12">
                            <div class="white-box">
                                <h3 class="box-title">NEW USERS TODAY</h3>
                                <ul class="list-inline two-part">
                                    <li><i class="fa fa-user-plus text-purple"></i></li>
                                    <li class="text-right"><span class="counter"><?php echo $day_user; ?></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-3 col-xs-12">
                            <div class="white-box">
                                <h3 class="box-title">BANNED USERS</h3>
                                <ul class="list-inline two-part">
                                    <li><i class="fa fa-user-times text-danger"></i></li>
                                    <li class="text-right"><span class=""><?php echo $banned_user; ?></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-3 col-xs-12">
                            <div class="white-box">
                                <h3 class="box-title">TOTAL USERS</h3>
                                <ul class="list-inline two-part">
                                    <li><i class="fa fa-users text-success"></i></li>
                                    <li class="text-right"><span class=""><?php echo $total_user; ?></span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row">
                <div class="col-md-6 col-lg-6 col-sm-12">
                    <div class="white-box">
                        <h3 class="box-title">Recent 5 Message</h3>
                        <div class="comment-center">
                            <?php
                            $sql = "select * from `".$config['db']['pre']."messages` where message_type = 'text' order by message_id DESC LIMIT 5";
                            $query_result = mysqli_query ($mysqli, $sql);
                            while ($chat = mysqli_fetch_array($query_result)) {
                                $content = $chat['message_content'];
                                $time = $chat['message_date'];
                                $time = date('M dS g:iA', strtotime($time));
                                $picname = "";
                                $picname2 = "";

                                $user1 = "SELECT * FROM `".$config['db']['pre']."$MySQLi_user_table_name` WHERE $MySQLi_username_field='" .mysqli_real_escape_string($mysqli,$chat['from_uname']). "' LIMIT 1";
                                $user_result1 = mysqli_query ($mysqli, $user1);
                                while ($info1 = mysqli_fetch_array($user_result1))
                                {
                                    $sender = $info1[$MySQLi_username_field];
                                    $picname = "small".$info1[$MySQLi_photo_field];
                                }


                            ?>
                                <div class="comment-body" style="width: 100%;">
                                    <div class="user-img"> <img src="../storage/user_image/<?php echo $picname; ?>" alt="<?php echo $sender ?>" class="img-circle"></div>
                                    <div class="mail-contnet">
                                        <h5><?php echo $sender; ?> to <?php echo $chat['to_uname']; ?></h5>
                                        <span class="mail-desc"><?php echo $content; ?></span>
                                        <a href="messages.php?from=<?php echo $sender; ?>&to=<?php echo $chat['to_uname']; ?>"><span class="label label-rounded label-info">View Conversation</span></a>
                                        <span class="time pull-right"><?php echo $time; ?></span>
                                    </div>
                                </div>
                            <?php } ?>


                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-sm-12">
                    <div class="white-box">
                        <h3 class="box-title">Recent Registered</h3>
                        <div class="row sales-report">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <h2>Today</h2>
                                <p>CREATE ACCOUNT REPORT</p>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 ">
                                <h1 class="text-right text-success m-t-20"><?php echo $day_user; ?></h1>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table ">
                                <thead>
                                <tr>
                                    <th>NAME</th>
                                    <th>EMAIL</th>
                                    <th>DATE</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query = "SELECT * FROM `".$config['db']['pre'].$MySQLi_user_table_name."` order by $MySQLi_userid_field DESC LIMIT 10";
                                $result = $mysqli->query($query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                    <tr>
                                        <td class="txt-oflo"><?php echo $row[$MySQLi_fullname_field]; ?></td>
                                        <td><span class="label label-megna label-rounded"><?php echo $row[$MySQLi_email_field]; ?></span> </td>
                                        <td class="txt-oflo"><?php echo date('M dS g:iA', strtotime($row[$MySQLi_joined_field])); ?></td>
                                    </tr>
                                <?php } ?>


                                </tbody>
                            </table>
                            <a href="users.php">Check all Users</a> </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->


<?php include("footer.php"); ?>