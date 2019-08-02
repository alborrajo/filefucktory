<script>
	import Router from 'svelte-spa-router';
	
	import Main from './routes/Main.svelte';
	import Register from './routes/Register.svelte';
	import Login from './routes/Login.svelte';
	
	import NotFound from './routes/NotFound.svelte';
	
	
	const routes = {
		'/':					Main,
		'/register/:inviteID':	Register,
		'/login':				Login,
		
		'*':					NotFound
	}
	
</script>

<Router {routes} />
