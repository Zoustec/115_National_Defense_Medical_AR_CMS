{{-- $paginator: LengthAwarePaginator, $route: route name for per-page form action --}}
<div class="card-footer bg-white border-top">
    <div class="d-flex justify-content-end align-items-center">
        <div class="d-flex align-items-center">
            <form method="GET" action="{{ route($route) }}" class="form-inline">
                @foreach (request()->except('per_page', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label for="per_page" class="mr-2 mb-0">{{ __('common.per_page') }}:</label>
                <select name="per_page" class="form-control form-control-sm" onchange="this.form.submit()">
                    @foreach (\App\Constants\Pagination::OPTIONS as $option)
                        <option value="{{ $option }}" {{ request('per_page', \App\Constants\Pagination::PER_PAGE) == $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </form>
            @if ($paginator->total() > 0)
                <div class="text-muted small ml-3 mr-3">
                    {{ __('common.showing_results', [
                        'first' => $paginator->firstItem() ?? 0,
                        'last' => $paginator->lastItem() ?? 0,
                        'total' => $paginator->total(),
                    ]) }}
                </div>
            @endif
            @if ($paginator->hasPages())
                <div>
                    {{ $paginator->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
