<script>
	import Layout from '../../views/main/Layout.svelte';
	import Alert from '../../views/Alert.svelte';
	
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
	
	function invite(event) {
		push("/invites");
	}
	
	function logout(event) {
		// Remove token from storage and go to the login page
		sessionStorage.removeItem('token');
		location.reload(); // TODO: Make this less hacky
	}
</script>

<style>
	.content {
		padding: 20px
	}
</style>


<Layout>
	<main slot="content" class="content">
		{#await userPromise then user}
			{#await invitesPromise}
				<h1>Invites</h1>
				<div class="mdl-spinner mdl-js-spinner is-active"></div>
			{:then invitesObj}
				<h1>
					Invites
					
					<!-- Disabled FAB button -->
					<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" disabled="{invitesObj.invites.length >= user.maxInvites}" on:click="{handleNewInvite}">
						{invitesObj.invites.length} / {user.maxInvites} <i class="material-icons">add</i>
					</button>
				</h1>
				
				
				<ul class="mdl-list">
					{#each invitesObj.invites as invite, index}
						<li class="mdl-list__item">
							<span class="mdl-list__item-primary-content">
								{#if !invite.pending}<i class="material-icons mdl-list__item-icon">check_box</i>{/if}
								<a href="#/invites/{invite.inviteID}">Invite #{index+1}</a>
							</span>
						</li>
					{/each}
				</ul>
			{:catch error}
				<Alert title="Error fetching invites." message={error} />
			{/await}
		{/await}
	</main>
</Layout>