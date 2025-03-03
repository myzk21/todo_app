<div class="w-full h-full z-50 fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="small_width_todo_create_container">
    <form action="" method="POST" class="shadow-sm mx-5 my-5 py-3 bg-white rounded-lg h-[calc(100%-32px)] w-4/5 overflow-y-auto" id="small_width_todo_create_form">
        @csrf
        <div id="smallWidthErrorContainer"></div> {{--バリデーションエラー表示--}}
        <div class="px-6">
            <label>TODO</label>
            <input type="text" class="mb-3 block border border-gray-500 rounded h-8 placeholder:text-sm placeholder:text-gray-300 w-full mx-auto" placeholder="TODOを入力" name="title" value="{{ old('title') }}" id="todo_title_input">
            <label>内容</label>
            <textarea placeholder="内容" class="placeholder:text-sm placeholder:text-gray-300 w-4/5 rounded w-full mb-3" name="description" id="todo_description_input">{{ old('description') }}</textarea>
            <div class="w-full sm:w-auto mb-3 sm:mb-0">
                <label for="percentage" class="select-none">進捗率</label>
                <select id="percentage" class="rounded mr-4 block w-full" name="progress_rate">
                    <option value="">--</option>
                    @for($i = 0; $i <= 100; $i += 10)
                        <option value="{{ $i }}" {{ old('progress_rate') !== null && old('progress_rate') == $i ? 'selected' : '' }}>
                            {{ $i }}%
                        </option>
                    @endfor
                </select>
            </div>
            <div class="w-full sm:w-auto mb-3 sm:mb-0">
                <label for="priority" class="select-none">優先度</label>
                <select id="priority" class="rounded mr-4 block w-full" name="priority">
                    <option value="">--</option>
                    <option value="高" {{ old('priority') == "高" ? 'selected' : '' }}>高</option>
                    <option value="中" {{ old('priority') == "中" ? 'selected' : '' }}>中</option>
                    <option value="低" {{ old('priority') == "低" ? 'selected' : '' }}>低</option>
                </select>
            </div>
            <div class="w-full sm:w-auto mb-3 sm:mb-0">
                <label for="due" class="select-none">期日</label>
                <input type="date" name="due" id="due" class="rounded block w-full" value="{{ old('due') }}">
            </div>
            @if($google_user && $google_user->access_token && $google_user->refresh_token)
                <input type="hidden" name="googleUser" value="{{ $google_user->id }}">
                <label class="inline-flex items-center cursor-pointer ml-2 mb-3">
                    <input type="checkbox" class="hidden peer" name="addToCalendar" id="smallWidthAddToCalendarCheckbox" value="1">
                    <div class="w-5 h-5 border border-gray-500 rounded-sm peer-checked:bg-[#8b8a8e] relative">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 peer-checked:block w-4 h-4 text-white text-center absolute inset-0 m-auto">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                    <p class="ml-1 select-none">Googleカレンダーに追加</p>
                </label>
            @else
                <label class="inline-flex items-center cursor-pointer ml-2 mb-3">
                    <input type="checkbox" class="hidden peer" name="addToCalendar" id="smallWidthAddToCalendarCheckbox" value="" disabled>
                    <div class="w-5 h-5 border border-gray-400 rounded-sm peer-checked:bg-[#8b8a8e] relative">
                        {{--チェックマーク--}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 peer-checked:block w-4 h-4 text-white text-center absolute inset-0 m-auto">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                    <p class="ml-1 select-none line-through text-gray-500">Googleカレンダーに追加</p>
                </label>
            @endif

            <button class="block mb-3 w-full bg-green-700 text-white text-sm px-4 py-2 rounded mx-auto hover:bg-opacity-80 select-none" id="small_width_todo_add_btn">追加</button>
            <p class="bg-gray-200 rounded shadow-sm p-1 m-1 cursor-pointer hover:underline hover:opacity-80 mb-3 text-center" id="close_smart_modal">キャンセル</p>
        </div>
    </form>
</div>
