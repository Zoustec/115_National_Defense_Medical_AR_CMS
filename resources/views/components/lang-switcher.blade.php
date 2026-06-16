<link rel="stylesheet" href="{{ asset('css/language/common.css') }}">
<div class="dropdown d-flex">
    <button class="border-0 p-2 bg-white rounded-left-right" type="button" id="dropdownMenuButton" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        @if (App::getLocale() == 'zh_TW')
            <img src="{{ asset('images/taiwan.svg') }}" alt="">
        @else
            <img src="{{ asset('images/en-uk.svg') }}" alt="">
        @endif
    </button>

    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <button class="dropdown-item" onclick="window.location.href='{{ route('lang.switch', 'en') }}'">
            <img src="{{ asset('images/en-uk.svg') }}" alt=""> {{ __('common.language.english') }}
        </button>
        <button class="dropdown-item" onclick="window.location.href='{{ route('lang.switch', 'zh_TW') }}'">
            <img src="{{ asset('images/taiwan.svg') }}" alt=""> {{ __('common.language.taiwan') }}
        </button>
    </div>
</div>
