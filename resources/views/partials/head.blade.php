<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.png" type="image/png">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">

<!-- Meta Tags -->
<meta name="author" content="Samuel Dorkey Jr." />
<meta name="keywords" content="Q-Vault, Laravel, Livewire, Examination Papers, Past Papers, Student Portal, Admin Portal, Secure Uploads, Search Functionality, Analytics" />
<meta name="description" content="Q-Vault is a robust Laravel + Livewire application designed for managing and accessing past examination papers with ease. The system supports both administrative and student access levels, offering secure upload, search, and analytics features.">

<!-- Control the behavior of search engine crawling and indexing -->
<meta name="robots" content="index,follow"><!-- All Search Engines -->
<meta name="googlebot" content="index,follow"><!-- Google Specific -->

<!-- Identify the software used to build the document (i.e. - WordPress, Dreamweaver) -->
<meta name="generator" content="Laravel + Livewire" />

<!-- Gives a general age rating based on the document's content -->
<meta name="rating" content="General">

<!-- Geo tags -->
<meta name="ICBM" content="latitude, longitude">
<meta name="geo.position" content="latitude;longitude">
<meta name="geo.region" content="Ghana">
<meta name="geo.placename" content="Koforidua">

<!-- Provides information about an author or another person -->
<link rel="me" href="mailto:samueldorkeyjr@gmail.com">
<link rel="me" href="sms:+233257774919">

<!-- Open Graph Protocol -->
<meta property="og:site_name" content="Q-Vault">
<meta property="og:title" content="{{ $title ?? config('app.name') }}">
<meta property="og:type" content="website">
<meta property="og:title" content="Q-Vault is a robust Laravel + Livewire application designed for managing and accessing past examination papers with ease. The system supports both administrative and student access levels, offering secure upload, search, and analytics features.">
<meta property="og:image" content="https://example.com/image.jpg">
<meta property="og:image:alt" content="A description of what is in the image (not a caption)">
<meta property="og:description" content="Description Here">
<meta property="og:site_name" content="Site Name">
<meta property="og:locale" content="en_US">
<meta property="article:author" content="">

<!-- Add to home screen -->
<meta name="mobile-web-app-capable" content="yes">
<!-- end of meta tags! -->

<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
