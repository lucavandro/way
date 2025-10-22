<?php
const WEEKDAYS = ["LUN", "MAR", "MER", "GIO", "VEN", "SAB"];
const WEEKDAYS_MAP = ["LUN" => "Lunedì", "MAR" => "Martedì", "MER" => "Mercoledì", "GIO" => "Giovedì", "VEN" => "Venerdì", "SAB" => "Sabato"];
const HOURS = ["08:15", "09:15", "10:15", "11:15", "12:15", "13:15"];
const SALT = "P9*NKP?uJYI@";
function getDBConnection()
{
    if (isset($_ENV["DEV"])) {
        $servername = $_ENV["MYSQL_HOST"];
        $username = $_ENV["MYSQL_USER"];
        $password = $_ENV["MYSQL_PASSWORD"];
        $dbname = $_ENV["MYSQL_DATABASE"];
    } else {
        $servername = "31.11.39.174";
        $dbname = "Sql1322552_2";
        $username = "Sql1322552";
        $password = "%Z7RQBN!#aYbLM";
    }

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    } else {
        return $conn;
    }
}

function getDbMW(){
	$dbHost     = "31.11.39.174"; 
	$dbUsername = "Sql1322552"; 
	$dbPassword = "%Z7RQBN!#aYbLM"; 
	$dbName     = "Sql1322552_5";

	$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName); 
 
	if ($db->connect_error) die("Connessione fallita: " . $db->connect_error); 

	return $db;
}


function get_lista_docenti()
{
    $conn = getDBConnection();

    //$sql = "SELECT distinct docente as docente FROM dada_lezioni WHERE docente<>'' ORDER BY docente";
    $sql = "SELECT nome as docente, nome_reale as nomeReale FROM docenti WHERE nome<>'' ORDER BY docente";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $docenti = [];
    while ($row = $result->fetch_assoc()) {
        $docenti[] = $row["docente"];
    }


    $stmt->close();
    $conn->close();
    return $docenti;
}

function get_docenti()
{
    $conn = getDBConnection();

    
    $sql = "SELECT nome, nome_reale FROM docenti ORDER BY nome";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $docenti = [];
    while ($row = $result->fetch_assoc()) {
        $docenti[] = $row;
    }


    $stmt->close();
    $conn->close();
    return $docenti;
}

function get_info_ora_classe($classe, $ora, $giorno)
{
    $conn = getDBConnection();
    $ora = map_ora($ora);
    $sql = "SELECT *  FROM dada_lezioni WHERE classe LIKE '" . $classe . "%' AND ora=? AND UPPER(giorno) LIKE '" . $giorno . "%'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ora);
    $stmt->execute();
    $result = $stmt->get_result();

    $info = [];
    while ($row = $result->fetch_assoc()) {
        $info[] = $row;
    }


    $stmt->close();
    $conn->close();
    return $info;

}

function get_info_ora_classe_pubblico($classe, $ora, $giorno)
{
    $conn = getDBConnection();
    $ora = map_ora($ora);
    $sql = "SELECT *  FROM dada_orario WHERE classe LIKE '" . $classe . "%' AND ora=? AND UPPER(giorno) LIKE '" . $giorno . "%'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ora);
    $stmt->execute();
    $result = $stmt->get_result();

    $info = [];
    while ($row = $result->fetch_assoc()) {
        $info[] = $row;
    }


    $stmt->close();
    $conn->close();
    return $info;

}
function get_info_ora_docente_pubblico($docente, $ora, $giorno)
{
    $conn = getDBConnection();
    $ora = map_ora($ora);
    $sql = "SELECT * FROM dada_orario WHERE docente=? AND ora=? AND UPPER(giorno) LIKE '" . $giorno . "%'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $docente, $ora);
    $stmt->execute();
    $result = $stmt->get_result();

    $info = [];
    while ($row = $result->fetch_assoc()) {
       
        $info[] = $row;
    }


    $stmt->close();
    $conn->close();
    return $info;

}

//elenco dei docenti liberi altri
function get_docs_liberi_ALT($ora,$day)
{    
    $conn = getDBConnection();

    $sql = "SELECT DISTINCT docente FROM dada_orario WHERE docente NOT IN (SELECT docente FROM dada_orario WHERE ora=? AND giorno=?) ORDER BY docente";
    $stmt = $conn->prepare($sql);

    $data = [];    
    $stmt->bind_param("ss", $ora, $day);            
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {        
        $data[]=$row['docente'];
    }

   
    $stmt->close();
    $conn->close();
    return $data;    
}

