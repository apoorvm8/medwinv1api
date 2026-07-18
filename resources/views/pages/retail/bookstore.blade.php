@extends('layouts.app')

@section('content')
@php
    $product = [
        "eyebrow" => "Bookstore billing and inventory",
        "title" => "Find every title faster. Bill every sale with confidence.",
        "intro" => "Use ISBN search and author, publisher and genre-wise reports alongside billing, purchase import, stock, GST reports and accounting.",
        "contactType" => "retailBookstore",
        "screenLabel" => "Bookstore billing",
        "image" => "assets/img/bookstore_soft0.jpg",
        "imageAlt" => "Medwin bookstore billing software screen",
        "imageTitle" => "Medwin Bookstore Software billing screen",
        "lightbox" => "bookstore_bill",
        "imageApproved" => false,
        "preview" => [
            "eyebrow" => "Counter sale",
            "title" => "Bookstore billing",
            "columns" => ["ISBN", "Title", "Qty", "Amount"],
            "rows" => [
                ["97881", "Business Basics", "1", "₹499"],
                ["97893", "Mathematics Grade 5", "2", "₹798"],
                ["97800", "World Atlas", "1", "₹650"],
                ["97812", "English Grammar", "1", "₹425"],
            ],
            "totalLabel" => "Bill total",
            "totalValue" => "₹2,372",
        ],
        "capabilityTitle" => "Keep titles, purchases and sales easy to follow.",
        "capabilityIntro" => "Organise the information your team uses to find books, serve customers and review performance.",
        "capabilities" => [
            [
                "icon" => "fas fa-barcode",
                "title" => "ISBN search",
                "description" => "Find a title quickly with ISBN search when a customer is waiting at the counter.",
            ],
            [
                "icon" => "fas fa-book",
                "title" => "Title-focused reports",
                "description" => "Review books by author, publisher and genre for a clearer view of your catalogue.",
            ],
            [
                "icon" => "fas fa-file-import",
                "title" => "Billing and purchases",
                "description" => "Create bills, record stock and import purchase data within a familiar workflow.",
            ],
            [
                "icon" => "fas fa-chart-line",
                "title" => "Reports and accounts",
                "description" => "Review sales, purchase and profit reports together with GST reports and accounting.",
            ],
        ],
        "processTitle" => "Move smoothly from new stock to the final bill.",
        "processIntro" => "Keep title information and business records useful at every stage of the sale.",
        "process" => [
            [
                "title" => "Add purchases",
                "description" => "Record or import purchase data as new titles arrive at your store.",
            ],
            [
                "title" => "Find the right title",
                "description" => "Search by ISBN and use author, publisher or genre details to locate books faster.",
            ],
            [
                "title" => "Bill and review",
                "description" => "Complete the sale and use stock, sales and profit reports to review the business.",
            ],
        ],
        "ctaTitle" => "See how Medwin fits your bookstore.",
        "ctaText" => "Tell us how you manage titles, billing and stock today. Our team will contact you to discuss your requirements.",
    ];
@endphp

@include('inc.product-page', ['product' => $product])
@endsection
