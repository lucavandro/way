<script>

    import TimeTableRow from "./TimeTableRow.svelte";
    export let data = [];
    export let fields = [];

  
</script>

<div class="overflow-auto">
    <table class="striped">
        <thead>
            <th>#</th>
            {#each fields as field}
                <th>{field}</th>
            {/each}
        </thead>
        <tbody>
            {#each {length: 6} as _, i}
               <TimeTableRow hourIndex={i} bind:data={data} fields={fields} />
            {/each}
        </tbody>
    </table>
</div>
