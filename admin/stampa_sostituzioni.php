<?php
require_once 'functions.php';
require_once 'login_guard.php';
require_once 'template/header.php';

if (isset($_GET['dataS']))
    $d=$_GET['dataS'];
else
    $d=date("Y-m-d");

$dataSel=DateTimeImmutable::createFromFormat('Y-m-d', $d);
$listaDocenti = get_lista_sostituzioni($d);

?>

<form id="selDate" name="selDate" method="GET">
    <div class="grid">
        <input type="date" id="dataS" name="dataS" value="<?= $d ?>">
        <button type="submit">Cerca</button>
    </div>    
</form>
<h2>Sostituzioni <?= $dataSel->format("d/m/Y") ?></h2>

<table class="w3-tiny w3-striped w3-centered">
    <thead>
        <tr>            
            <th>ASSENTE</th>
            <th>SOSTITUTO</th>
            <th>ORA</th>
            <th>CLASSE</th>
            <th>AULA</th>
            <th>NOTE</th>
        </tr>
    </thead>
    <tbody>       
        <?php 
        $i=0;
          
        
        foreach ($listaDocenti as $doc):                         
            if ($i%2) $c="w3-light-gray w3-medium no-padding"; else $c="w3-white w3-medium no-padding";
                $i++;
        ?>
            <tr>
                <td class="<?php echo $c; ?>" ><?= $doc['docSost']; ?></td>
                <td class="<?php echo $c; ?>" ><?= $doc['docDest']; ?></td>
                <td class="<?php echo $c; ?>" ><?= $doc['ora']; ?></td>
                <td class="<?php echo $c; ?>" ><?= $doc['classe']; ?></td>
                <td class="<?php echo $c; ?>" ><?= $doc['aula']; ?></td>
                <td class="<?php echo $c; ?>" ><?= $doc['note']; ?></td>
            </tr>
        <? endforeach; ?>
    </tbody>
</table>
<?php require_once 'template/footer.php'; ?>