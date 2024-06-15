<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data to prevent XSS and other issues
    $fname = htmlspecialchars($_POST['fname']);
    $lname = htmlspecialchars($_POST['lname']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $dept_city = htmlspecialchars($_POST['dept_city']);
    $arr_city = htmlspecialchars($_POST['arr_city']);
    $message = htmlspecialchars($_POST['message']);

    // Recipient email address
    $to = "manastom670@gmail.com";
    // Subject of the email
    $subject = "Form Submission from $fname $lname";

    // Email body content
    $body = "First Name: $fname\n";
    $body .= "Last Name: $lname\n";
    $body .= "Email: $email\n";
    $body .= "Phone: $phone\n";
    $body .= "City of Departure: $dept_city\n";
    $body .= "Delivery City: $arr_city\n";
    $body .= "Message:\n$message\n";

    // Email headers
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Sending email
    if (mail($to, $subject, $body, $headers)) {
        echo "Email successfully sent to $to";
    } else {
        echo "Email sending failed...";
    }
} else {
    echo "Invalid request method.";
}
?>
