<script>
	export let rootDir = false;	
	
	export let path;
	export let folder;
	
	function dirname(path) {
		return path.replace(/\\/g,'/').replace(/\/[^\/]*$/, '');
	}
</script>

<style>
	.drawer {
		margin: 20px
	}
</style>

<!-- DRAWER -->
<div class="drawer">
	<!-- Current folder -->
	<span class="mdl-layout-title">
			{#if folder.public}<i class="material-icons">lock_open</i>{/if} 
			{#if rootDir}
				/
			{:else}
				{folder.name}
			{/if}
	</span>

	<!-- Subfolders -->
	<nav class="mdl-navigation">
		<!-- Go up -->
			{#if !rootDir}
		<a class="mdl-navigation__link" href="#/{dirname(path)}">
			<i class="material-icons">arrow_upward</i>..</a>
			{/if}

		<!-- Subfolders -->
			{#each folder.contents as content (content.name)}
				{#if content.contents !== undefined}
		<a class="mdl-navigation__link" href="#/{path}/{content.name}">
			<i class="material-icons">{#if content.public}folder_shared{:else}folder{/if}</i>
						{content.name}
		</a>
				{/if}
			{/each}
	</nav>
</div>