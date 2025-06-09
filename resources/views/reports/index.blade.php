@extends('adminlte::page')

@section('title', __('general_content.reports_trans_key'))

@section('content_header')
    <h1>{{ __('general_content.reports_trans_key') }}</h1>
@stop

@section('content')
    <p class="text-muted">{{ __('general_content.coming_soon_trans_key') ?? 'Coming soon' }}</p>
@stop
