<?php
require 'vendor/autoload.php';
require 'mail_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendApplicationConfirmationEmail($to_email, $to_name, $job_title, $company_name) {
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) {
            error_log("PHPMailer Debug: $str");
        };

        // yim che setting
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->Port = SMTP_PORT;
        
        // ye gov auth
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        
        // ye chu enkrpt krn
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        // yamis gaxi
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to_email, $to_name);

        // ye gai mail shuru
        $mail->isHTML(true);
        $mail->Subject = 'Job Application Confirmation - ' . $job_title;

        // Email body
        $body = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #4A3AFF;'>Application Confirmation</h2>
                <p>Dear {$to_name},</p>
                <p>Thank you for applying for the position of <strong>{$job_title}</strong> at <strong>{$company_name}</strong>.</p>
                <p>We have received your application and will review it shortly. If your qualifications match our requirements, we will contact you for the next steps in the selection process.</p>
                <div style='margin: 30px 0; padding: 20px; background-color: #f8f9fe; border-radius: 8px;'>
                    <h3 style='color: #4A3AFF; margin-top: 0;'>Application Details:</h3>
                    <p><strong>Position:</strong> {$job_title}</p>
                    <p><strong>Company:</strong> {$company_name}</p>
                    <p><strong>Date Applied:</strong> " . date('F j, Y') . "</p>
                </div>
                <p>Please note that we receive many applications and will contact you if your profile matches our requirements.</p>
                <p>Best regards,<br>The {$company_name} Recruitment Team</p>
            </div>
        </body>
        </html>";

        $mail->Body = $body;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $body));

        $mail->send();
        error_log("Email sent successfully to: $to_email");
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        error_log("Exception details: " . $e->getMessage());
        return false;
    }
}
?> 