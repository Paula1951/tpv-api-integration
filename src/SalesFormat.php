<?php

use src\AgoraAPI;

class SalesFormat 
{
    public function getTicketsDay()
    {
        $ticketDay = '2023-10-01';
        $enpoint = "api/tickets?date=" . urlencode($ticketDay);

        $agoraAPI = new AgoraAPI();
        $apiResponse = $agoraAPI->connectionApi($enpoint);
        if ($apiResponse === false) {
            return ; 
        }
        $ticketResponse = $apiResponse['Tickets'][0];
        return $ticketResponse;
    }

    public function getLinesTickets()
    {
        $tickets = $this->getTicketsDay();
        $lines = $tickets['Tickets'][0]['Lines'];

        return $lines;
    }

    public function countAddinOccurrences($modifiers, $modifier_id) {
        return count(array_filter($modifiers, function($mod) use ($modifier_id) {
            return $mod['modifier_id'] === $modifier_id;
        }));
    }

    public function fillLines($linesData) {
        $lines = [];

        foreach ($linesData as $line) {

            if ($line['DiscountRate'] == $line['CashDiscount']) {
                $discountType = 'N/A';
            } else {
                $discountType = ($line['DiscountRate'] > 0) ? 'percentage' : 'amount';
            }            

            $discountAmount = ($discountType === 'percentage') ? $line['DiscountRate'] * 100 : $line['CashDiscount'];

            $discountConcept = 'N/A';
            if (!empty($line['Offers'])) {
                $discountConcept = $line['Offers'][0]['ApplicationMode'];
            }
            $taxRate = $line['VatRate'] * 100;
             
            $lineData = [
                'product_id' => $line['ProductId'],
                'name' => $line['ProductName'],
                'price' => $line['ProductPrice'],
                'quantity' => $line['Quantity'],
                'discount_type' => $discountType,
                'discount_amount' => $discountAmount,
                'discount_concept' => $discountConcept,
                'tax_rate' => $taxRate,
                'modifiers' => []
            ];

            foreach ($line['Addins'] as $addin) {
                $modifierData = [
                    'modifier_id' => $addin['ProductId'],
                    'name' => $addin['ProductName'],
                    'price' => $addin['ProductPrice'],
                    'quantity' => 0
                ];
                $lineData['modifiers'][] = $modifierData;
            }
            
            foreach ($line['Addins'] as &$addin) {
                $modifierData['quantity'] = $this->countAddinOccurrences($line['Addins'], $addin['modifier_id']);
            }
            $lines[] = $lineData;
        }

        return $lines;
    }

    public function formatSale()
    {
        $ticketData = $this->getTicketsDay();

        if (is_string($ticketData)) {
            $id = uniqid();

            $close_time = date('Y-m-d H:i:s', strtotime($ticketData['Date']));

            $jsonTicketData = json_decode($ticketData);
            $total = $jsonTicketData['Totals']['GrossAmount'];

            $discount = $jsonTicketData['Discounts'];

            if ($discount['DiscountRate'] == $discount['CashDiscount']) {
                $discountType = 'N/A';
            } else {
                $discountType = ($discount['DiscountRate'] > 0) ? 'percentage' : 'amount';
            }

            $discountAmount = ($discountType === 'percentage') ? $discount['DiscountRate'] * 100 : $discount['CashDiscount'];

            $discountConcept = 'N/A';
            if (!empty($jsonTicketData['Offers'])) {
                $discountConcept = $jsonTicketData['Offers'][0]['ApplicationMode'];
            }
        
            $jsonData = [
                "id" => $id,
                "close_time" => $close_time,
                "total" => $total,
                "discount_type" => $discountType,
                "discount_amount" => $discountAmount,
                "discount_concept" => $discountConcept,
                "lines" => []
            ];

            $linesData = $this->getLinesTickets();
            $filledLines = $this->fillLines($linesData);
            $jsonData["lines"] = $filledLines;
        }
    }
}
