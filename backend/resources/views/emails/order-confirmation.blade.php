<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>
</head>
<body>
    <h2>Order Details</h2>
    <div>
        <span>Order ID: </span>
        <span>#{{ $order->id }}</span>
    </div>
    <div>
        <span>Email: </span>
        <span>{{ $order->email }}</span>
    </div>
    <div>
        <span>Movie: </span>
        <span>{{ $order->schedule->movie->title }}</span>
    </div>
    <div>
        <span>Date: </span>
        <span >{{ \Carbon\Carbon::parse($order->schedule->date)->format('Y-m-d') }}</span>
    </div>
    <div>
        <span>Time: </span>
        <span>{{ $order->schedule->start_time }}</span>
    </div>
    <div>
        <span>Total Price: </span>
        <span>{{ number_format($order->total_price / 100, 2) }} EUr</span>
    </div>
    <div>
        <span>Number of Tickets: </span>
        <span>{{ $order->tickets->count() }}</span>
    </div>
</body>
</html>

