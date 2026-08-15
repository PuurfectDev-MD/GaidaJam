<script lang="ts">
  import '../app.css';

  let { children } = $props();
</script>

<svelte:head>
  <title>Chautari</title>
</svelte:head>

<div>
  {@render children()}
</div>