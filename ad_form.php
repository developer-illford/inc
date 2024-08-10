<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $fname = filter_var(trim($_POST['fname']), FILTER_SANITIZE_STRING);
    $cname = filter_var(trim($_POST['cname']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
    $shippingInquiryType = filter_var(trim($_POST['shippingInquiryType']), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);

    // Check if required fields are not empty
    if (!empty($fname) && !empty($cname) && !empty($email) && !empty($phone) && !empty($message)) {
    // Recipient email address
    $recipient = "sales@incshipping.com";
    $bcc = "dm.illforddigital@gmail.com, edb@illforddigital.com";
 
        $subject = "INC SHIPPING - From Ad Page - New form submission from $fname.";

        // Email content with HTML styling
        $email_content = "<html><body>";
        $email_content .= "<h2>New Form Submission</h2>";
        $email_content .= "<p><strong>Name:</strong> $fname</p>";
        $email_content .= "<p><strong>Company Name:</strong> $cname</p>";
        $email_content .= "<p><strong>Email:</strong> $email</p>";
        $email_content .= "<p><strong>Phone:</strong> $phone</p>";
        $email_content .= "<p><strong>Shipping Inquiry Type:</strong> $shippingInquiryType</p>";
        $email_content .= "<p><strong>Message:</strong><br>$message</p>";
        $email_content .= "</body></html>";

        // Email headers
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "BCC: $bcc\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        // Send the email
        if (mail($to, $subject, $email_content, $headers)) {
            // Redirect to a thank you page or display a success message
            echo "<script type='text/javascript'> window.location.href = 'thankyou.html';</script>";
        } else {
            echo "<script type='text/javascript'>alert('Oops! Something went wrong, and we could not send your message.');</script>";
        }
    } else {
        echo "<script type='text/javascript'>alert('Please fill in all required fields.');</script>";
    }
} else {
    echo "<script type='text/javascript'>alert('Invalid request method.');</script>";
}
?>
