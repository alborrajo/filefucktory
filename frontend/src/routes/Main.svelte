<script>
	import {push, pop, replace} from 'svelte-spa-router'
	import API from '../util/api.js';
	
	import Folder from '../views/Folder.svelte';
	
	import Upload from '../views/modals/Upload.svelte';
	const uploadProps = {};
	
	import Mkdir from '../views/modals/Mkdir.svelte';
	const mkdirProps = {};
	
	import Delete from '../views/modals/Delete.svelte';
	const deleteProps = {};
	
	import Public from '../views/modals/Public.svelte';
	const publicProps = {};
	
	import Move from '../views/modals/Move.svelte';
	const moveProps = {};
	
	
	let message;

	// Create API object using the sessionStorage 'token' as the auth token
	const token = sessionStorage.getItem('token');	
	const api = new API(token);
	
	let user;
	
	let userPromise = api.getUser();
	let folderPromise;
	
	userPromise.then((userData) => {
		user = userData
		folderPromise = api.getFolder(userData.folder)
	}).catch((err) => {
		push("/login");
	});
		
	function logout(event) {
		// Remove token from storage and go to the login page
		sessionStorage.removeItem('token');
		pop();
	}
	
	function handleActionButtons(event) {
		switch(event.detail.action) {
			case "mkdir":
				mkdirProps.path = event.detail.path;
				mkdirProps.show = true;
				break;
			case "delete":
				deleteProps.path = event.detail.path;
				deleteProps.show = true;
				break;
			case "public":
				publicProps.path = event.detail.path;
				publicProps.setPublic = event.detail.public;
				publicProps.show = true;
				break;
			case "move":
				moveProps.from = event.detail.path;
				moveProps.show = true;
				break;
			case "upload":
				uploadProps.path = event.detail.path;
				uploadProps.show = true;
				break;
			default:
				alert("UNIMPLEMENTED: "+JSON.stringify(event.detail));
				break;
		}
	}
	
	async function handleMkdir(event) {
		mkdirProps.show = false;
		const path = event.detail.path;
		const dirName = event.detail.dirName;
		const setPublic = event.detail.public;
	
		try {
			await api.mkdir(path, dirName, setPublic);
			folderPromise = api.getFolder(user.folder);
			message = null;
		}
		catch(err) {
			message = err ? err : "Error";
		}
	}
	
	async function handleDelete(event) {
		deleteProps.show = false;
		const path = event.detail.path;
	
		try {
			await api.rm(path);
			folderPromise = api.getFolder(user.folder);
			message = null;
		}
		catch(err) {
			message = err ? err : "Error";
		}
	}
	
	async function handlePublic(event) {
		publicProps.show = false;
		const path = event.detail.path;
		const setPublic = event.detail.public;
		
		try {
			await api.setPublic(path, setPublic);
			folderPromise = api.getFolder(user.folder);
			message = null;
		} 
		catch(err) {
			message = err ? err: "Error";
		}
	}
	
	async function handleMove(event) {
		moveProps.show = false;
		const from = event.detail.from;
		const to = event.detail.to;
		
		try {
			await api.move(from, to);
			folderPromise = api.getFolder(user.folder);
			message = null;
		}
		catch(err) {
			message = err ? err: "Error";
		}
	}
	
	async function handleUpload(event) {
		const path = event.detail.path;
		const file = event.detail.file;
		
		try {
			await api.upload(path, file, uploadProps);
			folderPromise = api.getFolder(user.folder);
			message = null;
		}
		catch(err) {
			message = err ? err: "Error";
		}
	}
	
</script>

<style>
	.container {
		margin-top: 20px;
	}
</style>

<nav class="navbar navbar-dark bg-dark">
    <img src="img/logowhite.png" height="30" class="d-inline-block align-top" alt="">
	
	<button on:click={logout} class="btn btn-sm btn-secondary">Logout <span class="fa fa-sign-out"></span></button>
</nav>

<main class="container">

	<h2>Files</h2>
	
	<Upload {...uploadProps} on:submit={handleUpload} /><br/>
	
	{#if message}<div class="alert alert-danger">{message}</div>{/if}
	
	{#await userPromise then user}
		{#await folderPromise then folder}
			<Folder rootDir={true} path={user.folder} {folder} on:action={handleActionButtons} />
							
			<!-- Action modals -->
			<Mkdir  {...mkdirProps}  on:submit={handleMkdir}  />
			<Delete {...deleteProps} on:submit={handleDelete} />
			<Public {...publicProps} on:submit={handlePublic} />
			<Move   {...moveProps}   on:submit={handleMove} path={user.folder} {folder} />
		{/await}
	{/await}
	
	
</main>