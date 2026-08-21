@extends('errors.layout')

@section('title', 'Session Expired')
@section('code', '419')
@section('heading', 'Session Expired')
@section('message', 'Your session has expired for your security. Please log in again to continue.')
@section('action')
    <a href="{{ route('login') }}" class="btn">Go to Login</a>
@endsection
