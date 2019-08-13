<script>
	import Router from 'svelte-spa-router';
	
	import Main from './routes/Main.svelte';
	import Register from './routes/Register.svelte';
	import Login from './routes/Login.svelte';
	import Invites from './routes/Invites.svelte';
	
	import NotFound from './routes/NotFound.svelte';
	
	
	const routes = {
		'/login':				Login,
		'/invites':				Invites,
		'/invites/*':			Invites,
		'/*':					Main,
		'*':					NotFound
	}
	
</script>

<Router {routes} />
