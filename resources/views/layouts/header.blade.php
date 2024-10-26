<div class="flex justify-between fixed top-0 w-full z-20">
    @auth
        <a href="{{route('home')}}" class="w-1/2 h-8 text-lg flex items-center justify-center bg-[#8b8a8e] text-white text-2xl py-3 rounded-sm hover:bg-[#eeeeee] hover:text-[#8b8a8e]">TODO</a>
        <a href="{{route('pdca')}}" class="w-1/2 h-8 text-lg flex items-center justify-center bg-[#8b8a8e] text-white text-2xl py-3 rounded-sm hover:bg-[#eeeeee] hover:text-[#8b8a8e]">PDCA</a>
    @endauth
    @guest
        <a href="{{route('login')}}" class="w-1/2 h-8 text-lg flex items-center justify-center bg-[#8b8a8e] text-white text-2xl py-3 rounded-sm hover:bg-[#eeeeee] hover:text-[#8b8a8e]">TODO</a>
        <a href="{{route('login')}}" class="w-1/2 h-8 text-lg flex items-center justify-center bg-[#8b8a8e] text-white text-2xl py-3 rounded-sm hover:bg-[#eeeeee] hover:text-[#8b8a8e]">PDCA</a>
    @endauth
</div>
