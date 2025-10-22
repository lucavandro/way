<?php
require_once 'functions.php';
require_once 'login_guard.php';
require_once 'template/header.php';
$docenteSelezionato = "";

if (isset($_POST['action'])) {
    if ($_POST['action']=='ADD_SOST') { 
        add_sostituzione($_POST['docDest'],$_POST['docS'],$_POST['giornoS'],$_POST['oraS'],$_POST['classeS'],$_POST['aulaS'],$_POST['dataS'],$_POST['noteS']);                
    }
}

//$listaDocenti = get_lista_docenti();
$listaDocenti=get_docenti();

if ($docenteSelezionato == "") {
    $docenteSelezionato = $_GET['docente'] ?? $listaDocenti[0]['nome'];
}
?>
<script language="javascript">
    function dataControl(dataScelta,giorno){

        var days=new Array();
        days.push("DOM","LUN","MAR","MER","GIO","VEN","SAB");

        var ds=new Date(dataScelta);
        var nd=new Date();
        nd.setHours(0,1,1);
        if (ds<nd) {
            alert("Data precedente ad oggi");
            return 0;
        }            
        else {
            if (giorno!=days[ds.getDay()]){
                alert("Giorni non corrispondenti");
                return 0;
            }
            else {
                return 1;
            }
        }            
    }
    function submitSost(dataScelta,giorno){
        if (document.getElementById('docDest').selectedIndex<=0)
            alert("Docente non selezionato")
        if ( (dataControl(dataScelta,giorno)==1) && (document.getElementById('docDest').selectedIndex>0) ) 
            document.getElementById('formSost').submit();
    }

    function submitSostNew(dataScelta,giorno){
        if (document.getElementById('docDest').value=="")
            alert("Docente non selezionato")
        else {
            if ( (dataControl(dataScelta,giorno)==1) && (document.getElementById('docDest').value!=="") ) 
                document.getElementById('formSost').submit();
            else 
                alert("Data non selezionata");
        }
        
    }

    function cambia_doc(campoS){
        document.getElementById('docDest').value=campoS.value;
    }

    function showLista(l){
        switch (l){
            case 'P':
                document.getElementById('titoloLista').innerHTML="<h5>Potenziamento</h5>";
                document.getElementById('docDestP').style.display="block";
                document.getElementById('docDestC').style.display="none";
                document.getElementById('docDestM').style.display="none";
                document.getElementById('docDestR').style.display="none";
                document.getElementById('docDestA').style.display="none";
                document.getElementById('docDestN').style.display="none";
                document.getElementById('docDestT').style.display="none";
                break;
            case 'C':
                document.getElementById('titoloLista').innerHTML="<h5>Stessa classe liberi</h5>";
                document.getElementById('docDestC').style.display="block";
                document.getElementById('docDestP').style.display="none";
                document.getElementById('docDestM').style.display="none";
                document.getElementById('docDestR').style.display="none";
                document.getElementById('docDestA').style.display="none";
                document.getElementById('docDestN').style.display="none";
                document.getElementById('docDestT').style.display="none";
                break;
            case 'M':
                document.getElementById('titoloLista').innerHTML="<h5>Stessa materia</h5>";
                document.getElementById('docDestC').style.display="none";
                document.getElementById('docDestP').style.display="none";
                document.getElementById('docDestM').style.display="block";
                document.getElementById('docDestR').style.display="none";
                document.getElementById('docDestA').style.display="none";
                document.getElementById('docDestN').style.display="none";
                document.getElementById('docDestT').style.display="none";
                break;
            case 'R':
                document.getElementById('titoloLista').innerHTML="<h5>Ricevimento</h5>";
                document.getElementById('docDestC').style.display="none";
                document.getElementById('docDestP').style.display="none";
                document.getElementById('docDestM').style.display="none";
                document.getElementById('docDestR').style.display="block";
                document.getElementById('docDestA').style.display="none";
                document.getElementById('docDestN').style.display="none";
                document.getElementById('docDestT').style.display="none";
                break;

            case 'A':
                document.getElementById('titoloLista').innerHTML="<h5>Altri liberi</h5>";
                document.getElementById('docDestC').style.display="none";
                document.getElementById('docDestP').style.display="none";
                document.getElementById('docDestM').style.display="none";
                document.getElementById('docDestR').style.display="none";
                document.getElementById('docDestA').style.display="block";
                document.getElementById('docDestN').style.display="none";
                document.getElementById('docDestT').style.display="none";
                break;
            
            case 'N':
                document.getElementById('titoloLista').innerHTML="<h5>Stessa classe occupati</h5>";
                document.getElementById('docDestC').style.display="none";
                document.getElementById('docDestP').style.display="none";
                document.getElementById('docDestM').style.display="none";
                document.getElementById('docDestR').style.display="none";
                document.getElementById('docDestA').style.display="none";
                document.getElementById('docDestN').style.display="block";
                document.getElementById('docDestT').style.display="none";
                break;
            
            case 'T':
                document.getElementById('titoloLista').innerHTML="<h5>Tutti</h5>";
                document.getElementById('docDestC').style.display="none";
                document.getElementById('docDestP').style.display="none";
                document.getElementById('docDestM').style.display="none";
                document.getElementById('docDestR').style.display="none";
                document.getElementById('docDestA').style.display="none";
                document.getElementById('docDestN').style.display="none";
                document.getElementById('docDestT').style.display="block";
                break;

            default:
                break;
        }
        
    }
