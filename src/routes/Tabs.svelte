<script>
	import { page } from "$app/stores";
	import { base} from "$app/paths";
	import { userEmail } from "$lib/stores.js";

</script>

<nav id="tabs">
	<ul>
		<li>
			<a href="." class:active={$page.url.pathname === base + "/"}
				>Classe</a
			>
		</li>
		<li>
			<a
				href="docente"
				class:active={$page.url.pathname === base + "/docente"}
				>Docente</a
			>
		</li>
		{#if $userEmail}
			<li>
				<a href="sostituzioni" class:active={$page.route.id === '/sostituzioni'}>Sostituzioni</a>
			</li>
		{/if}
		
		
	</ul>
</nav>

<style>
	/* Stile di base per il contenitore dei tab */
	#tabs {
		display: flex;
		justify-content: space-around;
	}

	/* Rimuove lo stile predefinito della lista */
	#tabs ul {
		list-style-type: none;
		padding: 0;
		margin: 0;
		display: flex;
		width: 100%;
	}

	/* Stile per ogni singolo tab */
	#tabs li {
		flex: 1;
		text-align: center;
		padding-bottom: 8px;
	}

	/* Stile per i link all'interno dei tab */
	#tabs a {
		display: flex;
		flex-direction: column;
		align-items: center;
		text-decoration: none;
		border-radius: 0;
		color: var(--pico-color)
	}



	/* Stile per il tab attivo */
	#tabs .active {
		font-weight: bold;
		border-bottom: 2px solid var(--pico-color);
		color: var(--pico-color);
	}
</style>
