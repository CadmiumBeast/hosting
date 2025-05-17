<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load Composer's autoloader
require 'db.php'; // Include your database connection file

// Function to create .ics file content
function createICS($summary, $description, $location, $startTime, $endTime) {
    $uid = uniqid(); // Unique event ID

    // Start the iCalendar format
    $icsContent = "BEGIN:VCALENDAR\r\n";
    $icsContent .= "VERSION:2.0\r\n";
    $icsContent .= "PRODID:-//Microsoft Corporation//Outlook 16.0 MIMEDIR//EN\r\n";
    $icsContent .= "BEGIN:VEVENT\r\n";
    $icsContent .= "SUMMARY:" . $summary . "\r\n";
    $icsContent .= "UID:" . $uid . "@example.com\r\n";
    $icsContent .= "DTSTART:" . $startTime . "\r\n"; // Start date-time in format YYYYMMDDTHHMMSSZ
    $icsContent .= "DTEND:" . $endTime . "\r\n"; // End date-time in format YYYYMMDDTHHMMSSZ
    $icsContent .= "LOCATION:" . $location . "\r\n";
    $icsContent .= "DESCRIPTION:" . $description . "\r\n";
    $icsContent .= "STATUS:CONFIRMED\r\n";
    $icsContent .= "BEGIN:VALARM\r\n";
    $icsContent .= "TRIGGER:-PT10M\r\n"; // Reminder 10 minutes before
    $icsContent .= "DESCRIPTION:Reminder\r\n";
    $icsContent .= "ACTION:DISPLAY\r\n";
    $icsContent .= "END:VALARM\r\n";
    $icsContent .= "END:VEVENT\r\n";
    $icsContent .= "END:VCALENDAR\r\n";

    return $icsContent;
}

// Function to send meeting email
function sendMeetingEmail($recipientEmail, $recipientName, $studentmail, $eventSummary, $eventDescription, $eventLocation, $startTime, $endTime) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io'; // Set your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = '09debc3531675d'; // SMTP username
        $mail->Password = '33132fd2bde7fa'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 2525;

        // Recipients
        $mail->setFrom('Click2Book@apiit.lk', 'Admin');
        $mail->addAddress($recipientEmail, $recipientName); // Recipient's email

        

        // HTML Email Body
        $mail->isHTML(true); // Enable HTML content
        if($studentmail != ""){
            // Subject
            $mail->Subject = 'Meeting Invitation';
            $mail->Body = "
            <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .header { font-size: 18px; font-weight: bold; color: #333; }
                        .content { margin-top: 10px; }
                        .footer { margin-top: 20px; font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <div class='header'>Meeting Invitation</div>
                    <div class='content'>
                        <p>You are invited to a meeting by <strong>$studentmail</strong>.</p>
                        <p><strong>Event Summary:</strong> $eventSummary</p>
                        <p><strong>Description:</strong> $eventDescription</p>
                        <p><strong>Location:</strong> $eventLocation</p>
                        <p><strong>Start Time:</strong> $startTime</p>
                        <p><strong>End Time:</strong> $endTime</p>
                    </div>
                    <div class='footer'>
                        Please see the attached calendar invitation to add this event to your calendar.
                    </div>
                </body>
            </html>
        ";
        }else{
            // Subject
        $mail->Subject = 'Discussion Room Booking';
        $mail->Body = "
            <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .header { font-size: 18px; font-weight: bold; color: #333; }
                        .content { margin-top: 10px; }
                        .footer { margin-top: 20px; font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <div class='header'>Discussion Booking Update</div>
                    <div class='content'>
                        <p><strong>Event Summary:</strong> $eventSummary</p>
                        <p><strong>Description:</strong> $eventDescription</p>
                        <p><strong>Location:</strong> $eventLocation</p>
                        <p><strong>Start Time:</strong> $startTime</p>
                        <p><strong>End Time:</strong> $endTime</p>
                    </div>
                    <div class='footer'>
                        Please see the attached calendar invitation to add this event to your calendar.
                    </div>
                </body>
            </html>
        ";
        }
        

        // Plain Text Alternative Body
        $mail->AltBody = "You are invited to a meeting by $studentmail.\n\n" .
                         "Event Summary: $eventSummary\n" .
                         "Description: $eventDescription\n" .
                         "Location: $eventLocation\n" .
                         "Start Time: $startTime\n" .
                         "End Time: $endTime\n\n" .
                         "Please see the attached calendar invitation to add this event to your calendar.";

        // Create ICS file content
        $icsContent = createICS($eventSummary, $eventDescription, $eventLocation, $startTime, $endTime);

        // Attach ICS file
        $icsFileName = 'meeting_invite.ics';
        $mail->addStringAttachment($icsContent, $icsFileName, 'base64', 'text/calendar');

        // Send email
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
