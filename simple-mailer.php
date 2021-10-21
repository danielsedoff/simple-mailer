<?php

/* the result message string */

$result_msg = '';
$hit_email_limit = "\r\n>>>I've just hit max email limit for today. Bye. \r\n";
$subscriber_file = 'subscribers.txt';
$unsubscriber_file = 'unsubscribers.txt';
$mailer_message_file = 'mailer-message.html';
$log_file = 'smlog.txt';

$max_daily_email_limit = 190;

if ( file_exists ( $log_file ) ) {
    die( 'please remove the log file before running simple-mailer' );
}

// FILE_APPEND: append. LOCK_EX: no simultaneous writing.
file_put_contents( $log_file, 'Start! \r\n', FILE_APPEND | LOCK_EX );

$subscribed_list = file_get_contents( $subscriber_file );
$unsubscribed_list = file_get_contents( $unsubscriber_file );
$template_message = file_get_contents( $mailer_message_file );

$exploded_subscribed_list = explode( ',', $subscribed_list );
$recepient_list = Array();

foreach ( $exploded_subscribed_list as &$client ) {
    $uns_pos = strpos( $unsubscribed_list, ( ',' . $client . ',' ) );
    if ( $uns_pos === false ) {
        array_push( $recepient_list, $client );
    }
}

// Set content type when sending HTML email
$headers = 'MIME-Version: 1.0'
. '\r\nContent-type:text/html;charset=UTF-8'
. '\r\nFrom: <no-reply@example.com>\r\n';
$subject = 'Check out example.com!';

foreach ( $recepient_list as &$recepient ) {
    if ( filter_var( $recepient, FILTER_VALIDATE_EMAIL ) === false ) {
        continue;
    }

    $max_daily_email_limit --;
    if ( $max_daily_email_limit == 0 ) {
        file_put_contents( $log_file, $hit_email_limit, FILE_APPEND | LOCK_EX );
        die();
    }

    $message = str_replace( '@RECEPIENT@', $recepient, $template_message );
    $bool_succ = mail( $recepient, $subject, $message, $headers );
    sleep( 10 );
    /* the server does not allow a higher frequency */

    if ( $bool_succ ) {
        $result_msg = 'ok';
    } else {
        $result_msg = 'fail';
    }

    file_put_contents( $log_file, 'sending to '
    . $recepient . '... ' . $result_msg
    . '\r\n', FILE_APPEND | LOCK_EX );
}

file_put_contents( $log_file, '\r\n\r\n*END*\r\n', FILE_APPEND | LOCK_EX );

?>
