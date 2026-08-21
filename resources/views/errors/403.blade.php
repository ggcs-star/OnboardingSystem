@extends('errors.layout')

@section('title', 'Access Denied')
@section('code', '403')
@section('heading', 'Access Denied')
@section('message', "You don't have permission to view this page.")
@section('action')
    <a href="{{ url('/') }}" class="btn">Go to Homepage</a>
@endsection
