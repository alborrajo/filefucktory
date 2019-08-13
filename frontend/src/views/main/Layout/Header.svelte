<script>
	import {push, pop, replace} from 'svelte-spa-router'

	function home(event) {
		push("/");
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

<div class="mdl-layout__header-row">

	<span class="mdl-layout-title">
		<a href="/">
			<img src="img/logowhite.png" height="30"/>
		</a>
	</span>

	<!-- Add spacer, to align navigation to the right -->
	<div class="mdl-layout-spacer"/>
	
	<!-- Navigation.-->
	<nav class="mdl-navigation">
		<a class="mdl-navigation__link" on:click={home}>	<i class="material-icons">home</i>			</a>
		<a class="mdl-navigation__link" on:click={invite}>	<i class="material-icons">contacts</i>		</a>
		<a class="mdl-navigation__link" on:click={logout}>	<i class="material-icons">exit_to_app</i>	</a>
	</nav>
</div>