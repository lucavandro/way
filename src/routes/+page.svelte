<script>
	import { onDestroy, onMount } from "svelte";
	import { getHourNum, getDay } from "$lib/dateutils.js";
	import ItemSelect from "./ItemSelect.svelte";
	import { getPrefClass, setPrefClass } from "$lib/utils.js";
	import { page } from "$app/stores";
	import TimeTable from "./TimeTable.svelte";
	import FullTimeTable from "./FullTimeTable.svelte";
	import FullTimeTableSwitch from "./FullTimeTableSwitch.svelte";
	export let data;
	let classes,
		classrooms,
		currentHour,
		currenDay = getDay(),
		teachers = [],
		selectedClass,
		showFullTimeTable = false,
		interval;

	// Declarations
	$: classWeekData = data?.data?.filter((e) => e.classe === selectedClass && e.materia != "INCL");
	$: classData = classWeekData?.filter((e) => e.day === currenDay);
	$: classes = data.classi.filter((e) => e != "");
	$: classrooms = data.aule;

	// Functions
	function onSelectedItemChange() {
		let queryClass = $page.url.searchParams.get("q");
		if (!queryClass || queryClass !== selectedClass) {
			setPrefClass(selectedClass);
		}
	}

	// Lifecycle's events
	onMount(() => {
		// Read query params to handle link from other pages
		let queryClass = $page.url.searchParams.get("q");
		if (queryClass && data.classi.includes(queryClass)) {
			selectedClass = queryClass;
		} else {
			selectedClass = getPrefClass() || classes[0];
		}

		interval = setInterval(() => {
			currenDay = getDay();
			currentHour = getHourNum();
		}, 1000);
		showFullTimeTable = JSON.parse(sessionStorage.getItem('way:classe:showFullTable'))
	});

	onDestroy(() => {
		clearInterval(interval)
		sessionStorage.setItem('way:classe:showFullTable', JSON.stringify(showFullTimeTable))
	});
</script>

<div>
	<ItemSelect
		label="Classe"
		bind:item={selectedClass}
		list={classes}
		onChange={onSelectedItemChange}
	/>

	<FullTimeTableSwitch bind:control={showFullTimeTable} />
	{#if showFullTimeTable}
		<FullTimeTable
			bind:tableData={classWeekData}
			fields={["materia", "docente"]}
		/>
	{:else}
		<TimeTable
			bind:data={classData}
			fields={["docente", "materia"]}
		/>
	{/if}
</div>
