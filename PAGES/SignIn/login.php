<?php
session_start(); // Start session to store user information

// Database credentials
$servername = "localhost"; // or "127.0.0.1"
$username = "root"; // default XAMPP MySQL username
$password = ""; // default XAMPP MySQL password is empty
$dbname = "user_registration"; // the name of the database you created

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_SESSION['error_message'])) {
    echo '<div style="color: red;">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']); // Clear the error message after displaying it
}


// Process login form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve user data from the database based on the entered email
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

       
        // Verify the entered password against the stored hashed password
        if (password_verify($password, $hashed_password)) {
            // Store user data in session for future use (e.g., user dashboard)
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_email'] = $row['email'];

            // Redirect to the user dashboard or another page
            header("Location: ../../PAGES/index.html");
            exit;
        } else {
            $_SESSION['error_message'] = "Invalid email or password"; // Set error message
            header("Location: signin.html"); // Redirect back to the sign-in page
            exit;
        }
    } else {
        $_SESSION['error_message'] = "Invalid email or password"; // Set error message
        header("Location: signin.html"); // Redirect back to the sign-in page
        exit;
    }


    if (isset($_SESSION['error_message'])) {
        echo 'alert("' . $_SESSION['error_message'] . '");';
        unset($_SESSION['error_message']); // Clear the error message after displaying it
    }
}

// Close the database connection
$conn->close();