<?php
session_start();
require_once 'functions.php';

$username = "";
$password = "";

if (isset($_POST['username'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $result = login($username, $password);
    if ($result) {
        $_SESSION['lscadmin:username'] = $username;
        header('location: index.php');
    } else {
        $error = 'Credenziali non valide';
    }
}

require_once 'template/header.php';
?>

<article>
    <form action="<?= $_SERVER["PHP_SELF"] ?>" method="POST">


        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?= $username ?>">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" value="<?= $password ?>">
        <button type="submit">Entra</button>
        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
    </form>
</article>

<style>
    article {
        max-width: 400px;
        margin: auto;
    }
</style>
<?php require_once 'template/footer.php'; ?>