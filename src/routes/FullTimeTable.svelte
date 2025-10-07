<script>

    import FullTimeTableRow from "./FullTimeTableRow.svelte";
    import { weekdays } from "$lib/dateutils.js"
    export let tableData = [];
    export let fields = [];

    $: rowsData = [
        tableData.filter((e) => e.ora === "08:15"),
        tableData.filter((e) => e.ora === "09:15"),
        tableData.filter((e) => e.ora === "10:15"),
        tableData.filter((e) => e.ora === "11:15"),
        tableData.filter((e) => e.ora === "12:15"),
        tableData.filter((e) => e.ora === "13:15"),
    ]
</script>


<div class="overflow-auto">
    <table class="striped">
        <thead>
            <th class="fixed">#</th>
            {#each weekdays.slice(0,6) as weekday}
                <th>{weekday}</th>
            {/each}
        </thead>
        <tbody>
            {#each {length: 6} as _, i}
               <FullTimeTableRow hourIndex={i} bind:rowData={rowsData[i]} fields={fields} />
            {/each}
        </tbody>
    </table>
   
</div>

<style>
    thead th {
        font-size: 0.75rem;
    }
</style>
