/**
 * WAY - App orario del Liceo Scientifico Nino Cortese
 * 
 * Copyright (c) 2025, WAY Project
 * Licensed under the BSD 3-Clause License
 * 
 * Configurazione per il Service Worker
 * 
 * Questo file contiene le configurazioni specifiche per il Service Worker
 * che non può utilizzare moduli ES6.
 */

// Importa configurazione centralizzata (solo i valori, non l'import ES6)
// Questi valori devono essere sincronizzati con build-config.js
const APP_DOMAIN = "https://www.liceoscientificocortese.edu.it";
const APP_BASE_PATH = "/app/way/tmp";
const API_BASE_URL = `${APP_DOMAIN}/app/way`;
const APP_BASE_URL = `${APP_DOMAIN}${APP_BASE_PATH}`;

// Configurazione degli URL per il Service Worker
const SW_CONFIG = {
    // URL per le API
    API_URLS: {
        teacherSubstitutions: `${API_BASE_URL}/docenti_sostituzioni_api.php`,
    },
    
    // URL per le risorse statiche
    ASSET_URLS: {
        favicon: `${APP_BASE_URL}/favicon.png`,
        substitutionsPage: `${APP_BASE_URL}/sostituzioni`,
    },
    
    // URL esterni
    EXTERNAL_URLS: {
        workboxCDN: "https://storage.googleapis.com/workbox-cdn/releases/5.1.2/workbox-sw.js",
    },
    
    // Configurazioni per le notifiche
    NOTIFICATION_CONFIG: {
        substitutionTagPrefix: "substitution-",
        title: "Sostituzione non confermata",
        bodyTemplate: (substitution) => 
            `Hai una sostituzione alle ${substitution.ora} per la classe ${substitution.classe} che necessita conferma.`,
    },
    
    // Configurazioni varie
    APP_CONFIG: {
        substitutionCheckInterval: 60000, // 1 minuto
        cacheName: "way-cache",
        offlineFallbackPage: "offline",
    }
};

// Esporta la configurazione per l'uso nel service worker
if (typeof self !== 'undefined') {
    self.SW_CONFIG = SW_CONFIG;
}