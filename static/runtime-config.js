/**
 * WAY - Runtime Configuration
 * 
 * Configurazione disponibile globalmente per script inline in app.html
 * Questo file viene importato come script e non come modulo ES6
 */

window.WAY_CONFIG = {
    APP_BASE_PATH: '', // Path base es. /app/way
    APP_DOMAIN: '', // Dominio es. https://www.icvespasiano.edu.it
    get APP_BASE_URL() {
        return this.APP_DOMAIN + this.APP_BASE_PATH;
    }
};