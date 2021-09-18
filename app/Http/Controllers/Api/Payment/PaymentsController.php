<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\HistoryPaymentRequest;
use App\Http\Requests\PaymentCreateRequest;
use App\Repositories\PaymentRepository;
use App\Repositories\RequestConsultingRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\UserRepository;
use App\Services\PaymentService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class PaymentsController.
 */
class PaymentsController extends ApiController
{
    protected $repository;
    protected $stripeService;
    protected $userRepository;
    protected $serviceRepository;
    protected $requestConsultingRepository;
    protected $paymentService;

    /**
     * PaymentsController constructor.
     */
    public function __construct(
        PaymentRepository $repository,
        ServiceRepository $serviceRepository,
        StripeService $stripeService,
        UserRepository $userRepository,
        RequestConsultingRepository $requestConsultingRepository,
        PaymentService $paymentService
    ) {
        $this->repository = $repository;
        $this->serviceRepository = $serviceRepository;
        $this->stripeService = $stripeService;
        $this->userRepository = $userRepository;
        $this->requestConsultingRepository = $requestConsultingRepository;
        $this->paymentService = $paymentService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function paymentService(PaymentCreateRequest $request)
    {
        try {
            \DB::beginTransaction();
            $paymentService = $this->paymentService;
            $pointValidOfUser = $this->userRepository->getPointOfUser(auth()->user()->id);
            $walletAmountExpire = $this->userRepository->getWalletExpire(auth()->user()->id);
            $walletAmountExpire = $walletAmountExpire ? $walletAmountExpire->amount : 0;
            $totalAmountWallet = $pointValidOfUser + $walletAmountExpire;
            $success = false;
            $service = $this->serviceRepository->find($request->service_id);
            $consulting = $this->requestConsultingRepository->find($request->consulting_id);
            //check request
            if ($consulting->price_requested < $request->amount || $request->amount < 0) {
                return response()->json(['error' => true, 'message' => __('api.common.failed')]);
            }

            //check enough point in wallet to payment
            if ($totalAmountWallet < $request->point) {
                return response()->json(['error' => true, 'message' => __('api.payment.point_not_enough')]);
            }
            // case service is free (price=0) or amount  =0

            if ($request->amount == 0 || $consulting->price_requested == 0) {
                //implement payment not use stripe
                $success = $paymentService->handlePayment($request->service_id, $consulting->price_requested, $request->consulting_id, $request->amount, $request->point, $request->coupon_id);
                if ($success) {
                    \DB::commit();

                    return response()->json(['success' => true, 'message' => __('api.payment.success')]);
                }

                return response()->json(['error' => true, 'message' => __('api.payment.failed')]);
            } else {
                if ($request->amount > 0 && $request->amount < 50) {
                    return response()->json(['success' => false, 'message' => __('api.payment.error_create_intent')]);
                }
                //implement payment use strip
                if (!$request->payment_method) {
                    return response()->json(['error' => true, 'message' => __('api.payment.error_card')]);
                }

                $intentData = $this->stripeService->createPaymentIntent($request->amount, $request->payment_method);

                if (!$intentData->id) {
                    return response()->json(['error' => true, 'message' => __('api.payment.create_intent_failed')]);
                }

                $paymentData = [
                    'user_id' => auth()->user()->id,
                    'payment_intent_id' => $intentData->id,
                    'customer' => $intentData->customer,
                    'object' => $intentData->object,
                    'currency' => $intentData->currency,
                    'receipt_email' => $intentData->receipt_email,
                    'status' => $intentData->status,
                ];

                //handle payment
                $success = $paymentService->handlePayment($request->service_id, $consulting->price_requested, $request->consulting_id, $request->amount, $request->point, $request->coupon_id, $paymentData);
                if ($success) {
                    $dataConfirm = $this->stripeService->confirmPaymentIntent($intentData->id);
                    if ($dataConfirm->status == 'succeeded') {
                        \DB::commit();

                        return response()->json(['success' => true, 'message' => __('api.payment.success')]);
                    }
                }
            }

            return response()->json([
                'error' => true,
                'message' => __('api.payment.failed'),
            ]);
        } catch (Throwable $th) {
            \Log::ERROR('Controllers\Api\Payment\PaymentsController - payment : '.$th->getMessage());
            \DB::rollback();

            return $this->respondError(__('api.exception'));
        }
    }

    public function createPaymentIntent(Request $request)
    {
        try {
            $service = $this->serviceRepository->where('id', $request->service_id)->first();

            if (!$service) {
                return response()->json([
                    'error' => __('api.services_not_exists'),
                ]);
            }
            //check request
            if ($service->price < $request->amount) {
                return response()->json([
                    'error' => 'ERROR',
                ]);
            }
            //create payment intent
            $paymentIntent = $this->stripeService->createPaymentIntent($request->amount, $request->payment_method);

            if (!$paymentIntent) {
                return response()->json([
                    'error' => true,
                    'message' => __('api.payment.error_create_intent'),
                ]);
            }

            return response()->json([
                'status' => true,
                'data' => $paymentIntent,
                'client_secret' => $paymentIntent->client_secret,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => __('api.payment.error_create_intent'),
            ]);
        }
    }

    /**
     * Check valid input point when perform payments.
     */
    public function checkInputPoint(Request $request)
    {
        if (isset($request->amount)) {
            if ($request->amount < 0) {
                return response()->json([
                    'status' => false,
                    'message' => __('api.payment.amount_invalid'),
                ]);
            }
            $amountWallet = $this->userRepository->getPointOfUser(auth()->user()->id);
            $walletAmountExpire = $this->userRepository->getWalletExpire(auth()->user()->id);
            $walletAmountExpire = $walletAmountExpire ? $walletAmountExpire->amount : 0;
            $totalAmountWallet = $amountWallet + $walletAmountExpire;

            if ($request->amount > $totalAmountWallet) {
                return response()->json([
                    'status' => false,
                    'message' => __('api.payment.point_not_enough'),
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => __('api.payment.valid_point'),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => __('api.payment.amount_required'),
            ]);
        }
    }

    public function comfirmPaymentIntent(Request $request)
    {
        $id = $request->id;
        $dataConfirm = $this->stripeService->confirmPaymentIntent($id);

        return response()->json([
            'status' => true,
            'data' => $dataConfirm,
        ]);
    }

    public function createPaymentMethod(Request $request)
    {
        $paymentMethod = $this->stripeService->createPaymentMethod();

        return response()->json([
            'status' => true,
            'data' => $paymentMethod,
        ]);
    }

    /**
     * get history pyament.
     */
    public function historyPayment(HistoryPaymentRequest $request)
    {
        try {
            $user = auth()->user();
            $startDate = $request->start_date ?? null;
            $endDate = $request->end_date ?? null;
            $type = $request->type ?? null;
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->paymentService->historyPayment($user->id, $startDate, $endDate, $type)->paginate($pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Api\Payment\PaymentsController - historyPayment : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    public function listCustomerPaymentMethods()
    {
        try {
            $user = auth()->user();
            $customerId = $user->stripe_customer_id;
            $paymentMethods = null;
            if ($customerId) {
                $paymentMethods = $this->stripeService->listCustomerPaymentMethods($user->stripe_customer_id);
            }

            return response()->json([
                'success' => true,
                'data' => $paymentMethods,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    public function attachPaymentMethodForUser(Request $request)
    {
        try {
            $user = auth()->user();
            $userId = $user->id;
            $customerId = $user->stripe_customer_id;
            if (!$request->payment_method) {
                return response()->json(['error' => true, 'message' => 'お支払い方法は必須です。']);
            }

            if (!$customerId) {
                $customerId = $this->stripeService->createCustomer($request->payment_method, $user->email, $user->user_name)->id;
                $this->userRepository->update(['stripe_customer_id' => $customerId], $userId);
            } else {
                $this->stripeService->attachPaymentMethod($request->payment_method, $customerId);
            }

            return response()->json(['success' => true, 'message' => 'カードを保存しました。']);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    public function detachPaymentMethod(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;
        if (!$request->payment_method) {
            return response()->json(['error' => true, 'message' => 'お支払い方法は必須です。']);
        }
        $this->stripeService->detachPaymentMethod($request->payment_method);

        return response()->json(['success' => true, 'message' => 'カードを保存しました。']);
    }

    public function createSessionCheckout(PaymentCreateRequest $request)
    {
        try {
            \DB::beginTransaction();
            $paymentService = $this->paymentService;
            $userId = auth('api')->user()->id;
            $pointValidOfUser = $this->userRepository->getPointOfUser($userId);
            $success = false;
            $service = $this->serviceRepository->find($request->service_id);
            $consulting = $this->requestConsultingRepository->find($request->consulting_id);

            //check request
            if ($consulting->price_requested < $request->amount || $request->amount < 0) {
                return response()->json(['error' => true, 'message' => __('api.common.failed')]);
            }
            //check enough point in wallet to payment
            if ($pointValidOfUser < $request->point) {
                return response()->json(['error' => true, 'message' => __('api.payment.point_not_enough')]);
            }

            // case service is free (price=0) or amount  =0
            if ($request->amount == 0 || $consulting->price_requested == 0) {
                //implement payment not use stripe
                $success = $paymentService->handleCheckoutPayment($userId, $request->service_id, $consulting->price_requested, $request->consulting_id, $request->amount, $request->point, null, $request->coupon_id);
                if ($success) {
                    \DB::commit();

                    return response()->json(['success' => true, 'message' => __('api.payment.success')]);
                }

                return response()->json(['error' => true, 'message' => __('api.payment.failed')]);
            } else {
                if ($request->amount > 0 && $request->amount < 50) {
                    return response()->json(['success' => false, 'message' => __('api.payment.error_create_intent')]);
                }
                $sessionData = [
                    'user_id' => $userId,
                    'service_id' => $service->id,
                    'price_requested' => $consulting->price_requested,
                    'consulting_id' => $request->consulting_id,
                    'amount' => $request->amount,
                    'point' => $request->point,
                    'coupon_id' => $request->coupon_id ?? null,
                    'service_title' => $service->service_title,
                ];

                $session = $this->stripeService->createSession($sessionData);
                if ($session->id) {
                    \DB::commit();

                    return response()->json(['success' => true, 'data' => $session]);
                }
            }

            return response()->json([
                'error' => true,
                'message' => __('api.payment.failed'),
            ]);
        } catch (Throwable $th) {
            \Log::ERROR('Controllers\Api\Payment\PaymentsController - checkout : '.$th->getMessage());
            \DB::rollback();

            return $this->respondError(__('api.exception'));
        }
    }
}
