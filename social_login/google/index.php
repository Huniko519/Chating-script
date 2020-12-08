<?php
// Include FB config file && User class
error_reporting(0);
require_once '../../config.php';
require_once('../../includes/dbcon.php');
require_once '../function.php';
require_once 'gpConfig.php';

error_reporting(1);
error_reporting(E_WARNING | E_PARSE);

if(isset($_GET['code'])){
	$gClient->authenticate($_GET['code']);
	$_SESSION['token'] = $gClient->getAccessToken();
	header('Location: ' . filter_var($redirectURL, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
	$gClient->setAccessToken($_SESSION['token']);
}

if ($gClient->getAccessToken()) {
	//Get user profile data from google
	$gpUserProfile = $google_oauthV2->userinfo->get();
	

	//Insert or update user data to the database
    $gpUserData = array(
        'oauth_provider'=> 'google',
        'oauth_uid'     => $gpUserProfile['id'],
        'first_name'    => $gpUserProfile['given_name'],
        'last_name'     => $gpUserProfile['family_name'],
        'email'         => $gpUserProfile['email'],
        'gender'        => $gpUserProfile['gender'],
        'locale'        => $gpUserProfile['locale'],
        'picture'       => $gpUserProfile['picture'],
        'link'          => $gpUserProfile['link']
    );

    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );
    $flargePic = file_get_contents($gpUserProfile['picture'], false, stream_context_create($arrContextOptions));
    $upOne = realpath(dirname(__FILE__) . '/../..');

    $picname = $gpUserProfile['id'].'.jpg';
    $lfile = $upOne.'/storage/user_image/'.$picname;
    $sfile = $upOne.'/storage/user_image/small'.$picname;

    file_put_contents($lfile, $flargePic);
    file_put_contents($sfile, $flargePic);

    if($gpUserData['email'] == "")
    {
        $error = "Please add email id in google account later try again";
        echo "<script type='text/javascript'>alert('$error');</script>";
        redirect_parent($config['site_url'] ."login.php",true);
        exit();
    }


    /* ---- Session Variables -----*/
    $userData = array();
    $userData = checkSocialUser($con,$config,$gpUserData,$picname);

    if(!is_array($userData))
    {
        $error = "Email address not exist";
        echo "<script type='text/javascript'>alert('$error');</script>";
        redirect_parent($config['site_url'] ."logout.php",true);
        exit();
    }
    elseif($userData['status'] == 2)
    {
        $error = "Your account banned by admin";
        echo "<script type='text/javascript'>alert('$error');</script>";
        redirect_parent($config['site_url'] ."logout.php",true);
        exit();
    }
    else
    {
        $_SESSION['username'] = $userData['username'];
        $_SESSION['id'] = $userData['id'];
        $_SESSION['email'] = $userData['email'];

        redirect_parent($config['site_url'] ."login.php",true);
    }
	
	//Render facebook profile data
    if(!empty($userData)){
        $output = '<h1>Google+ Profile Details </h1>';
        $output .= '<img src="'.$userData['picture'].'" width="300" height="220">';
        $output .= '<br/>Google ID : ' . $userData['oauth_uid'];
        $output .= '<br/>Name : ' . $userData['first_name'].' '.$userData['last_name'];
        $output .= '<br/>Email : ' . $userData['email'];
        $output .= '<br/>Gender : ' . $userData['gender'];
        $output .= '<br/>Locale : ' . $userData['locale'];
        $output .= '<br/>Logged in with : Google';
        $output .= '<br/><a href="'.$userData['link'].'" target="_blank">Click to Visit Google+ Page</a>';
        $output .= '<br/>Logout from <a href="logout.php">Google</a>'; 
    }else{
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    }
} else {
	$authUrl = $gClient->createAuthUrl();
	//$output = '<a href="'.filter_var($authUrl, FILTER_SANITIZE_URL).'"><img src="images/glogin.png" alt=""/></a>';

    echo "<script type='text/javascript'>window.location.href='$authUrl'</script>";
}
?>


<?php echo $output; ?>