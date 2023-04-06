{{-- Hello! {{ $order->userOrder->first_name }}


<h1> your order has been rejected by {{ $user->getFullNameAttribute() }}
</h1>
<h1>Product Details</h1> --}}
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
    Hello! {{ $order->userOrder->first_name }}


    <h1> your order has been rejected by {{ $user->getFullNameAttribute() }}
    </h1>
    <h2>Order Details</h2>

    <table>
        <thead>
            <tr>
                <th>Order No.</th>
                <th>Name</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($item as $product)
                <tr>
                    <td>{{ $item->first()->orders->order_num }}</td>
                    <td>{{ $item->first()->prodOrder->name }}</td>
                    <td>{{ $item->first()->price }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</body>

</html>
