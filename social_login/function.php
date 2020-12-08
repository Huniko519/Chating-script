<?php
/**
 * You Can set your own timezone here.
 * Example : Asia/Kolkata
 */
date_default_timezone_set('Asia/Kolkata');
$date = new DateTime("now", new DateTimeZone('Asia/Kolkata') );
$timenow = date('Y-m-d H:i:s');

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

function checkSocialUser($con,$config,$userData = array(),$picname){

    if(!empty($userData)){

        $fullname = $userData['first_name'].' '.$userData['last_name'];
        $fbfirstname = $userData['first_name'];

        // Check whether user data already exists in database
        $prevQuery = "SELECT * FROM ".$config['db']['pre']."userdata WHERE oauth_provider = '".$userData['oauth_provider']."' AND oauth_uid = '".$userData['oauth_uid']."'";
        $prevResult = mysqli_query($con, $prevQuery);
        if(mysqli_num_rows($prevResult)>0){

        }else{
            //mysql query to select field username if it's equal to the username that we check '
            $sql = "select username from ".$config['db']['pre']."userdata where username = '".$fbfirstname."'";
            $result = mysqli_query($con,$sql);

            //if number of rows fields is bigger them 0 that means it's NOT available '
            if(mysqli_num_rows($result)>0){
                $username = createusernameslug($con,$config,$fbfirstname);
            }
            else{
                $username = $fbfirstname;
            }

            // Insert user data
            $query = "INSERT INTO ".$config['db']['pre']."userdata SET
            oauth_provider = '".$userData['oauth_provider']."',
            oauth_uid = '".$userData['oauth_uid']."',
            status = '1',
            name = '$fullname',
            username = '$username',
            email = '".$userData['email']."',
            sex = '".$userData['gender']."',
            picname = '".$picname."',
            oauth_link = '".$userData['link']."',
            joined = '".date("Y-m-d H:i:s")."'";
            $insert = mysqli_query($con, $query);
        }

        // Get user data from the database
        $result = mysqli_query($con, $prevQuery);
        $userData = $result->fetch_assoc();
    }

    // Return user data
    return $userData;
}

function createusernameslug($con,$config,$title)
{
    $slug = $title;

    $query = "SELECT COUNT(*) AS NumHits FROM ".$config['db']['pre']."userdata WHERE username LIKE '$slug%'";
    $result = mysqli_query($con,$query);
    $row = mysqli_fetch_assoc($result);
    $numHits = $row['NumHits'];

    return ($numHits > 0) ? ($slug.$numHits) : $slug;
}

?>