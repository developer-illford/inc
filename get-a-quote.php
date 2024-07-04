<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $companyName = filter_var($_POST['companyName'], FILTER_SANITIZE_STRING);
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $deptCity = filter_var($_POST['deptCity'], FILTER_SANITIZE_STRING);
    $delCity = filter_var($_POST['delCity'], FILTER_SANITIZE_STRING);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

    if (!$email) {
        echo "<script type='text/javascript'> window.location.href = 'thankyou.html';</script>";
        exit;
    }

    // Recipient email address
    $recipient = "sales@incshipping.com";

    // Email subject
    $subject = "New Form Submission from $name";

    // Email body
    $email_body = "Company Name: $companyName\n";
    $email_body .= "Name: $name\n";
    $email_body .= "Phone: $phone\n";
    $email_body .= "Email: $email\n";
    $email_body .= "City of Departure: $deptCity\n";
    $email_body .= "Delivery City: $delCity\n";
    $email_body .= "Message: $message\n";

    // Headers for the email
    $headers = "From: sales@incshipping.com\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Send email to recipient
    if (mail($recipient, $subject, $email_body, $headers)) {
        // Send confirmation email to sender
        $confirmation_subject = "Thank you for your submission";
        $confirmation_body = "Dear $name,\n\nThank you for contacting us. We have received your submission and will get back to you shortly.\n\nBest regards,\nINC Shipping LLC";
        $confirmation_headers = "From: sales@incshipping.com\r\n";

        mail($email, $confirmation_subject, $confirmation_body, $confirmation_headers);

        echo "<script type='text/javascript'> window.location.href = 'thankyou.html';</script>";
    } else {
        echo "<script type='text/javascript'>alert('There was a problem sending your message. Please try again later.'); window.location.href = 'index.html';</script>";
    }
} else {
    echo "<script type='text/javascript'>alert('Invalid request method.'); window.location.href = 'index.html';</script>";
}
?>


