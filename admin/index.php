<?php
require_once 'login_guard.php';
require_once 'functions.php';

header('location: sost_docenti.php');

require_once 'template/header.php';
?>
<h1>ENV</h1>
<pre>
    <?= var_dump($_SESSION) ?> 
</pre>


<?php require_once 'template/footer.php'; ?>