<?php
/**
 * WAY - App orario del Liceo Scientifico Nino Cortese
 * 
 * Copyright (c) 2025, WAY Project
 * Licensed under the BSD 3-Clause License
 * 
 * API per i dati dell'applicazione (orari, classi, docenti)
 */

// Importa la configurazione centralizzata
require_once 'config.php';

// Crea connessione al database
$conn = createMySQLiConnection();
if (!$conn) {
    sendErrorResponse("Connessione al database fallita");
}

// Variabile per i dati della risposta
$response = [];


// DOCENTI

// Nomi completi
$sql_nomi_completi = "SELECT nome, nome_reale FROM docenti";
$docenti_map = array();
// Esecuzione della query nomi completi
$result = $conn->query($sql_nomi_completi);

if($result){
    // Fetch dei risultati
    while ($row = $result->fetch_assoc()) {
        $docenti_map[$row['nome']] = $row['nome_reale'];
    }
}

// Query per selezionare tutti i docenti
$sql_docenti = "SELECT DISTINCT docente FROM dada_orario ORDER BY docente";

// Esecuzione della query
$result = $conn->query($sql_docenti);

if ($result) {
    // Array per memorizzare i risultati
    $docenti = [];

    // Fetch dei risultati
    while ($row = $result->fetch_assoc()) {
        if(isset($docenti_map[$row['docente']])){
            $docenti[] = $docenti_map[$row['docente']] ;
        }else {
            $docenti[] = strtoupper($row['docente']);
        }
    }
    
    $response['docenti'] = $docenti;
}

// AULE

// Query per selezionare tutte le aule
$sql_aule = 'SELECT DISTINCT aula FROM dada_orario WHERE aula IS NOT NULL and aula NOT LIKE "%*" and aula <>  "" ORDER BY aula';

// Esecuzione della query
$result = $conn->query($sql_aule);

if ($result) {
    // Array per memorizzare i risultati
    $aule = [];

    // Fetch dei risultati
    while ($row = $result->fetch_assoc()) {
        $aule[] = $row['aula'];
    }
    
    $response['aule'] = $aule;
}


// CLASSI

// Query per selezionare tutte le classi
$sql_classi = 'SELECT DISTINCT classe FROM dada_orario WHERE classe IS NOT NULL and classe NOT LIKE "%*" and classe NOT LIKE "%." and classe <>  ""  ORDER BY classe';

// Esecuzione della query
$result = $conn->query($sql_classi);

if ($result) {
    // Array per memorizzare i risultati
    $classi = [];

    // Fetch dei risultati
    while ($row = $result->fetch_assoc()) {
        $classi[] = $row['classe'];
    }
    
    $response['classi'] = $classi;
}



// Query per selezionare tutti i dati dalla tabella dada_lezioni
$sql = 'SELECT UPPER(LEFT(giorno, 3)) as day, ora, classe, materia, UPPER(docente) as docente, aula FROM dada_orario WHERE docente not in (SELECT DISTINCT docente from dada_lezioni WHERE materia LIKE "%*") ORDER BY docente, day, ora';
// Esecuzione della query
$result = $conn->query($sql);

if ($result) {
    // Array per memorizzare i risultati
    $data = [];

    // Fetch dei risultati
    while ($row = $result->fetch_assoc()) {

        if(isset($docenti_map[$row['docente']])){
            $row['docente_abbr'] = $row['docente'];
            $row['docente'] = $docenti_map[$row['docente']];
        }
        $data[] = $row;
    }
    
    $response['data'] = $data;
}

// Query per selezionare tutti i dati dalla tabella dada_lezioni
$sql = 'SELECT * FROM dada_lezioni_inclusione ORDER BY docente, day, ora';
// Esecuzione della query
$result = $conn->query($sql);

if ($result) {
    // Array per memorizzare i risultati
  

    // Fetch dei risultati
    while ($row = $result->fetch_assoc()) {
        $current_row = clone $row;
        if(isset($docenti_map[$row['docente']])){
            $current_row['docente'] = $docenti_map[$row['docente']];
            $current_row['docente_abbr'] = $row['docente'];
        }
        $response['data'][] = $current_row;
        
       
    }
    
}

session_start();
if (isset($_SESSION['email'])){
    $response['user'] = $_SESSION['email'];
}
if (isset($_COOKIE['email'])){
    $response['user'] = $_COOKIE['email'];
}
$json = json_encode($response);
if(!$json){
    echo json_encode(["error" => "JSON encoding error"]);
} else {
    echo $json;
}

// Chiusura della connessione
$conn->close();
?>