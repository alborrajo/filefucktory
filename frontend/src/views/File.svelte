<script>
	import { createEventDispatcher } from 'svelte';
	
	export let path;
	export let file;
	
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

<a href="rest/files/{path}/{file.name}">{file.name}</a> ({formatBytes(file.size)})

<div class="btn-group pull-right">
	<!-- Delete --><button class="badge badge-danger" on:click="{() => dispatch('action', {action: 'delete', path: path+'/'+file.name})}"><span class="fa fa-trash"></span></button>
	<!--  Move  --><button class="badge badge-info" on:click="{() => dispatch('action', {action: 'move', path: path+'/'+file.name})}"><span class="fa fa-share"></span></button>
</div>