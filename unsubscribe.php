<?php
 $client = $_GET['client'];
 
 // validate e-mail
 if (filter_var($client, FILTER_VALIDATE_EMAIL) === false) {
  die("cannot unsubscribe $client: not an email.");
 }
 
 // unsubscribe
 $subscribed_list = file_get_contents('subscribers.txt');
 $unsubscribed_list = file_get_contents('unsubscribers.txt');
 
 $uns_pos = strpos($unsubscribed_list, ("," . $client . ","));
 if ($uns_pos !== false) {
  die("<html>
        <head>
         <meta charset=\"utf-8\">
        </head>
        <body>
        <h2>" 
        . $client . 
             "You have already unsubscribed.
        </h2>
       </body>
      </html>");
 	}
  // do not add unsubscribers more than once.
 	
  $subs_pos = strpos($subscribed_list, ("," . $client . ","));
  if($subs_pos !== false) {
    // If the subscriber is in the subscriber list, unsubscribe them.
    // unsubscribers must be added to a separate list
  file_put_contents("unsubscribe.txt", $client . ",", FILE_APPEND | LOCK_EX);
  echo("<html>
        <head>
         <meta charset=\"utf-8\">
        </head>
        <body>
         <h2>"
          . $client . 
          ": You have been unsubscribed.
         </h2>
        </body>
        </html>");
     } else {
     // Reject unsubscribing attempt if it was not subscribed.
  echo("<html>
        <head>
         <meta charset=\"utf-8\">
        </head>
        <body>
         <h2>"
          . $client . 
          ": You were not subscribed. 
         </h2>
        </body>
        </html>");
     };
 
 ?>
