<script>
	import { onMount } from 'svelte';
	import { createEventDispatcher } from 'svelte';
	const dispatch = createEventDispatcher();

	export let from = "";
	export let to = "";

	export let show = null;
	
	onMount(() => {
		// Have Material Design Lite register the buttons so it can show the ripple effect
		componentHandler.upgradeElements(document.getElementById("move"))
	});

	const handleSubmit = (event) => {
		dispatch('submit', {
			"from": event.currentTarget.from.value,
			"to": event.currentTarget.to.value
		});
	}

</script>

<style>
	.mdl-card-wide {
		width: 100%;
	}
</style>

<!-- move -->
<form class="mdl-card mdl-card-wide mdl-shadow--2dp" class:hidden="{!show}" on:submit|preventDefault="{handleSubmit}" id="move">

	<div class="mdl-card__title mdl-card--expand">
		<h2 class="mdl-card__title-text">Move {from.split('\\').pop().split('/').pop()}</h2>
	</div>

	<div class="mdl-card__supporting-text">
		Move <strong>{from.split('\\').pop().split('/').pop()}</strong> to <strong>{to.split('\\').pop().split('/').pop()}</strong>
	</div>

	<div class="mdl-card__actions mdl-card--border">
		<form on:submit|preventDefault="{handleSubmit}">
			<input type="hidden" name="from" value="{from}"/>
			<input type="hidden" name="to" value="{to}"/>
			
			<button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
				Move
			</button>
		</form>
	</div>

	<div class="mdl-card__menu">
		<button on:click|preventDefault="{() => show = false}" class="mdl-button mdl-js-button mdl-button--icon mdl-button-right">
			<i class="material-icons">close</i>
		</button>
	</div>
</form>