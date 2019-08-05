<script>
	export let alertClass="danger";
	export let title="Error";
</script>

<div class="alert alert-{alertClass} alert-dismissible fade show" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	  
	<h4 class="alert-heading">{title}</h4>
	<hr>
	<slot></slot>
</div>