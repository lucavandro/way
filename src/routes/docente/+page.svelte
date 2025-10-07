<script>
	import { onMount, onDestroy } from "svelte";
	// Lib
	import { getDay } from "$lib/dateutils.js";
	import { getPrefTeacher, setPrefTeacher } from "$lib/utils.js";
	import { page } from "$app/stores";
	// Components
	import TimeTable from "./../TimeTable.svelte";
	import ItemSelect from "./../ItemSelect.svelte";
	import FullTimeTable from "./../FullTimeTable.svelte";
	import FullTimeTableSwitch from "./../FullTimeTableSwitch.svelte";

	export let data;
	let interval,
		selectedTeacher,
		showFullTimeTable = false;

	// Declarations
	$: teacherWeekData = data?.data?.filter(
		(e) => e.docente === selectedTeacher,
	);
	$: teacherData = teacherWeekData?.filter((e) => e.day === getDay());
	$: teachers = data.docenti;


	onMount(async () => {
		let queryClass = $page.url.searchParams.get("q");
		if (queryClass && data.docenti.includes(queryClass)) {
			selectedTeacher = queryClass;
		} else {
			selectedTeacher = getPrefTeacher() || teachers[0];
		}

		showFullTimeTable = JSON.parse(sessionStorage.getItem('way:docente:showFullTable'))
	
	});

	onDestroy(() => {
		clearInterval(interval)
		sessionStorage.setItem('way:docente:showFullTable', JSON.stringify(showFullTimeTable))
	});

	function onSelectedItemChange() {
		let queryClass = $page.url.searchParams.get("q");
		if (!queryClass || queryClass !== selectedTeacher) {
			setPrefTeacher(selectedTeacher);
		}
	}
</script>

<div>
	<ItemSelect
		label="Docente"
		bind:item={selectedTeacher}
		list={teachers}
		onChange={onSelectedItemChange}
	/>
	<FullTimeTableSwitch bind:control={showFullTimeTable} />
	{#if showFullTimeTable}
		<FullTimeTable
			bind:tableData={teacherWeekData}
			fields={["classe", "materia"]}
		/>
	{:else}
		<TimeTable bind:data={teacherData} fields={["classe", "materia"]} />
	{/if}
</div>
