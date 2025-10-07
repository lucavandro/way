import { userEmail, notificationPermission } from './stores.js';
import { get } from 'svelte/store';
import { validateEmail } from './utils.js';
import { ASSET_URLS } from './config.js';

let notifiedSubstitutions = new Set();



// Funzione per ottenere l'email validata
function getValidatedUserEmail() {
  const email = get(userEmail);
  if (!validateEmail(email)) {
    // Se l'email non è valida, pulisci lo store
    userEmail.set(null);
    localStorage.removeItem('user:email');
    return null;
  }
  return email;
}

export async function requestNotificationPermission() {
	if (!('Notification' in window)) {
		// Le notifiche non sono supportate
		return false;
	}

	if (Notification.permission === 'granted') {
		notificationPermission.set(true);
		return true;
	}

	if (Notification.permission !== 'denied') {
		const permission = await Notification.requestPermission();
		const granted = permission === 'granted';
		notificationPermission.set(granted);
		return granted;
	}

	return false;
}

export function checkNotificationPermission() {
	const hasPermission = 'Notification' in window && Notification.permission === 'granted';
	notificationPermission.set(hasPermission);
	return hasPermission;
}

export function showSubstitutionNotification(substitution) {
	if (Notification.permission !== 'granted' || notifiedSubstitutions.has(substitution.id)) {
		return;
	}

	const notification = new Notification('Sostituzione non confermata', {
		body: `Hai una sostituzione alle ${substitution.ora} per la classe ${substitution.classe} che necessita conferma.`,
		icon: ASSET_URLS.favicon,
		badge: ASSET_URLS.favicon,
		tag: `substitution-${substitution.id}`,
		requireInteraction: true,
		data: {
			substitutionId: substitution.id,
			url: ASSET_URLS.substitutionsPage
		}
	});

	notification.onclick = function(event) {
		event.preventDefault();
		window.focus();
		window.location.href = ASSET_URLS.substitutionsPage;
		notification.close();
	};

	notifiedSubstitutions.add(substitution.id);
}

export function checkSubstitutionsForNotifications(substitutions) {
	if (Notification.permission !== 'granted') return;

	const today = new Date().toISOString().split('T')[0];
	const todaySubstitutions = substitutions.filter(s => 
		s.data === today && !s.accettato
	);

	todaySubstitutions.forEach(substitution => {
		showSubstitutionNotification(substitution);
	});
}

export function clearNotifiedSubstitutions() {
	notifiedSubstitutions.clear();
}

// Funzione per registrare il controllo periodico in background
export async function setupBackgroundSync() {
	if ('serviceWorker' in navigator) {
		const registration = await navigator.serviceWorker.ready;
		
		// Sincronizza sempre l'email dell'utente con il service worker
		syncUserEmailWithServiceWorker();
		
		// Setup del controllo periodico
		if ('sync' in window.ServiceWorkerRegistration.prototype) {
			return registration.sync.register('check-substitutions');
		}
	}
}

// Funzione per sincronizzare l'email dell'utente con il service worker
export function syncUserEmailWithServiceWorker() {
	if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
		const currentEmail = getValidatedUserEmail();
		navigator.serviceWorker.controller.postMessage({
			type: 'SET_USER_EMAIL',
			email: currentEmail
		});
	}
}

// Funzione per rimuovere l'utente dal service worker
export function clearUserFromServiceWorker() {
	if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
		navigator.serviceWorker.controller.postMessage({
			type: 'SET_USER_EMAIL',
			email: null
		});
	}
}
