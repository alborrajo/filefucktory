<script>
	import { createEventDispatcher } from 'svelte';

	import File from "./File.svelte"

	export let rootDir = false;
	
	export let path;
	export let folder;
		
	const dispatch = createEventDispatcher();

	
	function formatBytes(bytes, decimals = 2) {
		if (bytes === 0) return '0 B';

		const k = 1024;
		const dm = decimals < 0 ? 0 : decimals;
		const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

		const i = Math.floor(Math.log(bytes) / Math.log(k));

		return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
	}
</script>

<div class="panel panel-default">
	<li class="panel panel-heading list-group-item list-group-item-secondary">
		<!-- Folder button -->
		<button class="badge badge-primary" data-toggle="collapse" data-target="#collapse{md5(path)}">
			<span class="fa fa-folder-open"></span>
		</button>
		<!-- Folder name -->
		<strong>{#if folder.public}<span class="fa fa-unlock"></span>{/if} {folder.name}</strong> ({formatBytes(folder.size)})
		
		<!-- Buttons on the right -->
		<div class="btn-group pull-right">
			<!-- Upload --><button class="badge badge-primary" on:click="{() => dispatch('action', {action: 'upload', path: path})}"><span class="fa fa-cloud-upload"></span></button>
			<!--  Make  --><button class="badge badge-secondary" on:click="{() => dispatch('action', {action: 'mkdir', path: path})}"><span class="fa fa-plus-circle"></span> <span class="fa fa-folder-open"></span></button>
			
			{#if !rootDir}
				<!-- Delete --><button class="badge badge-danger" on:click="{() => dispatch('action', {action: 'delete', path: path})}"><span class="fa fa-trash"></span></button>
				<!--  Move  --><button class="badge badge-info" on:click="{() => dispatch('action', {action: 'move', path: path})}"><span class="fa fa-share"></span></button>
				
				<!-- Public -->
				<button class="badge badge-warning" on:click="{() => dispatch('action', {action: 'public', path: path, public: !folder.public})}">
					<!-- Show locked or unlocked icon depending on wether it currently is public or private -->
					<span class="fa" class:fa-unlock="{!folder.public}" class:fa-lock="{folder.public}"></span>
				</button>
			{/if}
			
		</div>		
	</li>
	
	<ul class="panel panel-body panel-collapse collapse list-group" class:show="{rootDir}" id="collapse{md5(path)}">
		{#each folder.contents as content (content.name)}
			<li class="list-group-item">
				<!-- If it's a folder -->
				{#if content.contents !== undefined}
					<svelte:self path={path}/{content.name} folder="{content}" on:action/>
				{:else}
					<File {path} file={content} on:action/>
				{/if}
			</li>
		{/each}
	</ul>
</div>