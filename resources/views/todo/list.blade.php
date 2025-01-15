<x-app-layout>
    @section('load-vite-todo-script', true) {{--TODOに関するTSを使用--}}

    <section class="bg-gray-50 px-8 py-5">
        @if(session()->has('invalidRefreshToken'))
            <script>
                window.onload = function() {
                    if (!localStorage.getItem('hasReloaded')) {
                        localStorage.setItem('hasReloaded', 'true');
                        location.reload(); //一度だけページをリロード
                    }
                };
            </script>
            <p class="text-red-500 font-semibold mb-1 underline">Googleアカウントに接続してください</p>
        @endif
        @if ($errors->has('googleAuthError'))
            <div class="relative bg-red-500 w-2/5 rounded mb-2 p-2 flex items-center ml-8">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-1 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <p class="text-white font-semibold">{{ $errors->first('googleAuthError') }}</p>
                <p class="text-right text-white absolute -top-1 right-2 cursor-pointer text-3xl" id="closeSystemError">×</p>
            </div>
        @endif

        <div id="systemErrorContainer">{{--システムエラーを表示--}}</div>
        <div class="fixed top-12 left-7 bg-green-600 text-white px-8 py-2 rounded z-50 hidden select-none" id="user_action_dialog"></div>{{--TODO追加、編集、完了、未完了のダイアログ--}}

        @if($weeklyGoal && $monthlyGoal)
            @if($weeklyGoal->due < now()->format('Y-m-d') && $monthlyGoal->due < now()->format('Y-m-d'))
                <div id="notice" class="text-red-500">
                    新しい週間目標、月間目標を設定しましょう
                </div>
            @elseif($weeklyGoal->due < now()->format('Y-m-d'))
                <div id="notice" class="text-red-500">
                    新しい週間目標を設定しましょう
                </div>
            @elseif($monthlyGoal->due < now()->format('Y-m-d'))
                <div id="notice" class="text-red-500">
                    新しい月間目標を設定しましょう
                </div>
            @endif
        @endif
        <div class=""  id="todo_list">
            <div class="container mx-auto">
                <div class="flex justify-between">
                    <label class="toggle-switch inline-block">
                        <div class="flex">
                            <p class="text-sm mr-1 select-none">月間目標を表示</p>
                            <input type="checkbox" id="monthly_check_box">
                            <span class="mb-2"></span>
                        </div>
                    </label>
                    @if($google_user && $google_user->access_token && $google_user->refresh_token && !session()->has('invalidRefreshToken'))
                        <div class="flex">
                            <p class="text-green-600 flex items-center text-sm select-none mr-1">Googleアカウント接続済み
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            </p>
                            <a class="text-xs bg-gray-200 rounded shadow-sm p-1 m-1 cursor-pointer hover:underline hover:opacity-80" href="{{ route('google.redirect') }}" id="connectToGoogle">再接続</a>
                        </div>
                    @else
                        <a class="flex items-center px-2 py-1 text-sm text-green-600 font-semibold bg-white hover:bg-gray-100 mb-1 rounded-md  select-none cursor-pointer" href="{{ route('google.redirect') }}" id="connectToGoogle">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        Googleアカウントに接続</a>
                    @endif
                </div>
                <div class="w-full mb-5 bg-white px-6 py-3">
                    <div class="flex mb-1">
                        <h2 class="text-xl font-bold">Weekly Goal / 週間目標</h2>
                        <a href="{{route('pdca')}}" class="ml-auto cursor-pointer text-gray-500 text-sm flex hover:underline select-none">作成
                        </a>
                    </div>
                    @if($weeklyGoal)
                        <p class="text-gray-700">{{ $weeklyGoal['title'] }}</p>
                        <p class="text-sm text-gray-700 underline text-right">期日: {{ $weeklyGoal['due'] }}</p>
                    @else
                        <p class="text-gray-700">週間目標はまだ設定されていません</p>
                    @endif

                    <div class="hidden" id="monthly_goal">
                        <div class="border border-gray-200 my-4"></div>
                        <div class="flex mb-1">
                            <h2 class="text-xl font-bold">Monthly Goal / 月間目標</h2>
                            <a href="{{route('pdca')}}" class="ml-auto cursor-pointer text-gray-500 text-sm flex hover:underline select-none">作成
                            </a>
                        </div>
                        @if($monthlyGoal)
                            <p class="text-gray-700">{{ $monthlyGoal['title'] }}</p>
                            <p class="text-sm text-gray-700 underline text-right">期日: {{ $monthlyGoal['due'] }}</p>
                        @else
                            <p class="text-gray-700">月間目標はまだ設定されていません</p>
                        @endif
                    </div>
                </div>
                {{--TODOリスト--}}
                <form action="" method="POST" class="shadow-sm mb-5 py-4 bg-white rounded-lg w-full overflow-x-auto" id="todo_create_form">
                    @csrf
                    <div id="errorContainer"></div> {{--バリデーションエラー表示--}}

                    <div class="flex px-6 items-center">
                        <input type="text" class="border border-gray-500 rounded h-8 placeholder:text-sm placeholder:text-gray-300 w-4/5" placeholder="TODOを入力" name="title" value="{{ old('title') }}" id="todo_title_input">
                        <div class="ml-2 flex flex-col items-center cursor-pointer" id="detail">
                            <p class="text-sm -mb-1 hover:underline select-none" id="detail_sign">詳細</p>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4" id="down_arrow">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                        <button class="bg-green-800 text-white text-sm px-4 py-2 rounded ml-auto hover:bg-opacity-80 select-none" id="todo_add_btn">追加</button>
                    </div>
                    @include('todo.components.create_form'){{--詳細フォームを読み込み--}}
                </form>
                <div class="bg-white shadow-sm rounded-lg w-full overflow-x-auto">
                    <div class="flex my-3">
                        <ul class="flex border-b">
                            <li class="mr-1">
                                <a href="#" class="inline-block py-1 px-6 whitespace-nowrap active" id="incompleteTask_tab">未完了のタスク</a>
                            </li>
                            <li class="mr-1">
                                <a href="#" class="inline-block py-1 px-6 whitespace-nowrap text-gray-500 hover:opacity-60" id="completeTask_tab">完了したタスク</a>
                            </li>
                        </ul>
                        <!--検索フォーム-->
                        <div class="ml-auto rounded bg-white flex mr-3">
                        <!----------------------------ここから改良する
                            <p class="rounded-full border border-[#8b8a8e] bg-white py-1 px-2 cursor-pointer text-[#8b8a8e] hover:bg-green-700 hover:text-white select-none flex mr-2 items-center whitespace-nowrap">本日のタスク</p>{{---TSでclickされたら色をトグル--}}

                            <select class="rounded-full border border-[#8b8a8e] bg-white py-1 px-2 cursor-pointer text-[#8b8a8e] select-none flex focus:outline-none pr-10 flex items-center mr-2">
                                <option value="" class="">ラベルを選択</option>
                                {{-- <option value="" class="">未選択</option> --}}
                            </select>
                        -------------------------------------------------------------------------------------->
                            <form action="{{ route('home') }}" method="GET" class="flex">
                                @csrf
                                <input type="text" placeholder="キーワードを入力" class="py-1 px-2 rounded focus:outline-none focus:ring-[#8b8a8e] placeholder:text-sm placeholder:text-gray-300" name="keyWord" value="{{ $keyWord ? $keyWord : '' }}">
                                <button class="flex items-center text-green-800 cursor-pointer ml-1 hover:opacity-80" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-8">
                                        <path d="M8.25 10.875a2.625 2.625 0 1 1 5.25 0 2.625 2.625 0 0 1-5.25 0Z" />
                                        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.125 4.5a4.125 4.125 0 1 0 2.338 7.524l2.007 2.006a.75.75 0 1 0 1.06-1.06l-2.006-2.007a4.125 4.125 0 0 0-3.399-6.463Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="" id="incompleteTaskContainer">
                        <table class="w-full text-left table-auto" id="incompleteTaskTable">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="px-4 py-3 text-gray-700 font-medium"></th>
                                    <th class="px-4 py-3 text-gray-700 font-medium text-center select-none">タイトル</th>
                                    <th class="px-4 py-3 text-gray-700 font-medium text-center select-none">内容</th>
                                    <th class="px-4 py-3 text-gray-700 font-medium text-center select-none">進捗率</th>
                                    <th class="px-4 py-3 text-gray-700 font-medium text-center select-none">優先度</th>
                                    <th class="px-4 py-3 text-gray-700 font-medium text-center select-none">期日</th>
                                    <th class="px-4 py-3 text-gray-700 font-medium"></th>
                                    <th class="px-4 py-3 text-gray-700 font-medium"></th>
                                </tr>
                            </thead>
                            <tbody class="mb-3 incompleteTaskTableBody">
                                @if(!$incompleteTodos->isEmpty())
                                    @foreach($incompleteTodos as $todo)
                                        <tr class="border-b border-gray-100 todo-item todo-container" id="{{ $todo['id'] }}" data-created-at="{{ $todo['created_at'] }}">
                                            <td class="px-4 py-3 text-center">
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="hidden peer todo-checkbox">
                                                    <div class="w-5 h-5 border border-gray-400 rounded-sm peer-checked:bg-[#8b8a8e] relative">
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
                                    <tr class="border border-gray-100" id="first-incompleted-todo-message">
                                        <td colspan="8" class="px-4 py-3 text-gray-400 text-sm text-center select-none">未完了のタスクはありません</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        {{ $incompleteTodos->links() }}
                    </div>
                    <div class="hidden" id="completeTaskContainer">
                        <table class="w-full text-left table-auto" id="completeTaskTable">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="px-4 py-3 text-gray-700 font-medium"></th>
                                    <th class="px-4 py-3 text-gray-700 font-medium text-center select-none">タイトル</th>
                                    <th class="px-4 py-3 text-gray-700 font-medium text-center select-none">内容</th>
                                    <th class="px-4 py-3 text-gray-700 font-medium text-center select-none">進捗率</th>
                                    <th class="px-4 py-3 text-gray-700 font-medium text-center select-none">優先度</th>
                                    <th class="px-4 py-3 text-gray-700 font-medium text-center select-none">期日</th>
                                    <th class="px-4 py-3 text-gray-700 font-medium"></th>
                                    <th class="px-4 py-3 text-gray-700 font-medium"></th>
                                </tr>
                            </thead>
                            <tbody class="mb-3 completeTaskTableBody">
                                @if(!$completeTodos->isEmpty())
                                    @foreach($completeTodos as $todo)
                                        <tr class="border-b border-gray-100 todo-item todo-container {{ $todo['is_completed'] ? 'opacity-30' : '' }}" id="{{ $todo['id'] }}">
                                            <td class="px-4 py-3 text-center">
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="hidden peer todo-checkbox">
                                                    <div class="w-5 h-5 border border-gray-400 rounded-sm peer-checked:bg-[#8b8a8e] {{ $todo['is_completed'] ? 'bg-[#8b8a8e]' : '' }} relative">
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
                                    <tr class="border border-gray-100" id="first-completed-todo-message">
                                        <td colspan="8" class="px-4 py-3 text-gray-400 text-sm text-center select-none">完了したタスクはありません</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        {{ $completeTodos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>

