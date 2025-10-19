<?php
require 'razorpay-php-master/Razorpay.php'; // If using Razorpay SDK via Composer

use Razorpay\Api\Api;

class RazorpayPayment {
    private $api;

    public function __construct() {
        // Replace with your Razorpay test key & secret
        $this->api = new Api('rzp_test_RTmMpWtxn5XN7d', 'sHIMJ4AwdNlBUFc2QUyoPuNB');
    }

    // Create order on Razorpay
    public function createOrder($amount, $receipt) {
        $orderData = [
            'receipt' => $receipt,
            'amount' => $amount * 100, // amount in paise
            'currency' => 'INR',
            'payment_capture' => 1
        ];
        return $this->api->order->create($orderData);
    }

    // Verify payment signature
    public function verifyPayment($data) {
        $attributes = [
            'razorpay_order_id' => $data['razorpay_order_id'],
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature' => $data['razorpay_signature']
        ];
        $this->api->utility->verifyPaymentSignature($attributes);
    }
}
