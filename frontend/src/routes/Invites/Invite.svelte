<script>
	import {push, pop, replace} from 'svelte-spa-router'
	export let params = {};
	
	import Alert from '../../views/Alert.svelte';
	
	import API from '../../util/api.js';
	const api = new API();
	
	let invitePromise = api.getInvite(params.inviteID);
	

	let userPromise;	
	const handleRegister = function(event) {
		userPromise = undefined;
		const username = event.currentTarget.username.value;
		const password = event.currentTarget.password.value;
		userPromise = api.register(params.inviteID, username, password);
	}
</script>


<!-- Fork me on GitHub -->
<a href="https://github.com/you"><img width="149" height="149" src="https://github.blog/wp-content/uploads/2008/12/forkme_left_red_aa0000.png?resize=149%2C149" class="attachment-full size-full" alt="Fork me on GitHub" data-recalc-dims="1"></a>

<div class="container text-center">

	<img class="mb-4" src="img/logo.png" alt="FileFucktory" width="300">

	<h1>Register</h1>
	
	<div class="row">
		<div class="col-sm"></div>
		
		<div class="col-sm">
			
			{#if userPromise}
				{#await userPromise}
					<div class="spinner-border" role="status"/>
				{:then user}
					<Alert title="Registered successfully" alertClass="success">
						<a href="/">Click here</a> to continue.
					</Alert>
				{:catch err}
					<Alert title="Error while registering" alertClass="warning">{err}</Alert>
				{/await}
			{/if}

			{#await invitePromise}
				<div class="spinner-border" role="status"/>
			{:then invite}
				{#if invite.pending}
					<form class="col-sm form-group" on:submit|preventDefault="{handleRegister}">
						<input class="form-control" name="username" type="text" placeholder="username"/>
						<input class="form-control" name="password" type="password" placeholder="password"/>
						<input class="btn btn-primary" type="submit"/>
					</form>
				{:else}
					<Alert title="Invite already used" alertClass="warning"/>
				{/if}
			{:catch err}
				<Alert title="Error fetching the Invite" alertClass="warning">The invite doesn't exist</Alert>
			{/await}


		</div>
		
		<div class="col-sm"></div>
	</div>
	
</div>