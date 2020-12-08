<?php
function checkinstall($config)
{
    if(!isset($config['installed']))
    {
        header("Location: ".$config['site_url']."install/");
        exit;
    }
}
function checkpurchase($config)
{
    if(!isset($config['purchase_key']))
    {
        header("Location: ".$config['site_url']."install/");
        exit;
    }
    else{
        $purchase_data = verify_envato_purchase_code($config['purchase_key']);

        if( isset($purchase_data['verify-purchase']['item_id']) )
        {
            if($purchase_data['verify-purchase']['item_id'] == '18047319'){
                return true;
            }
        }
        else
        {
            $url = $config['site_url'];
            echo 'Invalid Purchase code Or Check Internet connection.';
            //echo '<script type="text/javascript"> window.location = "'.$url.'install/" </script>';
            exit;
        }
    }
}

function install_chat_setting($code){
    global $config;
    // Set API Key
    $buyer_email = '';
    $installing_version = '1.6';
    $site_url = $config['site_url'];

    $url = "https://bylancer.com/api/api.php?verify-purchase=" . $code . "&version=" . $installing_version . "&site_url=" . $site_url . "&email=" . $buyer_email;
    // Open cURL channel
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //Set the user agent
    $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    // Decode returned JSON
    $output = json_decode(curl_exec($ch), true);
    // Close Channel
    curl_close($ch);

    return $output;
}

function check_user_lang($config)
{
    if(isset($config['userlangsel']))
    {
        if($config['userlangsel'])
        {
            if(isset($GLOBALS['sesId']))
            {
                if(isset($_SESSION['lang']))
                {
                    if($_SESSION['lang'] != '')
                    {
                        $config['lang'] = $_SESSION['lang'];
                    }
                }
            }
        }
    }

    return $config['lang'];
}

function getLocationInfoByIp(){
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = @$_SERVER['REMOTE_ADDR'];
    $result  = array('country'=>'', 'city'=>'');
    if(filter_var($client, FILTER_VALIDATE_IP)){
        $ip = $client;
    }elseif(filter_var($forward, FILTER_VALIDATE_IP)){
        $ip = $forward;
    }else{
        $ip = $remote;
    }
    $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));
    if($ip_data && $ip_data->geoplugin_countryName != null){
        $result['code'] = $ip_data->geoplugin_countryCode;
        $result['country'] = $ip_data->geoplugin_countryName;
        $result['city'] = $ip_data->geoplugin_city;
    }
    return $result;
}

function validStrLen($str, $min, $max, $con, $config, $lang){
    $len = strlen($str);
    if($len < $min){
        return $lang['USERSHORTMIN'].' '.$min.' '.$lang['CHARACTER'].' '."($max max)";
    }
    elseif($len > $max){
        return $lang['USERSHORTMIN'].' '.$max.' '.$lang['CHARACTER'].' '."($min min).";
    }
    elseif(!preg_match("/^[a-zA-Z0-9]+$/", $str))
    {
        return $lang['USERALPHA'];
    }
    else{
        //get the username
        $username = mysqli_real_escape_string($con, $_POST['username']);
        $TFuname = $GLOBALS['MySQLi_username_field'];
        //mysql query to select field username if it's equal to the username that we check '
        $result = mysqli_query($con, "select $TFuname from `".$config['db']['pre'].$GLOBALS['MySQLi_user_table_name']."` where $TFuname = '".$username."'");

        //if number of rows fields is bigger them 0 that means it's NOT available '
        if(mysqli_num_rows($result)>0){
            //and we send 0 to the ajax request
            return $lang['USERUNAV'];
        }
    }
    return TRUE;
}

function createusernameslug($config,$con,$title)
{
    $slug = $title;

    $query = "SELECT COUNT(*) AS NumHits FROM `".$config['db']['pre'].$GLOBALS['MySQLi_user_table_name']."` WHERE `".$GLOBALS['MySQLi_username_field']."`  LIKE '$slug%'";
    $result = mysqli_query($con,$query);
    $row = mysqli_fetch_assoc($result);
    $numHits = $row['NumHits'];

    return ($numHits > 0) ? ($slug.$numHits) : $slug;
}

