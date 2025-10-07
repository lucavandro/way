<script>
    import { onMount } from 'svelte';
    import QRCode from 'qrcode';
    import { ASSET_URLS } from "$lib/config.js";
    
    let showIns = false;
    let qrCodeDataUrl = '';
    let qrCodeContainer;
    let isGenerating = true;
    let generationError = false;
    const canShare = navigator.share !== undefined;
    const appURL = ASSET_URLS.appUrl;
    
    /**
     * Genera il QR code dinamicamente
     */
    async function generateQRCode() {
        try {
            isGenerating = true;
            generationError = false;
            
            // Genera il QR code come data URL
            qrCodeDataUrl = await QRCode.toDataURL(appURL, {
                width: 350,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#ffffff'
                },
                errorCorrectionLevel: 'M'
            });
            
            isGenerating = false;
        } catch (error) {
            // Errore nella generazione del QR code
            isGenerating = false;
            generationError = true;
        }
    }
    
    /**
     * Scarica il QR code come file PNG
     */
    function downloadQRCode() {
        if (qrCodeDataUrl) {
            const link = document.createElement('a');
            link.download = 'QR_WAY.png';
            link.href = qrCodeDataUrl;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
    
    /**
     * Condivide l'app usando l'API nativa di condivisione
     */
    async function share() {
        if (navigator.share) {
            try {
                // Prova a condividere anche l'immagine QR se supportato
                if (qrCodeDataUrl && navigator.canShare) {
                    // Converte il data URL in un file blob
                    const response = await fetch(qrCodeDataUrl);
                    const blob = await response.blob();
                    const file = new File([blob], 'QR_WAY.png', { type: 'image/png' });
                    
                    // Controlla se può condividere file
                    if (navigator.canShare({ files: [file] })) {
                        await navigator.share({
                            title: "WAY",
                            text: "Accedi all'app WAY",
                            url: appURL,
                            files: [file]
                        });
                        return;
                    }
                }
                
                // Fallback: condividi solo il link
                await navigator.share({
                    title: "WAY",
                    text: "Accedi all'app WAY",
                    url: appURL
                });
            } catch (error) {
                // Se la condivisione fallisce, copia il link
                copyLink();
            }
        } else {
            alert("La condivisione nativa non è supportata su questo browser.");
        }
    }

    /**
     * Copia il link dell'app negli appunti
     */
    function copyLink() {
        navigator.clipboard.writeText(appURL);
        showIns = true;
        // Nasconde il messaggio dopo 3 secondi
        setTimeout(() => showIns = false, 3000);
    }
    
    // Genera il QR code quando il componente viene montato
    onMount(() => {
        generateQRCode();
    });
</script>

<div class="text-center">
    {#if qrCodeDataUrl}
        <img src="{qrCodeDataUrl}" alt="QR Code per accedere all'app WAY" id="qr-code">
    {:else if isGenerating}
        <div class="qr-placeholder">
            <p>⏳ Generazione QR Code...</p>
        </div>
    {:else if generationError}
        <div class="qr-placeholder error">
            <p>❌ Errore nella generazione del QR Code</p>
            <button on:click={generateQRCode} class="retry-btn">🔄 Riprova</button>
        </div>
    {/if}
    
    {#if canShare}
        <button on:click={share}>Condividi</button>
        <button on:click={downloadQRCode} disabled={!qrCodeDataUrl}>Scarica QR Code</button>
    {:else}
        <input type="text" readonly value="{appURL}">
        <button on:click={copyLink}>Copia link</button>
        <button on:click={downloadQRCode} disabled={!qrCodeDataUrl}>Scarica QR Code</button>
        {#if showIns}
            <ins>Link copiato negli appunti!</ins>
        {/if}
    {/if}
</div>

<style>
    button,
    img {
        width: 350px;
        height: auto;
        margin: 5px auto;
        display: block;
    }

    .qr-placeholder {
        width: 350px;
        height: 350px;
        margin: 5px auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 2px dashed #ccc;
        border-radius: 8px;
        background-color: #f9f9f9;
    }

    .qr-placeholder.error {
        border-color: #ff6b6b;
        background-color: #ffe0e0;
    }

    .qr-placeholder p {
        color: #666;
        font-style: italic;
        margin: 0 0 10px 0;
    }

    .qr-placeholder.error p {
        color: #d63031;
    }

    .retry-btn {
        width: auto !important;
        padding: 8px 16px;
        font-size: 14px;
        margin: 0;
    }

    button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    #qr-code {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
</style>