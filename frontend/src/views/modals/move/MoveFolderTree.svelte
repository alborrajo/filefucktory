<script>
	import { createEventDispatcher } from 'svelte';

	export let rootDir = false;
	export let currentChosenPath = "";
	
	export let path;
	export let folder;

	const dispatch = createEventDispatcher();
</script>

<div class="panel panel-default">
	<li class="panel panel-heading list-group-item list-group-item-secondary">
		<!-- Folder button -->
		<button class="badge badge-primary" data-toggle="collapse" data-target="#moveFolderTree{md5(path)}">
			<span class="fa fa-folder-open"></span>
		</button>
		
		<!-- Folder name -->
		<strong>{#if folder.public}<span class="fa fa-unlock"></span>{/if} {folder.name}</strong> ({folder.size} B)
		
		
		<!-- Buttons on the right -->
		<div class="btn-group pull-right">
			<button class="badge" class:badge-info="{currentChosenPath !== path}" class:badge-success="{currentChosenPath === path}" on:click="{() => dispatch('chosen', {chosen: path})}">
				<span class="fa fa-arrow-circle-down"></span>
			</button>
		</div>
	</li>
	
	<ul class="panel panel-body panel-collapse collapse list-group" class:show="{rootDir}" id="moveFolderTree{md5(path)}">
		{#each folder.contents as content (content.name)}
			{#if content.contents !== undefined}
				<li class="list-group-item">
					<svelte:self path={path}/{content.name} folder="{content}" {currentChosenPath} on:chosen/>
				</li>
			{/if}
		{/each}
	</ul>
</div>