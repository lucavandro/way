# WAY

App orario e classi del Liceo Scientifico Nino Cortese

## Descrizione

WAY è un'applicazione web progressiva (PWA) per la consultazione degli orari delle lezioni e la gestione delle sostituzioni per il Liceo Scientifico Nino Cortese.

## Caratteristiche

- 📱 **PWA**: Installabile su dispositivi mobili e desktop
- 🔍 **Ricerca**: Filtro per classe, docente e aula
- 🔔 **Notifiche**: Sistema di notifiche push per le sostituzioni
- 📊 **Orari**: Visualizzazione degli orari in tempo reale
- 🔄 **Offline**: Funzionalità offline con service worker
- 🌙 **Tema scuro**: Supporto per modalità chiara e scura
- 📱 **QR Code dinamico**: Generazione e download di QR code per condivisione

## Tecnologie

- **Frontend**: SvelteKit
- **CSS**: PicoCSS
- **PWA**: Service Worker con Workbox
- **Build**: Vite + esbuild

## Struttura del Progetto

```text
src/
├── lib/                 # Librerie e utility
│   ├── config.js       # Configurazione centralizzata
│   ├── data.js         # API calls
│   ├── stores.js       # Store Svelte
│   └── utils.js        # Utility functions
├── routes/             # Pagine dell'applicazione
│   ├── +layout.svelte  # Layout principale
│   ├── +page.svelte    # Homepage (orari)
│   ├── sostituzioni/   # Pagina sostituzioni
│   ├── qr/            # Pagina QR code
│   └── docente/       # Pagina vista docente
└── service-worker.js   # Service Worker per PWA
```

## Licenza

Questo progetto è rilasciato sotto la [Licenza BSD 3-Clause](LICENSE).

## Documentazione

- [Configurazione e Struttura](CONFIGURATION.md)
- [Riepilogo Modifiche](MODIFICHE.md)
