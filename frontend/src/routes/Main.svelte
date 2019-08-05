<script>
	import {push, pop, replace} from 'svelte-spa-router'
	import API from '../util/api.js';
	
	import Navbar from '../views/Navbar.svelte';
	
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
		const files = event.detail.files;
		
		uploadProps.progress = [];
		for(let i=0; i < files.length; i++) {
			// Before upload
			uploadProps.progress[i] = {
				name: files[i].name,
				progress: 0,
				status: "primary"
			};
			
			// During upload
			const onprogress = (event) => {
				uploadProps.progress[i].progress = Math.ceil((event.loaded/event.total)*100);
			}
			
			try {
				// After upload (success)
				await api.upload(path+"/"+files[i].name, files[i], onprogress);
				uploadProps.progress[i].status = "success";
			}
			catch(err) {
				// After upload (error)
				uploadProps.progress[i].status = "danger";
			}
			
			folderPromise = api.getFolder(user.folder);
		}
		
		message = null;		
	}
	
</script>

<style>
	.container {
		margin-top: 20px;
	}
</style>

<Navbar/>

<main class="container">

	<h2>Files</h2>
	
	{#if message}<Alert title="Error." {message} />{/if}
	
	<Upload {...uploadProps} on:submit={handleUpload} /><br/>
	
	{#await userPromise then user}
		{#await folderPromise then folder}
			<Folder rootDir path={user.folder} {folder} on:action={handleActionButtons} />
							
			<!-- Action modals -->
			<Mkdir  {...mkdirProps}  on:submit={handleMkdir}  />
			<Delete {...deleteProps} on:submit={handleDelete} />
			<Public {...publicProps} on:submit={handlePublic} />
			<Move   {...moveProps}   on:submit={handleMove} path={user.folder} {folder} />
		{/await}
	{/await}
	
	
</main>