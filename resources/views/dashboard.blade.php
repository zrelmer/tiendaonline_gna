@extends('layouts.app')

@section('title', 'Usuario Dashboard')

@section('content')

<!-- Breadcrumb Section Start -->
<section class="breadcrumb-section pt-0">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-contain">
                    <h2>Usuario Dashboard</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">
                                    <i class="fa-solid fa-house"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Usuario Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- User Dashboard Section Start -->
<section class="user-dashboard-section section-b-space">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-xxl-3 col-lg-4">
                <div class="dashboard-left-sidebar">
                    <div class="close-button d-flex d-lg-none">
                        <button class="close-sidebar">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="profile-box">
                        <div class="cover-image">
                            <img src="../assets/images/inner-page/cover-img.jpg" class="img-fluid blur-up lazyload" alt="">
                        </div>

                        <div class="profile-contain">
                            <div class="profile-image">
                                <div class="position-relative">
                                    <img src="../assets/images/inner-page/user/5.png" class="blur-up lazyload update_img" alt="">
                                    
                                </div>
                            </div>

                            <div class="profile-name">
                                <h3>Vicki E. Pope</h3>
                                <h6 class="text-content">vicki.pope@gmail.com</h6>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-pills user-nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-dashboard-tab" data-bs-toggle="pill" data-bs-target="#pills-dashboard" type="button"><i data-feather="home"></i>
                                DashBoard</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-order-tab" data-bs-toggle="pill" data-bs-target="#pills-order" type="button"><i data-feather="shopping-bag"></i>Order</button>
                        </li>
                        
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-address-tab" data-bs-toggle="pill" data-bs-target="#pills-address" type="button" role="tab"><i data-feather="map-pin"></i>Address</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab"><i data-feather="user"></i>
                                Profile</button>
                        </li>
                       
                        <li class="nav-item" role="presentation"> 
                            <button class="nav-link" id="pills-security-tab" data-bs-toggle="pill" data-bs-target="#pills-security" type="button" role="tab"><i data-feather="shield"></i>
                                Privacy</button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-xxl-9 col-lg-8">
                <button class="btn left-dashboard-show btn-animation btn-md fw-bold d-block mb-4 d-lg-none">Show
                    Menu</button>
                <div class="dashboard-right-sidebar">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-dashboard" role="tabpanel">
                            <div class="dashboard-home">
                                <div class="title">
                                    <h2>My Dashboard</h2>
                                    <span class="title-leaf">
                                        <svg class="icon-width bg-gray">
                                            <use xlink:href="../assets/svg/leaf.svg#leaf"></use>
                                        </svg>
                                    </span>
                                </div>

                                <div class="dashboard-user-name">
                                    <h6 class="text-content">Hello, <b class="text-title">Vicki E. Pope</b></h6>
                                    <p class="text-content">From your My Account Dashboard you have the ability to
                                        view a snapshot of your recent account activity and update your account
                                        information. Select a link below to view or edit information.</p>
                                </div>

                                <div class="total-box">
                                    <div class="row g-sm-4 g-3">
                                        <div class="col-xxl-4 col-lg-6 col-md-4 col-sm-6">
                                            <div class="total-contain">
                                                <img src="../assets/images/svg/order.svg" class="img-1 blur-up lazyload" alt="">
                                                <img src="../assets/images/svg/order.svg" class="blur-up lazyload" alt="">
                                                <div class="total-detail">
                                                    <h5>Total Order</h5>
                                                    <h3>3658</h3>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-4 col-lg-6 col-md-4 col-sm-6">
                                            <div class="total-contain">
                                                <img src="../assets/images/svg/pending.svg" class="img-1 blur-up lazyload" alt="">
                                                <img src="../assets/images/svg/pending.svg" class="blur-up lazyload" alt="">
                                                <div class="total-detail">
                                                    <h5>Total Pending Order</h5>
                                                    <h3>254</h3>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-4 col-lg-6 col-md-4 col-sm-6">
                                            <div class="total-contain">
                                                <img src="../assets/images/svg/wishlist.svg" class="img-1 blur-up lazyload" alt="">
                                                <img src="../assets/images/svg/wishlist.svg" class="blur-up lazyload" alt="">
                                                <div class="total-detail">
                                                    <h5>Total Wishlist</h5>
                                                    <h3>32158</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="dashboard-title">
                                    <h3>Account Information</h3>
                                </div>

                                <div class="row g-4">
                                    <div class="col-xxl-6">
                                        <div class="dashboard-content-title">
                                            <h4>Contact Information <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editProfile">Edit</a>
                                            </h4>
                                        </div>
                                        <div class="dashboard-detail">
                                            <h6 class="text-content">MARK JECNO</h6>
                                            <h6 class="text-content">vicki.pope@gmail.com</h6>
                                            <a href="javascript:void(0)">Change Password</a>
                                        </div>
                                    </div>

                                    <div class="col-xxl-6">
                                        <div class="dashboard-content-title">
                                            <h4>Newsletters <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editProfile">Edit</a></h4>
                                        </div>
                                        <div class="dashboard-detail">
                                            <h6 class="text-content">You are currently not subscribed to any
                                                newsletter</h6>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="dashboard-content-title">
                                            <h4>Address Book <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editProfile">Edit</a></h4>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col-xxl-6">
                                                <div class="dashboard-detail">
                                                    <h6 class="text-content">Default Billing Address</h6>
                                                    <h6 class="text-content">You have not set a default billing
                                                        address.</h6>
                                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editProfile">Edit Address</a>
                                                </div>
                                            </div>

                                            <div class="col-xxl-6">
                                                <div class="dashboard-detail">
                                                    <h6 class="text-content">Default Shipping Address</h6>
                                                    <h6 class="text-content">You have not set a default shipping
                                                        address.</h6>
                                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editProfile">Edit Address</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-order" role="tabpanel">
                            <div class="dashboard-order">
                                <div class="title">
                                    <h2>My Orders History</h2>
                                    <span class="title-leaf title-leaf-gray">
                                        <svg class="icon-width bg-gray">
                                            <use xlink:href="../assets/svg/leaf.svg#leaf"></use>
                                        </svg>
                                    </span>
                                </div>

                                <div class="order-contain">
                                    <div class="order-box dashboard-bg-box">
                                        <div class="order-container">
                                            <div class="order-icon">
                                                <i data-feather="box"></i>
                                            </div>

                                            <div class="order-detail">
                                                <h4>Delivers <span>Pending</span></h4>
                                                <h6 class="text-content">Gouda parmesan caerphilly mozzarella
                                                    cottage cheese cauliflower cheese taleggio gouda.</h6>
                                            </div>
                                        </div>

                                        <div class="product-order-detail">
                                            <a href="product-left-thumbnail.html" class="order-image">
                                                <img src="../assets/images/vegetable/product/1.png" class="blur-up lazyload" alt="">
                                            </a>

                                            <div class="order-wrap">
                                                <a href="product-left-thumbnail.html">
                                                    <h3>Fantasy Crunchy Choco Chip Cookies</h3>
                                                </a>
                                                <p class="text-content">Cheddar dolcelatte gouda. Macaroni cheese
                                                    cheese strings feta halloumi cottage cheese jarlsberg cheese
                                                    triangles say cheese.</p>
                                                <ul class="product-size">
                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Price : </h6>
                                                            <h5>$20.68</h5>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Rate : </h6>
                                                            <div class="product-rating ms-2">
                                                                <ul class="rating">
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star"></i>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Sold By : </h6>
                                                            <h5>Fresho</h5>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Quantity : </h6>
                                                            <h5>250 G</h5>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="order-box dashboard-bg-box">
                                        <div class="order-container">
                                            <div class="order-icon">
                                                <i data-feather="box"></i>
                                            </div>

                                            <div class="order-detail">
                                                <h4>Delivered <span class="success-bg">Success</span></h4>
                                                <h6 class="text-content">Cheese on toast cheesy grin cheesy grin
                                                    cottage cheese caerphilly everyone loves cottage cheese the big
                                                    cheese.</h6>
                                            </div>
                                        </div>

                                        <div class="product-order-detail">
                                            <a href="product-left-thumbnail.html" class="order-image">
                                                <img src="../assets/images/vegetable/product/2.png" alt="" class="blur-up lazyload">
                                            </a>

                                            <div class="order-wrap">
                                                <a href="product-left-thumbnail.html">
                                                    <h3>Cold Brew Coffee Instant Coffee 50 g</h3>
                                                </a>
                                                <p class="text-content">Pecorino paneer port-salut when the cheese
                                                    comes out everybody's happy red leicester mascarpone blue
                                                    castello cauliflower cheese.</p>
                                                <ul class="product-size">
                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Price : </h6>
                                                            <h5>$20.68</h5>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Rate : </h6>
                                                            <div class="product-rating ms-2">
                                                                <ul class="rating">
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star"></i>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Sold By : </h6>
                                                            <h5>Fresho</h5>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Quantity : </h6>
                                                            <h5>250 G</h5>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="order-box dashboard-bg-box">
                                        <div class="order-container">
                                            <div class="order-icon">
                                                <i data-feather="box"></i>
                                            </div>

                                            <div class="order-detail">
                                                <h4>Delivere <span>Pending</span></h4>
                                                <h6 class="text-content">Cheesy grin boursin cheesy grin cheesecake
                                                    blue castello cream cheese lancashire melted cheese.</h6>
                                            </div>
                                        </div>

                                        <div class="product-order-detail">
                                            <a href="product-left-thumbnail.html" class="order-image">
                                                <img src="../assets/images/vegetable/product/3.png" alt="" class="blur-up lazyload">
                                            </a>

                                            <div class="order-wrap">
                                                <a href="product-left-thumbnail.html">
                                                    <h3>Peanut Butter Bite Premium Butter Cookies 600 g</h3>
                                                </a>
                                                <p class="text-content">Cow bavarian bergkase mascarpone paneer
                                                    squirty cheese fromage frais cheese slices when the cheese comes
                                                    out everybody's happy.</p>
                                                <ul class="product-size">
                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Price : </h6>
                                                            <h5>$20.68</h5>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Rate : </h6>
                                                            <div class="product-rating ms-2">
                                                                <ul class="rating">
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star"></i>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Sold By : </h6>
                                                            <h5>Fresho</h5>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Quantity : </h6>
                                                            <h5>250 G</h5>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="order-box dashboard-bg-box">
                                        <div class="order-container">
                                            <div class="order-icon">
                                                <i data-feather="box"></i>
                                            </div>

                                            <div class="order-detail">
                                                <h4>Delivered <span class="success-bg">Success</span></h4>
                                                <h6 class="text-content">Caerphilly port-salut parmesan pecorino
                                                    croque monsieur dolcelatte melted cheese cheese and wine.</h6>
                                            </div>
                                        </div>

                                        <div class="product-order-detail">
                                            <a href="product-left-thumbnail.html" class="order-image">
                                                <img src="../assets/images/vegetable/product/4.png" class="blur-up lazyload" alt="">
                                            </a>

                                            <div class="order-wrap">
                                                <a href="product-left-thumbnail.html">
                                                    <h3>SnackAmor Combo Pack of Jowar Stick and Jowar Chips</h3>
                                                </a>
                                                <p class="text-content">The big cheese cream cheese pepper jack
                                                    cheese slices danish fontina everyone loves cheese on toast
                                                    bavarian bergkase.</p>
                                                <ul class="product-size">
                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Price : </h6>
                                                            <h5>$20.68</h5>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Rate : </h6>
                                                            <div class="product-rating ms-2">
                                                                <ul class="rating">
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star" class="fill"></i>
                                                                    </li>
                                                                    <li>
                                                                        <i data-feather="star"></i>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Sold By : </h6>
                                                            <h5>Fresho</h5>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="size-box">
                                                            <h6 class="text-content">Quantity : </h6>
                                                            <h5>250 G</h5>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-address" role="tabpanel">
                            <div class="dashboard-address">
                                <div class="title title-flex">
                                    <div>
                                        <h2>My Address Book</h2>
                                        <span class="title-leaf">
                                            <svg class="icon-width bg-gray">
                                                <use xlink:href="../assets/svg/leaf.svg#leaf"></use>
                                            </svg>
                                        </span>
                                    </div>

                                    <button class="btn theme-bg-color text-white btn-sm fw-bold mt-lg-0 mt-3" data-bs-toggle="modal" data-bs-target="#add-address"><i data-feather="plus" class="me-2"></i> Add New Address</button>
                                </div>

                                <div class="row g-sm-4 g-3">
                                    <div class="col-xxl-4 col-xl-6 col-lg-12 col-md-6">
                                        <div class="address-box">
                                            <div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="jack" id="flexRadioDefault2" checked="">
                                                </div>

                                                <div class="label">
                                                    <label>Home</label>
                                                </div>

                                                <div class="table-responsive address-table">
                                                    <table class="table">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2">Jack Jennas</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Address :</td>
                                                                <td>
                                                                    <p>8424 James Lane South San Francisco, CA 94080
                                                                    </p>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>Pin Code :</td>
                                                                <td>+380</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Phone :</td>
                                                                <td>+ 812-710-3798</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="button-group">
                                                <button class="btn btn-sm add-button w-100" data-bs-toggle="modal" data-bs-target="#editProfile"><i data-feather="edit"></i>
                                                    Edit</button>
                                                <button class="btn btn-sm add-button w-100" data-bs-toggle="modal" data-bs-target="#removeProfile"><i data-feather="trash-2"></i>
                                                    Remove</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-4 col-xl-6 col-lg-12 col-md-6">
                                        <div class="address-box">
                                            <div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="jack" id="flexRadioDefault3">
                                                </div>

                                                <div class="label">
                                                    <label>Office</label>
                                                </div>

                                                <div class="table-responsive address-table">
                                                    <table class="table">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2">Terry S. Sutton</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Address :</td>
                                                                <td>
                                                                    <p>2280 Rose Avenue Kenner, LA 70062</p>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>Pin Code :</td>
                                                                <td>+25</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Phone :</td>
                                                                <td>+ 504-228-0969</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="button-group">
                                                <button class="btn btn-sm add-button w-100" data-bs-toggle="modal" data-bs-target="#editProfile"><i data-feather="edit"></i>
                                                    Edit</button>
                                                <button class="btn btn-sm add-button w-100" data-bs-toggle="modal" data-bs-target="#removeProfile"><i data-feather="trash-2"></i>
                                                    Remove</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-4 col-xl-6 col-lg-12 col-md-6">
                                        <div class="address-box">
                                            <div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="jack" id="flexRadioDefault4">
                                                </div>

                                                <div class="label">
                                                    <label>Neighbour</label>
                                                </div>

                                                <div class="table-responsive address-table">
                                                    <table class="table">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2">Juan M. McKeon</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Address :</td>
                                                                <td>
                                                                    <p>1703 Carson Street Lexington, KY 40593</p>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>Pin Code :</td>
                                                                <td>+78</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Phone :</td>
                                                                <td>+ 859-257-0509</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="button-group">
                                                <button class="btn btn-sm add-button w-100" data-bs-toggle="modal" data-bs-target="#editProfile"><i data-feather="edit"></i>
                                                    Edit</button>
                                                <button class="btn btn-sm add-button w-100" data-bs-toggle="modal" data-bs-target="#removeProfile"><i data-feather="trash-2"></i>
                                                    Remove</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-4 col-xl-6 col-lg-12 col-md-6">
                                        <div class="address-box">
                                            <div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="jack" id="flexRadioDefault5">
                                                </div>

                                                <div class="label">
                                                    <label>Home 2</label>
                                                </div>

                                                <div class="table-responsive address-table">
                                                    <table class="table">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2">Gary M. Bailey</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Address :</td>
                                                                <td>
                                                                    <p>2135 Burning Memory Lane Philadelphia, PA
                                                                        19135</p>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>Pin Code :</td>
                                                                <td>+26</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Phone :</td>
                                                                <td>+ 215-335-9916</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="button-group">
                                                <button class="btn btn-sm add-button w-100" data-bs-toggle="modal" data-bs-target="#editProfile"><i data-feather="edit"></i>
                                                    Edit</button>
                                                <button class="btn btn-sm add-button w-100" data-bs-toggle="modal" data-bs-target="#removeProfile"><i data-feather="trash-2"></i>
                                                    Remove</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-4 col-xl-6 col-lg-12 col-md-6">
                                        <div class="address-box">
                                            <div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="jack" id="flexRadioDefault1">
                                                </div>

                                                <div class="label">
                                                    <label>Home 2</label>
                                                </div>

                                                <div class="table-responsive address-table">
                                                    <table class="table">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2">Gary M. Bailey</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Address :</td>
                                                                <td>
                                                                    <p>2135 Burning Memory Lane Philadelphia, PA
                                                                        19135</p>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>Pin Code :</td>
                                                                <td>+26</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Phone :</td>
                                                                <td>+ 215-335-9916</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="button-group">
                                                <button class="btn btn-sm add-button w-100" data-bs-toggle="modal" data-bs-target="#editProfile"><i data-feather="edit"></i>
                                                    Edit</button>
                                                <button class="btn btn-sm add-button w-100" data-bs-toggle="modal" data-bs-target="#removeProfile"><i data-feather="trash-2"></i>
                                                    Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-profile" role="tabpanel">
                            <div class="dashboard-profile">
                                <div class="title">
                                    <h2>My Profile</h2>
                                    <span class="title-leaf">
                                        <svg class="icon-width bg-gray">
                                            <use xlink:href="../assets/svg/leaf.svg#leaf"></use>
                                        </svg>
                                    </span>
                                </div>

                                <div class="profile-detail dashboard-bg-box">
                                    <div class="dashboard-title">
                                        <h3>Profile Name</h3>
                                    </div>
                                    <div class="profile-name-detail">
                                        <div class="d-sm-flex align-items-center d-block">
                                            <h3>Vicki E. Pope</h3>
                                            <div class="product-rating profile-rating">
                                                <ul class="rating">
                                                    <li>
                                                        <i data-feather="star" class="fill"></i>
                                                    </li>
                                                    <li>
                                                        <i data-feather="star" class="fill"></i>
                                                    </li>
                                                    <li>
                                                        <i data-feather="star" class="fill"></i>
                                                    </li>
                                                    <li>
                                                        <i data-feather="star"></i>
                                                    </li>
                                                    <li>
                                                        <i data-feather="star"></i>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editProfile">Edit</a>
                                    </div>

                                    <div class="location-profile">
                                        <ul>
                                            <li>
                                                <div class="location-box">
                                                    <i data-feather="map-pin"></i>
                                                    <h6>Downers Grove, IL</h6>
                                                </div>
                                            </li>

                                            <li>
                                                <div class="location-box">
                                                    <i data-feather="mail"></i>
                                                    <h6>vicki.pope@gmail.com</h6>
                                                </div>
                                            </li>

                                            <li>
                                                <div class="location-box">
                                                    <i data-feather="check-square"></i>
                                                    <h6>Licensed for 2 years</h6>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="profile-description">
                                        <p>Residences can be classified by and how they are connected to
                                            neighbouring residences and land. Different types of housing tenure can
                                            be used for the same physical type.</p>
                                    </div>
                                </div>

                                <div class="profile-about dashboard-bg-box">
                                    <div class="row">
                                        <div class="col-xxl-7">
                                            <div class="dashboard-title mb-3">
                                                <h3>Profile About</h3>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tbody>
                                                        <tr>
                                                            <td>Gender :</td>
                                                            <td>Female</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Birthday :</td>
                                                            <td>21/05/1997</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Phone Number :</td>
                                                            <td>
                                                                <a href="javascript:void(0)"> +91 846 - 547 -
                                                                    210</a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Address :</td>
                                                            <td>549 Sulphur Springs Road, Downers, IL</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="dashboard-title mb-3">
                                                <h3>Login Details</h3>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tbody>
                                                        <tr>
                                                            <td>Email :</td>
                                                            <td>
                                                                <a href="javascript:void(0)">vicki.pope@gmail.com
                                                                    <span data-bs-toggle="modal" data-bs-target="#editProfile">Edit</span></a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Password :</td>
                                                            <td>
                                                                <a href="javascript:void(0)">●●●●●●
                                                                    <span data-bs-toggle="modal" data-bs-target="#editProfile">Edit</span></a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-xxl-5">
                                            <div class="profile-image">
                                                <img src="../assets/images/inner-page/dashboard-profile.png" class="img-fluid blur-up lazyload" alt="">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-security" role="tabpanel">
                            <div class="dashboard-privacy">
                                <div class="dashboard-bg-box">
                                    <div class="dashboard-title mb-4">
                                        <h3>Privacy</h3>
                                    </div>

                                    <div class="privacy-box">
                                        <div class="d-flex align-items-start">
                                            <h6>Allows others to see my profile</h6>
                                            <div class="form-check form-switch switch-radio ms-auto">
                                                <input class="form-check-input" type="checkbox" role="switch" id="redio">
                                                <label class="form-check-label" for="redio"></label>
                                            </div>
                                        </div>

                                        <p class="text-content">all peoples will be able to see my profile</p>
                                    </div>

                                    <div class="privacy-box">
                                        <div class="d-flex align-items-start">
                                            <h6>who has save this profile only that people see my profile</h6>
                                            <div class="form-check form-switch switch-radio ms-auto">
                                                <input class="form-check-input" type="checkbox" role="switch" id="redio2">
                                                <label class="form-check-label" for="redio2"></label>
                                            </div>
                                        </div>

                                        <p class="text-content">all peoples will not be able to see my profile</p>
                                    </div>

                                    <button class="btn theme-bg-color btn-md fw-bold mt-4 text-white">Save
                                        Changes</button>
                                </div>

                                <div class="dashboard-bg-box mt-4">
                                    <div class="dashboard-title mb-4">
                                        <h3>Account settings</h3>
                                    </div>

                                    <div class="privacy-box">
                                        <div class="d-flex align-items-start">
                                            <h6>Deleting Your Account Will Permanently</h6>
                                            <div class="form-check form-switch switch-radio ms-auto">
                                                <input class="form-check-input" type="checkbox" role="switch" id="redio3">
                                                <label class="form-check-label" for="redio3"></label>
                                            </div>
                                        </div>
                                        <p class="text-content">Once your account is deleted, you will be logged out
                                            and will be unable to log in back.</p>
                                    </div>

                                    <div class="privacy-box">
                                        <div class="d-flex align-items-start">
                                            <h6>Deleting Your Account Will Temporary</h6>
                                            <div class="form-check form-switch switch-radio ms-auto">
                                                <input class="form-check-input" type="checkbox" role="switch" id="redio4">
                                                <label class="form-check-label" for="redio4"></label>
                                            </div>
                                        </div>

                                        <p class="text-content">Once your account is deleted, you will be logged out
                                            and you will be create new account</p>
                                    </div>

                                    <button class="btn theme-bg-color btn-md fw-bold mt-4 text-white">Delete My
                                        Account</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- User Dashboard Section End -->
@endsection

