<script>
	import { onMount } from 'svelte';
	import { createEventDispatcher } from 'svelte';
	const dispatch = createEventDispatcher();

	export let path = "";

	export let show = null;

	onMount(() => {
		// Have Material Design Lite register the buttons so it can show the ripple effect
		componentHandler.upgradeElements(document.getElementById("mkdir"))
	});

	function handleSubmit(event) {
		dispatch('submit', {
			"path": event.currentTarget.path.value,
			"dirName": event.currentTarget.dirName.value,
			"public": event.currentTarget.public.checked
		});
	}

</script>

<style>
	.mdl-card-wide {
		width: 100%;
	}
</style>

<!-- mkdir -->
<form class="mdl-card mdl-card-wide mdl-shadow--2dp" class:hidden="{!show}" on:submit|preventDefault="{handleSubmit}" id="mkdir">

	<div class="mdl-card__title mdl-card--expand">
		<h2 class="mdl-card__title-text">Make directory</h2>
	</div>

	<div class="mdl-card__supporting-text">
		<input type="hidden" name="path" value="{path}"/>
		
		<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			<input class="mdl-textfield__input" type="text" name="dirName" id="dirName" required>
			<label class="mdl-textfield__label" for="dirName">Directory name</label>
		</div>
		
		<label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="dirPublic">
			<input type="checkbox" id="dirPublic" name="public" class="mdl-switch__input">
			<span class="mdl-switch__label">Public</span>
		</label>
	</div>

	<div class="mdl-card__actions mdl-card--border">
		<button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
			Make
		</button>
	</div>

	<div class="mdl-card__menu">
		<button on:click|preventDefault="{() => show = false}" class="mdl-button mdl-js-button mdl-button--icon mdl-button-right">
			<i class="material-icons">close</i>
		</button>
	</div>
</form>