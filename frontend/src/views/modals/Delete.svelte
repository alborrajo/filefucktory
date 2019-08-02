<script>
	import { createEventDispatcher } from 'svelte';
	const dispatch = createEventDispatcher();

	export let path = "";

	export let show = null;
	$: {
		jQuery('#deleteModal').modal(show ? 'show' : 'hide');
		show = null;
	}

	function handleSubmit(event) {
		dispatch('submit', {
			"path": event.currentTarget.path.value
		});
	}

</script>

<!-- mkdir -->
<div class="modal fade" id="deleteModal" tabindex="-1" >
	<div class="modal-dialog">
		<form class="modal-content" on:submit|preventDefault="{handleSubmit}">
			<div class="modal-header">
				<h5>Remove</h5>
			</div>
			<div class="modal-body input-group">
				<input type="hidden" name="path" value="{path}"/>
				<p>Are you sure you want to remove {path.split('\\').pop().split('/').pop()}?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-danger">Delete</button>
			</div>
		</form>
	</div>
</div>