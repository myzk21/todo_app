<div class="w-full h-full z-50 fixed insert-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="px-6 pb-5 pt-3 shadow-sm w-3/5 rounded bg-white" id="">
        <div class="pointer-events-none flex justify-end">
            <p class="text-4xl cursor-pointer hover:opacity-60 -mb-2 pointer-events-auto inline-block">×</p>{{--ここに閉じるボタンを設置--}}
        </div>
        <label for="todo_title_input" class="block pb-1">タイトル</label>
        <input type="text" class="border border-gray-500 rounded h-8 mb-2 placeholder:text-sm placeholder:text-gray-300 w-full" placeholder="TODOを入力" name="title" value="{{ old('title') }}" id="todo_title_input">
        <label for="todo_description_input" class="block mb-1">内容</label>
        <textarea placeholder="内容" class="mb-1 placeholder:text-sm placeholder:text-gray-300 rounded w-full" name="description" id="todo_description_input">{{ old('description') }}</textarea>
        <div class="flex flex-wrap pt-1">
            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                <label for="percentage">進捗率</label>
                <select id="percentage" class="rounded mr-4" name="progress_rate">
                    <option value="">--</option>
                    @for($i = 0; $i <= 100; $i += 10)
                        <option value="{{ $i }}" {{ old('progress_rate') !== null && old('progress_rate') == $i ? 'selected' : '' }}>
                            {{ $i }}%
                        </option>
                    @endfor
                </select>
            </div>
            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                <label for="priority">優先度</label>
                <select id="priority" class="rounded mr-4" name="priority">
                    <option value="">--</option>
                    <option value="high" {{ old('priority') == "high" ? 'selected' : '' }}>高</option>
                    <option value="middle" {{ old('priority') == "middle" ? 'selected' : '' }}>中</option>
                    <option value="low" {{ old('priority') == "low" ? 'selected' : '' }}>低</option>
                </select>
            </div>
            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                <label for="due">期日</label>
                <input type="date" name="due" id="due" class="rounded" value="{{ old('due') }}">
            </div>
            {{--余裕あるならここにラベルを追加--}}
        </div>
        <div class="w-full flex mt-1">
            <button class="bg-[#8b8a8e] text-white text-sm px-4 py-2 rounded ml-auto hover:bg-opacity-80 select-none flex justify-end" id="todo_add_btn">保存</button>
            <p class="hover:underline cursor-pointer px-4 py-2 text-gray-400 text-sm">削除</p>
        </div>
    </div>
</div>
