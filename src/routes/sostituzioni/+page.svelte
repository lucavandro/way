<script>
    import { userEmail, notificationPermission } from "$lib/stores.js";
    import { goto } from "$app/navigation";
    import { onMount, onDestroy } from "svelte";
    import { inviaConfermaSostituzione} from "$lib/data";
    import { requestNotificationPermission } from "$lib/notifications.js";
    import { getTodayDate, isDateBefore } from "$lib/utils.js";
    import { API_URLS } from "$lib/config.js";

    let sostituzioni = [];
    let loading = false;
    let error = null;
    let interval;
    let confirmingIds = new Set(); // Per tracciare quali sostituzioni sono in fase di conferma
    let errorMessages = {}; // Per tracciare errori specifici per ogni sostituzione

    // Computed per filtrare le sostituzioni odierne
    $: sostituzioniOggi = sostituzioni.filter((s) => s.data === getTodayDate());

    // Computed per filtrare le sostituzioni passate
    $: sostituzioniPassate = sostituzioni.filter((s) => isDateBefore(s.data, getTodayDate()));

    /**
     * Recupera le sostituzioni dal server
     */
    async function fetchSostituzioni() {
        if (!$userEmail) return;

        error = null;

        try {
            const response = await fetch(
                `${API_URLS.teacherSubstitutions}?email=${encodeURIComponent($userEmail)}`,
            );
            const data = await response.json();

            if (data.success && data.data != sostituzioni) {
                sostituzioni = data.data;
            } else {
                error = "Errore nel recupero dei dati";
            }
        } catch (err) {
            error = "Errore di connessione";
            // Errore fetch sostituzioni
        }
    }

    async function confermaSostituzione(id) {
        confirmingIds.add(id);
        confirmingIds = confirmingIds; // Trigger reactivity

        // Rimuovi eventuali errori precedenti
        delete errorMessages[id];
        errorMessages = errorMessages;

        
        try {
            const response= await inviaConfermaSostituzione(id);

            if (response.success) {
                // Aggiorna lo stato della sostituzione localmente
                sostituzioni = sostituzioni.map((s) =>
                    s.id === id ? { ...s, accettato: true } : s,
                );
            } else {
                errorMessages[id] = "Errore nella conferma della sostituzione";
                errorMessages = errorMessages;
            }
        } catch (err) {
            // Errore fetch conferma sostituzione
            errorMessages[id] = "Errore di connessione";
            errorMessages = errorMessages;
        } finally {
            confirmingIds.delete(id);
            confirmingIds = confirmingIds;
        }
    }

    async function enableNotifications() {
        await requestNotificationPermission();
    }

    onMount(() => {
        // Reindirizza alla home se l'utente non è loggato
        if (!$userEmail) {
            goto("/");
        } else {
            fetchSostituzioni();
            // Aggiorna i dati ogni minuto (5000ms)
            interval = setInterval(fetchSostituzioni, 5000);
        }
    });

    onDestroy(() => {
        if (interval) {
            clearInterval(interval);
        }
    });
</script>

<svelte:head>
    <title>Sostituzioni - WAY</title>
</svelte:head>

