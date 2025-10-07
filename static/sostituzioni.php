<?php
/**
 * WAY - App orario del Liceo Scientifico Nino Cortese
 * 
 * Copyright (c) 2025, WAY Project
 * Licensed under the BSD 3-Clause License
 * 
 * API per la gestione delle sostituzioni dei docenti
 */

// Importa la configurazione centralizzata
require_once 'config.php';

// Gestione richiesta POST - Accettazione sostituzione
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Legge il JSON dal corpo della richiesta
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Verifica che il JSON sia valido
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendErrorResponse('JSON non valido', 400);
    }
    
    // Verifica che ci sia l'ID
    if (!isset($data['id']) || empty($data['id'])) {
        sendErrorResponse('ID mancante', 400);
    }
    
    $id = intval($data['id']);
    
    // Verifica che l'ID sia valido
    if ($id <= 0) {
        sendErrorResponse('ID non valido - deve essere un numero positivo', 400);
    }
    
    try {
        // Connessione al database
        $pdo = createPDOConnection();
        if (!$pdo) {
            sendErrorResponse('Errore connessione database');
        }
        
        // Verifica che il record esista
        $checkSql = "SELECT id FROM sostituzioni WHERE id = :id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Record non trovato',
                'message' => 'Nessuna sostituzione trovata con l\'ID specificato'
            ]);
            exit();
        }
        
        // Aggiorna il campo accettato a 1
        $updateSql = "UPDATE sostituzioni SET accettato = 1 WHERE id = :id";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $updateStmt->execute();
        
        // Risposta di successo
        sendJsonResponse([
            'success' => true,
            'message' => 'Sostituzione accettata con successo',
            'id' => $id
        ]);
        
    } catch (PDOException $e) {
        sendErrorResponse('Errore database: ' . $e->getMessage());
    } catch (Exception $e) {
        sendErrorResponse('Errore interno: ' . $e->getMessage());
    }
    
    exit();
}

// Verifica che sia una richiesta GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendErrorResponse('Metodo non consentito - Solo richieste GET sono accettate', 405);
}

// Verifica presenza parametro email
session_start();
if (isset($_SESSION['email'])){
    $email = $_SESSION['email'];
}
else if (isset($_COOKIE['email'])){
    $email = $_COOKIE['email'];
}
else if(isset($_GET['email']) && !empty($_GET['email'])) {
    $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
} 
else {
    sendErrorResponse('Il parametro email è obbligatorio', 400);
}

// Validazione email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendErrorResponse('L\'indirizzo email fornito non è valido', 400);
}

try {
    // Connessione al database
    $pdo = createPDOConnection();
    if (!$pdo) {
        sendErrorResponse('Errore connessione database');
    }

    // Query con JOIN tra tabelle docenti e sostituzioni
    $sql = "SELECT 
                d.*,
                s.*
            FROM docenti d
            INNER JOIN sostituzioni s ON d.nome = s.docDest
            WHERE d.email = :email AND inviato = 1
            ORDER BY s.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $results = $stmt->fetchAll();
    
   // Formattazione dei campi
    foreach ($results as &$record) {
        // Formatta il campo data da "2025-05-17 00:00:00" a "2025-05-17"
        if (isset($record['data']) && !empty($record['data'])) {
            $record['data'] = date('Y-m-d', strtotime($record['data']));
        }
        
        // Converte i campi inviato e accettato da int (0/1) a boolean (true/false)
        if (isset($record['inviato'])) {
            $record['inviato'] = (bool) $record['inviato'];
        }
        
        if (isset($record['accettato'])) {
            $record['accettato'] = (bool) $record['accettato'];
        }
    }

    // Preparazione risposta
    $response = [
        'success' => true,
        'email' => $email,
        'count' => count($results),
        'data' => $results
    ];

    // Se non ci sono risultati
    if (empty($results)) {
        $response['message'] = 'Nessun risultato trovato per l\'email specificata';
    }

    sendJsonResponse($response);

} catch (PDOException $e) {
    sendErrorResponse('Errore database: ' . $e->getMessage());
} catch (Exception $e) {
    sendErrorResponse('Errore interno: ' . $e->getMessage());
}
?>