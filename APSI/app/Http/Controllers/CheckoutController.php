<?php

namespace App\Http\Controllers;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('midtrans.checkout', compact('cart'));
    }

    public function process(Request $request)
{
    // Validasi data
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'no_telepon' => 'required|string|max:15',
        'alamat' => 'required|string',
        'total' => 'required|numeric',
        'cart' => 'required|string', // Menambahkan validasi untuk cart
    ]);

    // Retrieve cart from request
    $cart = json_decode($request->input('cart'), true); // Mengambil data cart dari form
    $total = $request->input('total');
    $name = $request->input('name');
    $email = $request->input('email');
    $no_telepon = $request->input('no_telepon');
    $alamat = $request->input('alamat');

    // Create a new order
    $order = Order::create([
        'name' => $name,
        'email' => $email,
        'no_telepon' => $no_telepon,
        'alamat' => $alamat,
        'total' => $total,
        'status' => 'pending',
    ]);

    // Attach products to the order
    foreach ($cart as $id => $details) {
        $order->products()->attach($id, ['quantity' => $details['quantity']]);
    }

    // Konfigurasi Midtrans
    Config::$serverKey = config('midtrans.server_key');
    Config::$isProduction = false;
    Config::$isSanitized = true;
    Config::$is3ds = true;

    // Parameter untuk pembayaran Snap
    $params = [
        'transaction_details' => [
            'order_id' => $order->id,
            'gross_amount' => $total,
        ],
        'customer_details' => [
            'first_name' => $name,
            'email' => $email,
            'phone' => $no_telepon,
            'shipping_address' => [
                'address' => $alamat,
            ],
        ],
    ];

    try {
        $snapToken = Snap::getSnapToken($params);
        return response()->json(['snapToken' => $snapToken]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function showCart()
{
    $cart = session()->get('cart', []);

    // Hitung total harga
    $total = 0;
    foreach ($cart as $id => $details) {
        $total += $details['price'] * $details['quantity'];
    }

    return view('midtrans.checkout', compact('cart', 'total'));
}

    public function showOrder($id)
    {
        $order = Order::with('products')->find($id);
        if ($order && $order->status != 'Paid') {
            $order->update(['status' => 'Paid']);

            // Masukkan order ke dalam database untuk catatan (opsional)
            try {
                // Logika insert ke database di sini jika diperlukan
            } catch (\Exception $e) {
                // Tangani kesalahan insert database
                Log::error('Gagal menyimpan order ke database: ' . $e->getMessage());
            }

            // Log update yang berhasil
            Log::info('Status order diperbarui menjadi Paid', ['order_id' => $order->id]);
        }

        return view('midtrans.showOrder', compact('order'));
    }
}
