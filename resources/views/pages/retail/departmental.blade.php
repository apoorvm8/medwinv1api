@extends('layouts.app')

@section('content')
@php
    $product = [
        "eyebrow" => "Departmental store software",
        "title" => "Practical billing and inventory for busy stores.",
        "intro" => "Keep barcode billing, purchases, returns, stock, sales, profit reports, GST reports and accounting together in one practical system.",
        "contactType" => "retailSupermarket",
        "screenLabel" => "Departmental store billing",
        "image" => "assets/img/grocery_soft0.jpg",
        "imageAlt" => "Medwin departmental store billing software screen",
        "imageTitle" => "Medwin Departmental Store Software billing screen",
        "lightbox" => "departmental_bill",
        "imageApproved" => false,
        "preview" => [
            "eyebrow" => "Counter sale",
            "title" => "Departmental store billing",
            "columns" => ["Code", "Item", "Qty", "Amount"],
            "rows" => [
                ["1042", "Premium Basmati Rice", "2", "₹1,190"],
                ["2218", "Cooking Oil 1 L", "3", "₹510"],
                ["3145", "Toor Dal 1 kg", "2", "₹390"],
                ["4902", "Household Cleaner", "5", "₹645"],
            ],
            "totalLabel" => "Bill total",
            "totalValue" => "₹2,735",
        ],
        "capabilityTitle" => "Keep the counter moving and the numbers organised.",
        "capabilityIntro" => "Bring daily billing, stock movements and business reports into a clear working flow.",
        "capabilities" => [
            [
                "icon" => "fas fa-barcode",
                "title" => "Barcode billing",
                "description" => "Search products and create bills quickly with barcode support at the counter.",
            ],
            [
                "icon" => "fas fa-exchange-alt",
                "title" => "Purchases and returns",
                "description" => "Record purchases and keep sales returns and purchase returns properly accounted for.",
            ],
            [
                "icon" => "fas fa-boxes",
                "title" => "Stock reporting",
                "description" => "Review stock, sales and purchase reports to understand everyday movement.",
            ],
            [
                "icon" => "fas fa-chart-pie",
                "title" => "Profit, GST and accounts",
                "description" => "Use profit reports, GST reports and accounting records to review the business.",
            ],
        ],
        "processTitle" => "Keep daily store operations connected.",
        "processIntro" => "Follow each item through purchase, stock and sale without losing sight of the details.",
        "process" => [
            [
                "title" => "Record inward stock",
                "description" => "Enter purchases and update the stock records used by your team.",
            ],
            [
                "title" => "Bill at the counter",
                "description" => "Use barcode search to find products and complete customer bills efficiently.",
            ],
            [
                "title" => "Review performance",
                "description" => "Check stock, sales, purchase and profit reports with GST and accounting records.",
            ],
        ],
        "ctaTitle" => "See how Medwin fits your store.",
        "ctaText" => "Tell us about your counter, stock and reporting needs. Our team will contact you to discuss your requirements.",
    ];
@endphp

@include('inc.product-page', ['product' => $product])
@endsection
