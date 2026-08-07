<script lang="ts">
    let name = $state('')
    let slackId = $state('')
    let email = $state('')

    function handleSubmit(event: Event){
        event.preventDefault();

    }
</script>
<div class="flex justify-between items-center p-4 w-full ">
<div class="w-1/2 p-3">
<h1 class="tilt">MAKE, GAME</h1>
<h2 class="tilt">in Pokhara</h2>

<p class="mt-4">A 72 hours game jam to build the game of your dreams with animals, mountains, new people and the chilly winds of Pokhara</p>
</div>

<div class="w-1/2 p-2">
<img src="/images/rhino.jpg" class="w-full h-full object-cover">
</div>
</div>

<div class="bg-maroon w-full my-4">
  <div class="torn w-full"></div>
</div>

<section class="flex flex-col justify-center items-center p-4 w-full gap-y-6">
<div class="grid grid-cols-2 gap-x-0 w-2/3">
<h2 class="px-1">ONE</h2>
<div class="flex flex-col gap-y-3">
    <h3>Theme</h3>
    <p>Animals! Cutsey and awsome</p>
</div>
</div>


<div class="grid grid-cols-2 gap-x-0 w-2/3">
<h2 class="px-1">TWO</h2>
<div class="flex flex-col gap-y-3">
    <h3>Format</h3>
    <p>72 hours to make the game of your dreams. *with animals!</p>
</div>
</div>


<div class="grid grid-cols-2 gap-x-0 w-2/3">
<h2 class="px-1">THREE</h2>
<div class="flex flex-col gap-y-3">
    <h3>Place</h3>
    <p>Pokhara, Nepal (chilly winds and amazing animals)</p>
</div>
</div>

</section>



<div class="bg-maroon w-full my-4">
  <div class="torn w-full"></div>
</div>


<section class="max-w-md mx-auto my-12 p-8 bg-sun-soft/60 rounded-2xl shadow-sm border border-maroon/10">
  <!-- Section Title -->
  <h2 class="text-3xl text-maroon text-center mb-8 tilt">
    Reserve Your Spot
  </h2>

  <!-- Form Container -->
  <form onsubmit={handleSubmit} class="flex flex-col gap-y-6">
    <!-- Name Field -->
    <div class="flex flex-col gap-y-1">
      <label for="name" class="text-sm font-semibold text-ink/80 tracking-wide">
        Name
      </label>
      <input 
        id="name"
        type="text"
        bind:value={name}
        placeholder="whats do people call you?" 
        class="underline-input w-full py-2 text-ink focus:outline-none"
        required
      />
    </div>


    <div class="flex flex-col gap-y-1">
      <label for="email" class="text-sm font-semibold text-ink/80 tracking-wide">
        Email
      </label>
      <input 
        id="email"
        type="email"
        bind:value={email}
        placeholder="you@example.com" 
        class="underline-input w-full py-2 text-ink focus:outline-none"
        required
      />
    </div>


    <div class="flex flex-col gap-y-1">
      <label for="slack" class="text-sm font-semibold text-ink/80 tracking-wide">
        Slack Handle
      </label>
      <input 
        id="slack"
        type="text"
        bind:value={slackId}
        placeholder="@username" 
        class="underline-input w-full py-2 text-ink focus:outline-none"
      />
    </div>
    <button 
      type="submit" 
      class="mt-4 w-full bg-maroon text-sun-soft font-semibold py-3.5 px-6 rounded-full hover:opacity-95 active:scale-[0.98] transition cursor-pointer"
    >
      I'm intrested!
    </button>
  </form>
</section>