@extends('layouts.app')

@section('content')
@php
    $isWholesale = request()->query('segment') === 'wholesale';

    if ($isWholesale) {
        $product = [
            "eyebrow" => "Wholesale pharmacy software",
            "title" => "Wholesale pharmacy software for sales, stock and distribution.",
            "intro" => "Manage supplier purchases, customer orders, batch-wise inventory, expiry, GST billing and trade accounts from one connected system.",
            "contactType" => "wholeSalePharma",
            "screenLabel" => "Wholesale pharmacy sales",
            "image" => "assets/img/pharmacy-wholesale-sale-screen.png",
            "imageAlt" => "Medwin wholesale pharmacy sales and stock software screen",
            "imageTitle" => "Medwin Wholesale Pharmacy Software sales screen",
            "lightbox" => "wholesale_pharmacy_sale",
            "imageApproved" => true,
            "imageFit" => "contain",
            "capabilityTitle" => "Control every wholesale movement from purchase to dispatch.",
            "capabilityIntro" => "Keep trade rates, large inventories and customer transactions organised for daily wholesale work.",
            "capabilities" => [
                [
                    "icon" => "fas fa-file-invoice-dollar",
                    "title" => "Wholesale sales billing",
                    "description" => "Create trade invoices with item rates, quantities, discounts, GST and customer details.",
                ],
                [
                    "icon" => "fas fa-boxes",
                    "title" => "Batch-wise inventory",
                    "description" => "Track medicine stock, batches and expiry across purchases, orders and wholesale sales.",
                ],
                [
                    "icon" => "fas fa-truck-loading",
                    "title" => "Supplier purchases",
                    "description" => "Record supplier bills, purchase returns and changing rates with complete item details.",
                ],
                [
                    "icon" => "fas fa-chart-line",
                    "title" => "Trade accounts and reports",
                    "description" => "Review customer balances, sales, stock, GST and profit information in one place.",
                ],
            ],
            "processTitle" => "Move smoothly from supplier purchase to customer dispatch.",
            "processIntro" => "Keep purchasing, order fulfilment, billing and wholesale reporting connected.",
            "process" => [
                [
                    "title" => "Record supplier purchases",
                    "description" => "Capture supplier, batch, expiry, quantity, rate and GST details when stock arrives.",
                ],
                [
                    "title" => "Manage stock and orders",
                    "description" => "Check available inventory and prepare customer orders with the right trade rates.",
                ],
                [
                    "title" => "Bill, dispatch and review",
                    "description" => "Complete wholesale invoices and review customer, stock and business reports.",
                ],
            ],
            "ctaTitle" => "See how Medwin fits your wholesale pharmacy.",
            "ctaText" => "Tell us about your suppliers, customer orders and stock workflow. Our team will contact you to discuss your requirements.",
        ];
    } else {
        $product = [
            "eyebrow" => "Retail pharmacy software",
            "title" => "Retail pharmacy software for faster billing and stock control.",
            "intro" => "Manage counter sales, batch-wise medicine stock, expiry, purchases, returns, GST reports and accounts from one practical system.",
            "contactType" => "retailPharma",
            "screenLabel" => "Retail pharmacy billing",
            "image" => "assets/img/pharmacy_soft0.jpg",
            "imageAlt" => "Medwin retail pharmacy billing software screen",
            "imageTitle" => "Medwin Retail Pharmacy Software billing screen",
            "lightbox" => "bill",
            "imageApproved" => true,
            "capabilityTitle" => "Keep your retail counter and medicine stock working together.",
            "capabilityIntro" => "Give your team quick access to billing, batch, expiry and inventory details throughout the day.",
            "capabilities" => [
                [
                    "icon" => "fas fa-cash-register",
                    "title" => "Faster counter billing",
                    "description" => "Create and print customer bills with barcode support, MRP, GST and discounts.",
                ],
                [
                    "icon" => "fas fa-boxes",
                    "title" => "Batch and expiry tracking",
                    "description" => "Keep batch-wise medicine stock and expiry details visible while buying and selling.",
                ],
                [
                    "icon" => "fas fa-file-import",
                    "title" => "Purchases and returns",
                    "description" => "Record purchases, import purchase data and handle sales or purchase returns.",
                ],
                [
                    "icon" => "fas fa-calculator",
                    "title" => "GST and retail accounts",
                    "description" => "Review GST reports alongside ledgers, debtors, creditors and trial balance.",
                ],
            ],
            "processTitle" => "Connect inward stock with every customer bill.",
            "processIntro" => "Follow medicines from purchase entry through counter sales and daily reporting.",
            "process" => [
                [
                    "title" => "Receive medicine stock",
                    "description" => "Enter or import purchases with the relevant batch, expiry, quantity and rate details.",
                ],
                [
                    "title" => "Monitor batches and expiry",
                    "description" => "Use stock ledgers and expiry lists to keep the right medicines available.",
                ],
                [
                    "title" => "Bill customers and review",
                    "description" => "Complete counter bills and review daily sales, purchases and profit reports.",
                ],
            ],
            "ctaTitle" => "See Medwin working at your retail pharmacy.",
            "ctaText" => "Tell us about your counter billing and medicine stock workflow. Our team will contact you to discuss your requirements.",
        ];
    }
@endphp

@include('inc.product-page', ['product' => $product])
@endsection
