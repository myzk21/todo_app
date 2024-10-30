<x-app-layout>
    @section('load-vite-todo-script', true) {{--TODOに関するTSを使用--}}

    <section class="bg-gray-50 px-8 py-5">
        <div class="grid grid-cols-3 gap-5"  id="todo_list">
            <div class="container mx-auto col-span-2">
                <label class="toggle-switch inline-block">
                    <div class="flex">
                        <p class="text-sm mr-1">月間目標を表示</p>
                        <input type="checkbox" id="monthly_check_box">
                        <span class="mb-2"></span>
                    </div>
                </label>

                <div class="w-full mb-6 bg-white px-6 py-3">
                    <div class="flex mb-1">
                        <h2 class="text-xl font-bold">Weekly Goal / 週間目標</h2>
                        <a href="{{route('pdca')}}" class="ml-auto cursor-pointer text-gray-400 text-sm flex hover:underline select-none">作成
                        </a>
                    </div>
                    @if($weeklyGoal)
                        <p class="text-gray-700">{{ $weeklyGoal['title'] }}</p>
                        <p class="text-sm text-gray-400 underline text-right">期日: {{ $weeklyGoal['due'] }}</p>
                    @else
                        <p class="text-gray-700">週間目標はまだ設定されていません</p>
                    @endif

                    <div class="hidden" id="monthly_goal">
                        <div class="border border-gray-200 my-4"></div>
                        <div class="flex mb-1">
                            <h2 class="text-xl font-bold">Monthly Goal / 月間目標</h2>
                            <a href="{{route('pdca')}}" class="ml-auto cursor-pointer text-gray-400 text-sm flex hover:underline select-none">作成
                            </a>
                        </div>
                        @if($monthlyGoal)
                            <p class="text-gray-700">{{ $monthlyGoal['title'] }}</p>
                            <p class="text-sm text-gray-400 underline text-right">期日: {{ $monthlyGoal['due'] }}</p>
                        @else
                            <p class="text-gray-700">月間目標はまだ設定されていません</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-lg w-full overflow-x-auto">
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
                            @if(!$today_todos->isEmpty())
                                @foreach($today_todos as $todo)
                                    <tr class="border-b border-gray-100 todo-item todo-container {{ $todo['when_completed'] ? 'opacity-25' : '' }}" id="{{ $todo['id'] }}">
                                        <td class="px-4 py-3 text-center">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="hidden peer todo-checkbox">
                                                <div class="w-5 h-5 border border-gray-400 rounded-sm peer-checked:bg-[#8b8a8e] {{ $todo['when_completed'] ? 'bg-[#8b8a8e]' : '' }} relative">
                                                    {{--チェックマーク--}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 peer-checked:block w-4 h-4 text-white text-center absolute inset-0 m-auto">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                    </svg>
                                                </div>
                                            </label>
                                        </td>
                                        <td class="px-4 py-3 text-center title {{ $todo['when_completed'] ? 'line-through' : '' }}">{{ \Illuminate\Support\Str::limit($todo['title'], 15, '...') }}</td>
                                        <td class="px-4 py-3 text-center description {{ $todo['when_completed'] ? 'line-through' : '' }}">{{ \Illuminate\Support\Str::limit($todo['description'], 15, '...') ?? '--' }}</td>
                                        <td class="px-4 py-3 text-center progress_rate {{ $todo['when_completed'] ? 'line-through' : '' }}">{{ $todo['progress_rate'] ?? '--' }}%</td>
                                        <td class="px-4 py-3 text-center priority {{ $todo['when_completed'] ? 'line-through' : '' }}">{{ $todo['priority'] ?? '--' }}</td>
                                        <td class="px-4 py-3 text-center due {{ $todo['when_completed'] ? 'line-through' : '' }}">{{ $todo['due'] ?? '--' }}</td>
                                        <td class="px-4 py-3 text-gray-500 text-sm hover:underline text-center"><a href="#" class="showBtn" todo-id="{{ $todo['id'] }}">詳細</a></td>
                                        <td class="px-4 py-3 text-gray-400 text-sm hover:underline text-center">
                                            <a href="#" class="todo_delete_btn" todo-id="{{ $todo['id'] }}">{{--ゴミ箱アイコン--}}
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-gray-400">
                                                    <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="border border-gray-100" id="first-today-todo-message">
                                    <td colspan="8" class="px-4 py-3 text-gray-400 text-sm text-center select-none">本日のTODOはまだありません</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- <div class="container mx-auto col-span-1" id="not-today-todos-list"> --}}
            <div class="container mx-auto col-span-1">
                <div class="w-full">
                    <p class="font-bold text-center">本日以降のTODO</p>
                </div>
                @if(!$not_today_todos->isEmpty())
                    <div class="bg-white rounded p-2 mt-2" id="not-today-todos-list">
                        @foreach($not_today_todos as $not_today_todo)
                            <div class="bg-white shadow-sm rounded-lg border border-gray-200 px-6 py-4 max-w-md mx-auto my-3 todo-container {{ $not_today_todo['when_completed'] ? 'opacity-25' : '' }}" id="{{ $not_today_todo['id'] }}">
                                <div class="w-full flex mb-2">
                                    <label class="inline-flex items-center cursor-pointer -ml-2">
                                        <input type="checkbox" class="hidden peer todo-checkbox">
                                        <div class="w-5 h-5 border border-gray-400 rounded-sm peer-checked:bg-[#8b8a8e] {{ $not_today_todo['when_completed'] ? 'bg-[#8b8a8e]' : '' }} relative">
                                            {{--チェックマーク--}}
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 peer-checked:block w-4 h-4 text-white text-center absolute inset-0 m-auto">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                        </div>
                                    </label>
                                    <a href="#" class="showBtn ml-auto cursor-pointer text-sm hover:underline select-none mr-3" todo-id="{{ $not_today_todo['id'] }}">詳細</a>
                                    <a href="#" class="todo_delete_btn hover:underline text-center" todo-id="{{ $not_today_todo['id'] }}">{{--ゴミ箱アイコン--}}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-gray-400">
                                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </div>
                                <div class="mb-4">
                                    <p class="text-gray-700 font-bold">タイトル</p>
                                    <p class="text-gray-900 ml-2 title {{ $not_today_todo['when_completed'] ? 'line-through' : '' }}">{{ \Illuminate\Support\Str::limit($not_today_todo['title'], 15) }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="text-gray-700 font-bold">内容</p>
                                    <p class="text-gray-900 ml-2 description {{ $not_today_todo['when_completed'] ? 'line-through' : '' }}">{{ \Illuminate\Support\Str::limit($not_today_todo['description'], 15, '...') ?? '--' }}</p>
                                </div>
                                <div class="flex justify-between">
                                    <div class="">
                                        <p class="text-gray-700 font-bold">進捗率</p>
                                        <p class="text-gray-900 ml-2 progress_rate {{ $not_today_todo['when_completed'] ? 'line-through' : '' }}">{{ $not_today_todo['progress_rate'] ?? '--' }}%</p>
                                    </div>
                                    <div class="">
                                        <p class="text-gray-700 font-bold">優先度</p>
                                        <p class="text-gray-900 ml-2 priority {{ $not_today_todo['when_completed'] ? 'line-through' : '' }}">{{ $not_today_todo['priority'] ?? '--' }}</p>
                                    </div>
                                    <div class="">
                                        <p class="text-gray-700 font-bold">期日</p>
                                        <p class="text-gray-900 ml-2 due {{ $not_today_todo['when_completed'] ? 'line-through' : '' }}">{{ $not_today_todo['due'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        {{--ここに下ボタン表示--}}
                        {{-- <div class="text-center my-2">
                            <button id="load-more" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">もっと見る</button>
                        </div> --}}
                    </div>
                @else
                    <div class="w-full mt-3 bg-white p-6 rounded-lg shadow-sm" id="not-today-todos-list">
                        <p class="text-gray-400 text-center text-sm" id="first-message">本日以外のTODOはありません</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>

