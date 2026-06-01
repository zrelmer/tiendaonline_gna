<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Panel de administración GNA Core">
<meta name="keywords" content="admin, GNA Core, dashboard">
<meta name="author" content="GNA Core">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="robots" content="noindex, nofollow">

<link rel="icon" href="{{ asset('assets/admin/images/favicon.png') }}" type="image/x-icon">
<link rel="shortcut icon" href="{{ asset('assets/admin/images/favicon.png') }}" type="image/x-icon">
<title>@yield('title', 'Panel de administración') — GNA Core</title>

<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('assets/admin/css/linearicon.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/vendors/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/vendors/themify.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/ratio.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/remixicon.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/vendors/feather-icon.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/vendors/scrollbar.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/vendors/animate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/vendors/bootstrap.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/vector-map.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/vendors/slick.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/style.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/custom-admin.css') }}">

@stack('styles')
