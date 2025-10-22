<?php
function logout() {
    // Inizia la sessione se non è già stata avviata
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Elimina tutte le variabili di sessione
    unset($_SESSION["lscadmin:username"]);

    // Se si sta usando un cookie di sessione, eliminalo
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Distruggi la sessione
    session_destroy();
}

// Esempio di utilizzo
logout();
header("Location: login.php"); // Reindirizza alla pagina di login
exit();
?>