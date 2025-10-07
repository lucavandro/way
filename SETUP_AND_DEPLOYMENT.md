# WAY - Setup Completo e Deployment

## 📚 Indice
1. [Configurazione Path](#-configurazione-path-centralizzata)
2. [Configurazione Database PHP](#-configurazione-database-php)
3. [Prerequisiti](#-prerequisiti)
4. [Installazione](#-installazione)
5. [Sviluppo](#-sviluppo)
6. [Build e Deploy](#-build-e-deploy)
7. [Risoluzione Problemi](#-risoluzione-problemi)

---

## 🔗 Configurazione Path Centralizzata

### File di Configurazione Principale: `build-config.js`

Centralizza la configurazione dei path dell'applicazione:

```javascript
// Path base dell'applicazione - MODIFICA SOLO QUI
export const APP_BASE_PATH = "/app/way/tmp";
export const APP_DOMAIN = "https://www.liceoscientificocortese.edu.it";
```

### File Automaticamente Sincronizzati

- ✅ `svelte.config.js` - Configurazione build SvelteKit
- ✅ `src/lib/config.js` - Configurazione runtime applicazione  
- ✅ `src/lib/notifications.js` - URL per notifiche

### File da Aggiornare Manualmente

- ⚠️ `static/manifest.json` - start_url, scope, shortcuts
- ⚠️ `src/app.html` - scope service worker
- ⚠️ `src/lib/sw-config.js` - costanti del service worker

### Vantaggi della Centralizzazione

- 🔧 **Manutenibilità**: Un solo punto di modifica per i path
- 🚀 **Deploy semplificato**: Facile cambio ambiente (dev/prod)
- 🛡️ **Consistenza**: Riduzione errori di configurazione
- 📱 **PWA compliant**: Sync automatico manifest e service worker

---

## 🔐 Configurazione Database PHP

### File `config.php` - Configurazione Centralizzata

#### Configurazione Database (Sicura)

```php
// ⚠️ VALORI RIMOSSI PER SICUREZZA ⚠️
// La configurazione ora utilizza variabili d'ambiente o placeholder

define('DB_HOST', $_ENV['DB_HOST'] ?? 'CONFIGURA_HOST_DATABASE');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'CONFIGURA_NOME_DATABASE'); 
define('DB_USERNAME', $_ENV['DB_USERNAME'] ?? 'CONFIGURA_USERNAME');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? 'CONFIGURA_PASSWORD');
define('DB_CHARSET', 'utf8mb4');
```

#### Configurazione Autenticazione (Sicura)

```php
// ⚠️ SALT RIMOSSO PER SICUREZZA ⚠️
// Utilizza file .env o sostituisci il placeholder

define('AUTH_SALT', $_ENV['AUTH_SALT'] ?? 'CONFIGURA_SALT_CASUALE_LUNGO');
```

### Configurazione Database Richiesta

Per utilizzare l'applicazione è necessario:

1. **Creare file `.env`** nella cartella `static/` con:

```env
DB_HOST=tuo_host
DB_NAME=tuo_database
DB_USERNAME=tuo_username
DB_PASSWORD=tua_password
AUTH_SALT=salt_casuale_sicuro
```

2. **O modificare direttamente** i placeholder in `static/config.php`

3. **File di supporto disponibili:**
   - `.env.example` - Template configurazione
   - `config.php` - File di configurazione centralizzato

### Funzioni di Utilità Disponibili

1. **`createMySQLiConnection()`** - Crea connessione MySQLi
2. **`createPDOConnection()`** - Crea connessione PDO
3. **`setCommonHeaders()`** - Imposta header comuni per API
4. **`handleCORSPreflight()`** - Gestisce richieste OPTIONS
5. **`sendJsonResponse($data, $httpCode)`** - Invia risposta JSON
6. **`sendErrorResponse($message, $httpCode)`** - Invia errore JSON
7. **`isEmailValid($email)`** - Valida email del dominio scuola
8. **`generateVerificationCode($email)`** - Genera codice verifica

### File PHP Aggiornati

#### `api.php`
- ✅ Rimossa configurazione database duplicata
- ✅ Importa `config.php`
- ✅ Usa `createMySQLiConnection()`
- ✅ Usa `sendErrorResponse()` per errori

#### `sostituzioni.php`
- ✅ Rimossa configurazione database duplicata
- ✅ Importa `config.php`
- ✅ Usa `createPDOConnection()`
- ✅ Usa `sendJsonResponse()` e `sendErrorResponse()`
- ✅ Header comuni gestiti automaticamente

#### `auth.php`
- ✅ Rimossa configurazione AUTH_SALT duplicata
- ✅ Importa `config.php`
- ✅ Usa `isEmailValid()` e `generateVerificationCode()`
- ✅ Usa `sendAuthResponse()` per consistenza

---

## 📋 Prerequisiti

### Software Richiesto

1. **Node.js** (versione 18 o superiore)
   - Scarica da: https://nodejs.org/
   - Verifica installazione: `node --version`

2. **pnpm** (Package Manager)
   - Installa: `npm install -g pnpm`
   - Verifica installazione: `pnpm --version`

3. **Database MySQL/MariaDB** con charset UTF-8
   - MySQL 5.7+ o MariaDB 10.2+
   - Accesso con privilegi CREATE, READ, WRITE

4. **Server Web PHP** (per le API)
   - PHP 7.4 o superiore
   - Estensioni: mysqli, pdo, pdo_mysql
   - Apache o Nginx configurato

### Configurazione Database

#### Struttura Tabelle Richieste

- **Orari scolastici**: Contiene gli orari delle classi
- **Sostituzioni**: Gestisce le sostituzioni dei docenti
- **Utenti**: Sistema di autenticazione per docenti

#### Charset Database

```sql
ALTER DATABASE nome_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 🚀 Installazione

### 1. Clona il Repository

```bash
git clone https://github.com/your-repo/way.git
cd way
```

### 2. Installa le Dipendenze

```bash
pnpm install
```

Questo comando:
- Scarica tutti i pacchetti npm necessari
- Crea la cartella `node_modules/`
- Genera il file `pnpm-lock.yaml`

### 3. Configura i Path dell'Applicazione

Modifica `build-config.js` per impostare il path base:

```javascript
// Modifica questi valori per il tuo ambiente
export const APP_BASE_PATH = "/app/way/tmp";  // Path relativo al dominio
export const APP_DOMAIN = "https://www.liceoscientificocortese.edu.it";
```

**Nota**: Se cambi questi valori, aggiorna manualmente:
- `static/manifest.json` (start_url, scope, shortcuts)
- `src/app.html` (scope service worker)
- `src/lib/sw-config.js` (costanti)

### 4. Configura il Database

#### Metodo 1: File .env (Raccomandato)

```bash
# Copia il template
cd static
cp .env.example .env
```

Modifica `static/.env`:

```env
DB_HOST=localhost
DB_NAME=nome_tuo_database
DB_USERNAME=tuo_username
DB_PASSWORD=tua_password_sicura
AUTH_SALT=stringa_casuale_lunga_generata
```

#### Metodo 2: Modifica Diretta

Modifica direttamente `static/config.php` sostituendo i placeholder.

#### Genera un Salt Sicuro

```bash
# Su Linux/Mac:
openssl rand -base64 64

# Su Windows PowerShell:
[System.Convert]::ToBase64String((1..48 | ForEach {Get-Random -Max 256}))
```

### 5. Verifica la Configurazione

Visita uno dei file PHP per verificare la connessione:
- `http://tuo-server/static/api.php`
- `http://tuo-server/static/sostituzioni.php`

Se vedi "ERRORE: Configurazione database non completata!" devi ancora configurare i parametri.

---

## 💻 Sviluppo

### Avvia il Server di Sviluppo

```bash
npm run dev
```

Oppure con pnpm:

```bash
pnpm dev
```

#### Cosa fa `npm run dev`:

- ✅ Avvia il server di sviluppo SvelteKit
- ✅ Hot Module Replacement (HMR) attivo
- ✅ Server disponibile su `http://localhost:5173`
- ✅ Ricaricamento automatico al cambio dei file
- ✅ Mostra errori in tempo reale nel browser

#### Opzioni Aggiuntive:

```bash
# Avvia su porta specifica
npm run dev -- --port 3000

# Avvia e apri automaticamente il browser
npm run dev -- --open

# Avvia accessibile da altri dispositivi nella rete
npm run dev -- --host
```

### Struttura di Sviluppo

#### File da Modificare Durante lo Sviluppo:

```
src/
├── routes/              # Pagine e componenti Svelte
│   ├── +page.svelte    # Homepage
│   ├── +layout.svelte  # Layout principale
│   ├── docente/        # Sezione docenti
│   ├── qr/            # Lettore QR
│   └── sostituzioni/   # Sostituzioni
├── lib/                # Librerie e utilità
│   ├── config.js      # Configurazione (auto-sync)
│   ├── stores.js      # Store Svelte
│   └── data.js        # API calls
└── app.html           # Template HTML base
```

#### File Statici:

```
static/
├── app.css            # CSS personalizzato
├── manifest.json      # PWA manifest
├── *.php             # API Backend
└── .env              # Configurazione DB (NON committare!)
```

### Workflow di Sviluppo

1. **Modifica i file** in `src/`
2. **Salva** - Il browser si aggiorna automaticamente
3. **Controlla la console** per eventuali errori
4. **Testa le funzionalità** in tempo reale
5. **Commit** quando soddisfatto

---

## 🏗️ Build e Deploy

### Build per Produzione

```bash
npm run build
```

Oppure con pnpm:

```bash
pnpm build
```

#### Cosa fa `npm run build`:

- ✅ Compila l'applicazione SvelteKit
- ✅ Ottimizza JavaScript e CSS
- ✅ Genera file statici nella cartella `build/`
- ✅ Minifica il codice per produzione
- ✅ Genera sourcemaps per debugging
- ✅ Crea il Service Worker per la PWA

#### Output della Build:

```
build/
├── _app/              # Chunk JavaScript ottimizzati
│   ├── immutable/    # Asset con hash per caching
│   └── version.json  # Versione build
├── index.html        # HTML pre-renderizzato
├── manifest.json     # PWA manifest
├── service-worker.js # Service Worker compilato
└── *.css, *.png     # Asset statici
```

### Anteprima Build Locale

```bash
npm run preview
```

- Avvia un server per testare la build di produzione
- Disponibile su `http://localhost:4173`
- Utile per verificare prima del deploy

### Deploy su Server Web

#### 1. Preparazione File

```bash
# Dopo aver eseguito npm run build
cd build
```

#### 2. Copia i File sul Server

**Opzione A: Via FTP/SFTP**
- Carica la cartella `build/` sul tuo server
- Rinomina o sposta in base al tuo APP_BASE_PATH

**Opzione B: Via SSH**

```bash
# Comprimi la build
tar -czf build.tar.gz build/

# Carica sul server
scp build.tar.gz user@server:/path/to/web/

# Sul server, decomprimi
ssh user@server
cd /path/to/web/
tar -xzf build.tar.gz
```

#### 3. Configura il Server Web

**Apache (.htaccess):**

```apache
# Nel file build/.htaccess o nella configurazione Apache
RewriteEngine On
RewriteBase /app/way/tmp/

# Reindirizza tutte le richieste a index.html per SPA
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.html [L]

# Cache per asset immutabili
<FilesMatch "\.(js|css|png|jpg|jpeg|gif|svg|woff|woff2)$">
  Header set Cache-Control "max-age=31536000, immutable"
</FilesMatch>

# Service Worker - no cache
<FilesMatch "service-worker\.js$">
  Header set Cache-Control "no-cache, no-store, must-revalidate"
</FilesMatch>
```

**Nginx:**

```nginx
location /app/way/tmp/ {
    alias /path/to/build/;
    try_files $uri $uri/ /app/way/tmp/index.html;
    
    # Cache per asset
    location ~* \.(js|css|png|jpg|jpeg|gif|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # No cache per service worker
    location = /app/way/tmp/service-worker.js {
        add_header Cache-Control "no-cache, no-store, must-revalidate";
    }
}
```

#### 4. Deploy delle API PHP

Le API PHP nella cartella `static/` devono essere accessibili:

```bash
# Copia i file PHP
cp static/*.php /path/to/web/app/way/
cp static/config.php /path/to/web/app/way/
cp static/.env /path/to/web/app/way/
```

**IMPORTANTE**: Assicurati che `.env` NON sia accessibile pubblicamente!

#### 5. Permessi File

```bash
# Sul server
chmod 644 *.php
chmod 600 .env  # Solo lettura per il proprietario
chmod 755 .     # Directory eseguibile
```

### Verifica del Deploy

1. **Testa l'applicazione web**: `https://tuo-dominio/app/way/tmp/`
2. **Verifica PWA**: Installa come app dal browser
3. **Testa le API**: `https://tuo-dominio/app/way/api.php`
4. **Controlla Service Worker**: Console > Application > Service Workers
5. **Verifica funzionalità offline**: Disattiva la rete e ricarica

---

## 🔧 Risoluzione Problemi

### Problemi di Build

#### Errore: "Module not found"

```bash
# Reinstalla le dipendenze
rm -rf node_modules pnpm-lock.yaml
pnpm install
```

#### Errore: "Cannot find module '@sveltejs/kit'"

```bash
# Assicurati di aver installato le dipendenze
pnpm install

# Verifica la versione di Node.js
node --version  # Deve essere >= 18
```

### Problemi di Sviluppo

#### Il server dev non si avvia

```bash
# Verifica che la porta 5173 sia libera
netstat -ano | findstr :5173

# Usa una porta diversa
npm run dev -- --port 3000
```

#### Hot reload non funziona

- Verifica che i file siano nella cartella `src/`
- Riavvia il server dev: `Ctrl+C` poi `npm run dev`
- Pulisci la cache: `.svelte-kit` e riavvia

### Problemi di Database

#### Errore: "Configurazione database non completata!"

**Soluzioni:**
1. Verifica che `.env` esista in `static/`
2. Controlla la sintassi del file `.env`
3. Verifica i permessi di lettura del file

#### Errore: "Cannot connect to database"

**Soluzioni:**
1. Verifica host, porta e credenziali
2. Controlla che il database esista
3. Verifica i privilegi dell'utente database
4. Controlla il firewall del server database

#### Errore: "Charset not supported"

**Soluzioni:**
1. Imposta charset database su `utf8mb4`
2. Verifica configurazione MySQL/MariaDB
3. Esegui: `ALTER DATABASE nome_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`

### Problemi di Deploy

#### 404 su route dell'app

**Causa**: Server web non configurato per SPA

**Soluzione**: Configura il rewrite per puntare a `index.html` (vedi sezione Apache/Nginx)

#### Service Worker non si registra

**Soluzioni:**
1. Verifica che il sito sia servito via HTTPS (o localhost)
2. Controlla la console browser per errori
3. Verifica che `scope` in `app.html` corrisponda a `APP_BASE_PATH`
4. Pulisci la cache e riregistra

#### Asset non vengono caricati

**Soluzioni:**
1. Verifica che `APP_BASE_PATH` sia corretto
2. Controlla i percorsi in `manifest.json`
3. Verifica configurazione server web (alias/root)
4. Controlla i permessi dei file

### Debug Avanzato

#### Abilita Log Dettagliati

```bash
# Sviluppo con log verbose
npm run dev -- --debug

# Build con sourcemaps dettagliate
npm run build -- --debug
```

#### Controlla la Build

```bash
# Analizza la dimensione dei bundle
pnpm add -D @sveltejs/adapter-static
npm run build

# Controlla i file generati
ls -lah build/
```

#### Log del Service Worker

Apri la console del browser e vai su:
- **Chrome/Edge**: F12 > Application > Service Workers
- **Firefox**: F12 > Application > Service Workers

---

## 📁 Struttura File di Configurazione

```
way/
├── build-config.js         # ⚙️ Configurazione path centralizzata
├── svelte.config.js        # 🔧 Config SvelteKit (usa build-config.js)
├── package.json            # 📦 Dipendenze e script npm
├── pnpm-lock.yaml         # 🔒 Lock file dipendenze
├── .gitignore             # 🚫 File da escludere da git
│
├── src/
│   ├── lib/
│   │   ├── config.js      # 🌐 Config runtime (usa build-config.js)
│   │   └── sw-config.js   # 🔄 Config service worker
│   └── app.html           # 📄 Template HTML
│
├── static/
│   ├── .env               # 🔐 Config DB (NON committare!)
│   ├── .env.example       # 📋 Template config DB
│   ├── config.php         # ⚙️ Config PHP centralizzata
│   ├── api.php            # 🔌 API principale
│   ├── auth.php           # 🔑 API autenticazione
│   ├── sostituzioni.php   # 📝 API sostituzioni
│   └── manifest.json      # 📱 PWA manifest
│
└── build/                 # 📦 Output build (generato)
    ├── _app/
    ├── index.html
    └── service-worker.js
```

---

## 🎯 Quick Start

Per iniziare rapidamente:

```bash
# 1. Clona e installa
git clone https://github.com/your-repo/way.git
cd way
pnpm install

# 2. Configura database
cd static
cp .env.example .env
# Modifica .env con i tuoi dati
cd ..

# 3. Sviluppo
npm run dev

# 4. Build per produzione
npm run build

# 5. Deploy
# Carica la cartella build/ sul tuo server web
```

---

## ✅ Checklist Deploy

Prima di deployare in produzione:

- [ ] Configurato `APP_BASE_PATH` in `build-config.js`
- [ ] Aggiornato manualmente `manifest.json`, `app.html`, `sw-config.js`
- [ ] Creato file `.env` con credenziali database
- [ ] Testato build locale con `npm run preview`
- [ ] Configurato server web (Apache/Nginx)
- [ ] Impostati permessi corretti sui file
- [ ] File `.env` NON accessibile pubblicamente
- [ ] Testato PWA e installazione
- [ ] Verificato funzionamento API
- [ ] Testato funzionalità offline
- [ ] Configurato HTTPS (richiesto per PWA)

---

## 🆘 Supporto

Per problemi:

1. **Controlla i log di errore** del server web e della console browser
2. **Verifica la configurazione** seguendo questa guida
3. **Testa la connessione database** separatamente
4. **Controlla i permessi** dei file
5. **Consulta la documentazione** di SvelteKit: https://kit.svelte.dev/

---

## 📝 Note Finali

### Sicurezza

- ⚠️ NON committare mai il file `.env` nel repository
- ✅ Usa sempre HTTPS in produzione per le PWA
- ✅ Limita i privilegi dell'utente database
- ✅ Genera sempre un nuovo salt per ogni installazione

### Performance

- ✅ Gli asset in `_app/immutable/` hanno cache infinita
- ✅ Il Service Worker gestisce la cache offline
- ✅ Il codice è minificato e ottimizzato automaticamente
- ✅ Le immagini dovrebbero essere ottimizzate prima del deploy

### Best Practices

- 🔄 Testa sempre localmente prima del deploy
- 📊 Monitora le performance con Lighthouse
- 🧪 Testa su diversi browser e dispositivi
- 📱 Verifica l'installabilità PWA
- 🔍 Usa la console browser per debug

---

**Versione documento**: 1.0  
**Ultimo aggiornamento**: Ottobre 2025  
**Licenza**: BSD 3-Clause License