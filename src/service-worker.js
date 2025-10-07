/**
 * WAY - App orario del Liceo Scientifico Nino Cortese
 * 
 * Copyright (c) 2025, WAY Project
 * Licensed under the BSD 3-Clause License
 * 
 * Service Worker con esperienza offline combinata (Pagina offline + Cache delle pagine)
 */

// Importa la configurazione specifica per il Service Worker
importScripts('./lib/sw-config.js');

const CACHE = self.SW_CONFIG.APP_CONFIG.cacheName;
const offlineFallbackPage = self.SW_CONFIG.APP_CONFIG.offlineFallbackPage;

let userEmail = null;
let notifiedSubstitutions = new Set();
let checkInterval = null;

// Importa Workbox per la gestione del caching
importScripts(self.SW_CONFIG.EXTERNAL_URLS.workboxCDN);

self.addEventListener("message", (event) => {
  if (event.data && event.data.type === "SKIP_WAITING") {
    self.skipWaiting();
  } else if (event.data && event.data.type === "SET_USER_EMAIL") {
    const newEmail = event.data.email;
    
    // Se l'email cambia, resetta le notifiche inviate
    if (userEmail !== newEmail) {
      notifiedSubstitutions.clear();
    }
    
    userEmail = newEmail;
    
    // Gestisci l'intervallo di controllo
    if (userEmail) {
      startPeriodicCheck();
    } else {
      stopPeriodicCheck();
    }
  }
});

/**
 * Avvia il controllo periodico delle sostituzioni
 */
function startPeriodicCheck() {
  if (checkInterval) return; // Evita intervalli multipli
  
  checkInterval = setInterval(() => {
    if (userEmail) {
      checkSubstitutionsInBackground();
    }
  }, self.SW_CONFIG.APP_CONFIG.substitutionCheckInterval);
}

/**
 * Ferma il controllo periodico delle sostituzioni
 */
function stopPeriodicCheck() {
  if (checkInterval) {
    clearInterval(checkInterval);
    checkInterval = null;
  }
}

self.addEventListener('install', async (event) => {
  event.waitUntil(
    caches.open(CACHE)
      .then((cache) => {
        try {
          cache.add(offlineFallbackPage)
        } catch (error) {
          // Failed to add offline fallback page to cache
        }
      })
  );
});

if (workbox.navigationPreload.isSupported()) {
  workbox.navigationPreload.enable();
}

workbox.routing.registerRoute(
  new RegExp('/*'),
  new workbox.strategies.NetworkFirst({
    cacheName: CACHE
  })
);

self.addEventListener('fetch', (event) => {
  if (event.request.mode === 'navigate') {
    event.respondWith((async () => {
      try {
        const preloadResp = await event.preloadResponse;

        if (preloadResp) {
          return preloadResp;
        }

        const networkResp = await fetch(event.request);
        return networkResp;
      } catch (error) {

        const cache = await caches.open(CACHE);
        const cachedResp = await cache.match(offlineFallbackPage);
        return cachedResp;
      }
    })());
  }
});

// Gestione del background sync
self.addEventListener('sync', function(event) {
  if (event.tag === 'check-substitutions') {
    event.waitUntil(checkSubstitutionsInBackground());
  }
});

/**
 * Controlla le sostituzioni in background e invia notifiche
 */
async function checkSubstitutionsInBackground() {
  // Verifica che l'utente sia loggato
  if (!userEmail) {
    // Nessun utente loggato, skip controllo sostituzioni
    return;
  }

  try {
    const response = await fetch(`${self.SW_CONFIG.API_URLS.teacherSubstitutions}?email=${encodeURIComponent(userEmail)}`);
    const data = await response.json();
    
    if (data.success) {
      const today = new Date().toISOString().split('T')[0];
      const todaySubstitutions = data.data.filter(s => 
        s.data === today && !s.accettato && !notifiedSubstitutions.has(s.id)
      );

      for (const substitution of todaySubstitutions) {
        await self.registration.showNotification(self.SW_CONFIG.NOTIFICATION_CONFIG.title, {
          body: self.SW_CONFIG.NOTIFICATION_CONFIG.bodyTemplate(substitution),
          icon: self.SW_CONFIG.ASSET_URLS.favicon,
          badge: self.SW_CONFIG.ASSET_URLS.favicon,
          tag: `${self.SW_CONFIG.NOTIFICATION_CONFIG.substitutionTagPrefix}${substitution.id}`,
          requireInteraction: true,
          data: {
            substitutionId: substitution.id,
            url: self.SW_CONFIG.ASSET_URLS.substitutionsPage
          }
        });
        
        notifiedSubstitutions.add(substitution.id);
      }
      
      // Controllate sostituzioni e inviate notifiche
    }
  } catch (error) {
    // Errore nel controllo sostituzioni in background
  }
}

self.addEventListener('notificationclick', function(event) {
	event.notification.close();
	
	if (event.notification.data && event.notification.data.url) {
		event.waitUntil(
			clients.openWindow(event.notification.data.url)
		);
	}
});
