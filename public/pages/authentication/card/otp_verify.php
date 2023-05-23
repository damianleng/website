<?php
session_start();
// OTP verification process
$email = $_SESSION['email'];
$userlogin_id = $_SESSION['Userlogin_id'];
// Database connection details
$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "user_login";

// Create a database connection
$conn = new mysqli($host, $db_user, $db_password, $db_name);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the OTP form is submitted
if (isset($_POST['otp_verify'])) {
    // Retrieve the entered OTP
    $enteredOTP = $_POST['otp'];

    // Query to retrieve the OTP from the UserLogin table
    $query = "SELECT Userlogin_otp, Userlogin_otp_expiration FROM UserLogin WHERE Userlogin_id = '$userlogin_id'";
    $stmt = $conn->prepare($query);
    // Execute the query
    $stmt->execute();

    // Bind the result to a variable
    $stmt->bind_result($storedOTP, $otpExpiration);

    // Fetch the result
    $stmt->fetch();

    // Close the statement
    $stmt->close();
    date_default_timezone_set('Asia/Phnom_Penh'); //Set time-zone
    $expiryTime = strtotime($otpExpiration); 
    // Compare the entered OTP with the stored OTP
    if ($enteredOTP == $storedOTP && $expiryTime>time()) {
        // OTP is correct
        echo "OTP verification successful. Access granted!. <br>" ;
        // Update the Userlogin_is_verified_field
        $stmt = $conn->prepare("UPDATE UserLogin SET Userlogin_is_verified = 1 WHERE Userlogin_id = '$userlogin_id'");
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "User verification status updated. <br>";
        } else {
            echo "Error updating user verification status. <br>";
        }

    } else {
        // OTP is incorrect
        echo "OTP verification failed. Please try again.";
        // Additional actions or redirection can be performed here
    }
}

// Close the database connection
$conn->close();
?>
