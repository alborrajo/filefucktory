<script>
	import Alert from '../../views/Alert.svelte';
	import Navbar from '../../views/Navbar.svelte';
	
	import {push, pop, replace} from 'svelte-spa-router'
	export let params = {}
	
	// Create API object using the sessionStorage 'token' as the auth token
	import API from '../../util/api.js';
	const token = sessionStorage.getItem('token');	
	const api = new API(token);
	
	let userPromise = api.getUser()
		.catch(() => push("/login"));
	let invitesPromise = api.getInvites();

	
	function handleNewInvite(event) {
		// It all is run from inside an anonymous function
		// so invitesPromise is pending while addInvite is running
		// not only while getInvites is running
		invitesPromise = (async () => {
			await api.addInvite();
			return api.getInvites();
		})();
	}
</script>



<Navbar/>

<div class="container">

	{#await invitesPromise}
		<h1>Invites</h1>
		<div class="spinner-border" role="status">
		  <span class="sr-only">Loading...</span>
		</div>
	{:then invitesObj}
		<h1>
			Invites
			
			{#await userPromise then user}
				<span class="badge badge-{invitesObj.invites.length < user.maxInvites ? 'info' : 'warning'}">{invitesObj.invites.length} / {user.maxInvites}</span>
				{#if invitesObj.invites.length < user.maxInvites }
					<button type="button" class="badge badge-primary" on:click="{handleNewInvite}" alt="New invite"><i class="fa fa-plus"></i></button>
				{/if}
			{/await}
		</h1>
		
		
		<ul class="list-group">
			{#each invitesObj.invites as invite, index}
				<li class="list-group-item">
					<a href="#/invites/{invite.inviteID}">Invite #{index+1}</a>
					{#if invite.pending}<span class="pull-right badge badge-info">Pending <span class="fa fa-spinner fa-pulse"></span></span>{/if}
				</li>
			{/each}
		</ul>
	{:catch error}
		<Alert title="Error fetching invites." message={error} />
	{/await}
	
</div>