@extends('adminlte::page', ['iFrameEnabled' => true])

@section('title',"Iframe Mode")

@section('content_header')
<h1>Iframe Mode</h1>
@stop

@section('content')
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var loadingDiv = document.querySelector('.tab-loading > div');
            if (loadingDiv) {
                loadingDiv.innerHTML = `
                    <img src="{{ asset('images/factory/1708549820_simple-logo.jpg') }}" class="iframe-logo" alt="Logo">
                    <div class="iframe-progress"></div>
                `;
            }
        });
    </script>
@stop