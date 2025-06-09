<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('general_content.reports_trans_key') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 6px; border: 1px solid #ccc; text-align: left; }
        h1, h2 { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>{{ __('general_content.reports_trans_key') }}</h1>

    <h2>{{ __('general_content.orders_trans_key') }}</h2>
    <table>
        <tr>
            <th>{{ __('general_content.order_delivered_trans_key') }}</th>
            <td>{{ $deliveredOrdersPercentage }}%</td>
        </tr>
        <tr>
            <th>{{ __('general_content.order_invoiced_trans_key') }}</th>
            <td>{{ $invoicedOrdersPercentage }}%</td>
        </tr>
        <tr>
            <th>{{ __('general_content.service_rate_trans_key') }}</th>
            <td>{{ $serviceRate }}%</td>
        </tr>
    </table>

    <h2>{{ __('general_content.quote_trans_key') }}</h2>
    <table>
        <tr>
            <th>{{ __('general_content.average_quote_amount') }}</th>
            <td>{{ number_format($averageQuoteAmount, 2) }}</td>
        </tr>
        <tr>
            <th>{{ __('general_content.quote_conversion_rate') }}</th>
            <td>{{ $conversionRate }}%</td>
        </tr>
        <tr>
            <th>{{ __('general_content.quote_response_rate') }}</th>
            <td>{{ $responseRate }}%</td>
        </tr>
    </table>

    <h2>{{ __('general_content.top_customers_trans_key') }} - {{ __('general_content.orders_trans_key') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('general_content.customer_trans_key') }}</th>
                <th>{{ __('general_content.orders_trans_key') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topOrderCustomers as $customer)
                <tr>
                    <td>{{ $customer->companie->label ?? 'Internal' }}</td>
                    <td>{{ $customer->order_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>{{ __('general_content.top_customers_trans_key') }} - {{ __('general_content.quote_trans_key') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('general_content.customer_trans_key') }}</th>
                <th>{{ __('general_content.quote_trans_key') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topQuoteCustomers as $customer)
                <tr>
                    <td>{{ $customer->companie->label ?? 'Internal' }}</td>
                    <td>{{ $customer->quote_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
