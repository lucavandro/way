<script>
  let isLoading = false;
  function reload() {
    isLoading = true;
    setTimeout(() => location.reload(), 1000);
  }
  import ShareIcon from "../icons/ShareIcon.svelte";
  import ReloadIcon from "../icons/ReloadIcon.svelte";
  import StarIcon from "../icons/StarIcon.svelte";
  import { page } from "$app/stores";
  import { base } from "$app/paths";
</script>

{#if isLoading}
  <div class="container">
    <progress />
  </div>
{:else}
  <div></div>
{/if}

<footer>
  {#if isLoading}
    <progress />
  {/if}
  <div class="container">
    <button on:click={reload}>
      <ReloadIcon />
      <div>Ricarica</div>
    </button>
    <a
      role="button"
      href="qr"
      class:active={$page.url.pathname === base + "/qr"}
    >
      <ShareIcon />
      <div>Condividi</div>
    </a>
    <!--
    <a
      role="button"
      href="link"
      class:active={$page.url.pathname === base + "/link"}
    >
      <StarIcon />
      <div>Link utili</div>
    </a>
    -->
  </div>
</footer>

<style>
  footer {
    position: fixed;
    left: 0;
    bottom: 0;
    width: 100%;
    background-color: var(--pico-muted-border-color);
  }

  footer .container {
    display: flex;
    justify-content: space-around;
  }
  footer button,
  footer [role="button"] {
    background: none;
    border: none;
    font-size: 0.8em;
    cursor: pointer;
    color: var(--pico-primary-background);
    padding: 10px 6px;
  }
  @media (prefers-color-scheme: dark) {
    footer button,
    footer [role="button"] {
      color: white;
    }
  }
  div {
    text-align: center;
  }
  a.active {
		font-weight: bold;
		color: var(--pico-primary);
	}

   
</style>
