/**
 * WAY - App orario del Liceo Scientifico Nino Cortese
 * 
 * Copyright (c) 2025, WAY Project
 * Licensed under the BSD 3-Clause License
 */
import { userEmail } from "./stores";
import { API_URLS, HTTP_CONFIG } from "./config";

/**
 * Recupera i dati principali dell'applicazione (orari, classi, user)
 * @param {Function} fetch - Funzione fetch (da SvelteKit o standard)
 * @returns {Promise<Object>} Dati dell'applicazione
 */
export async function getData(fetch){
    const res = await fetch(
        API_URLS.main,
        HTTP_CONFIG.corsConfig
    );
    const data = await res.json();
    
    // Imposta l'email dell'utente se presente
    if(data.user){
        userEmail.set(data.user);
    }
    
    // Filtra le classi rimuovendo quelle con punti o asterischi
    data.classi = data.classi.filter(e=> !e.includes(".") && !e.includes("*"))
    
    // Pulisce i dati delle classi rimuovendo caratteri speciali
    data.data = data.data.map(e=>{
        if(e.classe.includes(".")){
            e.classe = e.classe.replace(".", "")
        } else if(e.classe.includes("*")){
            e.classe = e.classe.replace("*", "")
            // Se non c'è un'aula specificata, usa un placeholder
            if(!e.aula)
                e.aula = "-"
        }
        return e
    })
    
    return data
}

/**
 * Invia una richiesta di autenticazione con email
 * @param {string} email - Email dell'utente
 * @returns {Promise<Object>} Risposta del server
 */
export async function auth(email) {
    const res = await fetch(
        API_URLS.auth,
        {
            method: "POST",
            ...HTTP_CONFIG.corsConfig,
            headers: HTTP_CONFIG.jsonHeaders,
            body: JSON.stringify({ email: email })
        }
    );

    const data = await res.json();
    return data;
}

/**
 * Conferma l'autenticazione con email e codice
 * @param {string} email - Email dell'utente
 * @param {string} code - Codice di conferma
 * @returns {Promise<Object>} Risposta del server
 */
export async function confirm(email, code) {
    const res = await fetch(
        API_URLS.auth,
        {
            method: "POST",
            ...HTTP_CONFIG.corsConfig,
            headers: HTTP_CONFIG.jsonHeaders,
            body: JSON.stringify({ email: email, code: code })
        }
    );

    const data = await res.json();
    // Se l'autenticazione ha successo, salva l'email dell'utente
    if(data.success){
        userEmail.set(email);
    }
    return data;
}

/**
 * Richiede il logout dell'utente
 * @returns {Promise<Object>} Risposta del server
 */
export async function requestLogout() {
    const res = await fetch(
        API_URLS.auth,
        {
            method: "POST",
            ...HTTP_CONFIG.corsConfig,
            headers: HTTP_CONFIG.jsonHeaders,
            body: JSON.stringify({ action: "logout" })
        }
    );

    const data = await res.json();
    // Se il logout ha successo, rimuove l'email dell'utente
    if(data.success){
        userEmail.set(null);
    }
    return data;
}

/**
 * Invia la conferma di una sostituzione
 * @param {string|number} id - ID della sostituzione da confermare
 * @returns {Promise<Object>} Risposta del server
 */
export async function inviaConfermaSostituzione(id){
    const res = await fetch(
        API_URLS.teacherSubstitutions,
        {
            method: "POST",
            ...HTTP_CONFIG.corsConfig,
            headers: HTTP_CONFIG.jsonHeaders,
            body: JSON.stringify({ id: id }),
        }
    );

    const data = await res.json();
    return data;
}