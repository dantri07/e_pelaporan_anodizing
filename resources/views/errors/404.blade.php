@extends('layouts.app')
@section('title', '404 Not Found')
@section('content')
<div class="container text-center mt-5">
    <h1 class="display-1">404</h1>
    <h3>Page Not Found</h3>
    <p>The page you are looking for could not be found.</p>
    <a href="{{ url('/dashboard') }}" class="btn btn-primary mt-3">Back to Home</a>
</div>
@endsection 