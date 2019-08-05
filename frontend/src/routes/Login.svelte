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


<!-- Fork me on GitHub -->
<a href="https://github.com/you"><img width="149" height="149" src="https://github.blog/wp-content/uploads/2008/12/forkme_left_red_aa0000.png?resize=149%2C149" class="attachment-full size-full" alt="Fork me on GitHub" data-recalc-dims="1"></a>

<div class="container text-center">

	<img class="mb-4" src="img/logo.png" alt="FileFucktory" width="300">

	<h1>Login</h1>
	
	<div class="row">
		<div class="col-sm"></div>
		
		<div class="col-sm">
			{#if message}<Alert title="Error fetching invites." {message} />{/if}

			{#if userPromise}
				{#await userPromise}
					<div class="spinner-border" role="status"/>
				{/await}
			{/if}
			
			<form class="col-sm form-group" on:submit|preventDefault="{(event) => {login(event.currentTarget.username.value, event.currentTarget.password.value)}}">
				<input class="form-control" name="username" type="text" placeholder="username"/>
				<input class="form-control" name="password" type="password" placeholder="password"/>
				<input class="btn btn-primary" type="submit"/>
			</form>

		</div>
		
		<div class="col-sm"></div>
	</div>
	
</div>