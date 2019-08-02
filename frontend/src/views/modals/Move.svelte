<script>
	import { createEventDispatcher } from 'svelte';
	const dispatch = createEventDispatcher();
	
	import MoveFolderTree from './move/MoveFolderTree.svelte';

	export let path;
	export let folder;

	export let from = "";
	let to = "";

	export let show = null;
	$: {
		jQuery('#moveModal').modal(show ? 'show' : 'hide');
		show = null;
	}
	
	const setTo = (event) => {
		to = event.detail.chosen;
	}

	const handleSubmit = (event) => {
		dispatch('submit', {
			"from": event.currentTarget.from.value,
			"to": event.currentTarget.to.value
		});
	}

</script>

<!-- mkdir -->
<div class="modal fade" id="moveModal" tabindex="-1" >
	<div class="modal-content modal-dialog">
		<div class="modal-header">
			<h5>Move {from.split('\\').pop().split('/').pop()}</h5>
		</div>
		<div class="modal-body input-group">
			<MoveFolderTree rootDir=true {path} {folder} currentChosenPath={to} on:chosen={setTo}/>
		</div>
		<div class="modal-footer">
			<form on:submit|preventDefault="{handleSubmit}">
				<input type="hidden" name="from" value="{from}"/>
				<input type="hidden" name="to" value="{to}"/>
				
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-warning">Set</button>
			</form>
		</div>
	</div>
</div>