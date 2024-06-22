<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $name = htmlspecialchars($_POST['name']);
    $companyName = htmlspecialchars($_POST['companyName']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Validate the form data
    if (empty($name) || empty($email) || empty($message)) {
        echo  "<script type='text/javascript'>alert('Name, email, and message are required.'); window.location.href = 'contact.html';</script>";
        exit;
    }
    

    // Set the recipient email address
    $to = "sales@incshipping.com"; // Replace with your email address

    // Set the email subject for the recipient
    $subject = "New Contact Form Submission from " . $name;

    // Build the email content for the recipient
    $email_content = "Name: $name\n";
    $email_content .= "Company Name: $companyName\n";
    $email_content .= "Email: $email\n\n";
    $email_content .= "Message:\n$message\n";

    // Set the email headers
    $headers = "From: $name <$email>";

    // Send the email to the recipient
    if (mail($to, $subject, $email_content, $headers)) {
        echo "<script type='text/javascript'> window.location.href = 'thankyou.html';</script>";

        // Send a confirmation email to the sender
        $confirm_subject = "Thank you for contacting us";
        $confirm_message = "Hi $name,\n\n";
        $confirm_message .= "Thank you for reaching out to us. Here is a copy of your message:\n\n";
        $confirm_message .= "Name: $name\n";
        $confirm_message .= "Company Name: $companyName\n";
        $confirm_message .= "Email: $email\n\n";
        $confirm_message .= "Message:\n$message\n\n";
        $confirm_message .= "We will get back to you shortly.\n\n";
        $confirm_message .= "Best regards,\nINC Shipping LLC";

        $confirm_headers = "From: INC Shipping LLC <sales@incshipping.com>";

        // Send the confirmation email
        if (mail($email, $confirm_subject, $confirm_message, $confirm_headers)) {
            echo "<script type='text/javascript'> window.location.href = 'thankyou.html';</script>";
        } else {
            echo "<script type='text/javascript'>alert('However, we could not send a confirmation email to your address.'); window.location.href = 'contact.html';</script>";
        }
    } else {
        echo "<script type='text/javascript'>alert('Oops! Something went wrong, and we could not send your message.'); window.location.href = 'contact.html';</script>
        ";
    }
} else {
    echo "<script type='text/javascript'>alert('There was a problem with your submission. Please try again.'); window.location.href = 'contact.html';</script>";
}
?>


