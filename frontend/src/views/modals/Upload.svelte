<script>
	import { createEventDispatcher } from 'svelte';
	const dispatch = createEventDispatcher();
	
	export let progress = 0;

	export let path = "";

	export let show = null;
	$: {
		jQuery('#uploadCollapse').collapse(show ? 'show' : 'hide');
		show = null;
	}

	const handleSubmit = (event) => {
		if(event.currentTarget.file.files[0] !== undefined) {
			dispatch('submit', {
				"path": event.currentTarget.path.value+"/"+fileName,
				"file": event.currentTarget.file.files[0]
			});
		}
	}
	
	let fileName = "Choose file";
	const updateFileName = (event) => {
		fileName = event.currentTarget.files[0].name;
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

			<div class="progress">
				<div class="progress-bar progress-bar-striped" class:bg-success="{progress === 100}" class:progress-bar-animated="{progress < 100}"
				role="progressbar" style="width: {progress}%"/>
			</div>

			<br/>

			<form class="input-group mb-3" on:submit|preventDefault="{handleSubmit}">
				<div class="custom-file">
					<input type="hidden" name="path" value="{path}" />
					<input type="file" name="file" class="custom-file-input" on:change="{updateFileName}">
					<label class="custom-file-label">{fileName}</label>
				</div>
				<div class="input-group-append">
					<button type="submit" class="btn btn-primary">Upload</button>
				</div>
			</form>

		</div>

	</div>
</div>