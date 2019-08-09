<script>
	export let alertClass="danger";
	export let title="Error";
</script>

<style>
	.warning {
		background-color: lightyellow;
	}
	
	.danger {
		background-color: lightcoral;
	}
	
	.success {
		background-color: lightgreen;
	}
	
	.mdl-card-wide {
		min-height: 0px;
		width: 100%;
	}
</style>

<div class="mdl-card mdl-card-wide mdl-shadow--2dp {alertClass}">
	<div class="mdl-card__title">
		<h2 class="mdl-card__title-text">{title}</h2>
	</div>
	<div class="mdl-card__supporting-text">
		<slot></slot>
	</div>
</div>