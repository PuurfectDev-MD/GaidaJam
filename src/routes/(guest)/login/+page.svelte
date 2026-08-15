<script lang="ts">
  let { data } = $props();
</script>

<!-- Clean GET navigation link: no forms or POST requests -->
<a
  href={data.hackAuthUrl}
  class="inline-block px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg shadow transition text-center text-white no-underline"
>
  Sign in with Hack Club
</a>