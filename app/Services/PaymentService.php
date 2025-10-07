<?php

namespace App\Services;

use App\Models\BookingRequest;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Cashier;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class PaymentService
{
    private StripeClient $stripe;
    private float $platformFeeRate;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('cashier.secret'));
        $this->platformFeeRate = config('app.platform_fee_rate', 0.15);
    }

    public function createPaymentIntent(BookingRequest $booking): PaymentIntent
    {
        $amount = $booking->final_price ?? $booking->quoted_price;
        
        if (!$amount) {
            throw new \InvalidArgumentException('Montant du paiement non défini');
        }

        // Créer le payment record
        $payment = $this->createPaymentRecord($booking, $amount);

        // Créer le PaymentIntent Stripe
        $paymentIntent = $this->stripe->paymentIntents->create([
            'amount' => $amount * 100, // Stripe utilise les centimes
            'currency' => 'eur',
            'metadata' => [
                'booking_id' => $booking->uuid,
                'payment_id' => $payment->uuid,
                'client_id' => $booking->client_id,
                'provider_id' => $booking->provider_id,
            ],
            'capture_method' => 'automatic',
        ]);

        // Mettre à jour le payment avec l'ID Stripe
        $payment->update([
            'stripe_payment_intent_id' => $paymentIntent->id,
        ]);

        return $paymentIntent;
    }

    public function confirmPayment(string $paymentIntentId): Payment
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntentId)->firstOrFail();

        return DB::transaction(function () use ($payment, $paymentIntentId) {
            // Récupérer le PaymentIntent depuis Stripe
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                $payment->update([
                    'status' => 'completed',
                    'stripe_charge_id' => $paymentIntent->charges->data[0]->id ?? null,
                    'paid_at' => now(),
                    'stripe_metadata' => $paymentIntent->toArray(),
                ]);

                // Créer les transactions pour la commission et le paiement prestataire
                $this->createTransactions($payment);

                // Mettre à jour le statut de la réservation
                $payment->bookingRequest->update(['status' => 'paid']);
            } else {
                $payment->update([
                    'status' => 'failed',
                    'failure_reason' => $paymentIntent->last_payment_error->message ?? 'Échec du paiement',
                ]);
            }

            return $payment;
        });
    }

    public function refundPayment(Payment $payment, ?float $amount = null): bool
    {
        if (!$payment->stripe_charge_id) {
            throw new \InvalidArgumentException('Pas de charge Stripe à rembourser');
        }

        $refundAmount = $amount ?? $payment->amount;

        try {
            $refund = $this->stripe->refunds->create([
                'charge' => $payment->stripe_charge_id,
                'amount' => $refundAmount * 100,
                'metadata' => [
                    'payment_id' => $payment->uuid,
                    'booking_id' => $payment->bookingRequest->uuid,
                ],
            ]);

            $payment->update([
                'status' => 'refunded',
                'refunded_at' => now(),
            ]);

            // Créer une transaction de remboursement
            Transaction::create([
                'payment_id' => $payment->id,
                'user_id' => $payment->client_id,
                'type' => 'refund',
                'amount' => $refundAmount,
                'status' => 'completed',
                'description' => 'Remboursement du paiement',
                'processed_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            $payment->update([
                'failure_reason' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function transferToProvider(Payment $payment): bool
    {
        if (!$payment->isCompleted()) {
            throw new \InvalidArgumentException('Le paiement doit être complété avant le transfert');
        }

        try {
            $transfer = $this->stripe->transfers->create([
                'amount' => $payment->provider_amount * 100,
                'currency' => 'eur',
                'destination' => $payment->provider->stripe_account_id,
                'metadata' => [
                    'payment_id' => $payment->uuid,
                    'booking_id' => $payment->bookingRequest->uuid,
                    'provider_id' => $payment->provider_id,
                ],
            ]);

            // Mettre à jour la transaction payout
            $payoutTransaction = $payment->transactions()
                ->where('type', 'payout')
                ->where('user_id', $payment->provider_id)
                ->first();

            if ($payoutTransaction) {
                $payoutTransaction->update([
                    'status' => 'completed',
                    'stripe_transfer_id' => $transfer->id,
                    'processed_at' => now(),
                ]);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function createPaymentRecord(BookingRequest $booking, float $amount): Payment
    {
        $payment = new Payment([
            'booking_request_id' => $booking->id,
            'client_id' => $booking->client_id,
            'provider_id' => $booking->provider_id,
            'amount' => $amount,
            'currency' => 'EUR',
            'status' => 'pending',
        ]);

        $payment->calculatePlatformFee($this->platformFeeRate);
        $payment->save();

        return $payment;
    }

    private function createTransactions(Payment $payment): void
    {
        // Transaction commission plateforme
        Transaction::create([
            'payment_id' => $payment->id,
            'user_id' => 1, // ID admin/plateforme
            'type' => 'commission',
            'amount' => $payment->platform_fee,
            'status' => 'completed',
            'description' => "Commission plateforme ({$this->platformFeeRate * 100}%)",
            'processed_at' => now(),
        ]);

        // Transaction payout prestataire
        Transaction::create([
            'payment_id' => $payment->id,
            'user_id' => $payment->provider_id,
            'type' => 'payout',
            'amount' => $payment->provider_amount,
            'status' => 'pending',
            'description' => 'Paiement prestataire',
        ]);
    }
}