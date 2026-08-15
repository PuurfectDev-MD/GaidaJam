<script lang="ts">
    let { children } = $props();
</script>


<div class="relative w-full overflow-hidden">
<div class="flags absolute top-3 left-0 w-full flex justify-center z-0 pointer-events-none">
  <!-- Left high end -->
  <div class="flag bg-maroon -translate-y-3"></div>
  <div class="flag bg-sun -translate-y-2"></div>
  <div class="flag bg-forest -translate-y-1"></div>
  
  <!-- Mid-left curve descending -->
   <div class="flag bg-sun translate-y-1"></div>
   <div class="flag bg-forest translate-y-1"></div>
  <div class="flag bg-maroon translate-y-2"></div>
   <div class="flag bg-sun translate-y-1"></div>
  <div class="flag bg-forest translate-y-3"></div>
  <div class="flag bg-maroon translate-y-2"></div>
  <div class="flag bg-sun translate-y-1"></div>
  <div class="flag bg-forest translate-y-2"></div>
  <div class="flag bg-maroon translate-y-1"></div>
  
  <!-- Mid-right curve ascending -->
  <div class="flag bg-maroon translate-y-2"></div>
  <div class="flag bg-forest translate-y-1"></div>
  <div class="flag bg-sun translate-y-1"></div>
  
  <!-- Right high end -->
  <div class="flag bg-forest -translate-y-2"></div>
  <div class="flag bg-sun -translate-y-2"></div>
  <div class="flag bg-maroon -translate-y-3"></div>
  </div>

  
<header class="relative z-10 bg-transparent">
    <div class="flex flex-row justify-between p-4 ">
<h1 class="text-xl font-bold">GaindaJam</h1>
<a href="/login" class="rounded-xl bg-red-700 text-white px-3 py-2">Signup</a>
</div>
</header>
</div>


<main>
    {@render children()}
</main>
