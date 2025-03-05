<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Content Security Policy & Secure HTTP Headers
    header("Content-Security-Policy: default-src 'self'; script-src 'self' https://www.google.com https://www.gstatic.com; style-src 'self' 'unsafe-inline';");
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");
    header("X-Content-Type-Options: nosniff");
    
    // Server-Side Input Validation and Length Restrictions
    function sanitizeInput($data, $maxLength) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return substr($data, 0, $maxLength);
    }

    // Validate Inputs
    $name = sanitizeInput($_POST['name'], 100);
    $companyName = sanitizeInput($_POST['companyName'], 100);
    $email = filter_var(sanitizeInput($_POST['email'], 100), FILTER_SANITIZE_EMAIL);
    $message = sanitizeInput($_POST['message'], 500);
    $honeypot = $_POST['extraField'] ?? '';

    // Check if honeypot field is filled (bot detected)
    if (!empty($honeypot)) {
        exit;
    }

    // Validate Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address!'); window.location.href = 'contact.html';</script>";
        exit;
    }

    // Required Field Validation
    if (empty($name) || empty($email) || empty($message)) {
        echo "<script>alert('Name, email, and message are required.'); window.location.href = 'contact.html';</script>";
        exit;
    }

    // Google reCAPTCHA Validation
    $recaptcha_secret = "YOUR_SECRET_KEY";
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response";
    $recaptcha = json_decode(file_get_contents($recaptcha_url));

    if (!$recaptcha->success) {
        echo "<script>alert('reCAPTCHA verification failed. Please try again.'); window.location.href = 'contact.html';</script>";
        exit;
    }

    // Recipient email address
    $recipient = "sales@incshipping.com";
    $bcc = "dm.illforddigital@gmail.com, edb@illforddigital.com";

    // Email Content
    $subject = "INC - New Contact Form Submission from " . $name;
    $email_content = "<html><body>";
    $email_content .= "<h2>New Contact Form Submission</h2>";
    $email_content .= "<p><strong>Name:</strong> $name</p>";
    $email_content .= "<p><strong>Company Name:</strong> $companyName</p>";
    $email_content .= "<p><strong>Email:</strong> $email</p>";
    $email_content .= "<p><strong>Message:</strong><br>$message</p>";
    $email_content .= "</body></html>";

    // Email Headers
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Bcc: $bcc\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Send Email
    if (mail($recipient, $subject, $email_content, $headers)) {
        echo "<script>window.location.href = 'thankyou.html';</script>";

        // Confirmation Email to Sender
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

        mail($email, $confirm_subject, $confirm_message, $confirm_headers);
    } else {
        echo "<script>alert('Oops! Something went wrong, and we could not send your message.'); window.location.href = 'contact.html';</script>";
    }
} else {
    echo "<script>alert('There was a problem with your submission. Please try again.'); window.location.href = 'contact.html';</script>";
}
?>
