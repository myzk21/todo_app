@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        {{-- 下記は小さい画面の場合に表示される前へ次へボタン --}}
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-small text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md dark:text-gray-600 dark:bg-gray-800 dark:border-gray-600">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:focus:border-blue-700 dark:active:bg-gray-700 dark:active:text-gray-300">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:focus:border-blue-700 dark:active:bg-gray-700 dark:active:text-gray-300">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md dark:text-gray-600 dark:bg-gray-800 dark:border-gray-600">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-center">
            {{-- <div>
                <p class="text-sm text-gray-700 leading-5 dark:text-gray-400 ml-6">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('of') !!}
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div> --}}

            <div>
                <span class="relative z-0 inline-flex rtl:flex-row-reverse rounded-md sm:gap-x-2">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center my-1 px-1 py-1 text-sm font-medium text-gray-500 bg-white cursor-default rounded leading-5" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center my-1 px-1 py-1 text-sm font-medium text-gray-500 bg-white rounded leading-5 hover:bg-gray-200 focus:z-10 focus:outline-none focus:ring ring-gray-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $maxNum = $paginator->currentPage();
                    @endphp
                    @if($maxNum % 10 != 0){{--１０で割り切れない場合、割り切れるまで１を足し続ける--}}
                        @while ($maxNum % 10 != 0)
                            @php
                                $maxNum++
                            @endphp
                        @endwhile
                    @endif
                    @if($maxNum != 10){{--１ページ目に戻るボタン--}}
                        <a href="{{ url('/') }}" class="relative inline-flex items-center rounded my-1 px-2 py-1 -ml-px font-medium text-gray-800 bg-white leading-5 hover:bg-gray-200 focus:z-10 focus:outline-none focus:ring ring-gray-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">...</a>
                    @endif

                    @php
                        $numberOfElement = 0;//「・・・」などの区切り文字がelements配列の中に入ってくる。本当のページの数を数えるための変数
                    @endphp
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        {{-- @if (is_string($element) && $element == $firstElement)
                            <span aria-disabled="true">
                                <span class="relative inline-flex rounded items-center my-1 px-2 py-1 -ml-px text-sm font-medium cursor-default leading-5 text-gray-800 bg-white hover:bg-gray-200 hover:cursor-pointer focus:z-10 focus:outline-none focus:ring ring-gray-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">{{ $element }}</span>
                            </span>
                        @else
                        @endif --}}

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @php
                                $numberOfElement += count($element);
                            @endphp

                            @foreach ($element as $page => $url)
                                @if($page >= $maxNum - 9 && $page <= $maxNum)
                                    @if ($page == $paginator->currentPage())
                                        <span aria-current="page">
                                            <span class="relative inline-flex items-center rounded my-1 px-2 py-1 -ml-px text-sm font-medium cursor-default leading-5 bg-green-700 text-white">{{ $page }}</span>
                                        </span>
                                    @else
                                        <a href="{{ $url }}" class="relative inline-flex items-center rounded my-1 px-2 py-1 -ml-px text-sm font-medium text-gray-700 bg-white leading-5 hover:bg-gray-200 focus:z-10 focus:outline-none focus:ring ring-gray-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    @if($maxNum < $numberOfElement)
                        <a href="{{ url('/') }}?page={{ $maxNum + 1 }}" class="relative inline-flex items-center rounded my-1 px-2 py-1 -ml-px font-medium text-gray-800 bg-white leading-5 hover:bg-gray-200 focus:z-10 focus:outline-none focus:ring ring-gray-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">...</a>
                    @endif
                    {{--ここでclickで次の１０個を表示するのは難しいー＞JSで管理する必要がある--}}
                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center my-1 px-1 py-1 -ml-px text-sm font-medium text-gray-500 bg-white rounded leading-5 hover:bg-gray-200 focus:z-10 focus:outline-none focus:ring ring-gray-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center my-1 px-1 py-1 -ml-px text-sm font-medium text-gray-500 bg-white cursor-default rounded leading-5" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