//elenco dei docenti liberi di RIC
function get_docs_liberi_RIC($ora,$day)
{    
    $conn = getDBConnection();

    $sql = "SELECT DISTINCT docente FROM dada_orario WHERE ora=? AND giorno=? AND materia='RIC' ORDER BY docente";
    $stmt = $conn->prepare($sql);

    $data = [];    
    $stmt->bind_param("ss", $ora, $day);            
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {        
        $data[]=$row['docente'];
    }

   
    $stmt->close();
    $conn->close();
    return $data;    
}

//elenco dei docenti liberi di POT
function get_docs_liberi_POT($ora,$day)
{    
    $conn = getDBConnection();

    $sql = "SELECT DISTINCT docente FROM dada_orario WHERE ora=? AND giorno=? AND materia='POT' ORDER BY docente";
    $stmt = $conn->prepare($sql);

    $data = [];    
    $stmt->bind_param("ss", $ora, $day);            
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {        
        $data[]=$row['docente'];
    }

   
    $stmt->close();
    $conn->close();
    return $data;    
}

function get_dip($materia)
{
    switch ($materia){
        case 'COD':
            return 'INF';
        case 'GEO':
            return 'ITA';
        case 'LAT':
            return 'ITA';
        case 'FIS':
            return 'MAT';
        case 'MIC':
            return 'SCI';
        case 'STO':
            return 'FIL';
        case 'DIR':
            return 'FIL';
        default:
            return $materia;
    }
}

//elenco dei docenti liberi della stessa materia
function get_docs_liberi_MAT($ora,$day,$materia)
{
    //$dip=["'DIS'","'INF','COD'","'ING'","'ITA','LAT','GEO'","'MAT','FIS'","'REL'","SCI,MIC","SMO","'STO','FIL','DIR'"];

    $materia=get_dip($materia);

    $conn = getDBConnection();

    $sql = "SELECT DISTINCT docente FROM dada_orario WHERE docente NOT IN (SELECT docente FROM dada_orario WHERE ora=? AND giorno=?) AND docente IN (SELECT docente FROM dada_doc_ore WHERE materia =?) ORDER BY docente";
    $stmt = $conn->prepare($sql);

    $data = [];    
    $stmt->bind_param("sss", $ora, $day,$materia);            
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {        
        $data[]=$row['docente'];
    }

   
    $stmt->close();
    $conn->close();
    return $data;    
}

//elenco dei docenti liberi della stessa classe
function get_docs_liberi_CLA($ora,$day,$classe)
{    
    $conn = getDBConnection();

    $sql = "SELECT DISTINCT docente FROM dada_orario WHERE docente NOT IN (SELECT docente FROM dada_orario WHERE ora=? AND giorno=?) AND docente IN (SELECT docente FROM dada_doc_ore WHERE classe=?) ORDER BY docente";
    $stmt = $conn->prepare($sql);

    $data = [];    
    $stmt->bind_param("sss", $ora, $day,$classe);            
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {        
        $data[]=$row['docente'];
    }

   
    $stmt->close();
    $conn->close();
    return $data;    
}

function get_docs_occ_CLA($ora,$day,$classe)
{    
    $conn = getDBConnection();

    $sql = "SELECT DISTINCT docente FROM dada_orario WHERE docente IN (SELECT docente FROM dada_orario WHERE ora=? AND giorno=?) AND docente IN (SELECT docente FROM dada_doc_ore WHERE classe=?) ORDER BY docente";
    $stmt = $conn->prepare($sql);

    $data = [];    
    $stmt->bind_param("sss", $ora, $day,$classe);            
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {        
        $data[]=$row['docente'];
    }

   
    $stmt->close();
    $conn->close();
    return $data;    
}

function get_ore_giorno_docente($docente,$giorno){
    $conn = getDBConnection();

    $sql = "SELECT COUNT(ora) AS totOre, MIN(ora) AS inizio, MAX(ora) AS fine FROM `dada_orario` WHERE docente=? AND giorno=? ORDER BY ora";   
    $stmt = $conn->prepare($sql);

    $data = [];    
    $stmt->bind_param("ss", $docente, $giorno);            
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        if ($row['totOre']>0){
            $data=$row['totOre']."h "." ".$row['inizio']." ⭤ ";
            $oraFine=array_search($row['fine'],HOURS);
            if ($oraFine<5)
                $data.=HOURS[$oraFine+1] ;
            else
                $data.="14:15";
        }            
        else {
            $data= "Non disponibile";
        }
            
    }
    
   
    $stmt->close();
    $conn->close();
    return $data; 
}

