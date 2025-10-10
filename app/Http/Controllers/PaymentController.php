<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    public function create(BookingRequest $booking): Response
    {
        $this->authorize('pay', $booking);

        $booking->load(['service', 'provider', 'client']);

        return Inertia::render('Payments/Create', [
            'booking' => $booking,
            'stripeKey' => config('cashier.key'),
        ]);
    }

    public function createIntent(BookingRequest $booking): JsonResponse
    {
        $this->authorize('pay', $booking);

        try {
            $paymentIntent = $this->paymentService->createPaymentIntent($booking);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        try {
            $payment = $this->paymentService->confirmPayment($request->payment_intent_id);

            return response()->json([
                'success' => true,
                'payment' => $payment,
                'redirect_url' => route('bookings.show', $payment->booking),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function refund(Payment $payment): JsonResponse
    {
        $this->authorize('refund', $payment);

        try {
            $success = $this->paymentService->refundPayment($payment);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Remboursement effectué avec succès',
                ]);
            }

            return response()->json([
                'error' => 'Échec du remboursement',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $endpointSecret = config('cashier.webhook.secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $endpointSecret);

            switch ($event['type']) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($event['data']['object']);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event['data']['object']);
                    break;

                default:
                    // Type d'événement non géré
                    break;
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function handlePaymentSucceeded(array $paymentIntent): void
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent['id'])->first();

        if ($payment && $payment->isPending()) {
            $this->paymentService->confirmPayment($paymentIntent['id']);
        }
    }

    private function handlePaymentFailed(array $paymentIntent): void
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent['id'])->first();

        if ($payment && $payment->isPending()) {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $paymentIntent['last_payment_error']['message'] ?? 'Échec du paiement',
            ]);
        }
    }
}
