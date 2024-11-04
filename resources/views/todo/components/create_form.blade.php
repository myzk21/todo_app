<div class="px-6 pb-5 pt-3 hidden" id="todo_content">
    <div>
        <textarea placeholder="内容" class="placeholder:text-sm placeholder:text-gray-300 w-4/5 rounded" name="description" id="todo_description_input">{{ old('description') }}</textarea>
    </div>
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
                <option value="高" {{ old('priority') == "高" ? 'selected' : '' }}>高</option>
                <option value="中" {{ old('priority') == "中" ? 'selected' : '' }}>中</option>
                <option value="低" {{ old('priority') == "低" ? 'selected' : '' }}>低</option>
            </select>
        </div>
        <div class="w-full sm:w-auto mb-4 sm:mb-0">
            <label for="due">期日</label>
            <input type="date" name="due" id="due" class="rounded" value="{{ old('due') }}">
        </div>
        @if($google_user && $google_user->access_token && $google_user->refresh_token)
            <input type="hidden" name="googleUser" value="{{ $google_user->id }}">
            <label class="inline-flex items-center cursor-pointer ml-2 sm:mt-1">
                <input type="checkbox" class="hidden peer" name="addToCalendar" id="addToCalendarCheckbox" value="1">
                <div class="w-5 h-5 border border-gray-500 rounded-sm peer-checked:bg-[#8b8a8e] relative">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 peer-checked:block w-4 h-4 text-white text-center absolute inset-0 m-auto">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </div>
                <p class="ml-1 select-none">Googleカレンダーに追加</p>
            </label>
        @else
            <label class="inline-flex items-center cursor-pointer ml-2 sm:mt-1">
                <input type="checkbox" class="hidden peer" name="addToCalendar" id="addToCalendarCheckbox" value="" disabled>
                <div class="w-5 h-5 border border-gray-400 rounded-sm peer-checked:bg-[#8b8a8e] relative">
                    {{--チェックマーク--}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 peer-checked:block w-4 h-4 text-white text-center absolute inset-0 m-auto">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </div>
                <p class="ml-1 select-none line-through text-gray-500">Googleカレンダーに追加</p>
            </label>
        @endif
    </div>
</div>
