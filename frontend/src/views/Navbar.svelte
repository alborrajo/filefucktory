<script>
	import {push, pop, replace} from 'svelte-spa-router'

	function invite(event) {
		push("/invites");
	}
	
	function logout(event) {
		// Remove token from storage and go to the login page
		sessionStorage.removeItem('token');
		location.reload(); // TODO: Make this less hacky
	}
</script>

<nav class="navbar navbar-dark bg-dark">
    <a href="/"><img src="img/logowhite.png" height="30" class="d-inline-block align-top" alt=""/></a>
	
	<span class="button-group">
		<button on:click={invite} class="btn btn-sm btn-info">Invite <span class="fa fa-address-book"></span></button>
		<button on:click={logout} class="btn btn-sm btn-secondary">Logout <span class="fa fa-sign-out"></span></button>
	</span>
</nav>
