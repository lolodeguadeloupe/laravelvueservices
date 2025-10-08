<?php

namespace App\Services;

use App\Models\BookingRequest;
use App\Models\Transaction;
use App\Models\Wallet;

class CommissionService
{
    private const PLATFORM_COMMISSION_RATE = 0.15; // 15%

    public function calculateCommission(float $amount): float
    {
        return $amount * self::PLATFORM_COMMISSION_RATE;
    }

    public function processPayment(BookingRequest $booking): array
    {
        $totalAmount = $booking->final_price ?? $booking->quoted_price;
        $commission = $this->calculateCommission($totalAmount);
        $providerAmount = $totalAmount - $commission;

        // Ajouter les fonds au portefeuille du prestataire (en attente)
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $booking->provider_id],
            ['currency' => 'EUR', 'is_active' => true]
        );
        $wallet->addFunds(
            $providerAmount,
            'service_payment',
            "Paiement pour service #{$booking->id}"
        );

        // Créer la transaction de commission pour la plateforme
        Transaction::create([
            'user_id' => null, // Commission plateforme
            'wallet_id' => null,
            'type' => 'platform_commission',
            'amount' => $commission,
            'currency' => $booking->currency ?? 'EUR',
            'status' => 'completed',
            'description' => "Commission plateforme pour service #{$booking->id}",
            'metadata' => [
                'booking_id' => $booking->id,
                'commission_rate' => self::PLATFORM_COMMISSION_RATE,
                'total_amount' => $totalAmount,
                'provider_amount' => $providerAmount,
            ],
            'processed_at' => now(),
        ]);

        // Générer automatiquement les factures
        $invoiceService = app(\App\Services\InvoiceService::class);
        
        // Facture de service pour le client
        $serviceInvoice = $invoiceService->generateServiceInvoice($booking);
        
        // Facture de commission pour le prestataire
        $commissionInvoice = $invoiceService->generateCommissionInvoice($booking, $commission);

        return [
            'total_amount' => $totalAmount,
            'commission' => $commission,
            'provider_amount' => $providerAmount,
            'commission_rate' => self::PLATFORM_COMMISSION_RATE,
            'service_invoice' => $serviceInvoice,
            'commission_invoice' => $commissionInvoice,
        ];
    }

    public function confirmPayment(BookingRequest $booking): void
    {
        $wallet = Wallet::where('user_id', $booking->provider_id)->first();
        
        if ($wallet) {
            $providerAmount = $this->getProviderAmount($booking);
            $wallet->confirmFunds($providerAmount);
        }
    }

    public function refundPayment(BookingRequest $booking, float $refundAmount): array
    {
        $commission = $this->calculateCommission($refundAmount);
        $providerRefund = $refundAmount - $commission;

        $wallet = Wallet::where('user_id', $booking->provider_id)->first();
        
        if ($wallet && $wallet->balance >= $providerRefund) {
            // Déduire du portefeuille prestataire
            $wallet->decrement('balance', $providerRefund);

            // Créer la transaction de remboursement
            Transaction::create([
                'user_id' => $booking->provider_id,
                'wallet_id' => $wallet->id,
                'type' => 'refund',
                'amount' => -$providerRefund,
                'currency' => $booking->currency ?? 'EUR',
                'status' => 'completed',
                'description' => "Remboursement pour service #{$booking->id}",
                'metadata' => [
                    'booking_id' => $booking->id,
                    'original_amount' => $refundAmount,
                    'commission_deducted' => $commission,
                ],
                'processed_at' => now(),
            ]);
        }

        return [
            'refund_amount' => $refundAmount,
            'commission_deducted' => $commission,
            'provider_refund' => $providerRefund,
        ];
    }

    private function getProviderAmount(BookingRequest $booking): float
    {
        $totalAmount = $booking->final_price ?? $booking->quoted_price;
        $commission = $this->calculateCommission($totalAmount);
        return $totalAmount - $commission;
    }

    public function getPlatformRevenue(\DateTimeInterface $startDate, \DateTimeInterface $endDate): float
    {
        return Transaction::where('type', 'platform_commission')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
    }
}
