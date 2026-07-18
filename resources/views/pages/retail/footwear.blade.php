@extends('layouts.app')

@section('content')
@php
    $isWholesale = request()->query('segment') === 'wholesale';

    if ($isWholesale) {
        $product = [
            "eyebrow" => "Wholesale footwear software",
            "title" => "Wholesale footwear software for bulk orders, stock and dispatch.",
            "intro" => "Manage party-wise sales, bulk quantities, purchases, style-colour-size inventory, GST and trade accounts from one system.",
            "contactType" => "wholeSaleFootwear",
            "screenLabel" => "Wholesale footwear orders",
            "image" => "assets/img/footwear_soft0.jpg",
            "imageAlt" => "Medwin wholesale footwear order and inventory software preview",
            "imageTitle" => "Medwin Wholesale Footwear Software preview",
            "lightbox" => "wholesale_footwear",
            "imageApproved" => false,
            "preview" => [
                "eyebrow" => "Wholesale stock view",
                "title" => "Party order inventory",
                "columns" => ["Style", "Product", "Colour / Size", "Pairs"],
                "rows" => [
                    ["FW-104", "Classic Runner", "Black / 7-10", "96"],
                    ["FW-118", "Urban Sandal", "Tan / 6-9", "72"],
                    ["FW-205", "Daily Comfort", "Navy / 7-10", "84"],
                    ["FW-310", "Kids Active", "Blue / 1-5", "60"],
                ],
                "totalLabel" => "Order pairs",
                "totalValue" => "312",
            ],
            "capabilityTitle" => "Keep bulk footwear orders and variant stock under control.",
            "capabilityIntro" => "Organise wholesale billing, purchasing and inventory around every style, colour and size combination.",
            "capabilities" => [
                [
                    "icon" => "fas fa-file-invoice-dollar",
                    "title" => "Party-wise billing",
                    "description" => "Create wholesale invoices with trade rates, bulk quantities, discounts and GST details.",
                ],
                [
                    "icon" => "fas fa-boxes",
                    "title" => "Variant-wise bulk stock",
                    "description" => "Review available pairs by style, colour and size while preparing customer orders.",
                ],
                [
                    "icon" => "fas fa-truck-loading",
                    "title" => "Bulk purchase control",
                    "description" => "Record supplier purchases, import purchase data and handle purchase returns.",
                ],
                [
                    "icon" => "fas fa-chart-line",
                    "title" => "Trade accounts and reports",
                    "description" => "Review party balances, sales, purchases, stock, GST and profit reports together.",
                ],
            ],
            "processTitle" => "Move each wholesale order from purchase to dispatch.",
            "processIntro" => "Keep bulk inventory, party orders and trade billing connected throughout the day.",
            "process" => [
                [
                    "title" => "Receive stock in bulk",
                    "description" => "Record supplier purchases with style, colour, size, quantity, rate and GST details.",
                ],
                [
                    "title" => "Prepare party orders",
                    "description" => "Check variant-wise availability and organise the required pairs for each customer.",
                ],
                [
                    "title" => "Bill, dispatch and review",
                    "description" => "Complete wholesale invoices and review party, stock, sales and profit reports.",
                ],
            ],
            "ctaTitle" => "See how Medwin fits your wholesale footwear business.",
            "ctaText" => "Tell us how you manage suppliers, bulk orders, party billing and variant stock. Our team will contact you to discuss your requirements.",
        ];
    } else {
        $product = [
            "eyebrow" => "Retail footwear software",
            "title" => "Retail footwear software for faster billing and variant-wise stock.",
            "intro" => "Manage counter billing, barcodes, purchases and inventory by style, colour and size from one practical system.",
            "contactType" => "retailFootwear",
            "screenLabel" => "Retail footwear billing",
            "image" => "assets/img/footwear_soft0.jpg",
            "imageAlt" => "Medwin retail footwear billing and inventory software preview",
            "imageTitle" => "Medwin Retail Footwear Software preview",
            "lightbox" => "footwear_bill",
            "imageApproved" => false,
            "preview" => [
                "eyebrow" => "Retail stock view",
                "title" => "Footwear counter inventory",
                "columns" => ["Style", "Product", "Colour / Size", "Stock"],
                "rows" => [
                    ["FW-104", "Classic Runner", "Black / 9", "12"],
                    ["FW-118", "Urban Sandal", "Tan / 8", "9"],
                    ["FW-205", "Daily Comfort", "Navy / 7", "15"],
                    ["FW-310", "Kids Active", "Blue / 3", "11"],
                ],
                "totalLabel" => "Available pairs",
                "totalValue" => "47",
            ],
            "capabilityTitle" => "Make every retail footwear variation easier to manage.",
            "capabilityIntro" => "Keep counter billing, purchasing and stock organised around the product details your team uses every day.",
            "capabilities" => [
                [
                    "icon" => "fas fa-barcode",
                    "title" => "Faster counter billing",
                    "description" => "Search products by barcode and create customer bills quickly at the counter.",
                ],
                [
                    "icon" => "fas fa-shoe-prints",
                    "title" => "Style, colour and size stock",
                    "description" => "Review available pairs and reports across every footwear variation.",
                ],
                [
                    "icon" => "fas fa-file-import",
                    "title" => "Purchases and returns",
                    "description" => "Record purchases, import purchase data and handle customer or supplier returns.",
                ],
                [
                    "icon" => "fas fa-chart-line",
                    "title" => "Retail reports and accounts",
                    "description" => "Review counter sales, purchases, stock, GST and accounting reports together.",
                ],
            ],
            "processTitle" => "Keep every retail variation visible from purchase to sale.",
            "processIntro" => "Follow each pair through inward stock, barcode search, counter billing and reporting.",
            "process" => [
                [
                    "title" => "Add new stock",
                    "description" => "Enter or import purchases with style, colour, size, quantity and rate details.",
                ],
                [
                    "title" => "Find the right pair",
                    "description" => "Use barcode search and variant details to locate products faster for customers.",
                ],
                [
                    "title" => "Bill customers and review",
                    "description" => "Complete the counter sale and review stock, sales, purchase and GST reports.",
                ],
            ],
            "ctaTitle" => "See Medwin working at your retail footwear store.",
            "ctaText" => "Tell us how you manage counter billing, styles, sizes and stock. Our team will contact you to discuss your requirements.",
        ];
    }
@endphp

@include('inc.product-page', ['product' => $product])
@endsection
