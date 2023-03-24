{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>

    {{ $invoice->order_num }}
    {{ $invoice->invoice_num }}
    {{ $invoice->total_amount }}

</body>

</html> --}}


<!DOCTYPE html>
<html>

<head>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
</head>

<body>

    <h2>Hello {{ $invoice->invoiceUser->first_name }}</h2>

    <h2>{{ $invoice->invoiceUser->email }}</h2>

    <h2>Invoice Number::{{ $invoice->invoice_num }}</h2>


    <h2>Order Number::{{ $invoice->order_num }}</h2>

    <table>
        <tr>
            <th>Product name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>discount</th>


        </tr>
        @foreach ($productData as $data)
            <tr>
                <td>{{ $data->prodOrder->name }}</td>
                <td>{{ $data->price }}</td>
                <td>{{ $data->quantity }}</td>
                <td>{{ $data->prodOrder->discount }}</td>

            </tr>
        @endforeach

    </table>


    <h3>Total Amount::{{ $invoice->total_amount }}</h3>

</body>

</html>
