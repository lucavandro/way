/**
 * WAY - Configurazione Build
 * 
 * Copyright (c) 2025, WAY Project
 * Licensed under the BSD 3-Clause License
 * 
 * Configurazione condivisa per il build dell'applicazione
 * 
 * Questo file contiene le configurazioni che devono essere condivise
 * tra il runtime dell'applicazione e il processo di build.
 */

// ========================================
// CONFIGURAZIONE PATHS
// ========================================

// Path base dell'applicazione - MODIFICA SOLO QUI per cambiare il percorso
export const APP_BASE_PATH = "/way/";;

// Dominio principale (solo per URL completi es.https://www.icvespasiano.edu.it) )
export const APP_DOMAIN = "";

// ========================================
// CONFIGURAZIONE BUILD
// ========================================

// Configurazione per SvelteKit
export const BUILD_CONFIG = {
    basePath: APP_BASE_PATH,
    adapter: {
        pages: 'build',
        assets: 'build',
        fallback: undefined,
        precompress: false,
        strict: true
    }
};

// ========================================
// CONFIGURAZIONE PWA
// ========================================

// Configurazione per manifest.json e service worker
export const PWA_CONFIG = {
    basePath: APP_BASE_PATH,
    scope: APP_BASE_PATH,
    startUrl: APP_BASE_PATH,
    domain: APP_DOMAIN,
    fullBaseUrl: `${APP_DOMAIN}${APP_BASE_PATH}`
};

export default {
    APP_BASE_PATH,
    APP_DOMAIN,
    BUILD_CONFIG,
    PWA_CONFIG
};