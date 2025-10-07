/**
 * WAY - App orario del Liceo Scientifico Nino Cortese
 * 
 * Copyright (c) 2025, WAY Project
 * Licensed under the BSD 3-Clause License
 * 
 * Utility functions per l'applicazione WAY
 */
import { hours } from "./dateutils"

/**
 * Recupera il docente preferito dal localStorage
 * @returns {string|null} Il nome del docente preferito
 */
export function getPrefTeacher() {
   return localStorage.getItem("prefTeacher")
}

/**
 * Salva il docente preferito nel localStorage
 * @param {string} value - Il nome del docente da salvare
 */
export function setPrefTeacher(value) {
    if(value)
        localStorage.setItem("prefTeacher", value)
}

/**
 * Recupera la classe preferita dal localStorage
 * @returns {string|null} La classe preferita
 */
export function getPrefClass() {
    return localStorage.getItem("prefClass")
}
 
/**
 * Salva la classe preferita nel localStorage
 * @param {string} value - La classe da salvare
 */
export function setPrefClass(value) {
     if(value)
         localStorage.setItem("prefClass", value)
}

/**
 * Recupera l'aula preferita dal localStorage
 * @returns {string|null} L'aula preferita
 */
export function getPrefClassroom() {
    return localStorage.getItem("prefClassroom")
}
 
/**
 * Salva l'aula preferita nel localStorage
 * @param {string} value - L'aula da salvare
 */
export function setPrefClassroom(value) {
     if(value)
         localStorage.setItem("prefClassroom", value)
}

/**
 * Filtra i dati per un determinato orario
 * @param {Array} data - Array dei dati da filtrare
 * @param {number} hour - Indice dell'ora
 * @returns {Array} Dati filtrati per l'ora specificata
 */
export function getDataByHourIndex(data, hour){
    return data.filter(e => e.ora === hours[hour])
}

/**
 * Valida se un'email appartiene al dominio della scuola
 * @param {string} email - Email da validare
 * @returns {boolean} True se l'email è valida
 */
export function validateEmail(email) {
  return email && typeof email === 'string' && email.endsWith('@lscortese.com');
}

/**
 * Restituisce la data odierna in formato YYYY-MM-DD
 * @returns {string} Data odierna
 */
export function getTodayDate() {
  const today = new Date();
  return today.toISOString().split('T')[0];
}

/**
 * Controlla se una data è precedente a un'altra
 * @param {string} date1 - Prima data in formato YYYY-MM-DD
 * @param {string} date2 - Seconda data in formato YYYY-MM-DD
 * @returns {boolean} True se date1 è precedente a date2
 */
export function isDateBefore(date1, date2) {
  return new Date(date1) < new Date(date2);
}


export    const inclusioneInFondo = (a, b) => a.materia === "INC" ? 1 : -1

