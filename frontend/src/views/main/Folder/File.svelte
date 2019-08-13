<script>
	import { onMount } from 'svelte';
	import { createEventDispatcher } from 'svelte';
	
	// Create API object using the sessionStorage 'token' as the auth token
	import API from '../../../util/api.js';
	const token = sessionStorage.getItem('token');	
	const api = new API(token);
	
	export let path;
	export let file;
	
	// Inherir parameter from parent directory if the file/folder itself doesn't have a public parameter
	export let isPublic;
	if(file.public !== undefined) {isPublic = file.public}
	
	const dispatch = createEventDispatcher();
	
	onMount(() => {
		// Have Material Design Lite register the elements so it can show the dropdown menus
		componentHandler.upgradeElement(document.getElementById(md5(path+'/'+file.name)));
		componentHandler.upgradeElement(document.getElementById(md5(path+'/'+file.name)+"menu"));
	});
	
	async function tokenDownload(path) {
		// Get a new temporary token to download the file without auth
		//	This is done in order to avoid the browser's HTTP Basic Authentication dialog
		const token = await api.getDownloadToken(path);

		// Create URL with query parameters
		let target = "rest/files/"+path;
		target += '?';
		target += new URLSearchParams({
			token: token.token
		}).toString();

		window.open(target);
	}
	
	function formatBytes(bytes, decimals=2) {
		if (bytes === 0) return '0 B';

		const k = 1024;
		const dm = decimals < 0 ? 0 : decimals;
		const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

		const i = Math.floor(Math.log(bytes) / Math.log(k));

		return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
	}
</script>

<style>
	.mdl-card-full {
		width: 100%;
		height: 100%;
	}
	
	.mdl-button-right {
		float: right;
	}
</style> 


<div class="mdl-card mdl-card-full mdl-shadow--2dp">
	<div class="mdl-card__title mdl-card--expand"/>
	<div class="mdl-card__actions">
		{#if file.contents !== undefined}
			<a href="#/{path}/{file.name}">
				<i class="material-icons">{#if file.public}folder_shared{:else}folder{/if}</i>

				<strong>{file.name}</strong><br/>
				({formatBytes(file.size)})
			</a>
		{:else}
			{#if isPublic}
			<a href="rest/files/{path}/{file.name}">
				<strong>{file.name}</strong><br/>
				({formatBytes(file.size)})
			</a>
			{:else}
			<a on:click="{() => tokenDownload(path+'/'+file.name)}">
				<strong>{file.name}</strong><br/>
				({formatBytes(file.size)})
			</a>
			{/if}
		{/if}

		<!-- Right aligned menu below button -->
		<button id="{md5(path+'/'+file.name)}" class="mdl-button mdl-js-button mdl-button--icon mdl-button-right">
			<i class="material-icons">more_vert</i>
		</button>

	</div>
</div>

<ul for="{md5(path+'/'+file.name)}" id="{md5(path+'/'+file.name)}menu" class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect">
	<!-- File actions -->
	<li on:click="{() => dispatch('action', {action: 'move', path: path+'/'+file.name})}" 	class="mdl-menu__item mdl-button--primary"><i class="material-icons">reply</i>Move</li>
	<li on:click="{() => dispatch('action', {action: 'delete', path: path+'/'+file.name})}" class="mdl-menu__item mdl-button--accent"><i class="material-icons">delete_forever</i>Delete</li>
	{#if file.contents !== undefined}
	<!-- Directory actions -->
	<li on:click="{() => dispatch('action', {action: 'public', path: path+'/'+file.name, public: !file.public})}" class="mdl-menu__item"><i class="material-icons">{file.public ? "lock" : "lock_open"}</i>{file.public ? "Set as private" : "Set as public"}</li>
	<li on:click="{() => dispatch('action', {action: 'mkdir', path: path+'/'+file.name})}" class="mdl-menu__item"><i class="material-icons">create_new_folder</i>Make directory</li>
	<li on:click="{() => dispatch('action', {action: 'upload', path: path+'/'+file.name})}" class="mdl-menu__item mdl-button--accent"><i class="material-icons">cloud_upload</i>Upload file</li>
	{/if}
</ul>