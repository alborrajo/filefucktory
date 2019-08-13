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
		const password = sha512(event.currentTarget.password.value);
		userPromise = api.register(params.inviteID, username, password);
	}
	
</script>

<style>
	.center {
		margin: auto;
		width: 50%;
		padding: 10px;
	}
	
	.logo {
		max-width: 100%;
		max-height: 5em;
	}
</style>

<!-- Fork me on GitHub -->
<a href="https://github.com/you"><img width="149" height="149" src="https://github.blog/wp-content/uploads/2008/12/forkme_left_red_aa0000.png?resize=149%2C149" class="attachment-full size-full" alt="Fork me on GitHub" data-recalc-dims="1"></a>

<div class="center mdl-card mdl-shadow--2dp">
  <div class="mdl-card__title">
	<img src="img/logo.png" alt="FileFucktory" class="logo">
  </div>
  <form class="col-sm form-group" on:submit|preventDefault="{handleRegister}" >
	<div class="mdl-card__supporting-text">
	<h2 class="mdl-card__title-text">Register</h2>

		{#if userPromise}
			{#await userPromise}
				<div class="mdl-spinner mdl-js-spinner is-active"></div>
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
				<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					<input class="mdl-textfield__input" type="text" name="username" id="username" />
					<label class="mdl-textfield__label" for="username">Username</label>
				</div>
				
				<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					<input class="mdl-textfield__input" type="password" name="password" id="password" />
					<label class="mdl-textfield__label" for="password">Password</label>
				</div>
			{:else}
				<Alert title="Invite already used" alertClass="warning"/>
			{/if}
		{:catch err}
			<Alert title="Error fetching the Invite" alertClass="warning">The invite doesn't exist</Alert>
		{/await}
		
	</div>
	<div class="mdl-card__actions mdl-card--border">
		<input class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" type="submit"/>
	</div>
  </form>
  
</div>
