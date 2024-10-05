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

            <div class="w-3/4 mb-6 bg-white px-6 py-3">
                <div class="flex">
                    <h2 class="text-xl font-bold">Weekly Goal / 週間目標</h2>
                    <p class="ml-auto cursor-pointer text-gray-400 text-sm flex hover:underline select-none">作成
                    </p>
                </div>
                    <p class="text-gray-500">A list of all the users in your account including their name, title, email and role.</p>

                <div class="hidden" id="monthly_goal">
                    <div class="border border-gray-200 my-4"></div>
                    <div class="flex">
                        <h2 class="text-xl font-bold">Monthly Goal / 月間目標</h2>
                        <p class="ml-auto cursor-pointer text-gray-400 text-sm flex hover:underline select-none">作成
                        </p>
                    </div>
                    <p class="text-gray-500">A list of all the users in your account including their name, title, email and role.</p>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg w-3/4 overflow-x-auto">
                <form action="" method="" class="" id="">
                    <div class="flex px-6 pt-5 items-center">
                        <input type="text" class="border border-gray-500 rounded h-8 placeholder:text-sm placeholder:text-gray-300 w-4/5" placeholder="TODOを入力">

                        <div class="ml-2 flex flex-col items-center cursor-pointer" id="detail">
                            <p class="text-sm -mb-1 hover:underline select-none" id="detail_sign">詳細</p>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4" id="down_arrow">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                        <button class="bg-[#8b8a8e] text-white text-sm px-4 py-2 rounded h-8 ml-auto hover:bg-opacity-80 select-none">追加</button>
                    </div>
                    <div class="px-6 pb-5 pt-3 hidden" id="todo_content">
                        <div>
                            <textarea placeholder="内容" class="placeholder:text-sm placeholder:text-gray-300 w-4/5 rounded"></textarea>
                        </div>
                        <div class="flex flex-wrap pt-1">
                            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                                <label for="percentage">進捗率</label>
                                <select id="percentage" class="rounded mr-4">
                                    <option value="">--</option>
                                    @for($i = 0; $i <= 100; $i += 10)
                                        <option value="{{ $i }}">{{ $i }}%</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                                <label for="priority">優先度</label>
                                <select id="priority" class="rounded mr-4">
                                    <option value="">--</option>
                                    <option value="high">高</option>
                                    <option value="middle">中</option>
                                    <option value="low">低</option>
                                </select>
                            </div>
                            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                                <label for="due">期日</label>
                                <input type="date" name="due" id="due" class="rounded">
                            </div>
                            {{--余裕あるならここにラベルを追加--}}
                        </div>
                    </div>
                </form>

                <div class="border border-gray-100 my-4"></div>

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

