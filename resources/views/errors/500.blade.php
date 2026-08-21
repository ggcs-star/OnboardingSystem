@extends('errors.layout')

@section('title', 'Something Went Wrong')
@section('code', '500')
@section('heading', 'Something Went Wrong')
@section('message', "We've hit an unexpected problem on our end. Our team has been notified — please try again shortly.")
@section('action')
    <a href="{{ url('/') }}" class="btn">Go to Homepage</a>
@endsection
