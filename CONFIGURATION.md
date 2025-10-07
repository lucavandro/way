# WAY - Configurazione e Struttura

## 🔗 **Configurazione Path Centralizzata**

### **File di Configurazione Principale**: `build-config.js`

Centralizza la configurazione dei path dell'applicazione:

```javascript
// Path base dell'applicazione - MODIFICA SOLO QUI
export const APP_BASE_PATH = "/app/way/tmp";
export const APP_DOMAIN = "https://www.liceoscientificocortese.edu.it";
```

### **File Automaticamente Sincronizzati**
- ✅ `svelte.config.js` - Configurazione build SvelteKit
- ✅ `src/lib/config.js` - Configurazione runtime applicazione  
- ✅ `src/lib/notifications.js` - URL per notifiche

### **File da Aggiornare Manualmente**
- ⚠️ `static/manifest.json` - start_url, scope, shortcuts
- ⚠️ `src/app.html` - scope service worker
- ⚠️ `src/lib/sw-config.js` - costanti del service worker

> **Nota**: Vedi `PATH_UPDATE_GUIDE.md` per istruzioni dettagliate

### **Vantaggi della Centralizzazione**
- 🔧 **Manutenibilità**: Un solo punto di modifica per i path
- 🚀 **Deploy semplificato**: Facile cambio ambiente (dev/prod)
- 🛡️ **Consistenza**: Riduzione errori di configurazione
- 📱 **PWA compliant**: Sync automatico manifest e service worker

---

# Configurazione PHP Centralizzata

## File Creati/Modificati

### `config.php` - Configurazione Centralizzata
Nuovo file che contiene:

#### **Configurazione Database (Sicura)**
```php
// ⚠️ VALORI RIMOSSI PER SICUREZZA ⚠️
// La configurazione ora utilizza variabili d'ambiente o placeholder

define('DB_HOST', $_ENV['DB_HOST'] ?? 'CONFIGURA_HOST_DATABASE');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'CONFIGURA_NOME_DATABASE'); 
define('DB_USERNAME', $_ENV['DB_USERNAME'] ?? 'CONFIGURA_USERNAME');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? 'CONFIGURA_PASSWORD');
define('DB_CHARSET', 'utf8mb4');
```

#### **Configurazione Autenticazione (Sicura)**
```php
// ⚠️ SALT RIMOSSO PER SICUREZZA ⚠️
// Utilizza file .env o sostituisci il placeholder

define('AUTH_SALT', $_ENV['AUTH_SALT'] ?? 'CONFIGURA_SALT_CASUALE_LUNGO');
```

#### **Configurazione Richiesta**
Per utilizzare l'applicazione è necessario:

1. **Creare file `.env`** nella cartella `static/` con:
   ```env
   DB_HOST=tuo_host
   DB_NAME=tuo_database
   DB_USERNAME=tuo_username
   DB_PASSWORD=tua_password
   AUTH_SALT=salt_casuale_sicuro
   ```

2. **O modificare direttamente** i placeholder in `config.php`

3. **File di supporto creati:**
   - `.env.example` - Template configurazione
   - `DATABASE_SETUP.md` - Guida completa alla configurazione

#### **Funzioni di Utilità**

1. **`createMySQLiConnection()`** - Crea connessione MySQLi
2. **`createPDOConnection()`** - Crea connessione PDO
3. **`setCommonHeaders()`** - Imposta header comuni per API
4. **`handleCORSPreflight()`** - Gestisce richieste OPTIONS
5. **`sendJsonResponse($data, $httpCode)`** - Invia risposta JSON
6. **`sendErrorResponse($message, $httpCode)`** - Invia errore JSON
7. **`isEmailValid($email)`** - Valida email del dominio scuola
8. **`generateVerificationCode($email)`** - Genera codice verifica

### `api.php` - Aggiornato
- ✅ Rimossa configurazione database duplicata
- ✅ Importa `config.php`
- ✅ Usa `createMySQLiConnection()`
- ✅ Usa `sendErrorResponse()` per errori

### `sostituzioni.php` - Aggiornato
- ✅ Rimossa configurazione database duplicata
- ✅ Importa `config.php`
- ✅ Usa `createPDOConnection()`
- ✅ Usa `sendJsonResponse()` e `sendErrorResponse()`
- ✅ Header comuni gestiti automaticamente

### `auth.php` - Aggiornato
- ✅ Rimossa configurazione AUTH_SALT duplicata
- ✅ Importa `config.php`
- ✅ Usa `isEmailValid()` e `generateVerificationCode()`
- ✅ Usa `sendAuthResponse()` per consistenza

## Vantaggi della Centralizzazione

### 🔧 **Manutenibilità**
- Configurazione database in un solo punto
- Modifica credenziali da un solo file
- Eliminazione duplicazioni di codice

### 🛡️ **Sicurezza**
- Gestione errori centralizzata
- Configurazione sicura PDO/MySQLi
- Validazione email centralizzata

### 📊 **Consistenza**
- Header HTTP uniformi
- Formato risposta JSON standardizzato
- Gestione errori omogenea

### ⚡ **Performance**
- Configurazione PDO ottimizzata
- Gestione connessioni efficiente
- Header cache appropriati

## Utilizzo delle Funzioni

