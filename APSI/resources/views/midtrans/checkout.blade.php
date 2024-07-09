<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Hairnic - Single Product Website Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Poppins:wght@200;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="frontend/style.css" rel="stylesheet">

    <!-- Midtrans Snap JS -->
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <section id="order-item">
        <div class="hj">
            <div class="order">
                <h2>Order Form</h2>
                <ul id="checkout-cart-items">
                    @php $total = 0; @endphp
                    @foreach($cart as $id => $details)
                        <li>{{ $details['name'] }} ({{ $details['quantity'] }} x {{ $details['price'] }})</li>
                        @php $total += $details['quantity'] * $details['price']; @endphp
                    @endforeach
                </ul>
                <h3>Total: Rp. <span id="checkout-total"></span></h3>
                <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="total" value="{{ $total }}">
                    <div class="form-row">
                        <div class="form-left">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-right">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-left">
                            <label for="phone">No Telepon</label>
                            <input type="text" id="phone" name="no_telepon" required>
                        </div>
                        <div class="form-right">
                            <label for="pengiriman">Pengiriman</label>
                            <select class="form-control select2" id="pengiriman" name="pengiriman" required>
                                <option value="">Select Pengiriman</option>
                                <option value="JNE">JNE</option>
                                <option value="SICEPAT">SICEPAT</option>
                                <option value="JNT">JNT</option>
                                <option value="DJONY_FAST">DJONY FAST</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-left">
                            <label for="alamat">Alamat</label>
                            <textarea id="alamat" name="alamat" rows="1" required></textarea>
                        </div>
                    </div>
                    <button type="button" class="btn_submit" id="submit-button">Submit Order</button>
                </form>

                <!-- JavaScript untuk pengiriman data via Ajax -->
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    $(document).ready(function () {
                        $('#submit-button').on('click', function (event) {
                            event.preventDefault();
                            $.ajax({
                                url: $('#order-form').attr('action'),
                                method: 'POST',
                                data: $('#order-form').serialize(),
                                success: function (response) {
                                    // Redirect ke halaman pembayaran Midtrans
                                    snap.pay(response.snapToken, {
                                        onSuccess: function (result) {
                                            console.log('Payment success:', result);
                                            window.location.href = "/order-success";
                                        },
                                        onPending: function (result) {
                                            console.log('Payment pending:', result);
                                            window.location.href = "/order-pending";
                                        },
                                        onError: function (result) {
                                            console.log('Payment error:', result);
                                            window.location.href = "/order-failed";
                                        }
                                    });
                                },
                                error: function (xhr) {
                                    console.log('Error:', xhr.responseText);
                                    alert('An error occurred, please try again.');
                                }
                            });
                        });
                    });

                    document.getElementById('checkout-form').addEventListener('submit', function() {
                    var cart = @json(session('cart', [])); // Mengambil data cart dari server
                    var cartInput = document.getElementById('cart-input');
                    cartInput.value = JSON.stringify(cart); // Menyimpan data cart dalam format JSON di field tersembunyi
                });
                    
                </script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let checkoutCartItems = document.getElementById('checkout-cart-items');
    let checkoutTotal = document.getElementById('checkout-total');
    let checkoutTotalHidden = document.getElementById('checkout-total-hidden');
    
    let total = 0;

    cart.forEach(item => {
        let li = document.createElement('li');
        li.innerHTML = `${item.name} (${item.quantity} x Rp ${item.price.toLocaleString('id-ID')})`;
        checkoutCartItems.appendChild(li);

        total += item.price * item.quantity;
        
        // Add hidden inputs for cart items
        let hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'cart_items[]';
        hiddenInput.value = JSON.stringify(item);
        document.getElementById('checkout-form').appendChild(hiddenInput);
    });

    checkoutTotal.innerText = total.toLocaleString('id-ID');
    checkoutTotalHidden.value = total;
});
    </script>
            </div>
        </div>
    </section>
</body>

</html>