function get_info_aula($aula)
{
    $conn = getDBConnection();

    $sql = "SELECT *  FROM dada_aule WHERE nomeaula=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $aula);

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $stmt->close();
    $conn->close();
    return $row;


}

function get_info_classe($classe)
{
    $conn = getDBConnection();

    $sql = "SELECT *  FROM dada_classi WHERE classe=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $classe);

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $stmt->close();
    $conn->close();
    return $row;


}

function get_colore_ambito($codiceAmbito)
{
    switch ($codiceAmbito) {
        case 1:
            return "<span style='color:blue;font-size:0.4rem'>&#11044;</span>";
        case 2:
            return "<span style='color:green;font-size:0.4rem'>&#11044;</span>";
        case 3:
            return "<span style='color:#ffdc0a;font-size:0.4rem'>&#11044;</span>";
        case 4:
            return "<span style='color:red;font-size:0.4rem'>&#11044;</span>";
        case 5:
            return "<span style='color:gray;font-size:0.4rem'>&#11044;</span>";
        default:
        return "<span style='color:white;font-size:0.4rem'>&#11044;</span>";
    }
}
function riepilogo_ore_docenti($docente)
{
    $conn = getDBConnection();

    $sql = "SELECT *  FROM riepilogo_ore_docenti WHERE docente=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $docente);
    try {
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

    } catch (\Throwable $th) {
        $sql = "CREATE OR REPLACE VIEW riepilogo_ore_docenti AS SELECT docente, COUNT(*) as ore, COUNT(CASE WHEN materia NOT IN ('RIC', 'POT') THEN 1 END) as lezione, COUNT(CASE WHEN materia='RIC' THEN 1 END) as ricevimento, COUNT(CASE WHEN materia='POT' THEN 1 END) as potenziamento FROM `dada_lezioni` GROUP BY docente ORDER BY `dada_lezioni`.`docente` ASC;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $row = riepilogo_ore_docenti($docente);
    }

    $stmt->close();
    $conn->close();
    return $row;


}

function get_conflitti_aula()
{
    $conn = getDBConnection();
    // Conflitti aula
    //$sql = 'SELECT d1.aula, d1.giorno as giorno, d1.ora as ora, COUNT(*) as num FROM `dada_lezioni` as d1 JOIN `dada_lezioni` as d2 ON d1.aula=d2.aula AND d1.codice <> d2.codice and d1.giorno=d2.giorno AND d1.ora = d2.ora AND d1.classe <> "" AND d2.classe <> "" GROUP BY d1.aula, d1.giorno, d1.ora;';
    $sql = 'SELECT d1.aula, d1.giorno as giorno, d1.ora as ora, COUNT(*) as num FROM `dada_lezioni` as d1 JOIN `dada_lezioni` as d2 ON d1.aula=d2.aula AND d1.codice <> d2.codice and d1.giorno=d2.giorno AND d1.ora = d2.ora AND d1.classe <> "" AND d2.classe <> "" AND d1.materia<>"INC" AND d2.materia<>"INC" GROUP BY d1.aula, d1.giorno, d1.ora;';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $data;
}

/**
 * / Trova docenti diversi assegnati alla stessa classe in un dato giorno/ora
 * @param mixed $aula
 * @param mixed $giorno
 * @param mixed $ora
 * @return array
 */

function get_conflitti_classe()
{
    $conn = getDBConnection();
    // Conflitti classe
    //$sql = 'SELECT d1.classe, d1.giorno as giorno, d1.ora as ora, COUNT(*) as num FROM `dada_lezioni` as d1 JOIN `dada_lezioni` as d2 ON d1.classe=d2.classe AND d1.codice <> d2.codice and d1.giorno=d2.giorno AND d1.ora = d2.ora AND d1.classe <> "" AND d2.classe <> "" GROUP BY d1.classe, d1.giorno, d1.ora;';
    $sql = 'SELECT d1.classe, d1.giorno as giorno, d1.ora as ora, COUNT(*) as num FROM `dada_lezioni` as d1 JOIN `dada_lezioni` as d2 ON d1.classe=d2.classe AND d1.codice <> d2.codice and d1.giorno=d2.giorno AND d1.ora = d2.ora AND d1.classe <> "" AND d2.classe <> "" AND d1.materia<>"INC" AND d2.materia<>"INC" GROUP BY d1.classe, d1.giorno, d1.ora;';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $data;
}

