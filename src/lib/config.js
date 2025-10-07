/**
 * WAY - App orario del Liceo Scientifico Nino Cortese
 * 
 * Copyright (c) 2025, WAY Project
 * Licensed under the BSD 3-Clause License
 * 
 * Configurazione degli URL e delle costanti dell'applicazione
 * 
 * Questo file contiene tutti gli URL e le configurazioni utilizzate nell'applicazione
 * per facilitare la manutenzione e la gestione centralizzata delle risorse.
 */

import { PWA_CONFIG } from '../../build-config.js';

// ========================================
// CONFIGURAZIONE PATHS E DOMINI
// ========================================

// Dominio principale dell'applicazione
export const APP_DOMAIN = PWA_CONFIG.domain;

// Path base dell'applicazione (da build-config.js)
export const APP_BASE_PATH = PWA_CONFIG.basePath;

// URL base completo dell'applicazione
export const APP_BASE_URL = PWA_CONFIG.fullBaseUrl;

// URL base per le API 
const API_BASE_URL = `${APP_DOMAIN+APP_BASE_PATH}`;

// ========================================
// CONFIGURAZIONE API
// ========================================

// URL per le API del backend
export const API_URLS = {
    // API principale per i dati dell'applicazione (orari, classi, ecc.)
    main: `${API_BASE_URL}/api.php`,
    
    // API per l'autenticazione degli utenti
    auth: `${API_BASE_URL}/auth.php`,
    
    // API per le sostituzioni dei docenti
    teacherSubstitutions: `${API_BASE_URL}/sostituzioni.php`,
};

// URL per le risorse statiche e assets
export const ASSET_URLS = {
    // Favicon dell'applicazione
    favicon: `${APP_BASE_URL}/favicon.png`,
    
    // URL dell'applicazione web
    appUrl: `${APP_BASE_URL}/`,
    
    // URL della pagina sostituzioni
    substitutionsPage: `${APP_BASE_URL}/sostituzioni`,
};

// URL per risorse esterne (CDN, servizi terzi)
export const EXTERNAL_URLS = {
    // CSS framework PicoCSS
    picoCSS: "https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css",
    
    // Workbox service worker da Google CDN
    workboxCDN: "https://storage.googleapis.com/workbox-cdn/releases/5.1.2/workbox-sw.js",
};

// Configurazioni per le richieste HTTP
export const HTTP_CONFIG = {
    // Configurazione standard per le richieste CORS
    corsConfig: {
        mode: "cors",
        cache: 'no-cache',
    },
    
    // Headers per le richieste JSON
    jsonHeaders: {
        "Content-Type": "application/json"
    },
};

// Configurazioni per le notifiche
export const NOTIFICATION_CONFIG = {
    // Icona per le notifiche
    icon: ASSET_URLS.favicon,
    
    // Badge per le notifiche
    badge: ASSET_URLS.favicon,
    
    // Tag per identificare le notifiche di sostituzione
    substitutionTagPrefix: "substitution-",
};

// Configurazioni varie dell'applicazione
export const APP_CONFIG = {
    // Intervallo di controllo per le sostituzioni (in millisecondi)
    substitutionCheckInterval: 60000, // 1 minuto
    
    // Tag per il background sync
    backgroundSyncTag: "check-substitutions",
};

export default {
    API_URLS,
    ASSET_URLS,
    EXTERNAL_URLS,
    HTTP_CONFIG,
    NOTIFICATION_CONFIG,
    APP_CONFIG
};