### Connessione Database
```php
// MySQLi (per api.php)
$conn = createMySQLiConnection();
if (!$conn) {
    sendErrorResponse("Connessione fallita");
}

// PDO (per sostituzioni.php)
$pdo = createPDOConnection();
if (!$pdo) {
    sendErrorResponse("Connessione fallita");
}
```

### Gestione Risposte
```php
// Risposta di successo
sendJsonResponse(['success' => true, 'data' => $data]);

// Risposta di errore
sendErrorResponse('Messaggio errore', 400);

// Risposta autenticazione
sendAuthResponse(true, 'Login riuscito', true);
```

### Validazione Email
```php
if (!isEmailValid($email)) {
    sendErrorResponse('Email non valida', 400);
}
```

## File di Configurazione

Il file `config.php` viene automaticamente incluso e:
- Imposta header comuni (JSON, CORS, Cache)
- Gestisce richieste OPTIONS per CORS
- Fornisce funzioni di utilità
- Definisce costanti di configurazione

## Migrazione Completata

Tutti i file PHP ora utilizzano:
- ✅ Configurazione centralizzata
- ✅ Funzioni di utilità comuni
- ✅ Gestione errori uniforme
- ✅ Header HTTP standardizzati
- ✅ Sicurezza migliorata

## File di Configurazione

### `src/lib/config.js`
File principale di configurazione che contiene tutti gli URL e le costanti utilizzate nell'applicazione:

- **API_URLS**: URL per le API del backend
  - `main`: API principale per dati dell'applicazione
  - `auth`: API per l'autenticazione
  - `teacherSubstitutions`: API per le sostituzioni dei docenti

- **ASSET_URLS**: URL per le risorse statiche
  - `favicon`: Icona dell'applicazione
  - `appUrl`: URL dell'applicazione web
  - `substitutionsPage`: Pagina delle sostituzioni

- **EXTERNAL_URLS**: URL per risorse esterne
  - `picoCSS`: Framework CSS
  - `workboxCDN`: Service Worker di Google

- **HTTP_CONFIG**: Configurazioni per le richieste HTTP
- **NOTIFICATION_CONFIG**: Configurazioni per le notifiche
- **APP_CONFIG**: Configurazioni varie dell'applicazione

### `src/lib/sw-config.js`
Configurazione specifica per il Service Worker (non supporta moduli ES6):

- Stesse configurazioni di `config.js` ma in formato compatibile con Service Worker
- Template per i messaggi delle notifiche
- Configurazioni per il caching

## Struttura dei File

### Core Library (`src/lib/`)
- `config.js`: Configurazione centralizzata degli URL
- `sw-config.js`: Configurazione per il Service Worker
- `data.js`: Funzioni per le chiamate API
- `stores.js`: Store Svelte per lo stato globale
- `utils.js`: Funzioni di utilità generale
- `dateutils.js`: Utilità per date e orari scolastici
- `notifications.js`: Gestione delle notifiche push
- `hotspot.js`: Gestione degli hotspot

### Routes (`src/routes/`)
- `+layout.svelte`: Layout principale dell'applicazione
- `+page.svelte`: Pagina principale (orari)
- `Header.svelte`: Componente header con navigazione
- `Footer.svelte`: Componente footer
- Componenti per tabelle orarie
- Directory per le pagine specifiche (`docente/`, `qr/`, ecc.)

### Service Worker (`src/`)
- `service-worker.js`: Service Worker per funzionalità offline e notifiche

## Miglioramenti Implementati

### 1. Configurazione Centralizzata
- Tutti gli URL sono ora centralizzati in `config.js`
- Facile manutenzione e modifica degli endpoint
- Evita duplicazioni di URL nel codice

### 2. Documentazione
- Commenti JSDoc per tutte le funzioni
- Descrizioni chiare delle funzionalità
- Parametri e tipi di ritorno documentati

### 3. Organizzazione del Codice
- Separazione delle responsabilità
- Funzioni ben documentate con scopo chiaro
- Configurazioni separate per Service Worker

### 4. Miglioramenti di Performance
- Intervalli configurabili per i controlli
- Gestione ottimizzata degli stati
- Cleanup appropriato delle risorse

## Come Utilizzare la Configurazione

```javascript
// Importa la configurazione nei tuoi file
import { API_URLS, ASSET_URLS, HTTP_CONFIG } from '$lib/config.js';

// Usa gli URL configurati
const response = await fetch(API_URLS.main, HTTP_CONFIG.corsConfig);

// Accedi alle risorse statiche
const iconUrl = ASSET_URLS.favicon;
```

## Vantaggi della Nuova Struttura

1. **Manutenibilità**: Modifica degli URL da un singolo punto
2. **Scalabilità**: Facile aggiunta di nuovi endpoint
3. **Debugging**: URL centralizzati facilitano il debug
4. **Ambiente**: Possibilità di configurazioni diverse per dev/prod
5. **Documentazione**: Codice autodocumentato e comprensibile

## Note di Sviluppo

- Il Service Worker usa `sw-config.js` invece di `config.js` per problemi di compatibilità
- Tutti i componenti Svelte ora importano la configurazione centralizzata
- Gli intervalli sono ora configurabili tramite `APP_CONFIG`
- Le funzioni hanno documentazione JSDoc per migliore intellisense