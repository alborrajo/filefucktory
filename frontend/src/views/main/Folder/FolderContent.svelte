<script>
	import { onMount } from 'svelte';
	import { createEventDispatcher } from 'svelte';
	const dispatch = createEventDispatcher();

	import File from './File.svelte';	

	export let rootDir = false;
	
	export let path;
	export let folder;
	
	export let user;
	
	onMount(() => {
		// Have Material Design Lite register the buttons so it can show the ripple effect
		document.querySelectorAll(".mdl-js-button").forEach(
			node => componentHandler.upgradeElement(node)
		);
	});
	
	function formatBytes(bytes, decimals = 2) {
		if (bytes === 0) return '0 B';

		const k = 1024;
		const dm = decimals < 0 ? 0 : decimals;
		const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

		const i = Math.floor(Math.log(bytes) / Math.log(k));

		return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
	}
	
	function dirname(path) {
		return path.replace(/\\/g,'/').replace(/\/[^\/]*$/, '');
	}
	
</script>

<style>
	.mdl-card-wide {
		width: 100%;
	}
</style>

<!-- CONTENT -->
<main class="page-content">	
	
	<!-- Folder header -->
	<div class="mdl-card mdl-card-wide mdl-shadow--2dp">
		<div class="mdl-card__title">
			<h2 class="mdl-card__title-text">
				<i class="material-icons">{#if folder.public}folder_shared{:else}folder{/if}</i>
				{#if rootDir}
					/
				{:else}
					{folder.name}
				{/if}
			</h2>
		</div>
		<div class="mdl-card__supporting-text">
			<!-- If rootDir: (200 MB / 1 GB) -->
			<!-- Otherwise:  (200 MB) -->
			{path} ({formatBytes(folder.size)} {#if rootDir}/ {formatBytes(user.maxSpace)}{/if})
		</div>
		
		<!-- Directory actions -->
		<div class="mdl-card__actions mdl-card--border">	
			<button on:click="{() => dispatch('action', {action: 'upload', path: path})}" class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored">
				<i class="material-icons">cloud_upload</i><!-- Upload file -->
			</button>
			<button on:click="{() => dispatch('action', {action: 'mkdir', path: path})}" class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab">
				<i class="material-icons">create_new_folder</i><!-- Create folder -->
			</button>
		</div>
		
		<!-- Actions not available on root directory -->
		{#if !rootDir}
			<div class="mdl-card__menu">
				<button on:click="{() => dispatch('action', {action: 'public', path: path, public: !folder.public})}" class="mdl-button mdl-js-button mdl-button--icon">
					<i class="material-icons">{folder.public ? "lock" : "lock_open"}</i><!-- Set as public or private -->
				</button>
				<button on:click="{() => dispatch('action', {action: 'move', path: path})}" class="mdl-button mdl-js-button mdl-button--icon mdl-button--primary">
					<i class="material-icons">reply</i><!-- Move -->
				</button>
				<button on:click="{() => dispatch('action', {action: 'delete', path: path})}" class="mdl-button mdl-js-button mdl-button--icon mdl-button--accent">
					<i class="material-icons">delete_forever</i><!-- Delete -->
				</button>	
			</div>
		{/if}
	</div>
	
	<!-- Slot for alert and actions -->
	<slot></slot>

	<!-- File grid -->
	<div class="mdl-grid">
		{#each folder.contents as file (file.name)}
			<div class="mdl-cell mdl-cell--2-col">
				<File {path} {file} on:action />
			</div>
		{/each}
	</div>
	
</main>