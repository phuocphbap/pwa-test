<?php

namespace App\Services;

use App\Entities\Payment;
use Stripe;
use Stripe\StripeClient;

class StripeService
{
    protected $stripe;

    public function __construct()
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\ApplePayDomain::create([
            'domain_name' => env('APP_URL'),
        ]);
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    public function createPaymentIntent($amount, $payment_method)
    {
        try {
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => Payment::DEFAULT_CURRENCY,
                'payment_method' => $payment_method,
            ]);

            return $paymentIntent;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function retrievePaymentIntent($id)
    {
        $paymentIntent = \Stripe\PaymentIntent::retrieve($id);

        return $paymentIntent;
    }

    public function confirmPaymentIntent($id)
    {
        $intent = \Stripe\PaymentIntent::retrieve($id);
        $intent->confirm();

        return $intent;
    }

    public function createPaymentMethod()
    {
        $paymentMethod = \Stripe\PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'number' => '5555555555554444',
                'exp_month' => 2,
                'exp_year' => 2022,
                'cvc' => '314',
            ],
        ]);

        return $paymentMethod;
    }

    public function createCustomer($paymentMethodId, $email = null, $name = null)
    {
        $customer = \Stripe\Customer::create([
            'payment_method' => $paymentMethodId,
            'email' => $email,
            'name' => $name,
        ]);

        return $customer;
    }

    public function listCustomerPaymentMethods($customerId)
    {
        $paymentMethods = \Stripe\PaymentMethod::all([
            'customer' => $customerId,
            'type' => 'card',
        ]);

        return $paymentMethods;
    }

    public function attachPaymentMethod($paymentMethodId, $customerId)
    {
        $paymentMethod = $this->stripe->paymentMethods->attach(
            $paymentMethodId,
            ['customer' => $customerId]
        );

        return $paymentMethod;
    }

    public function detachPaymentMethod($paymentMethodId)
    {
        $paymentMethod = $this->stripe->paymentMethods->detach(
            $paymentMethodId,
            []
        );

        return $paymentMethod;
    }

    public function createSession($sessionData)
    {
        $session = $this->stripe->checkout->sessions->create([
            'success_url' => env('APP_URL').'/home/services/progress/'.$sessionData['consulting_id'],
            'cancel_url' => env('APP_URL').'/service/checkout/'.$sessionData['consulting_id'],
            'payment_method_types' => ['card'],
            'line_items' => [[
                'amount' => $sessionData['amount'],
                'name' => $sessionData['service_title'],
                'currency' => Payment::DEFAULT_CURRENCY,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => $sessionData,
          ]);

        return $session;
    }
}
