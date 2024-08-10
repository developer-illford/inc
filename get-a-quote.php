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
    $sourcePage = $_POST['hiddenId'];

    if (!$email) {
        echo "<script type='text/javascript'> window.location.href = 'thankyou.html';</script>";
        exit;
    }

    // Recipient email address
    $recipient = "sales@incshipping.com";
    $bcc = "dm.illforddigital@gmail.com, edb@illforddigital.com";

    // Email subject
    $subject = "INC - New Form Submission from $name";

    // Email body with HTML styling
    $email_body = "<html><body>";
    $email_body .= "<p>===========================</p>";
    $email_body .= "<h2>NEW FORM SUBMISSION</h2>";
    $email_body .= "<p>===========================</p>";
    $email_body .= "<p><strong>Company Name:</strong> $companyName</p>";
    $email_body .= "<p><strong>Name:</strong> $name</p>";
    $email_body .= "<p><strong>Phone:</strong> $phone</p>";
    $email_body .= "<p><strong>Email:</strong> $email</p>";
    $email_body .= "<p><strong>City of Departure:</strong> $deptCity</p>";
    $email_body .= "<p><strong>Delivery City:</strong> $delCity</p>";
    $email_body .= "<p><strong>Message:</strong><br>$message</p>";
    $email_body .= "<p>===========================</p>";
    $email_body .= "<p>Source page: $sourcePage</p>";
    $email_body .= "</body></html>";

    // Headers for the email
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "BCC: $bcc\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Send email to recipient
    if (mail($recipient, $subject, $email_body, $headers)) {
        // Send confirmation email to sender with HTML styling
        $confirmation_subject = "Thank you for your submission";
        $confirmation_body = "<html><body>";
        $confirmation_body .= "<p>Dear $name,</p>";
        $confirmation_body .= "<p>Thank you for contacting us. We have received your submission and will get back to you shortly.</p>";
        $confirmation_body .= "<p>Best regards,<br>INC Shipping LLC</p>";
        $confirmation_body .= "</body></html>";

        $confirmation_headers = "From: sales@incshipping.com\r\n";
        $confirmation_headers .= "MIME-Version: 1.0\r\n";
        $confirmation_headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        mail($email, $confirmation_subject, $confirmation_body, $confirmation_headers);

        echo "<script type='text/javascript'> window.location.href = 'thankyou.html';</script>";
    } else {
        echo "<script type='text/javascript'>alert('There was a problem sending your message. Please try again later.'); window.location.href = 'index.html';</script>";
    }
} else {
    echo "<script type='text/javascript'>alert('Invalid request method.'); window.location.href = 'index.html';</script>";
}
?>
