@if ($paginator->hasPages())
    <div class="pages--nav d-flex mt-5">
        <span class="col-3">{{ __('main.shown') }}: {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} {{ __('main.from') }} {{ $paginator->total() }}</span>
        <div class="pages--nav--list offset-2 d-flex align-items-center">
            <a href="{{ $paginator->previousPageUrl() }}">
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 7L0.46967 7.53033C0.176777 7.23744 0.176777 6.76256 0.46967 6.46967L1 7ZM6.46967 13.5303L0.46967 7.53033L1.53033 6.46967L7.53033 12.4697L6.46967 13.5303ZM0.46967 6.46967L6.46967 0.46967L7.53033 1.53033L1.53033 7.53033L0.46967 6.46967Z"
                          fill="#4979D2"/>
                </svg>
            </a>
            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <a href="{{ $url }}" class="active">{{ $page }}</a>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
            <a href="{{ $paginator->nextPageUrl() }}">
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 7L7.53033 7.53033C7.82322 7.23744 7.82322 6.76256 7.53033 6.46967L7 7ZM1.53033 13.5303L7.53033 7.53033L6.46967 6.46967L0.46967 12.4697L1.53033 13.5303ZM7.53033 6.46967L1.53033 0.46967L0.46967 1.53033L6.46967 7.53033L7.53033 6.46967Z"
                          fill="#4979D2"/>
                </svg>
            </a>
        </div>
    </div>
@endif
