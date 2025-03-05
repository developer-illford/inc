<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Set Security Headers
    header("Content-Security-Policy: default-src 'self'; script-src 'self' https://www.google.com/recaptcha/;");
    header("X-Frame-Options: DENY"); // Prevent Clickjacking  
    header("X-XSS-Protection: 1; mode=block"); // Prevent reflected XSS  
    header("X-Content-Type-Options: nosniff"); // Prevent MIME-type sniffing  
    header("Referrer-Policy: no-referrer-when-downgrade"); // Limit referrer exposure  
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains"); // Enforce HTTPS  

    // Your reCAPTCHA secret key
    $secretKey = "6LfMexkqAAAAACO73KZwp44Sb9itb6M-zsji6q7p";
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // Verifying the reCAPTCHA response
    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
    $responseData = json_decode($verifyResponse);

    if (!$responseData->success) {
        die("reCAPTCHA verification failed.");
    }

    // Function to sanitize and validate input
    function clean_input($input, $maxLength = 100) {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8'); // Prevent HTML/Script Injection
        if (strlen($input) > $maxLength) {
            die("Error: Input exceeds allowed length.");
        }
        if (!preg_match("/^[a-zA-Z0-9\s.,'-]+$/u", $input)) {
            die("Error: Invalid characters detected.");
        }
        return $input;
    }

    function validate_email($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("Error: Invalid email format.");
        }
        return htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    }

    // Sanitize & validate input data
    $companyName = clean_input($_POST['companyName']);
    $name = clean_input($_POST['name']);
    $phone = clean_input($_POST['phone'], 15); // Limit phone length
    $email = validate_email($_POST['email']);
    $deptCity = clean_input($_POST['deptCity']);
    $delCity = clean_input($_POST['delCity']);
    $message = clean_input($_POST['message'], 500); // Limit message length
    $sourcePage = clean_input($_POST['hiddenId'], 50);

    // Prevent Honeypot field submission
    if (!empty($_POST['honeypot'])) {
        die("Bot detected. Submission blocked.");
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
        die("Error: Unable to send email. Please try again later.");
    }
} else {
    die("Invalid request method.");
}
?>
