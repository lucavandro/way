<?php
session_start();
require_once 'functions.php';
?>
<body>
    <head>
        <title>Conferma sostituzione</title>
    </head>
<?php
confermaSost($_GET['id']);
?>
<body>
    <div style="display:block;margin-left: auto;margin-right:auto; width:150px">
        <img src="favicon.png" width="150"></div>
    </div>
    <h2 style="text-align:center">Sostituzione confermata</h2>
    <h3 style="text-align:center">Grazie</h3>

</body>
