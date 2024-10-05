<section class="flex bg-[#f9fafb] h-10 py-2">
    <div class="container mx-auto flex justify-between">
        <div class="flex w-full">
            @auth
                <a href="{{route('profile.edit')}}" class="ml-auto mr-3 hover:underline">マイページ</a>
            @endauth
            @guest
                <a href="{{route('register')}}" class="ml-auto mr-3 hover:underline">ユーザー登録</a>
                <a href="{{route('login')}}" class="mr-3 hover:underline">ログイン</a>
            @endguest
        </div>
    </div>
</section>

