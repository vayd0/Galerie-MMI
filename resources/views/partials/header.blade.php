<nav class="flex justify-between items-center px-3 py-8 mb-4 h-[8%] glass-morph">
  <button
    class="glass-morph w-[3rem] h-[3rem] flex justify-center items-center rounded-xl transition-all duration-300 text-black cursor-pointer"
    id="filter-photo" onclick="openModal('filterAlbumsModal')">
    <i class="fa-solid fa-filter"></i>
  </button>
  <label class="inline-flex cursor-pointer items-center" id="nav-toggle">
    <input type="checkbox" class="peer sr-only" id="nav-checkbox" />
    <div
      class="relative flex h-8 items-center gap-4 rounded-full glass-morph px-1 overflow-hidden text-sm text-darkblue">
      <div id="nav-slider"
        class="absolute left-1 top-1/2 -translate-y-1/2 h-6 w-[45%] rounded-full glass-morph transition-transform duration-300 ease-in-out">
      </div>

      <span class="px-4 z-10 relative" id="nav-albums">Albums</span>
      <span class="px-4 z-10 relative" id="nav-photos">Photos</span>
    </div>
  </label>
</nav>