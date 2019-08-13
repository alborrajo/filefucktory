<script>
	import { onMount } from 'svelte';
	import { createEventDispatcher } from 'svelte';
	const dispatch = createEventDispatcher();
	
	export let progress = [];

	export let path = "";

	export let show = null;
	
	onMount(() => {
		// Have Material Design Lite register the buttons so it can show the ripple effect
		componentHandler.upgradeElements(document.getElementById("move"))
	});

	const handleSubmit = (event) => {
		if(event.currentTarget.files.files !== undefined) {
			dispatch('submit', {
				"path": event.currentTarget.path.value,
				"files": event.currentTarget.files.files
			});
		}
	}
	
	const updateFileNames = (event) => {
		progress = [];
		for(let i=0; i < event.currentTarget.files.length; i++) {
			progress.push({
				name: event.currentTarget.files[i].name,
				progress: 0,
				status: "primary"
			});
		}
	}
</script>

<style>
	.mdl-card-wide {
		width: 100%;
	}
	
	#file {
		display: none;
	}
	
	.progress {
		background-color: rgb(207, 208, 214);
		position: relative;
		height: 0.5em;
	}

	.progress-status {
		background-color: rgb(63,81,181);
		height: 100%;
		display: block;
		position: relative;
		overflow: hidden;
	}
</style>

<!-- upload -->
<form class="mdl-card mdl-card-wide mdl-shadow--2dp" class:hidden="{!show}" on:submit|preventDefault="{handleSubmit}" id="upload">

	<div class="mdl-card__title mdl-card--expand">
		<h2 class="mdl-card__title-text">Upload</h2>
	</div>

	<div class="mdl-card__supporting-text">
		Upload to: <strong>{path}</strong><br/>
		
		<div class="custom-file">
			<input type="hidden" name="path" value="{path}" />
			
			<input id="file" type="file" multiple name="files" on:change="{updateFileNames}">
			<label for="file" class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored">
				<i class="material-icons">add</i>
			</label>			
		</div>
		
		<ul class="demo-list-item mdl-list">
			{#each progress as prog} 
				<li>
					<div class="mdl-list__item"><div class="mdl-list__item-primary-content">
						{prog.name}
					</div></div>
					
					<div class="progress" style="width:100%">
						<span class="progress-status" style="width: {prog.progress}%">&nbsp;</span>
					</div>
					
					<br/>
				</li>
			{/each}
		</ul>
		
	</div>

	<div class="mdl-card__actions mdl-card--border">			
		<button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
			Upload
		</button>
	</div>

	<div class="mdl-card__menu">
		<button on:click|preventDefault="{() => show = false}" class="mdl-button mdl-js-button mdl-button--icon mdl-button-right">
			<i class="material-icons">close</i>
		</button>
	</div>
</form>