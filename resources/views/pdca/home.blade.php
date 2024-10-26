<x-app-layout>
    @section('load-vite-pdca-script', true) {{--PDCAに関するTSを使用--}}
    @php
        $activeTab = session('activeTab', 'weekly');
    @endphp

    @if($weeklyGoal && $monthlyGoal)
        <div class="mx-auto px-8 py-4">
            @if($weeklyGoal->due < now()->format('Y-m-d') && $monthlyGoal->due < now()->format('Y-m-d'))
                <div id="notice" class="text-red-500">
                    週間目標、月間目標の振り返りをしましょう
                </div>
            @elseif($weeklyGoal->due < now()->format('Y-m-d'))
                <div id="notice" class="text-red-500">
                    週間目標の振り返りをしましょう
                </div>
            @elseif($monthlyGoal->due < now()->format('Y-m-d'))
                <div id="notice" class="text-red-500">
                    月間目標の振り返りをしましょう
                </div>
            @endif
            <div class="">
                <ul class="flex border-b">
                    <li class="-mb-px mr-1">
                        <a href="#" class="inline-block py-2 px-4 {{ $activeTab === 'weekly' ? 'active' : 'text-gray-500 hover:opacity-60' }}" id="weekly_tab">Weekly Goal / 週間目標</a>
                    </li>
                    <li class="mr-1">
                        <a href="#" class="inline-block py-2 px-4 {{ $activeTab === 'monthly' ? 'active' : 'text-gray-500 hover:opacity-60' }}" id="monthly_tab">Monthly Goal / 月間目標</a>
                    </li>
                </ul>
            </div>
            <div class="{{ $activeTab === 'weekly' ? '' : 'hidden' }}" id="weekly_container">
                <div class="bg-white shadow-md rounded p-6 mb-2">
                    <h2 class="text-xl font-semibold mb-2">週間目標 (Do): <span class="underline">{{ $weeklyGoal['title'] }}</span></h2>
                    <form action="{{ ($weeklyGoal->weeklyCheck && $weeklyGoal->weeklyAction) ? route('pdca.update-check-action', [$weeklyGoal->weeklyCheck->id, $weeklyGoal->weeklyAction->id]) : route('pdca.create-check-action') }}" method="POST">
                        @csrf
                        @if ($weeklyGoal->weeklyCheck && $weeklyGoal->weeklyAction)
                            @method('PATCH')
                        @endif
                        <input type="hidden" name="weeklyGoal_id" value="{{ $weeklyGoal['id'] }}">
                        <div class="mb-2">
                            <label class="block text-gray-700 text-sm font-bold mb-1 underline" for="comment1">振り返り / Check</label>
                            <div class="flex mb-1">
                                <input type="hidden" id="weeklyRating" name="weekly_check_rating" value="{{ old('weekly_check_rating', $weeklyGoal->weeklyCheck->review ?? '') }}">
                                @for($i = 0; $i < 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 weekly-star cursor-pointer" data-index="{{ $i }}">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                </svg>
                                @endfor
                                @error('weekly_check_rating')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                            @error('weekly_check_description')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            <textarea id="comment1" class="placeholder:text-sm placeholder:text-gray-300 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="2" name="weekly_check_description" placeholder="振り返りコメントを入力">{{ old('weekly_check_description', $weeklyGoal->weeklyCheck->description ?? '') }}</textarea>
                        </div>
                        <div class="mb-2">
                            <label class="block text-gray-700 text-sm font-bold mb-1 underline" for="action1">改善点 / Act</label>
                            @error('weekly_action_description')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            <textarea id="action1" class="placeholder:text-sm placeholder:text-gray-300 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="weekly_action_description" rows="2" placeholder="改善点を入力">{{ old('weekly_action_description', $weeklyGoal->weeklyAction->description ?? '') }}</textarea>
                        </div>

                        <button class="bg-[#8b8a8e] text-white text-sm px-6 py-2 mt-2 rounded flex ml-auto hover:bg-opacity-80 select-none" id="pdca_save_btn">保存</button>
                    </form>
                </div>
                <div class="bg-white shadow-md rounded p-6 mb-6 {{ (!$weeklyGoal->weeklyCheck || !$weeklyGoal->weeklyAction) ? 'opacity-40' : '' }}">
                    <div class="mb-1">
                        @if(!$weeklyGoal->weeklyCheck || !$weeklyGoal->weeklyAction)
                            <p class="opacity-100 z-10 text-right underline select-none">目標の振り返りをしてください</p>
                        @endif
                        <label class="block text-gray-700 text-xl font-semibold mb-1" for="next">週間目標 / Plan</label>
                        <form action="{{ route('pdca.create-goal') }}" method="POST">
                            @csrf
                            @error('weekly_goal')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            <div class="flex">
                                <input type="text" id="next" class="placeholder:text-sm placeholder:text-gray-300 shadow appearance-none border rounded w-4/5 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="3" placeholder="週間目標を入力" name="weekly_goal">
                                @if(!$weeklyGoal->weeklyCheck || !$weeklyGoal->weeklyAction)
                                    <p class="bg-[#8b8a8e] text-white text-sm px-6 py-2 mt-2 rounded flex ml-auto select-none">作成</p>
                                @else
                                    <button class="bg-[#8b8a8e] text-white text-sm px-6 py-2 mt-2 rounded flex ml-auto hover:bg-opacity-80 select-none" id="pdca_save_btn" type="submit">作成</button>
                                @endif
                            </div>
                        </form>
                    </div>
                    <p class="text-gray-500 text-xs mt-1">※期日は自動的に「今週末」または「今月末」に設定されます</p>
                </div>
            </div>
            {{---月間目標タブ--}}
            <div class="{{ $activeTab === 'monthly' ? '' : 'hidden' }}" id="monthly_container">
                <div class="bg-white shadow-md rounded p-6 mb-2">
                    <h2 class="text-xl font-semibold mb-2">月間目標 (Do): <span class="underline">{{ $monthlyGoal['title'] }}</span></h2>
                    <form action="{{ ($monthlyGoal->monthlyCheck && $monthlyGoal->monthlyAction) ? route('pdca.update-check-action', [$monthlyGoal->monthlyCheck->id, $monthlyGoal->monthlyAction->id]) : route('pdca.create-check-action') }}" method="POST">
                        @csrf
                        @if ($monthlyGoal->monthlyCheck && $monthlyGoal->monthlyAction)
                            @method('PATCH')
                        @endif
                        <input type="hidden" name="monthlyGoal_id" value="{{ $monthlyGoal['id'] }}">
                        <div class="mb-2">
                            <label class="block text-gray-700 text-sm font-bold mb-1 underline" for="comment1">振り返り / Check</label>
                            <div class="flex mb-1">
                                <input type="hidden" id="monthlyRating" name="monthly_check_rating" value="{{ old('monthly_check_rating', $monthlyGoal->monthlyCheck->review ?? '') }}">
                                @for($i = 0; $i < 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 monthly-star cursor-pointer" data-index="{{ $i }}">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                </svg>
                                @endfor
                                @error('monthly_check_rating')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                            @error('monthly_check_description')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            <textarea id="comment1" class="placeholder:text-sm placeholder:text-gray-300 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="2" name="monthly_check_description" placeholder="振り返りコメントを入力">{{ old('monthly_check_description', $monthlyGoal->monthlyCheck->description ?? '') }}</textarea>
                        </div>
                        <div class="mb-2">
                            <label class="block text-gray-700 text-sm font-bold mb-1 underline" for="action1">改善点 / Act</label>
                            @error('monthly_action_description')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            <textarea id="action1" class="placeholder:text-sm placeholder:text-gray-300 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="monthly_action_description" rows="2" placeholder="改善点を入力">{{ old('monthly_action_description', $monthlyGoal->monthlyAction->description ?? '') }}</textarea>
                        </div>

                        <button class="bg-[#8b8a8e] text-white text-sm px-6 py-2 mt-2 rounded flex ml-auto hover:bg-opacity-80 select-none" id="pdca_save_btn">保存</button>
                    </form>
                </div>
                <div class="bg-white shadow-md rounded p-6 mb-6 {{ (!$monthlyGoal->monthlyCheck || !$monthlyGoal->monthlyAction) ? 'opacity-40' : '' }}">
                    <div class="mb-1">
                        @if(!$monthlyGoal->monthlyCheck || !$monthlyGoal->monthlyAction)
                            <p class="opacity-100 z-10 text-right underline select-none">目標の振り返りをしてください</p>
                        @endif
                        <label class="block text-gray-700 text-xl font-semibold mb-1" for="next">月間目標 / Plan</label>
                        <form action="{{ route('pdca.create-goal') }}" method="POST">
                            @csrf
                            @error('monthly_goal')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            <div class="flex">
                                <input type="text" id="next" class="placeholder:text-sm placeholder:text-gray-300 shadow appearance-none border rounded w-4/5 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="3" placeholder="週間目標を入力" name="monthly_goal">
                                @if(!$monthlyGoal->monthlyCheck || !$monthlyGoal->monthlyAction)
                                    <p class="bg-[#8b8a8e] text-white text-sm px-6 py-2 mt-2 rounded flex ml-auto select-none">作成</p>
                                @else
                                    <button class="bg-[#8b8a8e] text-white text-sm px-6 py-2 mt-2 rounded flex ml-auto hover:bg-opacity-80 select-none" id="pdca_save_btn" type="submit">作成</button>
                                @endif
                            </div>
                        </form>
                    </div>
                    <p class="text-gray-500 text-xs mt-1">※期日は自動的に「今週末」または「今月末」に設定されます</p>
                </div>
            </div>
        </div>
    @else
        <form class="mx-auto rounded shadow-md bg-white px-6 py-3 w-4/5 justify-center" action="{{ route('pdca.create-first-goal') }}" method="POST">
            @csrf
            <h1 class="text-xl underline font-semibold text-center mb-3 pt-2">目標を設定しましょう</h1>
            @if ($errors->any())
                <div class="text-red-500">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="mb-5">
                <h2 class="text-xl font-bold mb-1">Weekly Goal / 週間目標</h2>
                <input type="text" name="weekly-goal" class="placeholder:text-sm placeholder:text-gray-300 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="2" placeholder="週間目標を入力" value="{{ old('weekly-goal') }}">
            </div>
            <div class="mb-4">
                <h2 class="text-xl font-bold mb-1">Monthly Goal / 月間目標</h2>
                <input type="text" name="monthly-goal" class="placeholder:text-sm placeholder:text-gray-300 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-1" rows="2" placeholder="月間目標を入力" value="{{ old('monthly-goal') }}">
                <p class="text-gray-500 text-xs mt-1">※期日は自動的に「今週末」または「今月末」に設定されます</p>
            </div>
                <button type="submit" class="bg-[#8b8a8e] text-white text-sm px-6 py-2 rounded flex ml-auto hover:bg-opacity-80 select-none" id="pdca_save_btn">作成</button>
        </form>
    @endif
</x-app-layout>
