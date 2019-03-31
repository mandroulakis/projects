<?php

function send_email_to_admin($sender,$receiver,$f_name,$vacation_start,$vacation_end,$reason,$last_id){
    $from = $sender;
    $to = $receiver;
    $subject = "Vacation requested";
    $message = "Dear supervisor, employee {$f_name} requested for some time off, starting on <br>";
    $message .= "{$vacation_start} and ending on {$vacation_end}, stating the reason: <br>";
    $message .= "{$reason}\n";
    $message .= "Click on one of the below links to approve or reject the application: <br>";
    $message .= "<a href=http://localhost/php_project/outcome.php?action=approved&id={$last_id}>Approve</a> - <br>";
    $message .= "<a href=http://localhost/php_project/outcome.php?action=rejected&id={$last_id}>Reject</a>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From:" . $from;
    mail($to,$subject,$message, $headers);
}

function send_email_to_user($sender,$receiver,$action,$submission_date){
    $from = $sender;
    $to = $receiver;
    $subject = "Vacation requested";
    $message = "Dear employee, your supervisor has {$action} your application submitted on {$submission_date}";
    $headers = "From:" . $from;
    mail($to,$subject,$message, $headers);
}






