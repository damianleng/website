<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "user_login";

$conn = new mysqli($host, $db_user, $db_password, $db_name);
if($conn->connect_error){
    die("Connection failed ". $conn->connect_error);
}

if(isset($_POST['send']))
{
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    function getUserDeviceType($userAgent) {
        // Check if the user agent contains specific keywords for different device types
        if (strpos($userAgent, 'Mobile') !== false) {
            return 'Mobile';
        } elseif (strpos($userAgent, 'Tablet') !== false) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }
    function getUserOS($userAgent) {
        $os = "Unknown";
        $osList = array(
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );
    
        foreach ($osList as $regex => $value) {
            if (preg_match($regex, $userAgent)) {
                $os = $value;
                break;
            }
        }
    
        return $os;
    }
    // Function to parse the user's browser from the user agent
function getUserBrowser($userAgent) {
    $browser = "Unknown";
    $browserList = array(
        '/msie/i'       => 'Internet Explorer',
        '/firefox/i'    => 'Firefox',
        '/safari/i'     => 'Safari',
        '/chrome/i'     => 'Chrome',
        '/edge/i'       => 'Edge',
        '/opera/i'      => 'Opera',
        '/netscape/i'   => 'Netscape',
        '/maxthon/i'    => 'Maxthon',
        '/konqueror/i'  => 'Konqueror',
        '/mobile/i'     => 'Handheld Browser'
    );

    foreach ($browserList as $regex => $value) {
        if (preg_match($regex, $userAgent)) {
            $browser = $value;
            break;
        }
    }

    return $browser;
}
    $otp = mt_rand(100000, 999999); // Generate a random OTP
    $email = $_POST['email']; // Get the email address from the form submission
    $userIP = $_SERVER['REMOTE_ADDR']; // retrieve user IP address
    $deviceType = getUserDeviceType($userAgent);
    $deviceOS = getUserOS($userAgent);
    $deviceBrowser = getUserBrowser($userAgent);
    $_SESSION['email'] = $email;

    //Check if the email already exists in the UserDetails table
    $query = "SELECT UserDetails_email FROM UserDetails WHERE UserDetails_email = '$email'";
    $stmt = $conn->prepare($query);
  //  $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows >0){
    // Configure PHPMailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'damianlengdy@gmail.com'; // Replace with your SMTP username
        $mail->Password = 'smglenxrusmcwuiv'; // Replace with your SMTP password
        $mail->Port = 465; // Replace with your SMTP port
        $mail->SMTPSecure = 'ssl';
        $mail->isHTML(true);
        $mail->setFrom($email, 'Fusegap');
        $mail->addAddress($email); 
        $mail->Subject = 'OTP for Verification';
        $mail->Body = 'Your OTP is: ' . $otp;
            // Send the email
        if ($mail->send()) {
            echo 'OTP sent to ' . $email;
        } else {
            echo 'Error sending email: ' . $mail->ErrorInfo;
        }

        $query = "INSERT INTO UserLogin (Userlogin_email, Userlogin_otp, Userlogin_otp_expiration, Userlogin_IP_address, Userlogin_user_device_type, Userlogin_user_device_OS, Userlogin_user_device_browser)
        VALUES ('$email', '$otp', DATE_ADD(NOW(), INTERVAL 5 MINUTE), '$userIP', '$deviceType', '$deviceOS', '$deviceBrowser');
        ";
        $stmt = $conn->prepare($query);
        $userLoginId = null; // Set to NULL as it will be auto-generated
       // $stmt->bind_param("isss", $userLoginId, $email, $firstName, $lastName);
        $stmt->execute();
        // Execute the query to select the latest login_id with the specific email
        $stmt = $conn->prepare("SELECT Userlogin_id FROM UserLogin WHERE Userlogin_email = '$email' ORDER BY Userlogin_id DESC LIMIT 1");
        $stmt->execute();
        $stmt->bind_result($loginID);
        $stmt->fetch();
        $stmt->close();

    
        $_SESSION['Userlogin_id'] = $loginID;
        header("Location: otp.html");
        }else{
            echo"Email does not exist";
        }
        $stmt = $conn->prepare("UPDATE UserDetails
        SET UserDetails_Userlogin_id = $loginID WHERE UserDetails_email = '$email';");
        $stmt->execute();
        $stmt->close();   
    }
$stmt->close();
$conn->close();

?>
