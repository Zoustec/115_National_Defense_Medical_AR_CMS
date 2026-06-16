@extends('adminlte::page')

@section('title', __('cms.list_title', ['resource' => __('cms.learning_units')]))

@section('content_header')
    <div class="d-flex align-items-center mb-2">
        <a href="javascript:history.back()" class="btn btn-link p-0 shadow-none text-secondary mr-2"
            title="{{ __('common.back') }}" style="transition:color 0.2s;">
            <i class="fas fa-arrow-left fa-lg"></i>
        </a>
        <h1 class="mb-0">{{ __('cms.list_title', ['resource' => __('cms.learning_units')]) }}</h1>
    </div>
@stop

@section('content')
    @include('admin._flash')

    <div class="row">
        <div class="col-12">
            <div class="card-header bg-light">
                <div class="card-tools">
                    <a href="{{ route('admin.learning-units.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> {{ __('common.add') }}
                    </a>
                    <a href="{{ route('admin.learning-units.export') }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-export"></i> {{ __('exports.export_csv') }}
                    </a>
                    <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#importUnitModal">
                        <i class="fas fa-file-import"></i> {{ __('imports.import_button') }}
                    </button>
                </div>
            </div>

            {{-- Import Learning Unit Modal --}}
            <div class="modal fade" id="importUnitModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form method="POST" action="{{ route('admin.learning-units.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('imports.learning_unit_section') }}</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted">{{ __('imports.learning_unit_description') }}</p>
                                <a href="{{ route('admin.learning-units.import-template') }}" class="btn btn-sm btn-outline-secondary mb-3">
                                    <i class="fas fa-download"></i> {{ __('imports.download_template') }}
                                </a>
                                <div class="form-group">
                                    <label>{{ __('imports.file_label') }}</label>
                                    <div class="custom-file">
                                        <input type="file" name="file" id="importFileInput"
                                            accept=".csv,text/csv" class="custom-file-input" required>
                                        <label class="custom-file-label" for="importFileInput"
                                            data-browse="{{ __('imports.choose_file') }}"
                                            data-default="{{ __('imports.no_file') }}">{{ __('imports.no_file') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-file-import"></i> {{ __('imports.submit') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.learning-units.index') }}" id="searchForm">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="keyword">{{ __('common.search_label') }}</label>
                                <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}"
                                    class="form-control" placeholder="{{ __('common.search_placeholder') }}">
                            </div>
                            <div class="form-group col-md-4 d-flex align-items-end">
                                <fieldset class="btn-group">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-search"></i> {{ __('common.search') }}
                                    </button>
                                    <a href="{{ route('admin.learning-units.index') }}" class="btn btn-secondary">
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
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-fixed mb-0">
                            <colgroup>
                                <col style="width:64px;">
                                <col style="width:72px;">
                                <col style="width:18%;">
                                <col style="width:16%;">
                                <col style="width:18%;">
                                <col style="width:70px;">
                                <col style="width:80px;">
                                <col style="width:140px;">
                                <col style="width:120px;">
                            </colgroup>
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>{{ __('cms.image_short') }}</th>
                                    <th>{{ __('cms.name') }}</th>
                                    <th>{{ __('cms.applicable_objects') }}</th>
                                    <th>{{ __('cms.dietary_recommendations') }}</th>
                                    <th>{{ __('cms.is_locked') }}</th>
                                    <th>{{ __('cms.status') }}</th>
                                    <th>{{ __('common.last_update') }}</th>
                                    <th>{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($learningUnits as $unit)
                                    <tr>
                                        <td>{{ $unit->id }}</td>
                                        <td>
                                            <img src="{{ $unit->image_url ?: asset('img/no-image.svg') }}" alt=""
                                                loading="lazy"
                                                onerror="this.onerror=null;this.src='{{ asset('img/no-image.svg') }}';"
                                                style="width:48px;height:48px;object-fit:contain;background:#fff;border:1px solid #dee2e6;border-radius:4px;padding:2px;">
                                        </td>
                                        <td class="text-truncate" title="{{ $unit->name }}">{{ $unit->name }}</td>
                                        <td class="cell-tags">
                                            @foreach ($unit->applicable_objects ?? [] as $tag)
                                                <span class="badge badge-info">{{ $tag }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-truncate">
                                            @if (filled($unit->dietary_recommendations))
                                                <span class="text-muted" title="{{ strip_tags($unit->dietary_recommendations) }}">{{ \Illuminate\Support\Str::limit(strip_tags($unit->dietary_recommendations), 60) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>@include('admin.learning-units._lock', ['unit' => $unit])</td>
                                        <td>
                                            <span class="badge badge-{{ $unit->status ? 'success' : 'secondary' }}">
                                                {{ $unit->status ? __('common.active') : __('common.inactive') }}
                                            </span>
                                        </td>
                                        <td>{{ optional($unit->updated_at)->format('Y-m-d H:i:s') ?? '-' }}</td>
                                        <td>@include('admin.learning-units._actions', ['unit' => $unit])</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-list fa-3x mb-3"></i>
                                            <p class="mb-0">{{ __('common.no_results_found') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @include('admin._paginator', ['paginator' => $learningUnits, 'route' => 'admin.learning-units.index'])
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Fixed table layout: columns keep their declared widths regardless of content length */
        .table-fixed {
            table-layout: fixed;
            width: 100%;
        }

        /* Long text is clipped with an ellipsis instead of stretching the column */
        .table-fixed td.text-truncate,
        .table-fixed th {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 0;
        }

        /* Tag/badge cells wrap their badges instead of forcing the column wider */
        .table-fixed td.cell-tags {
            white-space: normal;
            word-break: break-word;
        }

        .table-fixed td.cell-tags .badge {
            display: inline-block;
            margin-bottom: 2px;
            white-space: normal;
        }
    </style>
@stop

@section('js')
    <script>
        $(function () {
            // Show the chosen file name in the custom file input, fall back to 未選擇檔案.
            $('#importFileInput').on('change', function () {
                var $label = $(this).next('.custom-file-label');
                $label.text(this.files.length ? this.files[0].name : $label.data('default'));
            });
        });
    </script>
@stop

@include('admin._delete_script')
