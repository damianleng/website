<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_login";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if (isset($_POST['register'])) {
    // Get form input values
    $email = $_POST['email'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $dateOfBirth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $phoneNumber = $_POST['phone_number'];

    // Prepare and execute SQL statement to insert data into UserDetails table
    $stmt = $conn->prepare("INSERT INTO UserDetails (UserDetails_email, UserDetails_first_name, UserDetails_last_name, UserDetails_date_of_birth, UserDetails_gender, UserDetails_address, UserDetails_city, UserDetails_country, UserDetails_phone_number) 
    VALUES ('$email', '$firstName', '$lastName', '$dateOfBirth', '$gender', '$address', '$city', '$country', '$phoneNumber');");
    //$stmt->bind_param("sssssssss", $email, $firstName, $lastName, $dateOfBirth, $gender, $address, $city, $country, $phoneNumber);
    $stmt->execute();

    // Check if the data was successfully inserted
    if ($stmt->affected_rows > 0) {
        echo "Data inserted successfully!";
    } else {
        echo "Error inserting data.";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
