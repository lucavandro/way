<script>
	import { getSchoolHour, getDay } from "$lib/dateutils.js";
	import { onDestroy, onMount } from "svelte";
	import PwaButton from "./PWAButton.svelte";
	import Tabs from "./Tabs.svelte";
	import { userEmail } from "$lib/stores.js";
	import { requestNotificationPermission, checkSubstitutionsForNotifications, clearNotifiedSubstitutions, setupBackgroundSync, checkNotificationPermission, syncUserEmailWithServiceWorker, clearUserFromServiceWorker } from "$lib/notifications.js";
	import { requestLogout } from "$lib/data.js";
	import { API_URLS, APP_CONFIG } from "$lib/config.js";

	let day, schoolHour, timeInterval, substitutionInterval;

	/**
	 * Aggiorna l'orario e il giorno corrente
	 */
	function updateTime() {
		schoolHour = getSchoolHour();
		day = getDay();
	}

	/**
	 * Controlla le sostituzioni per l'utente loggato e invia notifiche se necessario
	 */
	async function checkSubstitutions() {
		if (!$userEmail) return;

		try {
			const response = await fetch(`${API_URLS.teacherSubstitutions}?email=${encodeURIComponent($userEmail)}`);
			const data = await response.json();
			
			if (data.success) {
				checkSubstitutionsForNotifications(data.data);
			}
		} catch (err) {
			// Errore nel controllo sostituzioni per notifiche
		}
	}

	/**
	 * Effettua il logout dell'utente e pulisce i dati locali
	 */
	function logout() {
		requestLogout();
		clearNotifiedSubstitutions();
		clearUserFromServiceWorker();
		if (substitutionInterval) {
			clearInterval(substitutionInterval);
			substitutionInterval = null;
		}
	}
	updateTime();

	// Lifecycle events
	onMount(async () => {
		// Aggiorna l'orario ogni secondo
		timeInterval = setInterval(updateTime, 1000);
	
		// Controlla lo stato delle notifiche
		checkNotificationPermission();
		
		// Setup background sync per il controllo offline
		await setupBackgroundSync();
		
		// Se l'utente è loggato, configura le notifiche e controlli
		if ($userEmail) {
			await requestNotificationPermission();
			syncUserEmailWithServiceWorker();
			checkSubstitutions();
			// Controlla le sostituzioni ogni minuto (configurabile)
			substitutionInterval = setInterval(checkSubstitutions, APP_CONFIG.substitutionCheckInterval);
		}
	});

	onDestroy(() => {
		// Pulisce tutti gli intervalli quando il componente viene distrutto
		clearInterval(timeInterval);
		if (substitutionInterval) {
			clearInterval(substitutionInterval);
		}
	});

	// Reagisci ai cambiamenti dello stato di login
	$: if ($userEmail) {
		// Utente appena loggato
		requestNotificationPermission().then(() => {
			syncUserEmailWithServiceWorker();
			checkSubstitutions();
			if (!substitutionInterval) {
				substitutionInterval = setInterval(checkSubstitutions,5000);
			}
		});
	} else if (substitutionInterval) {
		// Utente appena sloggato
		clearInterval(substitutionInterval);
		substitutionInterval = null;
		clearNotifiedSubstitutions();
		clearUserFromServiceWorker();
	}
</script>

<header>
	<div class="container">
		<nav>
			<ul>
				<li><strong>WAY</strong></li>
				<li><PwaButton /></li>
			</ul>
			<ul>
				<li>{day}</li>
				<li>{schoolHour}</li>
				{#if $userEmail}
					<!-- svelte-ignore a11y-missing-attribute -->
					<!-- svelte-ignore a11y-click-events-have-key-events -->
					<!-- svelte-ignore a11y-no-static-element-interactions -->
					<li><a on:click={logout}>Disconnetti</a></li>
				{:else}
					<li><a href="signin">Accedi</a></li>
				{/if}
				
			</ul>
		</nav>
		<Tabs></Tabs>
	</div>
</header>

<style>
	@media (prefers-color-scheme: dark) {
		header {
			background-color: #1d232f;
		}
	}

	header {
		background-color: var(--pico-muted-border-color);
	}


</style>
