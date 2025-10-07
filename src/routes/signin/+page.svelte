<script>
	import { auth, confirm } from "$lib/data.js";
	import { onDestroy, onMount } from "svelte";
	import { page } from "$app/stores";
	import {userEmail} from "$lib/stores.js";
	// Lib

	// Components

	let email = "",
		code = "",
		showConfirm = false,
		errorMessage = "",
		successMessage = "";


	// Handlers
	async function onSubmit(e) {
		e.preventDefault();
		if (showConfirm) {
			const response = await confirm(email, code);
			if (response.success) {
				userEmail.set(email);
				successMessage = "Accesso effettuato";
			} else {
				showConfirm = true;
				errorMessage = response.message;
				successMessage = ""
			}

		} else {
			const response = await auth(email);
			if (response.success) {
				showConfirm = true;
				errorMessage = ""
				successMessage = response.message;
			} else {
				showConfirm = false;
				errorMessage = response.message;
				successMessage = ""
			}
		}
	}

	// Lifecycle's events
	onMount(async () => {});

	onDestroy(() => {});
</script>

<div>
	
	<form on:submit={onSubmit}>
		<fieldset>
			<h4>Funzionalità riservata ai docenti</h4>
			{#if !$userEmail}
				<label>
					Email
					<input
						bind:value={email}
						type="email"
						id="email"
						name="email"
						placeholder="Inserisci la tua email @lscortese.com"
						readonly={showConfirm}
						required
					/>
				</label>
				{#if showConfirm}
					<label>
						Codice
						<input
							bind:value={code}
							type="text"
							id="code"
							name="code"
							placeholder="Inserisci il codice ricevuto"
							required
						/>
					</label>
				{/if}
				<button type="submit">Invia</button>
				{#if showConfirm}
				<button class="outline" on:click={()=>showConfirm=false} tabindex="0">Indietro</button>
				{/if}
				{#if errorMessage}
					<p class="error">{errorMessage}</p>
				{/if}
			{/if}
			{#if successMessage}
				<p class="success">{successMessage}</p>
			{/if}
		</fieldset>
	</form>
</div>

<style>
	fieldset {
		margin: 60px auto;
		max-width: 400px;
	}

	button {
		width: 100%;
		margin-bottom: 10px;
	}
</style>
