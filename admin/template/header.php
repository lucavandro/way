<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WAY Admin - Sostituzioni</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="static/style.css">
    <style>
        .no-padding{
            padding:1px;
        }
        @media print {
            .no-print{
                display:none;
            }
        }
        
    </style>
</head>

<body>
    <header class="container no-print">
        <nav>
            <ul>
                <li><img src='favicon.png' style="width:36px"><strong>WAY Admin - Sostituzioni</strong></li>
            </ul>
            <ul>
                <?php if (isset($_SESSION["lscadmin:username"])): ?> 
                    <li><a href="sost_docenti.php">Orario</a></li> 
                    <li><a href="sostituzioni.php">Sostituzioni</a></li> 
                    <li><a href="stampa_sostituzioni.php">Stampa</a></li>                                                            
                    
                                           
                    <li><a href="logout.php">Logout</a></li>                    
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif ?>
            </ul>
        </nav>
    </header>
    <main class="container">