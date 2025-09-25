<?php
// Set headers for JSON response
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $fullName = $_POST['fullName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $service = $_POST['service'] ?? '';
    $message = $_POST['message'] ?? '';

    // Validate inputs
    $errors = [];
    
    if (empty($fullName)) {
        $errors[] = 'Full name is required';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    }

    // If no errors, process the form
    if (empty($errors)) {
        // Recipient email
        $to = 'aakash@pivotmkg.com';
        $subject = 'New Contact Form Submission from ' . $fullName;

        // Email headers
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        // Email body
        $emailBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #23235b; color: white; padding: 15px; border-radius: 5px 5px 0 0; }
                .content { padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
                .footer { margin-top: 20px; font-size: 12px; color: #777; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2 style='margin: 0;'>New Contact Form Submission</h2>
                </div>
                <div class='content'>
                    <p><strong>Name:</strong> $fullName</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Phone:</strong> $phone</p>
                    <p><strong>Service Interested In:</strong> " . ($service ?: 'Not specified') . "</p>
                    <p><strong>Message:</strong></p>
                    <p>" . nl2br(htmlspecialchars($message)) . "</p>
                </div>
                <div class='footer'>
                    <p>This email was sent from the contact form on James Douglas website.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        try {
            // Send email
            $mailSent = mail($to, $subject, $emailBody, $headers);
            
            if ($mailSent) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Thank you for your message! We will get back to you soon.',
                    'redirect' => '../thank-you.html'
                ]);
            } else {
                throw new Exception('Failed to send email. Please try again later.');
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    } else {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Please fill in all required fields.',
            'errors' => $errors
        ]);
    }
} else {
    // If someone tries to access this file directly, redirect to contact page
    header('Location: ../contact-us.html');
    exit();
}
?>
