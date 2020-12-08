<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);

function db_connect($config)
{
    // Create connection in MYsqli
    $con = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);
    // Check connection in MYsqli
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

    return $con;
}

function check_allow()
{
    return TRUE;
}

function checkloggedadmin()
{
    if(isset($_SESSION['admin']['id']))
    {
        return TRUE;
    }
    else
    {
        echo '<script>window.location="login.php"</script>';
    }
}

function get_country_list($config,$con,$selected="")
{
    $countries = array();
    $count = 0;

    $query = "SELECT printable_name FROM ".$config['db']['pre']."countries ORDER BY printable_name";
    $query_result = mysqli_query($con,$query);
    while ($info = mysqli_fetch_array($query_result))
    {
        $countries[$count]['title'] = $info['printable_name'];
        if($selected != "")
        {
            if($selected==$info['printable_name'])
            {
                $countries[$count]['selected'] = "selected";
            }
            else{
                $countries[$count]['selected'] = "";
            }

        }
        $count++;
    }

    return $countries;
}

function validStrLen($str, $min, $max, $con, $config){
    $len = strlen($str);
    if($len < $min){
        return "Username is too short, minimum is $min characters ($max max)";
    }
    elseif($len > $max){
        return "Username is too long, maximum is $max characters ($min min).";
    }
    elseif(!preg_match("/^[a-zA-Z0-9]+$/", $str))
    {
        return "Only use numbers and letters please";
    }
    else{
        //get the username
        $username = mysqli_real_escape_string($con, $_POST['username']);

        //mysql query to select field username if it's equal to the username that we check '
        $result = mysqli_query($con, "select username from `".$config['db']['pre'].$GLOBALS['MySQLi_user_table_name']."` where `".$GLOBALS['MySQLi_username_field']."` = '".$username."'");

        //if number of rows fields is bigger them 0 that means it's NOT available '
        if(mysqli_num_rows($result)>0){
            //and we send 0 to the ajax request
            return "Error: Username not available";
        }
    }
    return TRUE;
}

function transfer($config,$url,$msg,$page_title='')
{
    if(!$config['transfer_filter'])
    {
        echo '<script>window.location="'.$url.'"</script>';
        exit;
    }
    ob_start();
    echo "<html>\n";
    echo "<head>\n";
    echo "<title>\n";
    echo $page_title;
    echo "</title>\n";
    echo "<STYLE>\n";
    echo "<!--\n";
    echo "TABLE, TR, TD                { font-family:Verdana, Tahoma, Arial;font-size: 7.5pt; color:#000000}\n";
    echo "a:link, a:visited, a:active  { text-decoration:underline; color:#000000 }\n";
    echo "a:hover                      { color:#465584 }\n";
    echo "#alt1   { font-size: 16px; }\n";
    echo "body {\n";
    echo "	background-color: #e8ebf1\n";
    echo "	z-index: 99999\n";
    echo "}\n";
    echo "-->\n";
    echo "</STYLE>\n";
    echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
    echo "function changeurl(){\n";
    echo "window.location='" . $url . "';\n";
    echo "}\n";
    echo "</script>\n";
    echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"></head>\n";
    echo "<body onload=\"window.setTimeout('changeurl();',2000);\">\n";
    echo "<table width='95%' height='85%'>\n";
    echo "<tr>\n";
    echo "<td valign='middle'>\n";
    echo "<table align='center' border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"#fff\">\n";
    echo "<tr>\n";
    echo "<td id='mainbg'>";
    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"12\">\n";
    echo "<tr>\n";
    echo "<td width=\"100%\" align=\"center\" id=alt1>\n";
    echo $msg . "<br><br>\n";
    echo "<div><img src=\"" . $config['site_url'] . "/images/loading.gif\"/></div><br><br>\n";
    echo "(<a href='" . $url . "'>Or click here if you do not wish to wait</a>)</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</body></html>\n";
    ob_end_flush();
}



?>