<main class="container">
    {#if $userEmail}
        <!-- Banner notifiche -->
        {#if !$notificationPermission}
            <article class="notification-banner">
                <header>
                    <strong>🔔 Abilita le notifiche</strong>
                </header>
                <p>
                    Le notifiche ti aiutano a rimanere aggiornato sulle sostituzioni che richiedono conferma. 
                    Riceverai un avviso quando ci sono sostituzioni non confermate per la giornata odierna.
                </p>
                <footer>
                    <button on:click={enableNotifications} class="enable-notifications-btn">
                        Abilita notifiche
                    </button>
                </footer>
            </article>
        {/if}

        {#if error}
            <article class="error">
                <p>{error}</p>
                <button on:click={fetchSostituzioni}>Riprova</button>
            </article>
        {:else}
            <!-- Sezione sostituzioni odierne -->
            <section class="today-section">
                <h2>Sostituzioni di oggi</h2>
                {#if sostituzioniOggi.length > 0}
                    <div class="today-cards">
                        {#each sostituzioniOggi as sostituzione}
                            <article class="today-card">
                                <header>
                                    <strong
                                        >{sostituzione.ora} - {sostituzione.classe}</strong
                                    >
                                    <div class="header-actions">
                                        {#if sostituzione.accettato}
                                            <span
                                                class="badge"
                                                class:accepted={sostituzione.accettato}
                                                class:rejected={!sostituzione.accettato}
                                            >
                                                {sostituzione.accettato
                                                    ? "Accettato"
                                                    : "Rifiutato"}
                                            </span>
                                        {:else}
                                            <button
                                                class="confirm-btn"
                                                on:click={() =>
                                                    confermaSostituzione(
                                                        sostituzione.id,
                                                    )}
                                                disabled={confirmingIds.has(
                                                    sostituzione.id,
                                                )}
                                                aria-busy={confirmingIds.has(
                                                    sostituzione.id,
                                                )}
                                            >
                                                {confirmingIds.has(
                                                    sostituzione.id,
                                                )
                                                    ? "Confermando..."
                                                    : "Conferma"}
                                            </button>
                                        {/if}
                                    </div>
                                </header>
                                <p>
                                    <strong>Aula:</strong>
                                    {sostituzione.aula}
                                </p>
                                <p>
                                    <strong>Docente da sostituire: </strong>
                                    {sostituzione.docSost}
                                </p>
                                {#if sostituzione.note}
                                    <p>
                                        <strong>Note:</strong>
                                        {sostituzione.note}
                                    </p>
                                {/if}
                                {#if errorMessages[sostituzione.id]}
                                    <div class="error-message">
                                        {errorMessages[sostituzione.id]}
                                    </div>
                                {/if}
                            </article>
                        {/each}
                    </div>
                {:else}
                    <p class="no-substitutions">
                        Non sono previste sostituzioni per te oggi. 🥳
                    </p>
                {/if}
            </section>

            <!-- Sezione storico -->
            {#if sostituzioniPassate.length > 0}
                <section class="history-section">
                    <h2>Storico sostituzioni</h2>
                    <p>Trovate {sostituzioniPassate.length} sostituzioni passate</p>
                    
                    <div class="history-cards">
                        {#each sostituzioniPassate as sostituzione}
                            <article class="history-card">
                                <header>
                                    <div class="card-header-left">
                                        <strong>{sostituzione.data} - {sostituzione.giorno}</strong>
                                        <small>{sostituzione.ora}</small>
                                    </div>
                                    <span class="badge" class:accepted={sostituzione.accettato} class:rejected={!sostituzione.accettato}>
                                        {sostituzione.accettato ? "Accettato" : "Rifiutato"}
                                    </span>
                                </header>
                                <div class="card-content">
                                    <div class="card-row">
                                        <span class="label">Classe:</span>
                                        <span class="value">{sostituzione.classe}</span>
                                    </div>
                                    <div class="card-row">
                                        <span class="label">Aula:</span>
                                        <span class="value">{sostituzione.aula}</span>
                                    </div>
                                    <div class="card-row">
                                        <span class="label">Titolare:</span>
                                        <span class="value">{sostituzione.docSost}</span>
                                    </div>
                                    {#if sostituzione.note}
                                        <div class="card-row">
                                            <span class="label">Note:</span>
                                            <span class="value">{sostituzione.note}</span>
                                        </div>
                                    {/if}
                                </div>
                            </article>
                        {/each}
                    </div>
                </section>
            {:else if sostituzioni.length > 0}
                <section class="history-section">
                    <h2>Storico sostituzioni</h2>
                    <p>Nessuna sostituzione passata trovata.</p>
                </section>
            {/if}
        {/if}
    {:else}
        <h1>Accesso negato</h1>
        <p>Devi effettuare l'accesso per visualizzare questa pagina.</p>
    {/if}
</main>

<style>
    .error {
        background-color: var(--pico-del-background-color);
        border: 1px solid var(--pico-del-color);
        border-radius: var(--pico-border-radius);
        padding: 1rem;
        margin: 1rem 0;
    }

    .history-section {
        margin-top: 2rem;
    }

    .history-cards {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        margin-top: 1rem;
    }

    .history-card {
        background: var(--pico-background-color);
        border: 1px solid var(--pico-muted-border-color);
        border-radius: var(--pico-border-radius);
        margin: 0;
        transition: box-shadow 0.2s ease;
    }

    .history-card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .history-card header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 1rem 1rem 0.5rem 1rem;
        margin: 0;
        border-bottom: 1px solid var(--pico-muted-border-color);
        gap: 1rem;
    }

    .card-header-left {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .card-header-left strong {
        font-size: 1rem;
        margin: 0;
    }

    .card-header-left small {
        color: var(--pico-muted-color);
        font-size: 0.875rem;
    }

    .card-content {
        padding: 1rem;
    }

    .card-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
        gap: 1rem;
    }

    .card-row:last-child {
        margin-bottom: 0;
    }

    .card-row .label {
        font-weight: 500;
        color: var(--pico-muted-color);
        flex-shrink: 0;
        min-width: 120px;
    }

    .card-row .value {
        text-align: right;
        word-break: break-word;
    }

    /* Rimuovi gli stili della tabella obsoleti */
    .overflow-auto {
        display: none;
    }

    table {
        display: none;
    }

    @media (max-width: 768px) {
        .history-cards {
            grid-template-columns: 1fr;
        }
        
        .card-row {
            align-items: flex-start;
            gap: 0.25rem;
        }
        
        .card-row .value {
            text-align: left;
        }
        
        .card-row .label {
            min-width: auto;
        }
    }

    .badge {
        padding: 0.25rem 0.5rem;
        border-radius: var(--pico-border-radius);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .badge.accepted {
        background-color: var(--pico-ins-background-color);
        color: var(--pico-ins-color);
    }

    .badge.rejected {
        background-color: var(--pico-del-background-color);
        color: var(--pico-del-color);
    }

    .today-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid var(--pico-muted-border-color);
    }

    .today-cards {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        margin-top: 1rem;
    }

    .today-card {
        background: var(--pico-background-color);
        border: 1px solid var(--pico-muted-border-color);
        border-radius: var(--pico-border-radius);
        padding: 1rem;
        margin: 0;
    }

    .today-card header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--pico-muted-border-color);
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .confirm-btn {
        background-color: var(--pico-primary);
        color: var(--pico-primary-inverse);
        border: none;
        border-radius: var(--pico-border-radius);
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .confirm-btn:hover:not(:disabled) {
        background-color: var(--pico-primary-hover);
    }

    .confirm-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .error-message {
        background-color: var(--pico-del-background-color);
        color: var(--pico-del-color);
        padding: 0.5rem;
        border-radius: var(--pico-border-radius);
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .notification-banner {
        color: var(--pico-primary-inverse);
        border: none;
        margin-bottom: 2rem;
    }

    .notification-banner header strong {
        color: var(--pico-primary-inverse);
        font-size: 1.1rem;
    }

    .notification-banner p {
        margin: 1rem 0;
        opacity: 0.9;
    }



    @media (max-width: 768px) {
        table {
            font-size: 0.875rem;
        }

        th,
        td {
            padding: 0.5rem 0.25rem;
        }
    }
</style>
