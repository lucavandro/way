**Descrizione**
La cartella `admin` contiene le pagine e gli script utilizzati per la gestione amministrativa delle sostituzioni.

**Caratteristiche**

- **Autenticazione**: pagine per login/logout e protezione delle rotte amministrative.
- **Gestione sostituzioni**: visualizzazione, conferma e stampa delle sostituzioni.
- **Invio email**: integrazione con `PHPMailer` per l'invio di notifiche e conferme.

**Tecnologia utilizzata**

- **PHP**: logica server-side e pagine dinamiche.
- **PHPMailer**: libreria per invio email (`phpmailer/src` e `phpmailer/language`).
- **HTML/CSS**: markup delle pagine e stile (in `static/style.css`).

**Struttura del progetto (cartella `admin`)**

- `confermaSost.php`: endpoint/pagina per confermare manualmente una sostituzione (es. azione di conferma inviata via POST o GET).
- `functions.php`: funzioni ausiliarie condivise usate dalle pagine admin (es. helper per DB, formattazione, ecc.).
- `index.php`: punto di ingresso dell'area admin o dashboard principale.
- `login.php`: pagina e logica per l'autenticazione degli utenti amministratori.
- `login_guard.php`: script per proteggere le pagine admin verificando che l'utente sia autenticato.
- `logout.php`: endpoint per terminare la sessione e disconnettere l'utente.
- `sost_docenti.php`: pagina per la gestione delle sostituzioni per docente (lista/filtri per docente).
- `sostituzioni.php`: pagina principale che mostra le sostituzioni disponibili o programmate.
- `stampa_sostituzioni.php`: versione o endpoint pensato per la stampa delle sostituzioni (formattazione compatibile con stampa).
- `phpmailer/`: libreria PHPMailer inclusa nel progetto per l'invio di email.
  - `phpmailer/language/`: file di traduzione messaggi di PHPMailer (es. `phpmailer.lang-it.php`).
  - `phpmailer/src/`: sorgenti PHPMailer (`PHPMailer.php`, `SMTP.php`, `POP3.php`, `OAuth.php`, ecc.).
- `static/`: risorse statiche specifiche per l'area admin.
  - `static/style.css`: file CSS per lo stile delle pagine admin.
  - `static/images/`: immagini usate nelle pagine admin (favicon/logo ecc.).
- `template/`:
  - `template/header.php`: header condiviso per le pagine admin (menu, titolo, meta).
  - `template/footer.php`: footer condiviso per le pagine admin.

Nota: ci sono anche file di configurazione a livello di progetto (es. `static/config.php` nella root del progetto) che gestiscono DB e parametri globali. Assicurarsi che le credenziali e i parametri per PHPMailer siano configurati correttamente.

**Documentazione**

- Installazione e requisiti:
  - Server con supporto PHP 7.4+ (o versione compatibile usata dal progetto).
  - Estensione `openssl` per la firma e TLS se si usa SMTP sicuro.
  - Permessi di scrittura per eventuali cartelle temporanee usate dall'applicazione.

- Configurazione PHPMailer:
  - Modificare i parametri SMTP (host, porta, username, password, secure) nel file di configurazione dell'applicazione o in uno script di inizializzazione prima di inviare email.
  - I file di PHPMailer si trovano in `phpmailer/src` e le lingue in `phpmailer/language`.

- Come usare l'area `admin`:
  - Accedere con le credenziali amministrative tramite `login.php`.
  - Le pagine sono protette da `login_guard.php`; assicurarsi che la sessione sia gestita correttamente.
  - Per la stampa, usare `stampa_sostituzioni.php` che fornisce una versione impaginata per la stampa.

