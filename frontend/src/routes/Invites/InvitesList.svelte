<script>
	import { onMount } from 'svelte';
	
	import {push, pop, replace} from 'svelte-spa-router'
	export let params = {}
	
	// Create API object using the sessionStorage 'token' as the auth token
	import API from '../../util/api.js';
	const token = sessionStorage.getItem('token');	
	const api = new API(token);
		
	let invitesPromise = api.getInvites()
		.catch(() => push("/login"));


	function invite(event) {
		push("/invites");
	}
	
	function logout(event) {
		// Remove token from storage and go to the login page
		sessionStorage.removeItem('token');
		pop();
	}

</script>



<nav class="navbar navbar-dark bg-dark">
    <a href="/"><img src="img/logowhite.png" height="30" class="d-inline-block align-top" alt=""/></a>
	
	<span class="button-group">
		<button on:click={invite} class="btn btn-sm btn-info">Invite <span class="fa fa-address-book"></span></button>
		<button on:click={logout} class="btn btn-sm btn-secondary">Logout <span class="fa fa-sign-out"></span></button>
	</span>
</nav>

<div class="container">

	<h1>Invites</h1>

	{#await invitesPromise}
		<div class="spinner-border" role="status">
		  <span class="sr-only">Loading...</span>
		</div>
	{:then invitesObj}
		<ul class="list-group">
			{#each invitesObj.invites as invite, index}
				<li class="list-group-item">
					<a href="/invites/{index}">Invite #{index}</a>
					{#if invite.pending}<span class="pull-right badge badge-info">Pending <span class="fa fa-spinner"></span></span>{/if}
				</li>
			{/each}
		</ul>
	{:catch error}
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
	  
			<h4 class="alert-heading">Error fetching invites</h4>
			<hr>
			<p>{error}</p>
		</div>
	{/await}
	
</div>