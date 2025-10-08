<?php

namespace App\Services;

use App\Models\BookingRequest;
use App\Models\Invoice;

class InvoiceService
{
    private const TAX_RATE = 20.00; // TVA 20%

    public function generateServiceInvoice(BookingRequest $booking): Invoice
    {
        $invoiceNumber = $this->generateInvoiceNumber();
        $subtotal = $booking->final_price ?? $booking->quoted_price;
        $taxAmount = $subtotal * (self::TAX_RATE / 100);
        $totalAmount = $subtotal + $taxAmount;

        $lineItems = [
            [
                'description' => $booking->service->title,
                'quantity' => 1,
                'unit_price' => $subtotal,
                'total' => $subtotal,
            ]
        ];

        if ($booking->estimated_duration) {
            $lineItems[0]['details'] = "Durée estimée: {$booking->estimated_duration} minutes";
        }

        return Invoice::create([
            'invoice_number' => $invoiceNumber,
            'booking_request_id' => $booking->id,
            'client_id' => $booking->client_id,
            'provider_id' => $booking->provider_id,
            'type' => 'service_invoice',
            'status' => 'draft',
            'subtotal' => $subtotal,
            'tax_rate' => self::TAX_RATE,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'line_items' => $lineItems,
            'billing_address' => $booking->client_address ?? [],
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'notes' => "Facture pour le service: {$booking->service->title}",
        ]);
    }

    public function generateCommissionInvoice(BookingRequest $booking, float $commissionAmount): Invoice
    {
        $invoiceNumber = $this->generateInvoiceNumber('COM');
        $taxAmount = $commissionAmount * (self::TAX_RATE / 100);
        $totalAmount = $commissionAmount + $taxAmount;

        $lineItems = [
            [
                'description' => 'Commission plateforme',
                'quantity' => 1,
                'unit_price' => $commissionAmount,
                'total' => $commissionAmount,
                'details' => "Commission sur service #{$booking->id} - 15%",
            ]
        ];

        return Invoice::create([
            'invoice_number' => $invoiceNumber,
            'booking_request_id' => $booking->id,
            'client_id' => $booking->provider_id, // Le prestataire est "facturé" pour la commission
            'provider_id' => null, // Facture de la plateforme
            'type' => 'commission_invoice',
            'status' => 'draft',
            'subtotal' => $commissionAmount,
            'tax_rate' => self::TAX_RATE,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'line_items' => $lineItems,
            'billing_address' => $booking->provider->profile->address ?? [],
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(15)->toDateString(),
            'notes' => "Commission plateforme pour le service: {$booking->service->title}",
        ]);
    }

    public function markAsPaid(Invoice $invoice): void
    {
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function sendInvoice(Invoice $invoice): bool
    {
        // Générer le PDF si pas encore fait
        if (!$invoice->pdf_path) {
            $this->generatePDF($invoice);
        }

        // Envoyer par email
        try {
            // TODO: Implémenter l'envoi d'email avec le PDF en pièce jointe
            
            $invoice->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function generateInvoiceNumber(string $prefix = 'INV'): string
    {
        $year = now()->year;
        $month = now()->format('m');
        
        $lastInvoice = Invoice::where('invoice_number', 'like', "{$prefix}-{$year}{$month}%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('%s-%s%s%04d', $prefix, $year, $month, $nextNumber);
    }

    private function generatePDF(Invoice $invoice): string
    {
        // TODO: Implémenter la génération de PDF avec une library comme DomPDF ou wkhtmltopdf
        
        $filename = "invoice_{$invoice->invoice_number}.pdf";
        $path = "invoices/{$filename}";
        
        // Placeholder pour la génération PDF
        $invoice->update(['pdf_path' => $path]);
        
        return $path;
    }

    public function getMonthlyInvoices(int $year, int $month): array
    {
        $startDate = now()->create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return [
            'service_invoices' => Invoice::where('type', 'service_invoice')
                ->whereBetween('issue_date', [$startDate, $endDate])
                ->with(['booking.service', 'client', 'provider'])
                ->get(),
            
            'commission_invoices' => Invoice::where('type', 'commission_invoice')
                ->whereBetween('issue_date', [$startDate, $endDate])
                ->with(['booking.service', 'client'])
                ->get(),
        ];
    }
}
