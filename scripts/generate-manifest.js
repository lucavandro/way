#!/usr/bin/env node

/**
 * Script per generare manifest.json con i path corretti
 * Usa la configurazione centralizzata da build-config.js
 */

import { readFileSync, writeFileSync } from 'fs';
import { PWA_CONFIG } from '../build-config.js';

// Template del manifest.json
const manifestTemplate = {
    "name": "WAY",
    "short_name": "WAY",
    "description": "App orario Nino Cortese",
    "display": "standalone",
    "start_url": PWA_CONFIG.basePath,
    "scope": PWA_CONFIG.basePath,
    "theme_color": "#e7eaf0",
    "background_color": "#e7eaf0",
    "orientation": "portrait",
    "categories": ["education", "lifestyle", "productivity"],
    "lang": "it",
    "dir": "ltr",
    "icons": [
        {
            "src": "icon-192.png",
            "sizes": "192x192",
            "type": "image/png",
            "purpose": "any"
        },
        {
            "src": "icon-512.png",
            "sizes": "512x512",
            "type": "image/png",
            "purpose": "any"
        },
        {
            "src": "favicon.png",
            "sizes": "180x180",
            "type": "image/png",
            "purpose": "any"
        }
    ],
    "screenshots": [
        {
            "src": "ss_1.png",
            "sizes": "828x1792",
            "type": "image/png",
            "platform": "wide",
            "label": "Homescreen of WAY"
        },
        {
            "src": "ss_2.png",
            "sizes": "828x1792",
            "type": "image/png",
            "platform": "wide",
            "label": "WAY showing a schedule"
        },
        {
            "src": "ss_3.png",
            "sizes": "828x1792",
            "type": "image/png",
            "platform": "wide",
            "label": "WAY QR code scanner"
        }
    ],
    "shortcuts": [
        {
            "name": "Orario",
            "url": `${PWA_CONFIG.basePath}/`,
            "description": "Visualizza l'orario delle lezioni"
        },
        {
            "name": "Sostituzioni",
            "url": `${PWA_CONFIG.basePath}/docente`,
            "description": "Controlla le sostituzioni per i docenti"
        }
    ],
    "related_applications": [
        {
            "platform": "webapp",
            "url": PWA_CONFIG.fullBaseUrl
        }
    ],
    "prefer_related_applications": false,
    "edge_side_panel": {
        "preferred_width": 480
    },
    "launch_handler": {
        "client_mode": "navigate-existing"
    },
    "protocol_handlers": [
        {
            "protocol": "web+way",
            "url": `${PWA_CONFIG.basePath}/qr?data=%s`
        }
    ],
    "share_target": {
        "action": `${PWA_CONFIG.basePath}/qr`,
        "method": "GET",
        "params": {
            "title": "title",
            "text": "text",
            "url": "url"
        }
    },
    "file_handlers": [
        {
            "action": `${PWA_CONFIG.basePath}/qr`,
            "accept": {
                "image/*": [".png", ".jpg", ".jpeg", ".gif", ".webp"]
            }
        }
    ],
    "scope_extensions": [
        {
            "origin": PWA_CONFIG.domain
        }
    ]
};

// Scrive il manifest.json generato
console.log('Generazione manifest.json con configurazione:');
console.log(`- Base Path: ${PWA_CONFIG.basePath}`);
console.log(`- Domain: ${PWA_CONFIG.domain}`);
console.log(`- Full URL: ${PWA_CONFIG.fullBaseUrl}`);

writeFileSync('./static/manifest.json', JSON.stringify(manifestTemplate, null, 2));
console.log('✅ manifest.json generato con successo!');