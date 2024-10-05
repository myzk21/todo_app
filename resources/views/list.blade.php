<x-app-layout>
    <section class="bg-gray-50 p-8">
        <div class="container mx-auto">
            <label class="toggle-switch">
                <div class="flex">
                    <p class="text-sm mr-1">月間目標を表示</p>
                    <input type="checkbox" id="monthly_check_box">
                    <span class="mb-2"></span>
                </div>
            </label>

            <div class="w-3/4 mb-6 bg-white px-6 py-3">{{--shadow-md rounded-lg--}}
                <div class="flex">
                    <h2 class="text-xl font-bold">Weekly Goal / 週間目標</h2>
                    <p class="ml-auto cursor-pointer text-gray-400 text-sm flex hover:underline">作成
                    </p>
                </div>
                    <p class="text-gray-500">A list of all the users in your account including their name, title, email and role.</p>

                <div class="hidden" id="monthly_goal">
                    <div class="border border-gray-200 my-4"></div>
                    <div class="flex">
                        <h2 class="text-xl font-bold">Monthly Goal / 月間目標</h2>
                        <p class="ml-auto cursor-pointer text-gray-400 text-sm flex hover:underline">作成
                        </p>
                    </div>
                    <p class="text-gray-500">A list of all the users in your account including their name, title, email and role.</p>
                </div>
            </div>

            {{-- <div class="mb-6 w-3/4 bg-white px-6 py-3">{{--shadow-md rounded-lg--}}
                {{-- <div class="flex">
                    <p class="cursor-pointer text-gray-400 text-sm flex hover:underline flex">詳細入力
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </p>
                    <input type="text" class="border border-gray-500 rounded h-8 placeholder:text-sm placeholder:text-gray-300 w-3/4" placeholder="TODOを入力">
                    <button class="bg-[#8b8a8e] text-white text-sm px-4 py-2 rounded h-8 ml-auto hover:bg-opacity-80">追加</button>
                </div>
            </div> --}}


            <div class="bg-white shadow-md rounded-lg w-3/4 overflow-x-auto">
                <form action="" method="" class="" id="">
                    <div class="flex px-6 py-5 items-center">
                        <input type="text" class="border border-gray-500 rounded h-8 placeholder:text-sm placeholder:text-gray-300 w-4/5" placeholder="TODOを入力">

                        <div class="ml-2 flex flex-col items-center cursor-pointer" id="detail">
                            <p class="text-sm -mb-1 hover:underline">詳細</p>
                            <p class="text-sm -mb-1 hover:underline hidden">閉じる</p>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                        <button class="bg-[#8b8a8e] text-white text-sm px-4 py-2 rounded h-8 ml-auto hover:bg-opacity-80">追加</button>
                    </div>
                    <div class="">
                        <textarea placeholder="内容" class="placeholder:text-sm placeholder:text-gray-300"></textarea>
                        <label for="percentage-dropdown">進捗率</label>
                        <select id="percentage-dropdown">
                            @for($i = 0; $i <= 100; $i+10)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                            {{-- <option value="10">10%</option>
                            <option value="20">20%</option>
                            <option value="30">30%</option>
                            <option value="40">40%</option>
                            <option value="50">50%</option>
                            <option value="60">60%</option>
                            <option value="70">70%</option>
                            <option value="80">80%</option>
                            <option value="90">90%</option>
                            <option value="100">100%</option> --}}
                        </select>

                    </div>
                </form>

                <table class="w-full text-left table-auto">
                    <thead>
                        <tr class="border-b">
                            <th class="px-6 py-3 text-gray-700 font-medium"></th>
                            <th class="px-6 py-3 text-gray-700 font-medium">Name</th>
                            <th class="px-6 py-3 text-gray-700 font-medium">Title</th>
                            <th class="px-6 py-3 text-gray-700 font-medium">Email</th>
                            <th class="px-6 py-3 text-gray-700 font-medium">Role</th>
                            <th class="px-6 py-3 text-gray-700 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="px-6 py-4">
                                <input type="checkbox" class="form-checkbox">
                            </td>
                            <td class="px-6 py-4">Lindsay Walton</td>
                            <td class="px-6 py-4">Front-end Developer</td>
                            <td class="px-6 py-4">lindsay.walton@example.com</td>
                            <td class="px-6 py-4">Member</td>
                            <td class="px-6 py-4 text-blue-500 hover:underline"><a href="#">Edit</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-app-layout>

