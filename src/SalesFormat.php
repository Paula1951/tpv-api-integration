<?php

require_once __DIR__ . '/vendor/autoload.php';
use src\AgoraAPI;
use Dotenv\Dotenv;

class SalesFormat 
{
    public string $endpointUrl;

    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $ticketDay = getenv('TICKET_DAY');
        $enpointTicketDay = getenv('ENDPOINT_TICKET_DAY');

        $this->endpointUrl = $enpointTicketDay . urlencode($ticketDay);
    }

    public function getTicketsDay()
    {
        $agoraAPI = new AgoraAPI();
        $apiResponse = $agoraAPI->connectionApi($this->endpointUrl);
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

    public function buildLineData($line, $discountType, $discountAmount, $discountConcept, $taxRate)
    {
        return [
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
    }

    public function addModifiersToLine($lineData, $addins) {
        foreach ($addins as $addin) {
            $lineData['modifiers'][] = [
                'modifier_id' => $addin['ProductId'],
                'name' => $addin['ProductName'],
                'price' => $addin['ProductPrice'],
                'quantity' => 1
            ];
        }
        return $lineData;
    }


    public function fillLines($linesData) {
        $lines = [];

        foreach ($linesData as $line) {

            $discountType = $this->determineDiscountType($line);
            $discountAmount = ($discountType === 'percentage') ? $line['DiscountRate'] * 100 : $line['CashDiscount'];
            $discountConcept = $this->getDiscountConcept($line);

            $taxRate = $line['VatRate'] * 100;
             
            $lineData = $this->buildLineData(
                $line, 
                $discountType, 
                $discountAmount, 
                $discountConcept, 
                $taxRate
            );

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

    public function formatCloseTime($date)
    {
        return date('Y-m-d H:i:s', strtotime($date));
    }
    
    public function determineDiscountType($discount)
    {
        if ($discount['DiscountRate'] == $discount['CashDiscount']) {
            return 'N/A';
        }

        return ($discount['DiscountRate'] > 0) ? 'percentage' : 'amount';
    }

    public function getDiscountConcept($jsonTicketData)
    {
        if (!empty($jsonTicketData['Offers'])) {
            return $jsonTicketData['Offers'][0]['ApplicationMode'];
        }

        return 'N/A';
    }

    public function buildJsonData($id, $close_time, $total, $discountType, $discountAmount, $discountConcept)
    {
        return [
            "id" => $id,
            "close_time" => $close_time,
            "total" => $total,
            "discount_type" => $discountType,
            "discount_amount" => $discountAmount,
            "discount_concept" => $discountConcept,
            "lines" => []
        ];
    }

    public function formatSale()
    {
        $ticketData = $this->getTicketsDay();

        if (is_string($ticketData)) {
            $jsonTicketData = json_decode($ticketData);

            $id = uniqid();
            $close_time = $this->formatCloseTime($jsonTicketData['Date']);
            $total = $jsonTicketData['Totals']['GrossAmount'];

            $discount = $jsonTicketData['Discounts'];
            $discountType = $this->determineDiscountType($discount);
            $discountAmount = ($discountType === 'percentage') ? $discount['DiscountRate'] * 100 : $discount['CashDiscount'];
            $discountConcept = $this->getDiscountConcept($jsonTicketData);
        
            $jsonData = $this->buildJsonData(
                $id, 
                $close_time, 
                $total, 
                $discountType,
                $discountAmount, 
                $discountConcept
            );

            $linesData = $this->getLinesTickets();
            $filledLines = $this->fillLines($linesData);
            $jsonData["lines"] = $filledLines;

            return $jsonData;
        }

        return null;
    }
}
