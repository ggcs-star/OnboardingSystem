@extends('errors.layout')

@section('title', 'Too Many Requests')
@section('code', '429')
@section('heading', 'Too Many Requests')
@section('message', "You've made too many requests in a short time. Please wait a moment and try again.")
@section('action')
    <a href="{{ url('/') }}" class="btn">Go to Homepage</a>
@endsection
