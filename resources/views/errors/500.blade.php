@extends('layouts.app')
@section('title', '500 Internal Server Error')
@section('content')
<div class="container text-center mt-5">
    <h1 class="display-1">500</h1>
    <h3>Internal Server Error</h3>
    <p>Something went wrong on our server. Please try again later.</p>
    <a href="{{ url('/dashboard') }}" class="btn btn-primary mt-3">Back to Home</a>
</div>
@endsection 