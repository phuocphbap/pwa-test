<?php

namespace App\Http\Controllers\Api\Webhook;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\Controller;
use App\Constant\StatusConstant;

class WebhookController extends Controller
{
    protected $paymentService;
    protected $firebaseService;

    public function __construct(PaymentService $paymentService, FirebaseService $firebaseService)
    {
        $this->paymentService = $paymentService;
        $this->firebaseService = $firebaseService;
    }

    public function checkout(Request $request)
    {
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['message' => 'Invalid payload',], 200);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['message' => 'Invalid signature',], 200);
        }
        
        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                try {
                    DB::beginTransaction();
                    $checkout = $event->data->object;
                    $data = $checkout->metadata;
                    $this->paymentService->handleCheckoutPayment(
                        $data->user_id, $data->service_id, $data->price_requested, $data->consulting_id, $data->amount, $data->point, isset($data->coupon_id) ?? null, $checkout->id
                    );

                    $this->firebaseService->pushNoticeProgressPaymentFinish(
                        StatusConstant::PROGRESS_STEP_PAYMENT,
                        $data->service_id,
                        $data->consulting_id,
                        $data->service_title
                    );

                    DB::commit();
                } catch (\Throwable $th) {
                    Log::ERROR('Controllers\Api\Payment\PaymentsController - checkout : '.$th->getMessage());
                    DB::rollback();
                }
                break;
                
            default:
                echo 'Received unknown event type ' . $event->type;
        }
        http_response_code(200);
    }
}
