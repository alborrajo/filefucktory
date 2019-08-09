<script>
	import { onMount } from 'svelte';
	import { createEventDispatcher } from 'svelte';
	const dispatch = createEventDispatcher();

	export let setPublic = false;
	export let path = "";

	export let show = false;
	
	onMount(() => {
		// Have Material Design Lite register the buttons so it can show the ripple effect
		document.querySelectorAll(".mdl-js-button").forEach(
			node => componentHandler.upgradeElement(node)
		);
	});

	function handleSubmit(event) {
		dispatch('submit', {
			"path": event.currentTarget.path.value,
			"public": setPublic
		});
	}

</script>

<style>
	.mdl-card-wide {
		width: 100%;
	}
</style>

<!-- mkdir -->
<form class="mdl-card mdl-card-wide mdl-shadow--2dp" class:hidden="{!show}" on:submit|preventDefault="{handleSubmit}">

	<div class="mdl-card__title mdl-card--expand">
		<h2 class="mdl-card__title-text">Set as {setPublic ? "public": "private"}</h2>
	</div>
	
	<div class="mdl-card__supporting-text">
		<input type="hidden" name="path" value="{path}"/>
		<input type="hidden" name="public" value="{setPublic}"/>
		<p>Are you sure you want to set {path.split('\\').pop().split('/').pop()} as <strong>{setPublic ? "public" : "private"}</strong>?</p>
	</div>
	
	<div class="mdl-card__actions mdl-card--border">
		<button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
			Set
		</button>
	</div>
	
	<div class="mdl-card__menu">
		<button on:click|preventDefault="{() => show = false}" class="mdl-button mdl-js-button mdl-button--icon mdl-button-right">
			<i class="material-icons">close</i>
		</button>
	</div>
</form>