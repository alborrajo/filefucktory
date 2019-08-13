<script>
	import {push, pop, replace} from 'svelte-spa-router'
	export let params = {}
	
	// Create API object using the sessionStorage 'token' as the auth token
	import API from '../util/api.js';
	const token = sessionStorage.getItem('token');	
	const api = new API(token);
	
	import Alert from '../views/Alert.svelte';

	import Layout from '../views/main/Layout.svelte';
	
	import FolderDrawer from '../views/main/Folder/FolderDrawer.svelte';
	import FolderContent from '../views/main/Folder/FolderContent.svelte';	
	

	import Upload from '../views/main/modals/Upload.svelte';
	const uploadProps = {};
	
	import Mkdir from '../views/main/modals/Mkdir.svelte';
	const mkdirProps = {};

	import Delete from '../views/main/modals/Delete.svelte';
	const deleteProps = {};
	
	import Public from '../views/main/modals/Public.svelte';
	const publicProps = {};
	
	import Move from '../views/main/modals/Move.svelte';
	const moveProps = {};
	
	
	let user;
	let folder;
	
	let userPromise = api.getUser();
			
	let message;

	
	$: {
		if(params.wild != null) {
			updateFolder(params.wild);
		}
		else{
			userPromise.then((userData) => {
				push("/"+userData.folder);
			});
		}
	}
		
	userPromise.then((userData) => {
		user = userData;
	}).catch((err) => {
		push("/login");
	});	
	
	async function updateFolder(path) {
		folder = await api.getFolder(path);
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
		const path = event.detail.path;
		const dirName = event.detail.dirName;
		const setPublic = event.detail.public;
			
		try {
			await api.mkdir(path, dirName, setPublic);
			updateFolder(params.wild);
			
			mkdirProps.show = false;
			message = null;
		}
		catch(err) {
			message = err ? err : "Error";
		}
	}
	
	async function handleDelete(event) {
		const path = event.detail.path;	
		
		try {
			await api.rm(path);
			updateFolder(params.wild);
			
			deleteProps.show = false;
			message = null;
		}
		catch(err) {
			message = err ? err : "Error";
		}
	}
	
	async function handlePublic(event) {
		const path = event.detail.path;
		const setPublic = event.detail.public;
		
		try {
			await api.setPublic(path, setPublic);
			updateFolder(params.wild);
			
			publicProps.show = false;
			message = null;
		} 
		catch(err) {
			message = err ? err: "Error";
		}
	}
	
	async function handleMove(event) {
		const from = event.detail.from;
		const to = event.detail.to;
		
		try {
			await api.move(from, to);
			updateFolder(params.wild);
			moveProps.show = false;
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
			
			api.upload(path+"/"+files[i].name, files[i], onprogress)
				.then(() => uploadProps.progress[i].status = "success") // After upload (success)
				.catch(() => uploadProps.progress[i].status = "danger") // After upload (error)
				.finally(() => updateFolder(params.wild))
		}
		
		message = null;		
	}
	
</script>

<style>
	.page-content {
		padding: 20px;
	}
</style>

{#if user}
	{#if folder}
		{#if params.wild} <!-- Avoid sending a null params.wild -->
			<Layout>
				<div slot="drawer"><FolderDrawer rootDir={params.wild == user.folder} {folder} path={params.wild} /></div>
				
				<div slot="content">
					<FolderContent rootDir={params.wild == user.folder} {folder} path={params.wild} {user} on:action="{handleActionButtons}">
						{#if message}<Alert title="Error" {message} />{/if}
						
						<!-- Action dialogs -->
						<Mkdir {...mkdirProps} on:submit="{handleMkdir}" />
						<Public {...publicProps} on:submit="{handlePublic}" />
						<Delete {...deleteProps} on:submit="{handleDelete}" />
						<Move {...moveProps} to={params.wild} on:submit="{handleMove}" />
						<Upload {...uploadProps} on:submit="{handleUpload}" />
						
					</FolderContent>
				</div>
			</Layout>
		{/if}
	{/if}
{/if}		