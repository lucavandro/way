/**
 * WAY - App orario del Liceo Scientifico Nino Cortese
 * 
 * Copyright (c) 2025, WAY Project
 * Licensed under the BSD 3-Clause License
 * 
 * Utility per la gestione di date e orari scolastici
 */

/**
 * Array dei giorni della settimana abbreviati
 */
export const weekdays = ["LUN", "MAR", "MER", "GIO", "VEN", "SAB", 'DOM'];

/**
 * Array degli orari delle lezioni
 */
export const hours = ["08:15", "09:15", "10:15", "11:15", "12:15", "13:15"];

/**
 * Calcola il numero dell'ora scolastica corrente
 * @returns {number} Numero dell'ora (0 = fuori orario, 1-6 = ore di lezione)
 */
export function getHourNum() {
    const now = new Date();
    const hours = now.getHours();
    const min = now.getMinutes();
    const sec = now.getSeconds();

    // Converte l'ora corrente in minuti trascorsi dalla prima ora di lezione
    const totalMin = hours * 60 + min + sec / 60;
    
    if (totalMin >= 8 * 60 + 15 && totalMin < 9 * 60 + 15) {
        return 1; // Prima ora
    } else if (totalMin >= 9 * 60 + 15 && totalMin < 10 * 60 + 15) {
        return 2; // Seconda ora
    } else if (totalMin >= 10 * 60 + 15 && totalMin < 11 * 60 + 15) {
        return 3; // Terza ora
    } else if (totalMin >= 11 * 60 + 15 && totalMin < 12 * 60 + 15) {
        return 4; // Quarta ora
    } else if (totalMin >= 12 * 60 + 15 && totalMin < 13 * 60 + 15) {
        return 5; // Quinta ora
    } else if (totalMin >= 13 * 60 + 15 && totalMin < 14 * 60 + 15) {
        return 6; // Sesta ora
    } else {
        return 0; // Fuori orario
    }
}

/**
 * Restituisce l'orario di inizio dell'ora scolastica corrente
 * @returns {string} Orario in formato HH:MM
 */
export function getHour(){
        return getHourNum() > 0 ? hours[getHourNum()-1] : hours[0]
}

/**
 * Restituisce il giorno corrente abbreviato
 * @returns {string} Giorno della settimana abbreviato (LUN, MAR, ecc.)
 */
export function getDay(){
    return weekdays[ new Date().getDay() - 1 ] || "DOM"
}

/**
 * Restituisce una descrizione testuale dell'ora scolastica corrente
 * @returns {string} Descrizione dell'ora (es. "Prima ora", "Fuori orario")
 */
export function getSchoolHour() {
    const hourNum = getHourNum()
    const lessonHourList = ["Fuori orario", "Prima ora", "Seconda ora", "Terza ora", "Quarta ora", "Quinta ora", "Sesta ora"]
    return lessonHourList[hourNum];
}

