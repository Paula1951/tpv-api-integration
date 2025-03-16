<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/SalesFormat.php';

echo "Processing sales format...\n";

$salesFormat = new SalesFormat();
$formattedSale = $salesFormat->formatSale();

echo "Sales format successfully completed.\n";
echo "Result:\n" . json_encode($formattedSale, JSON_PRETTY_PRINT);