<?php
include_once 'config/init.php';

$sale = new Sale;

//initializing front Page template for first page grid view
$template = new Template('templates/frontpage.php');

$template->title = 'Book Shop Sales';

//Sales object for access methods in view
$template->salesObj = $sale;

if (isset($_GET['filter'])) {
    $template->sales = $sale->getSalesByFilters($_GET['filter']);
} else {
    $template->sales = $sale->getAllSales();
}

//Upload JSON data to DB
if (isset($_GET['uploadJson'])) {
    $sale->updateDb();
}

//Load template
echo $template;
