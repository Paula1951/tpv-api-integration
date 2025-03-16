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
        return $apiResponse;
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
        foreach ($linesData as $line) {
            $lineData = [
                'product_id' => $line['ProductId'],
                'name' => $line['ProductName'],
                'price' => $line['ProductPrice'],
                'quantity' => $line['Quantity'],
                'discount_type' => "",
                'discount_amount' => "",
                'discount_concept' => "",
                'tax_rate' => "",
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
        }
        return $lineData;
    }

    public function formating()
    {
        $ticketData = $this->getTicketsDay();

        if (is_string($ticketData)) {
            $id = uniqid();
            $jsonTicketData = json_decode($ticketData);
            $total = $jsonTicketData->Ticket->Totals->NetAmount;
    
            $jsonData = [
                "id" => $id,
                "close_time" => "",
                "total" => $total,
                "discount_type" => "",
                "discount_amount" => 0,
                "discount_concept" => "",
                "lines" => []
            ];

            $linesData = $this->getLinesTickets();
            $filledLines = $this->fillLines($linesData);
            $jsonData["lines"] = $filledLines;


        }
    }
}
