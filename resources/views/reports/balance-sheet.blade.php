@extends('adminlte::page')

@section('title', __('general_content.balance_sheet_trans_key'))

@section('content_header')
    <h1>{{ __('general_content.balance_sheet_trans_key') }}</h1>
@stop

@section('content')
<div class="mb-3">
    <a href="{{ route('reports.accounting.balance-sheet.pdf') }}" class="btn btn-primary">
        {{ __('general_content.pdf_trans_key') }}
    </a>
</div>
<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('general_content.account_number_trans_key') }}</th>
                    <th>{{ __('general_content.description_trans_key') }}</th>
                    <th>{{ __('general_content.debit_trans_key') }}</th>
                    <th>{{ __('general_content.credit_trans_key') }}</th>
                    <th>{{ __('general_content.balance_trans_key') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($balances as $entry)
                    <tr>
                        <td>{{ $entry->account_number }}</td>
                        <td>{{ $entry->account_label }}</td>
                        <td>{{ $entry->total_debit }}</td>
                        <td>{{ $entry->total_credit }}</td>
                        <td>{{ $entry->balance }}</td>
                    </tr>
                @empty
                    <x-EmptyDataLine col="5" text="{{ __('general_content.no_data_trans_key') }}" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