function userloginfb($config,$con,$name,$email,$fbfirstname,$picname,$gender)
{
    $userinfo = array();

    $email = stripslashes($email);

    $query1 = "SELECT * FROM `".$config['db']['pre'].$GLOBALS['MySQLi_user_table_name']."` WHERE `".$GLOBALS['MySQLi_email_field']."` ='" . $email . "' LIMIT 1";
    $query_result1 = mysqli_query($con,$query1);
    $num_rows = mysqli_num_rows($query_result1);
    if($num_rows>0) {
        $info = mysqli_fetch_array($query_result1);

            $userinfo['id'] = $info[$GLOBALS['MySQLi_userid_field']];
            $userinfo['username'] = $info[$GLOBALS['MySQLi_username_field']];
            $userinfo['name'] = $info[$GLOBALS['MySQLi_fullname_field']];
            $userinfo['email'] = $info[$GLOBALS['MySQLi_email_field']];
            $userinfo['status'] = $info[$GLOBALS['MySQLi_status_field']];
    }
    else
    {
        $countryIP = getLocationInfoByIp();
        $countrycode = $countryIP['code'];
        $countryname = $countryIP['country'];
        $password=randomPassword();
        $image = $picname.'.jpg';
        $username = $GLOBALS['MySQLi_username_field'];
        $timenow = $GLOBALS['timenow'];
        //mysql query to select field username if it's equal to the username that we check '
        $result = mysqli_query($con, "select $username from `".$config['db']['pre'].$GLOBALS['MySQLi_user_table_name']."` where $username = '".$fbfirstname."'");

        //if number of rows fields is bigger them 0 that means it's NOT available '
        if(mysqli_num_rows($result)>0){
            $username = createusernameslug($config,$con,$fbfirstname);
        }
        else{
            $username = $fbfirstname;
        }
        $query = "insert into `".$config['db']['pre'].$GLOBALS['MySQLi_user_table_name']."` set `".$GLOBALS['MySQLi_status_field']."` = '1', `".$GLOBALS['MySQLi_fullname_field']."` ='$name', `".$GLOBALS['MySQLi_email_field']."` ='$email', `".$GLOBALS['MySQLi_username_field']."` ='$username', `".$GLOBALS['MySQLi_password_field']."` ='$password', `".$GLOBALS['MySQLi_sex_field']."` ='$gender', `".$GLOBALS['MySQLi_photo_field']."` ='$image', `".$GLOBALS['MySQLi_joined_field']."` = '$timenow', `".$GLOBALS['MySQLi_country_field']."` ='$countryname' ";
        $query_result = $con->query($query);

        $user_id = $con->insert_id;
        if (isset($user_id)) {

            $from = "Wchat";
            $to = $username;
            $to_id = $user_id;
            $from_id = 1;
            $message = "Weclome to wchat you can test better if you login with 2 diffrent browser and with other userid. Also Seacrh user and start chat with people.";

            /*$sql = "insert into `".$config['db']['pre']."messages` (from_uname,to_uname,from_id,to_id,message_content,message_type,message_date) values ('$from', '$to','$from_id','$to_id','".addslashes($message)."','text',NOW())";

            $query = $con->query($sql);*/

            $userinfo['id'] = $user_id;
            $userinfo['username'] = $username;
            $userinfo['name'] = $name;
            $userinfo['email'] = $email;
            $userinfo['status'] = 1;
        }

    }
    return $userinfo;
}

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function redirect_parent($url,$close=false)
{
    echo "<script type='text/javascript'>";
    if ($close)
    {
        echo "window.close(); ";
        echo "window.opener.location.href='$url'";
    }
    else
    {
        echo "window.location.href='$url'";
    }
    echo "</script>";

}


function verify_envato_purchase_code($code_to_verify) {
    // Your Username
    $username = 'bylancer';

    // Set API Key
    $api_key = 'yuo2pufs90ptj6nsoqzo4l60tiyce8lj';

    // Open cURL channel
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, "http://marketplace.envato.com/api/edge/". $username ."/". $api_key ."/verify-purchase:". $code_to_verify .".json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //Set the user agent
    $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    // Decode returned JSON
    $output = json_decode(curl_exec($ch), true);

    // Close Channel
    curl_close($ch);

    // Return output
    return $output;
}

?>