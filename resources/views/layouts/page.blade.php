@extends('layouts.app')

@section('title', $title ?? config('app.name', 'Laravel'))

@section('header', $header ?? 'Dashboard')

@push('styles')
    @stack('page_styles')
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            @yield('page_content')
        </div>
    </div>
@endsection

@push('scripts')
    @stack('page_scripts')
@endpush 