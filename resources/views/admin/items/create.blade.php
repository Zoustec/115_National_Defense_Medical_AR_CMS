@extends('adminlte::page')

@section('title', __('cms.create_title', ['resource' => __('cms.item')]))

@section('content_header')
    <div class="d-flex align-items-center mb-2">
        <a href="{{ route('admin.items.index') }}" class="btn btn-link p-0 shadow-none text-secondary mr-2"
            title="{{ __('common.back') }}" style="transition:color 0.2s;">
            <i class="fas fa-arrow-left fa-lg"></i>
        </a>
        <h1 class="mb-0">{{ __('cms.create_title', ['resource' => __('cms.item')]) }}</h1>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.items.store') }}" enctype="multipart/form-data">
                @csrf
                @include('admin.items._form')
                <button class="btn btn-success"><i class="fas fa-save"></i> {{ __('common.save') }}</button>
                <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
            </form>
        </div>
    </div>
@stop
