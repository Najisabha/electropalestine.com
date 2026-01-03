<?php

namespace App\Services;

use TCPDF;

class InvoicePdfService
{
    public function generateInvoice($order, $user)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetCreator('electropalestine');
        $pdf->SetAuthor('electropalestine');
        $pdf->SetTitle('فاتورة #' . $order->id);
        $pdf->SetSubject('فاتورة طلبية');
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        
        $pdf->setRTL(true);
        $pdf->setFont('dejavusans', '', 9);
        
        // العنوان الرئيسي
        $pdf->SetFont('dejavusans', 'B', 22);
        $pdf->SetTextColor(13, 183, 119);
        $pdf->Cell(0, 14, 'electropalestine', 0, 1, 'R');
        
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 7, '(فاتورة ضريبية مبسطة)', 0, 1, 'R');
        $pdf->Ln(12);
        
        // معلومات الفاتورة - كل سطر منفصل وواضح
        $pdf->SetFillColor(240, 252, 248);
        $pdf->SetDrawColor(13, 183, 119);
        $pdf->Rect(10, $pdf->GetY(), 190, 32, 'DF');
        
        $pdf->SetY($pdf->GetY() + 5);
        
        // رقم الفاتورة - سطر كامل
        $pdf->SetFont('dejavusans', 'B', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(45, 7, 'رقم الفاتورة:', 0, 0, 'R');
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->setRTL(false);
        $pdf->SetTextColor(13, 183, 119);
        $pdf->Cell(145, 7, '# ' . $order->id, 0, 1, 'L');
        $pdf->setRTL(true);
        
        // التاريخ - سطر كامل
        $pdf->SetFont('dejavusans', 'B', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(45, 7, 'تاريخ الإصدار:', 0, 0, 'R');
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->setRTL(false);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(145, 7, $order->created_at->format('Y-m-d'), 0, 1, 'L');
        $pdf->setRTL(true);
        
        // الحالة - سطر كامل
        $pdf->SetFont('dejavusans', 'B', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(45, 7, 'حالة الطلبية:', 0, 0, 'R');
        $pdf->SetFont('dejavusans', 'B', 9);
        $status = $order->status === 'confirmed' ? 'مؤكد' : 'قيد الانتظار';
        $statusColor = $order->status === 'confirmed' ? [13, 183, 119] : [255, 193, 7];
        $pdf->SetTextColor($statusColor[0], $statusColor[1], $statusColor[2]);
        $pdf->Cell(145, 7, $status, 0, 1, 'R');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(15);
        
        // معلومات العميل
        $pdf->SetFont('dejavusans', 'B', 13);
        $pdf->SetFillColor(13, 183, 119);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 12, '  بيانات العميل  ', 0, 1, 'R', true);
        $pdf->Ln(5);
        
        $pdf->SetTextColor(0, 0, 0);
        
        // الاسم
        $pdf->SetFont('dejavusans', 'B', 9);
        $pdf->Cell(35, 8, 'الاسم الكامل:', 0, 0, 'R');
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->setRTL(false);
        $pdf->Cell(155, 8, $user->first_name . ' ' . $user->last_name, 0, 1, 'L');
        $pdf->setRTL(true);
        
        // الجوال
        $pdf->SetFont('dejavusans', 'B', 9);
        $pdf->Cell(35, 8, 'رقم الجوال:', 0, 0, 'R');
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->setRTL(false);
        $pdf->Cell(155, 8, $user->whatsapp_prefix . $user->phone, 0, 1, 'L');
        $pdf->setRTL(true);
        
        // البريد الإلكتروني
        $pdf->SetFont('dejavusans', 'B', 9);
        $pdf->Cell(35, 8, 'البريد الإلكتروني:', 0, 0, 'R');
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->setRTL(false);
        $pdf->Cell(155, 8, $user->email, 0, 1, 'L');
        $pdf->setRTL(true);
        
        // العنوان
        $pdf->SetFont('dejavusans', 'B', 9);
        $pdf->Cell(35, 8, 'عنوان الشحن:', 0, 0, 'R');
        $pdf->SetFont('dejavusans', '', 9);
        $address = $order->shipping_address ?? 'غير متوفر';
        if (preg_match('/[a-zA-Z]/', $address) && !preg_match('/[\x{0600}-\x{06FF}]/u', $address)) {
            $pdf->setRTL(false);
            $pdf->Cell(155, 8, $address, 0, 1, 'L');
            $pdf->setRTL(true);
        } else {
            $pdf->Cell(155, 8, $address, 0, 1, 'R');
        }
        $pdf->Ln(12);
        
        // قسم المنتجات - جدول
        $pdf->SetFont('dejavusans', 'B', 13);
        $pdf->SetFillColor(13, 183, 119);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 12, '  تفاصيل المنتجات  ', 0, 1, 'R', true);
        $pdf->Ln(5);
        
        // رؤوس الجدول
        $pdf->SetFont('dejavusans', 'B', 9);
        $pdf->SetFillColor(13, 183, 119);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(13, 183, 119);
        
        // رؤوس الجدول - الترتيب: المنتج، الكمية، سعر الوحدة، المجموع (من اليمين)
        $pdf->Cell(100, 10, '  المنتج  ', 1, 0, 'C', true);
        $pdf->Cell(30, 10, '  الكمية  ', 1, 0, 'C', true);
        $pdf->Cell(35, 10, '  سعر الوحدة  ', 1, 0, 'C', true);
        $pdf->Cell(25, 10, '  المجموع  ', 1, 1, 'C', true);
        
        // بيانات المنتجات (قد تكون عنصر واحد أو عدة عناصر في حقل items)
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(200, 200, 200);

        $items = is_array($order->items ?? null) ? $order->items : [];

        if (!empty($items)) {
            foreach ($items as $item) {
                $startY = $pdf->GetY();
                $name = $item['name'] ?? '';
                $qty = $item['quantity'] ?? 0;
                $unitPrice = $item['unit_price'] ?? 0;
                $lineTotal = $item['total'] ?? ($qty * $unitPrice);

                $pdf->setRTL(true);
                $pdf->MultiCell(100, 8, '  ' . $name . '  ', 1, 'R', true, 0);
                $endY = $pdf->GetY();
                $rowHeight = max(8, $endY - $startY);

                $pdf->SetY($startY);
                $pdf->setRTL(false);
                $pdf->Cell(30, $rowHeight, '  ' . $qty . '  ', 1, 0, 'C', true);
                $pdf->Cell(35, $rowHeight, '  $' . number_format($unitPrice, 2) . '  ', 1, 0, 'R', true);

                $pdf->SetFont('dejavusans', 'B', 9);
                $pdf->SetTextColor(13, 183, 119);
                $pdf->Cell(25, $rowHeight, '  $' . number_format($lineTotal, 2) . '  ', 1, 1, 'R', true);

                $pdf->SetY(max($endY, $startY + $rowHeight));
                $pdf->setRTL(true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('dejavusans', '', 9);
            }
        } else {
            // توافقية مع الطلبات القديمة (منتج واحد)
            $startY = $pdf->GetY();
            $productName = $order->product_name;
            $pdf->setRTL(true);
            $pdf->MultiCell(100, 10, '  ' . $productName . '  ', 1, 'R', true, 0);
            $endY = $pdf->GetY();
            $rowHeight = max(10, $endY - $startY);

            $pdf->SetY($startY);
            $pdf->setRTL(false);
            $pdf->Cell(30, $rowHeight, '  ' . $order->quantity . '  ', 1, 0, 'C', true);
            $pdf->Cell(35, $rowHeight, '  $' . number_format($order->unit_price, 2) . '  ', 1, 0, 'R', true);

            $pdf->SetFont('dejavusans', 'B', 9);
            $pdf->SetTextColor(13, 183, 119);
            $pdf->Cell(25, $rowHeight, '  $' . number_format($order->total, 2) . '  ', 1, 1, 'R', true);

            $pdf->SetY(max($endY, $startY + $rowHeight));
            $pdf->setRTL(true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('dejavusans', '', 9);
        }

        $pdf->Ln(12);
        
        // الإجمالي المطلوب - في صندوق منفصل
        $pdf->SetFillColor(240, 252, 248);
        $pdf->SetDrawColor(13, 183, 119);
        $pdf->Rect(10, $pdf->GetY(), 190, 20, 'DF');
        $pdf->SetY($pdf->GetY() + 6);
        $pdf->SetFont('dejavusans', 'B', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(45, 8, 'الإجمالي المطلوب:', 0, 0, 'R');
        $pdf->SetFont('dejavusans', 'B', 14);
        $pdf->SetTextColor(13, 183, 119);
        $pdf->setRTL(false);
        $pdf->Cell(145, 8, '$' . number_format($order->total, 2), 0, 1, 'L');
        $pdf->setRTL(true);
        $pdf->Ln(16);
        
        // التذييل
        $pdf->SetTextColor(120, 120, 120);
        $pdf->SetFont('dejavusans', '', 8);
        $pdf->Cell(0, 7, 'شكراً لاختيارك electropalestine', 0, 1, 'C');
        $pdf->Cell(0, 7, 'هذه الفاتورة تم إصدارها آلياً وهي صالحة دون توقيع', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('dejavusans', '', 8);
        $pdf->Cell(0, 7, 'لأي استفسار، يرجى التواصل معنا عبر:', 0, 1, 'C');
        $pdf->setRTL(false);
        $pdf->SetFont('dejavusans', 'B', 8);
        $pdf->SetTextColor(13, 183, 119);
        $pdf->Cell(0, 7, 'info@electropalestine.com', 0, 1, 'C');
        $pdf->setRTL(true);
        
        return $pdf;
    }
}
