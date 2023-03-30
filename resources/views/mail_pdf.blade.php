<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Invoice #6</title>

    <style>
        html,
        body {
            margin: 10px;
            padding: 10px;
            font-family: sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        span,
        label {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0px !important;
        }

        table thead th {
            height: 28px;
            text-align: left;
            font-size: 16px;
            font-family: sans-serif;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 14px;
        }

        .heading {
            font-size: 24px;
            margin-top: 12px;
            margin-bottom: 12px;
            font-family: sans-serif;
        }

        .small-heading {
            font-size: 18px;
            font-family: sans-serif;
        }

        .total-heading {
            font-size: 18px;
            font-weight: 700;
            font-family: sans-serif;
        }

        .order-details tbody tr td:nth-child(1) {
            width: 20%;
        }

        .order-details tbody tr td:nth-child(3) {
            width: 20%;
        }

        .text-start {
            text-align: left;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .company-data span {
            margin-bottom: 4px;
            display: inline-block;
            font-family: sans-serif;
            font-size: 14px;
            font-weight: 400;
        }

        .no-border {
            border: 1px solid #fff !important;
        }

        .bg-blue {
            background-color: #414ab1;
            color: #fff;
        }
    </style>
</head>

<body>

    <table class="order-details">
        <thead>
            <tr>
                <th width="50%" colspan="2">
                    <h2 class="text-start">Grocery Management</h2>
                </th>
                <th width="50%" colspan="2" class="text-end company-data">
                    <span>Invoice Id:{{ $order->orderInvoice->invoice_num }}</span> <br>
                    <span>{{ now()->format('Y-m-d') }}</span> <br>
                    {{-- {{ dd($order->userOrder) }} --}}
                    <span>Zip code :{{ $order->userOrder->UserAddress->first()->zip_code }}</span> <br>
                    <span>Address:{{ $order->userOrder->UserAddress->first()->address1 }}</span> <br>
                </th>
            </tr>
            <tr class="bg-blue">
                <th width="50%" colspan="2">Order Details</th>
                <th width="50%" colspan="2">User Details</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Order Id:</td>
                <td>{{ $order->id }}</td>

                <td>Full Name:</td>
                <td> {{ $order->userOrder->getFullNameAttribute() }}</td>
            </tr>
            <tr>
                <td>Tracking Id/No.:</td>
                <td>{{ $order->order_num }} </td>

                <td>Email Id:</td>
                <td>{{ $order->userOrder->email }}</td>
            </tr>
            <tr>
                <td>Ordered Date:</td>
                <td>{{ $order->created_at }}</td>

                <td>Phone:</td>
                <td>{{ $order->userOrder->phone }}</td>
            </tr>
            <tr>
                <td>Payment status:</td>
                <td>{{ $order->orderInvoice->payment_status }}</td>

                <td>Address:</td>
                <td>{{ $order->userOrder->UserAddress->first()->address1 }},
                    {{ $order->userOrder->UserAddress->first()->address2 }}</td>
            </tr>
            <tr>
                <td>Order Status:</td>
                <td>{{ $order->status }}</td>

                <td>Pin code:</td>
                <td>{{ $order->userOrder->UserAddress->first()->zip_code }}</td>
            </tr>
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th class="no-border text-start heading" colspan="5">
                    Order Items
                </th>
            </tr>
            <tr class="bg-blue">
                <th>ID</th>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Discount</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($item as $product)
                <tr>
                    <td width="10%">{{ $product->prodOrder->id }}</td>
                    <td>
                        {{ $product->prodOrder->name }}
                    </td>
                    <td width="10%">{{ $product->prodOrder->price }}</td>
                    <td width="10%">{{ $product->quantity }}</td>
                    <td width="10%">{{ $product->discount }}</td>
                    <td width="15%" class="fw-bold">{{ $product->prodOrder->price * $product->quantity }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" class="total-heading">Total Amount - <small>Inc. all vat/tax</small> :</td>
                <td colspan="1" class="total-heading">{{ $order->orderInvoice->total_amount }}
                </td>
            </tr>
        </tbody>
    </table>


    <br>
    <p class="text-center">
        Thank your for shopping
    </p>

</body>

</html>
