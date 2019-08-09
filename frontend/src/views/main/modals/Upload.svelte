<script>
	import { createEventDispatcher } from 'svelte';
	const dispatch = createEventDispatcher();
	
	export let progress = [];

	export let path = "";

	export let show = null;
	$: {
		//jQuery('#uploadCollapse').collapse(show ? 'show' : 'hide');
		show = null;
	}

	const handleSubmit = (event) => {
		if(event.currentTarget.files.files !== undefined) {
			dispatch('submit', {
				"path": event.currentTarget.path.value,
				"files": event.currentTarget.files.files
			});
		}
	}
	
	let fileNames;
	const updateFileNames = (event) => {
		fileNames = event.currentTarget.files[0].name;
		for(let i=1; i < event.currentTarget.files.length; i++) {
			fileNames += ", "+event.currentTarget.files[i].name;
		}
	}
</script>

<div class="collapse" id="uploadCollapse">
	<div class="card card-body">

		<button type="button" class="close" data-toggle="collapse" data-target="#uploadCollapse">
			<span>&times;</span>
		</button>

		<h5 class="card-title">Upload</h5>
		<h6 class="card-subtitle mb-2 text-muted">To: {path}</h6>		

		<div class="container">

			{#each progress as prog} 
				<div class="progress">
					<div class="progress-bar progress-bar-striped bg-{prog.status}" class:progress-bar-animated="{prog.progress < 100}"
					role="progressbar" style="width: {prog.progress}%">
						{prog.name}
					</div>
				</div>
			{/each}
			
			<br/>

			<form class="input-group mb-3" on:submit|preventDefault="{handleSubmit}">
				<div class="custom-file">
					<input type="hidden" name="path" value="{path}" />
					<input type="file" multiple name="files" class="custom-file-input" on:change="{updateFileNames}">
					<label class="custom-file-label">{fileNames ? fileNames : 'Choose file'}</label>
				</div>
				<div class="input-group-append">
					<button type="submit" class="btn btn-primary">Upload</button>
				</div>
			</form>

		</div>

	</div>
</div>