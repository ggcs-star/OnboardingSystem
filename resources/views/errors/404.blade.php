@extends('errors.layout')

@section('title', 'Page Not Found')
@section('code', '404')
@section('heading', 'Page Not Found')
@section('message', "The page you're looking for doesn't exist or may have been moved.")
@section('action')
    <a href="{{ url('/') }}" class="btn">Go to Homepage</a>
@endsection
