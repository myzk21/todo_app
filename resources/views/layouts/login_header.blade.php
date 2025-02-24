<section class="flex bg-[#f9fafb] h-10 py-2 mt-8">
    <div class="container mx-auto flex justify-between">
        <div class="flex w-full">
            @auth
                <form method="POST" action="{{ route('logout') }}" class="w-full flex justify-end">
                    @csrf
                    <button type="submit" class="mr-3 hover:underline max-sm:text-xs">
                        ログアウト
                    </button>
                </form>
            @endauth
            @guest
                <a href="{{route('register')}}" class="ml-auto mr-3 hover:underline">ユーザー登録</a>
                <a href="{{route('login')}}" class="mr-3 hover:underline">ログイン</a>
            @endguest
        </div>
    </div>
</section>

