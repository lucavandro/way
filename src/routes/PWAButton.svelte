<script>
    import { onMount } from "svelte";
    // PWA prompt
    let deferredPrompt;
    let installButtonVisible = false;

    onMount(() => {
        if ("deferredInstallPrompt" in window) {
            // deferredInstallPrompt found
            deferredPrompt = window.deferredInstallPrompt;
            installButtonVisible = true;
        } else if ("BeforeInstallPromptEvent" in window) {
            // BeforeInstallPromptEvent supported but not fired yet
        } else {
            // BeforeInstallPromptEvent NOT supported
        }

        window.addEventListener("beforeinstallprompt", (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installButtonVisible = true;
            // BeforeInstallPromptEvent fired
        });

        window.addEventListener("appinstalled", (e) => {
            // AppInstalled fired
            installButtonVisible = false;
        });
    });

    async function installApp() {
        // installApp button clicked
        if (deferredPrompt) {
            deferredPrompt.prompt();
            // Installation Dialog opened
            // Find out whether the user confirmed the installation or not
            const { outcome } = await deferredPrompt.userChoice;
            // The deferredPrompt can only be used once.
            deferredPrompt = null;
            // Act on the user's choice
            if (outcome === "accepted") {
                // User accepted the install prompt
                // Hide the install button
                installButtonVisible = false;
            } else if (outcome === "dismissed") {
                // User dismissed the install prompt
            }
        }
    }
</script>

{#if installButtonVisible}
    <button on:click={installApp} class="outline contrast"> Installa </button>
{/if}

<style>
</style>
