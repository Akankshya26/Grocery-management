<!DOCTYPE html>
<html>

<head>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 60%;
        }

        td,
        th {
            border: 1px solid #111212;
            background-color: #f2f5f6;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }

        .button1 {
            background-color: #4CAF50;
        }

        .button2 {
            background-color: #d8000b;
        }
    </style>
</head>

<body>

    <h2>Hello {{ $user->getFullNameAttribute() }}</h2>
    <h2>This Customer Is wants to buy your added product</h2>

    <table>
        <tr>
            <th>Customer Name</th>
            <th>Product name</th>
            <th>Price</th>
            <th>discount</th>

        </tr>
        <tr>
            <td>{{ $orderItems->orders->userOrder->first_name }}</td>
            <td>{{ $orderItems->prodOrder->name }}</td>
            <td>{{ $orderItems->prodOrder->price }}</td>
            <td>{{ $orderItems->prodOrder->discount }}</td>
        </tr>

    </table>
    <br>
    <a href="{{ route('admin.approve', $orderItems->order_id) }}"><button class="button button1">Approve</button></a>
    <a href="{{ route('admin.decline', $orderItems->order_id) }}"><button class="button button2">Decline</button></a>

</body>

</html>
