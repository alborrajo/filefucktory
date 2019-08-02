<script>
	import { createEventDispatcher } from 'svelte';
	const dispatch = createEventDispatcher();

	export let setPublic = false;
	export let path = "";

	export let show = null;
	$: {
		jQuery('#publicModal').modal(show ? 'show' : 'hide');
		show = null;
	}

	function handleSubmit(event) {
		dispatch('submit', {
			"path": event.currentTarget.path.value,
			"public": setPublic
		});
	}

</script>

<!-- mkdir -->
<div class="modal fade" id="publicModal" tabindex="-1" >
	<div class="modal-dialog">
		<form class="modal-content" on:submit|preventDefault="{handleSubmit}">
			<div class="modal-header">
				<h5>Set as {setPublic ? "public" : "private"}?</h5>
			</div>
			<div class="modal-body input-group">
				<input type="hidden" name="path" value="{path}"/>
				<input type="hidden" name="public" value="{setPublic}"/>
				<p>Are you sure you want to set {path.split('\\').pop().split('/').pop()} as <strong>{setPublic ? "public" : "private"}</strong>?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-warning">Set</button>
			</div>
		</form>
	</div>
</div>