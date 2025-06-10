<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('general_content.balance_sheet_trans_key') }}</title>
    <style type="text/css">
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
<h1>{{ __('general_content.balance_sheet_trans_key') }}</h1>
<table>
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
    @foreach($balances as $entry)
        <tr>
            <td>{{ $entry->account_number }}</td>
            <td>{{ $entry->account_label }}</td>
            <td>{{ $entry->total_debit }}</td>
            <td>{{ $entry->total_credit }}</td>
            <td>{{ $entry->balance }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