</script>

<div class="grid">
    <article>
        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="get">
            <label for="docente">Docente</label>
            <select name="docente" id="docente" onchange="this.form.submit()">
                <?php foreach ($listaDocenti as $i => $docente): ?>
                    <option value="<?= $docente['nome'] ?>" <?= $docenteSelezionato == $docente['nome'] ? "selected" : ""; ?>>
                        <?= $docente['nome_reale'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </article>
    <article><b>Sostituzioni</b> <?php $totS=get_sostituzioni($docenteSelezionato); echo $totS; ?></article>    
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
                            <?php $info = get_info_ora_docente_pubblico($docenteSelezionato, $ora, $giorno); ?>
                            <?php if (count(value: $info) == 0): 
                                    echo " - ";
                                elseif (count($info) == 1): ?>
                                <form method="POST"
                                    action="<?= $_SERVER["PHP_SELF"] . "?" . http_build_query(array("docente" => $docenteSelezionato)) ?>">
                                    <input type="hidden" name="codice" value="<?= $info[0]['codice'] ?>">
                                    <input type="hidden" name="docente" value="<?= $docenteSelezionato ?>">
                                    <input type="hidden" name="ora" value="<?= $ora ?>">
                                    <input type="hidden" name="giorno" value="<?= $giorno ?>">
                                    <input type="hidden" name="classe" value="<?= $info[0]['classe'] ?>">
                                    <input type="hidden" name="materia" value="<?= $info[0]['materia'] ?>">
                                    <input type="hidden" name="aula" value="<?= $info[0]['aula'] ?>">
                                    <input type="hidden" name="action" value="SOST">
                                    <button type="submit" style="font-size:1.5rem">⥂</button>
                                </form>
                                <?= $info[0]['materia']; ?>
                                <?php 
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
                                            $classe1=get_info_ora_classe_pubblico($aulaP,$ora,$giorno);                                           
                                            $aulaP=$classe1[0]['aula'];
                                            
                                        }
                                        else {
                                            $aulaP=$info[0]['aula'];
                                        }
                                        echo "<br>";                                                                                
                                        $infoAula=get_info_aula($aulaP);
                                        
                                        echo get_colore_ambito($infoAula['ambito']);
                                        echo $aulaP;
                                        echo "<sup>".$infoAula['capienza']."</sup>";
                                       
                                    }
                                    else {
                                        echo "";
                                    }
                                ?>                              
                            <? endif; ?>

                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div>

