<script>
	import { createEventDispatcher } from 'svelte';
	
	export let path;
	export let file;
	
	const dispatch = createEventDispatcher();
</script>

<a href="rest/files/{path}/{file.name}">{file.name}</a> ({file.size} B)

<div class="btn-group pull-right">
	<!-- Delete --><button class="badge badge-danger" on:click="{() => dispatch('action', {action: 'delete', path: path+'/'+file.name})}"><span class="fa fa-trash"></span></button>
	<!--  Move  --><button class="badge badge-info" on:click="{() => dispatch('action', {action: 'move', path: path+'/'+file.name})}"><span class="fa fa-share"></span></button>
</div>