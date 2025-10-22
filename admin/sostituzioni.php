<?php
require_once 'functions.php';
require_once 'login_guard.php';
require_once 'template/header.php';
$docenteSelezionato = "";

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'DELETE') {
        $codice = $_POST['codice'];
        $docenteSelezionato = $_POST['docente'];
        rimuovi_sost_docente($codice);
    } elseif ($_POST['action'] == 'MAIL') {
        $codice = $_POST['codice'];
        invia_sost_docente($codice);
        $docenteSelezionato = $_POST['docente'];
    } 

}
$listaDocenti = get_lista_assenti();

if ($docenteSelezionato == "") {
    if (isset($_GET['docente'])) $docenteSelezionato = $_GET['docente'];
    else if ($listaDocenti) $docenteSelezionato =$listaDocenti[0];
}
?>

<div class="grid">

    <form action="<?= $_SERVER["PHP_SELF"] ?>" method="get">
        <label for="docente">Docente assente</label>        
        <select name="docente" id="docente" onchange="this.form.submit()">
            <option value="" disabled selected>Seleziona docente</option>
            <?php foreach ($listaDocenti as $i => $docente): ?>
                <option value="<?= $docente ?>" <?= $docenteSelezionato == $docente ? "selected" : ""; ?>>
                    <?= $docente ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>        
</div>

<div class="overflow-auto">
    <table class="striped">
        <thead>
            <th>#</th>
            <th>LUN</th>
            <th>MAR</th>
            <th>MER</th>
            <th>GIO</th>
            <th>VEN</th>
            <th>SAB</th>
        </thead>
        <tbody>
            <?php for ($ora = 1; $ora < 7; $ora++): ?>
                <tr>                     
                    <td><?= $ora; ?></td>
                    <?php foreach (WEEKDAYS as $giorno): ?>
                        <td>
                            <?php $info = get_info_sost_docente($docenteSelezionato, $ora, $giorno); ?>
                            <?php if (count($info) == 0): ?>
                                -
                            <? elseif (count($info) == 1): ?>  
                                <?php if ($info[0]['inviato']==0){ ?>
                                <form method="POST"
                                    action="<?= $_SERVER["PHP_SELF"] . "?" . http_build_query(array("docente" => $docenteSelezionato)) ?>">
                                    <input type="hidden" name="codice" value="<?= $info[0]['id'] ?>">
                                    <input type="hidden" name="docente" value="<?= $docenteSelezionato ?>">
                                    <input type="hidden" name="action" value="MAIL">
                                    <button type="submit" style="font-size:1.2rem">&#9993;</button>
                                </form>  
                                <?php } else { 
                                            if ($info[0]['accettato']==0){?>
                                            &#x231B;
                                        <?php }
                                            else { ?>
                                            &check;
                                        <?php    }
                                        }?>                                                                                         
                                <?php 
                                    echo date_format(date_create($info[0]['data']),"d/m")."<br>";
                                    echo $info[0]['docDest'];
                                    if ($info[0]['classe'] != ''){
                                        if ((substr($info[0]['classe'],-1,1)==".") || (substr($info[0]['classe'],-1,1)=="*")){
                                            $classeP=substr($info[0]['classe'],0,-1);
                                        }
                                        else {
                                            $classeP=$info[0]['classe'];
                                        }                                                                                
                                        echo "<br>".$classeP;
                                        $infoClasse=get_info_classe($classeP);                                        
                                        echo "<sup>".$infoClasse['alunni']."</sup>";
                                    }
                                    else {
                                        echo "";
                                    } ?>
                                <?php 
                                    if ( ($info[0]['aula'] != '') && (substr($info[0]['aula'],0,-1)!="PAL") && (substr($info[0]['aula'],0,-1)!="POT") ){
                                        if ((substr($info[0]['aula'],-1)==".") || (substr($info[0]['aula'],-1)=="*")){                                            
                                            $aulaP=substr($info[0]['aula'],0,-1);                                            
                                            $classe1=get_info_ora_classe($aulaP,$ora,$giorno);                                           
                                            $aulaP=$classe1[0]['aula'];
                                            
                                        }
                                        else {
                                            $aulaP=$info[0]['aula'];
                                        }
                                        echo " ";                                                                                
                                        $infoAula=get_info_aula($aulaP);
                                        
                                        echo get_colore_ambito($infoAula['ambito']);
                                        echo $aulaP;
                                        echo "<sup>".$infoAula['capienza']."</sup>";
                                       
                                    }
                                    else {
                                        echo "";
                                    }
                                ?>
                                <form method="POST"
                                    action="<?= $_SERVER["PHP_SELF"] . "?" . http_build_query(array("docente" => $docenteSelezionato)) ?>">
                                    <input type="hidden" name="codice" value="<?= $info[0]['id'] ?>">
                                    <input type="hidden" name="docente" value="<?= $docenteSelezionato ?>">
                                    <input type="hidden" name="action" value="DELETE">
                                    <button type="submit">&#128465;</button>
                                </form>                                
                            <? endif; ?>

                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div>

<?php require_once 'template/footer.php'; ?>