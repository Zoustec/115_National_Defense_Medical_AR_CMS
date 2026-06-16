@extends('adminlte::page')

@section('title', __('cms.list_title', ['resource' => __('cms.recommend_items')]))

@section('content_header')
    <div class="d-flex align-items-center mb-2">
        <a href="javascript:history.back()" class="btn btn-link p-0 shadow-none text-secondary mr-2"
            title="{{ __('common.back') }}" style="transition:color 0.2s;">
            <i class="fas fa-arrow-left fa-lg"></i>
        </a>
        <h1 class="mb-0">{{ __('cms.list_title', ['resource' => __('cms.recommend_items')]) }}</h1>
    </div>
@stop

@section('content')
    @include('admin._flash')

    <div class="row">
        <div class="col-12">
            <div class="card-header bg-light">
                <div class="card-tools">
                    <a href="{{ route('admin.recommend-items.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> {{ __('common.add') }}
                    </a>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.recommend-items.index') }}" id="searchForm">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="keyword">{{ __('common.search_label') }}</label>
                                <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}"
                                    class="form-control" placeholder="{{ __('common.search_placeholder') }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="category_id">{{ __('cms.category_id') }}</label>
                                <select name="category_id" id="category_id" class="form-control">
                                    <option value="">{{ __('common.all') }}</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ (string) request('category_id') === (string) $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="status">{{ __('cms.status') }}</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">{{ __('common.all') }}</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('common.active') }}</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('common.inactive') }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3 d-flex align-items-end">
                                <fieldset class="btn-group">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-search"></i> {{ __('common.search') }}
                                    </button>
                                    <a href="{{ route('admin.recommend-items.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> {{ __('common.reset') }}
                                    </a>
                                </fieldset>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>{{ __('cms.category_id') }}</th>
                                <th>{{ __('cms.name') }}</th>
                                <th>{{ __('cms.status') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recommendItems as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->category?->name ?? '-' }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->status ? 'success' : 'secondary' }}">
                                            {{ $item->status ? __('common.active') : __('common.inactive') }}
                                        </span>
                                    </td>
                                    <td>
                                        @include('admin._row_actions', [
                                            'editUrl' => route('admin.recommend-items.edit', $item),
                                            'deleteUrl' => route('admin.recommend-items.destroy', $item),
                                        ])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-list fa-3x mb-3"></i>
                                        <p class="mb-0">{{ __('common.no_results_found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @include('admin._paginator', ['paginator' => $recommendItems, 'route' => 'admin.recommend-items.index'])
            </div>
        </div>
    </div>
@stop

@include('admin._delete_script')
