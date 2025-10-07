/**
 * WAY - App orario del Liceo Scientifico Nino Cortese
 * 
 * Copyright (c) 2025, WAY Project
 * Licensed under the BSD 3-Clause License
 * 
 * Store Svelte per la gestione dello stato globale dell'applicazione
 */
import { writable } from 'svelte/store'

/**
 * Store per l'email dell'utente attualmente loggato
 * @type {import('svelte/store').Writable<string|null>}
 */
export const userEmail = writable(null);

/**
 * Store per il permesso delle notifiche
 * @type {import('svelte/store').Writable<boolean>}
 */
export const notificationPermission = writable(false);



