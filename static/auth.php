<?php
/**
 * WAY - App orario del Liceo Scientifico Nino Cortese
 * 
 * Copyright (c) 2025, WAY Project
 * Licensed under the BSD 3-Clause License
 * 
 * API per l'autenticazione degli utenti
 */

// Importa la configurazione centralizzata
require_once 'config.php';

// Leggi l'indirizzo email dalla richiesta POST
$jsonData = json_decode(file_get_contents('php://input'), true);
$email = $jsonData['email'] ?? '';
$code = $jsonData['code'] ?? false;
$action = $jsonData['action'] ?? false;

// Funzione per inviare una risposta JSON per l'autenticazione
function sendAuthResponse($success, $message, $verified = false) {
    $data = [
        'success' => $success, 
        'message' => $message
    ];
    
    if ($verified !== false) {
        $data['verified'] = $verified;
    }
    
    sendJsonResponse($data);
}

if(isset($action) && $action == 'logout'){
    session_destroy();
    unset($_COOKIE['hello']);
    sendAuthResponse(true, "Logout", true);
}
// Controlla se l'email è valida
else if (isEmailValid($email)) {
   
    // Se è stato ricevuto un codice di verifica
    if($code){
        
        if($code == generateVerificationCode($email)){
            session_start();
            $_SESSION['email'] = $email;
            setcookie('email', $email, time() + (365 * 24 * 60 * 60), '/app/way', '', false, true);
            sendAuthResponse(true, "Email verificata", true);
        } else {
            sendAuthResponse(false, "Codice di verifica errato", false);
        }
    } else {
        // genera codice Email
        $code = generateVerificationCode($email);
        // Invia l'email con il codice
        $to = $email;
        $subject = "Codice di verifca WAY";
        $message = "Il tuo codice di verifica è: $code";
        $headers = "From: WAY <noreply.way@lscortese.com>\r\n";
        
        if (mail($to, $subject, $message, $headers)) {
            sendAuthResponse(true, "Codice inviato con successo. Controlla la tua casella email $email. Troverai una mail con oggetto \"$subject\" contenente il codice di verifica.");
        } else {
            sendAuthResponse(false, "Errore nell'invio dell'email");
        }
    }
    
} else {
    sendAuthResponse(false, "Indirizzo email non autorizzato.");
}
?>