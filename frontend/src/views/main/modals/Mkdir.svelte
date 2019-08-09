<script>
	import { createEventDispatcher } from 'svelte';
	const dispatch = createEventDispatcher();

	export let path = "";

	export let show = null;
	$: {
		//jQuery('#mkdirModal').modal(show ? 'show' : 'hide');
		show = null;
	}

	function handleSubmit(event) {
		dispatch('submit', {
			"path": event.currentTarget.path.value,
			"dirName": event.currentTarget.dirName.value,
			"public": event.currentTarget.public.checked
		});
	}

</script>

<!-- mkdir -->
<div class="modal fade" id="mkdirModal" tabindex="-1" >
	<div class="modal-dialog">
		<form class="modal-content" on:submit|preventDefault="{handleSubmit}">
			<div class="modal-header">
				<h5>Make directory</h5>
			</div>
			<div class="modal-body input-group">
				<input type="hidden" name="path" value="{path}"/>
				<input type="text" name="dirName" class="form-control" required placeholder="Directory name"/>
				<div class="input-group-append input-group-text">
					<input type="checkbox" name="public"/> Public
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Make</button>
			</div>
		</form>
	</div>
</div>