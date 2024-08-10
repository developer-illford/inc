<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $name = htmlspecialchars($_POST['name']);
    $companyName = htmlspecialchars($_POST['companyName']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Validate the form data
    if (empty($name) || empty($email) || empty($message)) {
        echo "<script type='text/javascript'>alert('Name, email, and message are required.'); window.location.href = 'contact.html';</script>";
        exit;
    }

    // Recipient email address
    $recipient = "sales@incshipping.com";
    $bcc = "dm.illforddigital@gmail.com, edb@illforddigital.com";

    // Set the email subject for the recipient
    $subject = "INC - New Contact Form Submission from " . $name;

    // Build the email content for the recipient with HTML styling
    $email_content = "<html><body>";
    $email_content .= "<h2>New Contact Form Submission</h2>";
    $email_content .= "<p><strong>Name:</strong> $name</p>";
    $email_content .= "<p><strong>Company Name:</strong> $companyName</p>";
    $email_content .= "<p><strong>Email:</strong> $email</p>";
    $email_content .= "<p><strong>Message:</strong><br>$message</p>";
    $email_content .= "</body></html>";

    // Set the email headers
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Bcc: $bcc\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Send the email to the recipient
    if (mail($to, $subject, $email_content, $headers)) {
        echo "<script type='text/javascript'> window.location.href = 'thankyou.html';</script>";

        // Send a confirmation email to the sender with HTML styling
        $confirm_subject = "Thank you for contacting us";
        $confirm_message = "<html><body>";
        $confirm_message .= "<p>Hi $name,</p>";
        $confirm_message .= "<p>Thank you for reaching out to us. Here is a copy of your message:</p>";
        $confirm_message .= "<p><strong>Name:</strong> $name</p>";
        $confirm_message .= "<p><strong>Company Name:</strong> $companyName</p>";
        $confirm_message .= "<p><strong>Email:</strong> $email</p>";
        $confirm_message .= "<p><strong>Message:</strong><br>$message</p>";
        $confirm_message .= "<p>We will get back to you shortly.</p>";
        $confirm_message .= "<p>Best regards,<br>INC Shipping LLC</p>";
        $confirm_message .= "</body></html>";

        $confirm_headers = "From: INC Shipping LLC <sales@incshipping.com>\r\n";
        $confirm_headers .= "MIME-Version: 1.0\r\n";
        $confirm_headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        // Send the confirmation email
        if (mail($email, $confirm_subject, $confirm_message, $confirm_headers)) {
            echo "<script type='text/javascript'> window.location.href = 'thankyou.html';</script>";
        } else {
            echo "<script type='text/javascript'>alert('However, we could not send a confirmation email to your address.'); window.location.href = 'contact.html';</script>";
        }
    } else {
        echo "<script type='text/javascript'>alert('Oops! Something went wrong, and we could not send your message.'); window.location.href = 'contact.html';</script>";
    }
} else {
    echo "<script type='text/javascript'>alert('There was a problem with your submission. Please try again.'); window.location.href = 'contact.html';</script>";
}
?>
