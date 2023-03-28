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
    </style>
</head>

<body>

    <h2>Hello {{ $orderItems->prodOrder->first_name }}</h2>
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

    <a href="{{ route('admin.approve', $orderItems->order_id) }}" class="btn btn-success">Approve</a>
    <a href="{{ route('admin.decline', $orderItems->order_id) }}" class="btn btn-danger">Decline</a>

</body>

</html>
