<script>
	import { onMount } from 'svelte';
	
	import Header from './Layout/Header.svelte';
	
	onMount(() => {
		// Have Material Design Lite register the layout so it can show the drawer menu icon
		componentHandler.upgradeElement(document.getElementById("layout"));
	})
	
</script>

<div id="layout" class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
		
	<!-- HEADER -->
	<header class="mdl-layout__header">
		<slot name="header">
			<!-- By default: Header -->
			<Header />
		</slot>
	</header>

	<!-- DRAWER -->
	<div class="mdl-layout__drawer">
		<slot name="drawer"></slot>
	</div>

	<!-- CONTENT -->
	<main class="mdl-layout__content">
		<slot name="content"></slot>
	</main>

</div>