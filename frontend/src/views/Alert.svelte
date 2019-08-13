<script>
	import { onMount, afterUpdate } from 'svelte';

	export let alertClass="danger";
	export let title="Error";
	
	export let show = true;
	
	afterUpdate(() => show = true);
	
	onMount(() => {
		// Have Material Design Lite register the buttons so it can show the ripple effect
		document.querySelectorAll(".mdl-js-button").forEach(
			node => componentHandler.upgradeElement(node)
		);
	});
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

<div class="mdl-card mdl-card-wide mdl-shadow--2dp {alertClass}" class:hidden="{!show}">
	<div class="mdl-card__title">
		<h2 class="mdl-card__title-text">{title}</h2>
	</div>
	<div class="mdl-card__supporting-text">
		<slot></slot>
	</div>
	
	<div class="mdl-card__menu">
		<button on:click|preventDefault="{() => show = false}" class="mdl-button mdl-js-button mdl-button--icon mdl-button-right">
			<i class="material-icons">close</i>
		</button>
	</div>
</div>