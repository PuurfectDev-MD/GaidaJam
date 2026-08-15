<script lang="ts">
  let { data } = $props();
</script>
<h1> this is the dashabord guys</h1>



<h1>Dashboard</h1>
<p>Name: {data.user?.name}</p>
<p>Email: {data.user?.email}</p>