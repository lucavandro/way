# 🔧 Configurazione Database WAY

## 📋 Prerequisiti

Prima di utilizzare l'applicazione WAY, è necessario configurare:

1. **Database MySQL/MariaDB** con charset UTF-8
2. **Parametri di connessione** (host, database, utente, password)
3. **Salt per autenticazione** (stringa casuale sicura)

## ⚙️ Configurazione Rapida

### Passo 1: Copia il file di esempio
```bash
cp .env.example .env
```

### Passo 2: Modifica il file `.env`
Apri il file `.env` e inserisci i tuoi dati:

```env
DB_HOST=il_tuo_host_database
DB_NAME=il_tuo_nome_database
DB_USERNAME=il_tuo_username
DB_PASSWORD=la_tua_password_sicura
AUTH_SALT=stringa_casuale_lunga_generata
```

### Passo 3: Genera un salt sicuro
```bash
# Su Linux/Mac:
openssl rand -base64 64

# Su Windows PowerShell:
[System.Convert]::ToBase64String((1..48 | ForEach {Get-Random -Max 256}))
```

## 🗄️ Struttura Database

L'applicazione richiede le seguenti tabelle:

### Tabelle Principali
- **Orari scolastici**: Contiene gli orari delle classi
- **Sostituzioni**: Gestisce le sostituzioni dei docenti  
- **Utenti**: Sistema di autenticazione per docenti

### Charset Richiesto
```sql
ALTER DATABASE nome_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 🔐 Sicurezza

### ⚠️ IMPORTANTE - NON committare mai:
- Il file `.env` con le credenziali reali
- Password o salt nel codice sorgente
- Configurazioni hardcoded nei file PHP

### ✅ Buone Pratiche:
- Usa password forti per il database
- Limita i privilegi dell'utente database
- Cambia sempre il salt per ogni installazione
- Mantieni aggiornato il database

## 🧪 Test della Configurazione

### Verifica 1: Controllo file
```bash
# Verifica che il file .env esista
ls -la .env

# Verifica che contenga i parametri necessari
grep "DB_HOST\|DB_NAME\|AUTH_SALT" .env
```

### Verifica 2: Test connessione
Visita uno dei file PHP dell'applicazione:
- `api.php`
- `sostituzioni.php` 
- `auth.php`

Se vedi il messaggio:
```
ERRORE: Configurazione database non completata!
```

Significa che devi ancora configurare i parametri.

### Verifica 3: Test database
Se la configurazione è corretta, l'applicazione dovrebbe:
- Connettersi al database senza errori
- Restituire dati JSON validi
- Gestire le richieste CORS correttamente

## 🔧 Risoluzione Problemi

### Errore di connessione database
```
Cannot connect to database
```
**Soluzioni:**
- Verifica host, porta e credenziali
- Controlla che il database esista
- Verifica i permessi dell'utente

### Errore charset
```
Charset not supported
```
**Soluzioni:**
- Imposta charset database su `utf8mb4`
- Verifica configurazione MySQL/MariaDB

### Errore file .env
```
Configuration not found
```
**Soluzioni:**
- Verifica che `.env` esista nella cartella `static/`
- Controlla permessi di lettura del file
- Verifica sintassi del file .env

## 📁 File di Configurazione

```
static/
├── .env                 # La TUA configurazione (non committare!)
├── .env.example        # Template di esempio
├── config.php          # File di configurazione PHP
├── api.php             # API principale
├── sostituzioni.php    # API sostituzioni  
└── auth.php            # API autenticazione
```

## 🆘 Supporto

Per problemi di configurazione:

1. **Controlla i log di errore** del server web
2. **Verifica la configurazione** con gli step sopra
3. **Testa la connessione database** separatamente
4. **Controlla i permessi** dei file

---

✅ **Una volta completata la configurazione, l'applicazione WAY sarà pronta all'uso!**