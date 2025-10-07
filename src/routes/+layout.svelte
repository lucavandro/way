<script>
	import Header from "./Header.svelte";
	import { getDay } from "$lib/dateutils.js";
	import { onDestroy, onMount } from "svelte";
    import Footer from "./Footer.svelte";
	import { EXTERNAL_URLS } from "$lib/config.js";

	let day = getDay();
	let intervalTimer;

	// Lifecycle events
	onMount(() => {
		// Aggiorna il giorno ogni minuto
		intervalTimer = setInterval(() => {
			day = getDay();
		}, 60 * 1000);
	});

	onDestroy(() => {
		clearInterval(intervalTimer);
	});
	
</script>

<svelte:head>
	<!-- Framework CSS PicoCSS -->
	<link
		rel="stylesheet"
		href="{EXTERNAL_URLS.picoCSS}"
	/>
	<script>
		// Gestisce l'evento per l'installazione PWA
		window.addEventListener("beforeinstallprompt", (event) => {
			event.preventDefault();
			window.deferredInstallPrompt = event;
		});
	</script>
</svelte:head>

<div class="app">
	<Header />

	<main class="container-fluid">
		<slot />
	</main>

	<Footer />
</div>

<style>
	.app {
		display: flex;
		flex-direction: column;
	}

	main {
		flex: 1;
		display: flex;
		flex-direction: column;
		padding: 1rem;
		width: 100%;
		max-width: 64rem;
		margin: 0 auto;
		box-sizing: border-box;
	}


</style>
