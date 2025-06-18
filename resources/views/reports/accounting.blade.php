@extends('adminlte::page')

@section('title', __('general_content.accounting_reports_trans_key'))

@section('content_header')
    <h1>{{ __('general_content.accounting_reports_trans_key') }}</h1>
@stop

@section('content')
    <iframe src="http://ReportServer:Report123@msi/Reports/browse/Accounting%20Reports" style="width: 100%; height: 80vh; border: none;"></iframe>
@stop