<dialog id="sostDialog" <?php if($_POST['action'] == 'SOST') echo "open";    ?> >
    <div class="container">
        <h3>Sostituzione docente</h3>
        <div>            
            <form id="formSost" name="formSost" method="POST"
                action="<?= $_SERVER["PHP_SELF"] . "?" . http_build_query(array("docente" => $docenteSelezionato)) ?>">
                <?php $info = get_info_ora_docente_pubblico($docenteSelezionato, $_POST['ora'], $_POST['giorno']); ?>
                <input type="hidden" name="action" value="ADD_SOST">
                <?php $msg= $docenteSelezionato." in ".$_POST['classe']. " ".WEEKDAYS_MAP[$_POST['giorno']]." alle ".HOURS[$_POST['ora']-1]." nella ".$info[0]['aula']; ?>
                <input type="hidden" name="msg" id="msg" value="<?= $msg ?>">                
                <input type="hidden" name="docS" id="docS" value="<?= $docenteSelezionato ?>">
                <input type="hidden" name="giornoS" id="giornoS" value="<?= WEEKDAYS_MAP[$_POST['giorno']] ?>">
                <input type="hidden" name="oraS" id="oraS" value="<?= HOURS[$_POST['ora']-1] ?>">
                <input type="hidden" name="classeS" id="classeS" value="<?= $_POST['classe'] ?>">
                <input type="hidden" name="aulaS" id="aulaS" value="<?= $info[0]['aula'] ?>">                
                <h4><?= $msg ?></h4>
                <!--<fieldset> -->            
                <div class="grid">
                    <!--<label for="dataS">Data</label>-->
                    <input type="date" id="dataS" name="dataS" value="<?= date("Y-m-d")?>" onchange="dataControl(this.value,'<?=$_POST['giorno'] ?>')">
                    <!--<label for="docDest">Docente scelto</label>-->
                    <input type="text" readonly name="docDest" id="docDest" value="" placeholder="Docente scelto">
                </div>               
                <input type="text" id="noteS" name="noteS" placeholder="Informazioni aggiuntive">
                        
                <div class="grid">
                    <input type="button" class="secondary" onclick="showLista('P');" value="POT">
                    <input type="button" class="secondary" onclick="showLista('C');" value="<?=$_POST['classe']?> Lib">
                    <input type="button" class="secondary" onclick="showLista('M');" value="<?= $_POST['materia']?> Lib">
                    <input type="button" class="secondary" onclick="showLista('R');" value="RIC">
                    <input type="button" class="secondary" onclick="showLista('A');" value="Altri Lib">
                    <input type="button" class="secondary" onclick="showLista('N');" value="<?=$_POST['classe']?> OCC">
                    <input type="button" class="secondary" onclick="showLista('T');" value="Tutti">
                </div>      
                <div id="titoloLista" class="grid"><h5>Potenziamento</h5></div>         

                <select name="docDestP" id="docDestP" size="10" onchange="cambia_doc(this);">                            
                    <!--<option disabled value="noPot">Docenti di Potenziamento</option>-->
                    <?php foreach (get_docs_liberi_POT(HOURS[$_POST['ora']-1],WEEKDAYS_MAP[$_POST['giorno']]) as $docSm): ?>                                
                            <option value="<?= $docSm ?>"><?= $docSm ?><?= " (".get_ore_giorno_docente($docSm,WEEKDAYS_MAP[$_POST['giorno']]).")" ?></option>                              
                    <?php endforeach; ?>
                </select>
                
                <select name="docDestC" id="docDestC" size="10" style="display:none" onchange="cambia_doc(this);">
                    <!--<option disabled value="noSC">Docenti stessa classe liberi</option>-->
                    <?php foreach (get_docs_liberi_CLA(HOURS[$_POST['ora']-1],WEEKDAYS_MAP[$_POST['giorno']], $_POST['classe']) as $docSm): ?>
                        <?php if (get_ore_giorno_docente($docSm,WEEKDAYS_MAP[$_POST['giorno']])!="Non disponibile") {?>
                        <option value="<?= $docSm ?>"><?= $docSm ?><?= " (".get_ore_giorno_docente($docSm,WEEKDAYS_MAP[$_POST['giorno']]).")" ?></option>                    
                        <?php } ?>
                    <?php endforeach; ?>
                </select>
                
                <select name="docDestM" id="docDestM" size="10" style="display:none" onchange="cambia_doc(this);">
                    <!--<option disabled value="noSM">Docenti stessa materia</option>-->
                    <?php foreach (get_docs_liberi_MAT(HOURS[$_POST['ora']-1],WEEKDAYS_MAP[$_POST['giorno']], $_POST['materia']) as $docSm): ?>
                        <?php if (get_ore_giorno_docente($docSm,WEEKDAYS_MAP[$_POST['giorno']])!="Non disponibile") {?>
                        <option value="<?= $docSm ?>"><?= $docSm ?><?= " (".get_ore_giorno_docente($docSm,WEEKDAYS_MAP[$_POST['giorno']]).")" ?></option> 
                        <?php } ?>                  
                    <?php endforeach; ?>   
                </select>
                
                <select name="docDestR" id="docDestR" size="10" style="display:none" onchange="cambia_doc(this);">
                    <!--<option disabled value="noPot">Docenti con Ricevimento</option>-->
                    <?php foreach (get_docs_liberi_RIC(HOURS[$_POST['ora']-1],WEEKDAYS_MAP[$_POST['giorno']]) as $docSm): ?>                                
                            <option value="<?= $docSm ?>"><?= $docSm ?><?= " (".get_ore_giorno_docente($docSm,WEEKDAYS_MAP[$_POST['giorno']]).")" ?></option>                              
                    <?php endforeach; ?>
                </select>
                
                <select name="docDestA" id="docDestA" size="10" style="display:none" onchange="cambia_doc(this);">
                    <!--<option disabled value="noAD">Altri docenti liberi</option>-->
                    <?php foreach (get_docs_liberi_ALT(HOURS[$_POST['ora']-1],WEEKDAYS_MAP[$_POST['giorno']]) as $docSm): ?>
                        <?php if (get_ore_giorno_docente($docSm,WEEKDAYS_MAP[$_POST['giorno']])!="Non disponibile") {?>
                        <option value="<?= $docSm ?>"><?= $docSm ?><?= " (".get_ore_giorno_docente($docSm,WEEKDAYS_MAP[$_POST['giorno']]).")" ?></option> 
                        <?php } ?>                   
                    <?php endforeach; ?>  
                </select>
                <select name="docDestN" id="docDestN" size="10" style="display:none" onchange="cambia_doc(this);">
                    <!--<option disabled value="noAD">Stessa classe occupati</option>-->
                    <?php foreach (get_docs_occ_CLA(HOURS[$_POST['ora']-1],WEEKDAYS_MAP[$_POST['giorno']], $_POST['classe']) as $docSm): ?>
                        <?php if (get_ore_giorno_docente($docSm,WEEKDAYS_MAP[$_POST['giorno']])!="Non disponibile") {?>
                        <option value="<?= $docSm ?>"><?= $docSm ?><?= " (".get_ore_giorno_docente($docSm,WEEKDAYS_MAP[$_POST['giorno']]).")" ?></option>                    
                        <?php } ?>
                    <?php endforeach; ?>
                </select>
                <select name="docDestT" id="docDestT" size="10" style="display:none" onchange="cambia_doc(this);">
                    <!--<option disabled value="noAD">Tutti</option>-->
                    <?php foreach (get_lista_docenti() as $docSm): ?>                        
                        <option value="<?= $docSm ?>"><?= $docSm ?><?= " (".get_ore_giorno_docente($docSm,WEEKDAYS_MAP[$_POST['giorno']]).")" ?></option>                                           
                    <?php endforeach; ?>
                </select>
                    
                
                <!--</fieldset>-->                                        
            </form>            
        </div>  
        <div class="grid">
            <button type="button" onclick="submitSostNew(document.getElementById('dataS').value,'<?=$_POST['giorno'] ?>')">Carica</button>                    
            <button type="button" class="secondary" onclick="document.getElementById('sostDialog').close()">Chiudi</button>        
        </div>                                
            
    </div>
</dialog>

<?php require_once 'template/footer.php'; ?>