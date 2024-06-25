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
        // Email configuration
        $to = "recipient@example.com"; // Replace with your email address
        $subject = "New Form Submission";
        
        // Email content
        $email_content = "Name: $fname\n";
        $email_content .= "Company Name: $cname\n";
        $email_content .= "Email: $email\n";
        $email_content .= "Phone: $phone\n";
        $email_content .= "Shipping Inquiry Type: $shippingInquiryType\n";
        $email_content .= "Message:\n$message\n";
        
        // Email headers
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Bcc: dm.illforddigital@gmail.com\r\n"; // Add the Bcc recipient here

        // Send the email
        if (mail($to, $subject, $email_content, $headers)) {
            // Redirect to a thank you page or display a success message
            echo "<script type='text/javascript'> window.location.href = 'thankyou.html';</script>";
        } else {
            echo "<script type='text/javascript'>alert('Oops! Something went wrong, and we could not send your message.');</script>"
        }
    } else {
        echo "<script type='text/javascript'>alert('Please fill in all required fields.');</script>";
    }
} else {
    echo "<script type='text/javascript'>alert('Invalid request method.');</script>";
}
?>
