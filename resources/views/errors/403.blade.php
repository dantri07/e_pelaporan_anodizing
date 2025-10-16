@extends('layouts.app')
@section('title', '403 Forbidden')
@section('content')
<div class="container text-center mt-5">
    <h1 class="display-1">403</h1>
    <h3>Forbidden</h3>
    <p>You are not authorized to access this page.</p>
    <a href="{{ url('/dashboard') }}" class="btn btn-primary mt-3">Back to Home</a>
</div>
@endsection 