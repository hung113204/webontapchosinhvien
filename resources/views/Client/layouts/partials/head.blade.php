<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'IT Study Support – Lập trình C++')</title>

<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github-dark.min.css" />
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('frontend/asset/images/t2.png') }}">
<link rel="stylesheet" href="{{ asset('frontend/asset/css/style2.css') }}" />
<link rel="stylesheet" href="{{ asset('frontend/asset/css/hero-visual.css') }}" />
<link rel="stylesheet" href="{{ asset('frontend/asset/css/profile.css') }}" />
<link rel="stylesheet" href="{{ asset('frontend/asset/css/sections_style.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/asset/css/exam-extra.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/asset/css/subject-card-banner.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/asset/css/faq.css') }}">
<link rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>

@stack('styles')