function map_ora($i)
{
    return HOURS[$i - 1];
}

function login($username, $password)
{
    try {
        $hash = get_user($username);
        return check_password($password, $hash);
    } catch (\Throwable $th) {
        return false;
    }


}

function get_user($username)
{
    $utenti = [
        "luca" => "126f3ab4774a6fc59d46c43adfc1acfa",
        "ivano" => "014a886bf1d2eb1a4a7a2f8ecc902587",
        "rosa" => "b0fbcdb8fd83a8ddb1ba4a2459be5987"
    ];
    return $utenti[$username];
}
function check_password($password, $hash)
{
    return md5($password . SALT) == $hash;
}

function add_sostituzione($docenteDest,$docenteSost,$giorno,$ora,$classe,$aula,$data,$note)
{
        $conn = getDBConnection();
       
        $sql = "SELECT * FROM `sostituzioni` WHERE docDest=? AND giorno=? AND ora=? AND data=?";    
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $docenteDest, $giorno, $ora, $data);
        $stmt->execute();   
        $stmt->store_result();        
        if ($stmt->num_rows==0){
            $sql = "SELECT * FROM `sostituzioni` WHERE classe=? AND giorno=? AND ora=? AND data=?";    
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $classe, $giorno, $ora, $data);
            $stmt->execute();   
            $stmt->store_result();        
            if ($stmt->num_rows==0){
                $sql = "INSERT INTO `sostituzioni`(docDest,docSost,giorno,ora,classe,aula,data,note) VALUES(?,?,?,?,?,?,?,?)";    
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssss", $docenteDest, $docenteSost,$giorno,$ora,$classe,$aula,$data,$note);
                $stmt->execute();      
            }
            else {
                show_message("Esiste già una sostituzione per questa data.");
            }
        }
        else {
            show_message("Il docente è già impegnato in un'altra sotituzione");
        }

        
}

function get_sostituzioni($doc)
{
    $conn = getDBConnection();
    
    $sql = "SELECT COUNT(id) AS totSost FROM sostituzioni WHERE docDest=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s",$doc);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = 0;
    while ($row = $result->fetch_assoc()) {
        $data = $row['totSost'];
    }
    $stmt->close();
    $conn->close();    
    return $data;
}

function get_lista_sostituzioni($d)
{
    $conn = getDBConnection();
    
    $sql = "SELECT * FROM `sostituzioni` WHERE data=? ORDER BY  data,docSost,giorno,ora";    
    $stmt = $conn->prepare($sql);    
    $stmt->bind_param("s", $d);    
    $stmt->execute(); 
    $result = $stmt->get_result();
    $data=[];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $data;
}
function get_lista_assenti()
{
    $conn = getDBConnection();
       
    $sql = "SELECT DISTINCT docSost as docente FROM `sostituzioni` WHERE data>=CURRENT_DATE()";    
    $stmt = $conn->prepare($sql);   
    $stmt->execute(); 
    $result = $stmt->get_result();
    $data=[];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row['docente'];
    }
    $stmt->close();
    $conn->close();
    return $data;

}

function get_info_sost_docente($docente, $ora, $giorno)
{
    $conn = getDBConnection();
    $ora = map_ora($ora);
    $sql = "SELECT * FROM sostituzioni WHERE docSost=? AND ora=? AND UPPER(giorno) LIKE '" . $giorno . "%' AND data>=CURRENT_DATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $docente, $ora);
    $stmt->execute();
    $result = $stmt->get_result();

    $info = [];
    while ($row = $result->fetch_assoc()) {
       
        $info[] = $row;
    }


    $stmt->close();
    $conn->close();
    return $info;

}

function rimuovi_sost_docente($codice)
{
    $conn = getDBConnection();

    $sql = "SELECT * FROM sostituzioni WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codice);   
    $stmt->execute();

    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        if ($row['inviato']==1) invia_sost_annullata($codice);
        if ($row['inviato']==0) canc_sost($codice);
    }
   
    $stmt->close();
    $conn->close();        
}


