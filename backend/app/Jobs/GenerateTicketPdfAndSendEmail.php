<?php

namespace App\Jobs;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use TCPDF;

class GenerateTicketPdfAndSendEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::with(['tickets.seat', 'tickets.schedule.movie', 'schedule.movie'])
            ->findOrFail($this->order->id);

        $pdfFiles = [];

        foreach ($order->tickets as $ticket) {
            $pdfPath = $this->generateTicketPdf($ticket);
            $pdfFiles[] = $pdfPath;
        }

        try {
            Mail::to($order->email)->send(new OrderConfirmation($order, $pdfFiles));
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    private function generateTicketPdf($ticket): string
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetTitle('Ticket #'.$ticket->id);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, $ticket->schedule->movie->title, 0, 1, 'L');

        $pdf->Ln(5);

        $pdf->SetFont('helvetica', '', 12);
        $eventDate = \Carbon\Carbon::parse($ticket->schedule->date.' '.$ticket->schedule->start_time);
        $pdf->Cell(0, 10, 'Date: '.$eventDate->format('F d, Y'), 0, 1, 'L');
        $pdf->Cell(0, 10, 'Time: '.$eventDate->format('g:i A'), 0, 1, 'L');

        $pdf->Ln(5);

        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Seat: '.$ticket->seat->seat_name, 0, 1, 'L');

        $pdf->Ln(10);

        $ticketUrl = url('/ticket/'.$ticket->uuid);
        $qrCodeSvg = QrCode::format('svg')
            ->size(150)
            ->generate($ticketUrl);

        $svgPath = storage_path('app/temp/qr_'.$ticket->uuid.'.svg');
        if (! file_exists(dirname($svgPath))) {
            mkdir(dirname($svgPath), 0755, true);
        }
        file_put_contents($svgPath, $qrCodeSvg);

        $pdf->ImageSVG($svgPath, 80, 120, 50, 50);

        unlink($svgPath);

        // saving PDF file tickets for testing to see a ticket
        $pdfPath = 'tickets/ticket_'.$ticket->id.'.pdf';
        $fullPath = storage_path('app/'.$pdfPath);
        if (! file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }
        $pdf->Output($fullPath, 'F');

        return $pdfPath;
    }
}
