<script>
    import OfflineIconDark from '$lib/images/icons/offline-icon-dark.svg'
    import OfflineIconLight from '$lib/images/icons/offline-icon-light.svg'
</script>

<div>
    <img width="150"  src="{ window.matchMedia('(prefers-color-scheme: light)') ? OfflineIconLight : OfflineIconDark}" alt="Sei offline" >
    <h3>Sei offline</h3>
    <p>Ricarica l'app appena hai connessione</p>
</div>

<style>
    img{
        margin-bottom: 2rem;
    }
    div{
        align-content: center;
        text-align: center;
    }
 
</style>