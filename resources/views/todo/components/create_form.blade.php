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
</div>