function get_nome_email_docente($docente)
{
    $conn = getDBConnection();
    $sql = "SELECT * FROM docenti WHERE nome=?";    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $docente);
    $stmt->execute();
    $result = $stmt->get_result();

    $info = [];
    while ($row = $result->fetch_assoc()) {       
        $info['nome_reale'] = $row['nome_reale'];
        $info['email'] = $row['email'];
    }    
    $stmt->close();
    $conn->close();
    return $info;
}

function invia_sost_docente($codice)
{
    $conn = getDBConnection();
    
    $sql = "SELECT * FROM sostituzioni WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codice);   
    $stmt->execute();

    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $infoTo=get_nome_email_docente($row['docDest']);
        $infoFrom=get_nome_email_docente($row['docSost']);

        $to=$infoTo['email'];
                
        $msg='<html><head><meta Content-type:text/html><meta charset=UTF-8><link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"></head><body>';
        $msg.="<div class='w3-card w3-margin w3-padding'>";
        $msg.="<div class='w3-panel w3-indigo'>Comunicazione da WAY CORTESE</div>";
        $msg.="<p>Ciao ".$infoTo['nome_reale'].",<br>";
        $msg.="sostituirai <strong>".$infoFrom['nome_reale']."</strong> ";
        $msg.=" ".$row['giorno']." <strong>".date_format(date_create($row['data']),"d/m/Y"). "</strong> alle <strong>".$row['ora']. "</strong> in <strong>". $row['classe']."</strong> aula <strong>".$row['aula']."</strong></p>";
        $msg.="<p><strong>Altre informazioni:&nbsp;</strong>".$row['note']."</p>";        
        $msg.="<button class='w3-button w3-green w3-center'><a href='https://www.liceoscientificocortese.edu.it/app/orario-admin/confermaSost.php?id=".$row['id']."'>INVIA CONFERMA</a></button></div>";

        $msg.="</body></html>";

        $subject = 'Liceo Scientifico "Nino Cortese" - Sostituzione';
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: orario@lscortese.com' . "\r\n" .'Reply-To: orario@lscortese.com' . "\r\n" .'X-Mailer: PHP/' . phpversion();

        if (mail($to, $subject, $msg, $headers)){
            $sql = "UPDATE sostituzioni SET inviato=1 WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $codice);   
            $stmt->execute();
        }
        else {
            echo "Messaggio non inviato";
        }               
    }
    $stmt->close();
    $conn->close();    
}
function invia_sost_annullata($codice)
{
    $conn = getDBConnection();
    
    $sql = "SELECT * FROM sostituzioni WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codice);   
    $stmt->execute();

    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $infoTo=get_nome_email_docente($row['docDest']);
        $infoFrom=get_nome_email_docente($row['docSost']);

        $to=$infoTo['email'];
                
        $msg='<html><head><meta Content-type:text/html><meta charset=UTF-8><link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"></head><body>';
        $msg.="<div class='w3-card w3-margin w3-padding'>";
        $msg.="<div class='w3-panel w3-deep-orange'>Comunicazione da WAY CORTESE</div>";
        $msg.="<p>Ciao ".$infoTo['nome_reale'].",<br>";
        $msg.="La sostituzione di <strong>".$infoFrom['nome_reale']."</strong> ";
        $msg.=" ".$row['giorno']." <strong>".date_format(date_create($row['data']),"d/m/Y"). "</strong> alle <strong>".$row['ora']. "</strong> in <strong>". $row['classe']."</strong> aula <strong>".$row['aula']."</strong>";        
        $msg.=" è stata annullata.</p></div>";

        $msg.="</body></html>";

        $subject = 'Liceo Scientifico "Nino Cortese" - Sostituzione Annullata';
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: orario@lscortese.com' . "\r\n" .'Reply-To: orario@lscortese.com' . "\r\n" .'X-Mailer: PHP/' . phpversion();

        if (mail($to, $subject, $msg, $headers)){
            $sql = "DELETE FROM sostituzioni WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $codice);   
            $stmt->execute();
        }
        else {
            echo "Messaggio non inviato";
        }               
    }
    $stmt->close();
    $conn->close();    
}
function canc_sost($codice)
{
    $conn = getDBConnection();
    $sql = "DELETE FROM sostituzioni WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codice);   
    $stmt->execute();

    $stmt->close();
    $conn->close();    
}