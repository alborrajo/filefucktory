<script>
	import {push, pop, replace} from 'svelte-spa-router'
	import API from '../util/api.js';
	
	import Alert from '../views/Alert.svelte';

	
	let userPromise;
	let message;
	
	const login = function(username, password) {
		const token = btoa(username+":"+password);
		const api = new API(token);
		
		userPromise = api.getUser();
		userPromise.then(() => {
				sessionStorage.setItem('token', token);
				push("/")
			})
			.catch((response) => {
				if(response) {message = response;}
				else {message = "Can't reach the server";}
				userPromise = undefined;
			})
	}
</script>

<style>
	.center {
		margin: auto;
		width: 50%;
		padding: 10px;
	}
</style>

<!-- Fork me on GitHub -->
<a href="https://github.com/you"><img width="149" height="149" src="https://github.blog/wp-content/uploads/2008/12/forkme_left_red_aa0000.png?resize=149%2C149" class="attachment-full size-full" alt="Fork me on GitHub" data-recalc-dims="1"></a>

<div class="center mdl-card mdl-shadow--2dp">
  <div class="mdl-card__title">
	<img src="img/logo.png" alt="FileFucktory" width="300">
  </div>
  <form class="col-sm form-group" on:submit|preventDefault="{(event) => {login(event.currentTarget.username.value, event.currentTarget.password.value)}}">
	<div class="mdl-card__supporting-text">
	<h2 class="mdl-card__title-text">Login</h2>
    {#if message}<Alert title="Error while logging in." {message} />{/if}

	{#if userPromise}
		{#await userPromise}
			<div class="mdl-spinner mdl-js-spinner is-active"></div>
		{/await}
	{/if}
		
		<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			<input class="mdl-textfield__input" type="text" name="username" placeholder="Username"/>
			<label class="mdl-textfield__label" for="sample3">Username</label>
		</div>
		
		<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			<input class="mdl-textfield__input" type="password" name="password" placeholder="Password"/>
			<label class="mdl-textfield__label" for="sample3">Password</label>
		</div>
		
	</div>
	<div class="mdl-card__actions mdl-card--border">
		<input class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" type="submit"/>
	</div>
  </form>
</div>