<x-app-layout>
    <section class="bg-gray-50 p-8">
        <div class="container mx-auto">
            <label class="toggle-switch inline-block">
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

            <div class="bg-white shadow-md rounded-lg w-3/4 overflow-x-auto" id="todo_list">
                <form action="" method="POST" class="" id="todo_create_form">
                    @csrf

                    <div id="errorContainer"></div> {{--バリデーションエラー表示--}}

                    <div class="flex px-6 pt-5 items-center">
                        <input type="text" class="border border-gray-500 rounded h-8 placeholder:text-sm placeholder:text-gray-300 w-4/5" placeholder="TODOを入力" name="title" value="{{ old('title') }}" id="todo_title_input">
                        <div class="ml-2 flex flex-col items-center cursor-pointer" id="detail">
                            <p class="text-sm -mb-1 hover:underline select-none" id="detail_sign">詳細</p>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4" id="down_arrow">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                        <button class="bg-[#8b8a8e] text-white text-sm px-4 py-2 rounded ml-auto hover:bg-opacity-80 select-none" id="todo_add_btn">追加</button>
                    </div>
                    @include('todo.components.create_form'){{--詳細フォームを読み込み--}}
                </form>

                <div class="border border-gray-100 my-4"></div>

                <table class="w-full text-left table-auto">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-4 py-3 text-gray-700 font-medium"></th>
                            <th class="px-4 py-3 text-gray-700 font-medium text-center">タイトル</th>
                            <th class="px-4 py-3 text-gray-700 font-medium text-center">内容</th>
                            <th class="px-4 py-3 text-gray-700 font-medium text-center">進捗率</th>
                            <th class="px-4 py-3 text-gray-700 font-medium text-center">優先度</th>
                            <th class="px-4 py-3 text-gray-700 font-medium text-center">期日</th>
                            <th class="px-4 py-3 text-gray-700 font-medium"></th>
                            <th class="px-4 py-3 text-gray-700 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="mb-3" id="todo-table">
                        @foreach($today_todos as $todo)
                            <tr class="border-b border-gray-100 todo-item" id="{{ $todo['id'] }}">
                                <td class="px-4 py-3 text-center">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" class="hidden peer">
                                        <div class="w-5 h-5 border border-gray-400 rounded-sm peer-checked:bg-gray-500 relative">
                                            {{--チェックマーク--}}
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 peer-checked:block w-4 h-4 text-white text-center absolute inset-0 m-auto">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                        </div>
                                    </label>
                                </td>
                                <td class="px-4 py-3 text-center title">{{ \Illuminate\Support\Str::limit($todo['title'], 15, '...') }}</td>
                                <td class="px-4 py-3 text-center description">{{ \Illuminate\Support\Str::limit($todo['description'], 15, '...') ?? '--' }}</td>
                                <td class="px-4 py-3 text-center progress_rate">{{ $todo['progress_rate'] ?? '--' }}%</td>
                                <td class="px-4 py-3 text-center priority">{{ $todo['priority'] ?? '--' }}</td>
                                <td class="px-4 py-3 text-center due">{{ $todo['due'] ?? '--' }}</td>
                                <td class="px-4 py-3 text-gray-400 text-sm hover:underline text-center"><a href="#" class="showBtn" todo-id="{{ $todo['id'] }}">詳細</a></td>
                                <td class="px-4 py-3 text-gray-400 text-sm hover:underline text-center">
                                    <a href="#" class="todo_delete_btn" todo-id="{{ $todo['id'] }}">{{--ゴミ箱アイコン--}}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-gray-400">
                                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    {{-- <tr>
                        <td colspan="8" class="px-4 py-4 font-bold border-b border-gray-100">本日完了したタスク</td>
                    </tr>

                    @if($completed_todos->isEmpty())
                        <tr class="border border-gray-100">
                            <td colspan="8" class="px-4 py-3 text-sm text-center select-none">完了したタスクはありません</td>
                        </tr>
                    @else
                        @foreach($completed_todos as $completed_todo)
                            <tr class="border-b border-gray-100 todo-item" id="{{ $todo['id'] }}">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" class="form-checkbox">
                                </td>
                                    <td class="px-4 py-3 text-center title">{{ \Illuminate\Support\Str::limit($todo['title'], 15) }}</td>
                                    <td class="px-4 py-3 text-center description">{{ \Illuminate\Support\Str::limit($todo['description'] ?? '--', 15) }}</td>
                                    <td class="px-4 py-3 text-center progress_rate">{{ $todo['progress_rate'] ?? '--' }}%</td>
                                    <td class="px-4 py-3 text-center priority">{{ $todo['priority'] ?? '--' }}</td>
                                    <td class="px-4 py-3 text-center due">{{ $todo['due'] ?? '--' }}</td>
                                    <td class="px-4 py-3 text-gray-400 text-sm hover:underline text-center"><a href="#" class="showBtn" todo-id="{{ $todo['id'] }}">詳細</a></td>
                                    <td class="px-4 py-3 text-gray-400 text-sm hover:underline text-center">
                                        <a href="#"id="todo-delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-gray-400">
                                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    </td>
                            </tr>
                        @endforeach
                    @endif--}}
                </table>
            </div>
        </div>
    </section>
</x-app-layout>

