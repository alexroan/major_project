<?php
require '../api/message_controller.php';
session_start();
if(!$_SESSION['ps_email']){
    header("Location: index.php");
}
else{
    $_SESSION['error'] = "Message failed to send";
    $m_controller = new Message_Controller();
    $ps_email_receiver = htmlentities($_POST['send_to']);
    $table_ref = $_SESSION['table_ref'];
    $table_ref_pk = $_SESSION['table_ref_pk'];
    $title = htmlentities($_POST['subject']);
    $body = htmlentities($_POST['message_content']);
    
    if(!$table_ref){
        $table_ref = "";
    }
    if(!$table_ref_pk){
        $table_ref_pk = "";
    }
    
    $send_message = $m_controller->SendMessage($_SESSION['ps_email'], $ps_email_receiver, $table_ref, $table_ref_pk, $title, $body);
    $m_controller->close();
    if($send_message){
        $_SESSION['error'] = "Message sent";
    }
    
    header("location: messages.php");
}
?>
