<!DOCTYPE html>
<!---->
<html lang="{{ app()->getLocale() }}">

<!-- begin::Head -->

<head>

    <!--begin::Base Path (base relative path for assets of this page) -->
    <base href="../">
    <title>{{ __($pageTitle ?? '') }}</title>
    <!--end::Base Path -->
    <meta charset="utf-8" />
    {{-- <title>@yield('pageTitle') -
        {{ $settings['app_title'] ?? config('app.name', 'ResCode Admin') }}</title> --}}

    <meta name="description" content="Base form control examples">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Scripts from laravel layout-->
    <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->

    <!-- CSRF Token from laravel layout-->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php $dir = (session('locale')['dir'] == 'rtl') ? 'rtl' : '' ; @endphp
    @include('admin.layouts.styles')


</head>
<!-- end::Head -->






<!-- begin::Body -->

<body
    class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">
    <!-- begin:: Page -->


    <!-- begin:: Header Mobile -->
    <div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
        <x-logos.admin-logo-mobile />
        <div class="kt-header-mobile__toolbar">
            <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left"
                id="kt_aside_mobile_toggler"><span></span></button>
            <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler">
                <i class="flaticon-more"></i>
            </button>
        </div>
    </div>

    <!-- end:: Header Mobile -->
    <div class="kt-grid kt-grid--hor kt-grid--root">
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">

            <!-- begin:: Aside -->
            <button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
            <div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop"
                id="kt_aside">

                <!-- begin:: Aside -->
                <div class="kt-aside__brand kt-grid__item " id="kt_aside_brand">
                    <x-logos.admin-logo-web />
                    <div class="kt-aside__brand-tools">
                        <button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler">
                            <span><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                                        <path
                                            d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z"
                                            id="Path-94" fill="#000000" fill-rule="nonzero"
                                            transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) " />
                                        <path
                                            d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z"
                                            id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3"
                                            transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) " />
                                    </g>
                                </svg></span>
                            <span><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                                        <path
                                            d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z"
                                            id="Path-94" fill="#000000" fill-rule="nonzero" />
                                        <path
                                            d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z"
                                            id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3"
                                            transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) " />
                                    </g>
                                </svg></span>
                        </button>

                        <!-- <button class="kt-aside__brand-aside-toggler kt-aside__brand-aside-toggler--left" id="kt_aside_toggler"><span></span></button> -->
                    </div>
                </div>

                <!-- end:: Aside -->

                <!-- begin:: Aside Menu -->
                <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
                    <div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1"
                        data-ktmenu-dropdown-timeout="500">
                        <ul class="kt-menu__nav ">
                            <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a
                                    href="{{ route('admin.home') }}" class="kt-menu__link "><span
                                        class="kt-menu__link-icon">
                                    <img src="https://t3halal.jaderplus.com/storage/app/settings/cpanel.png" width="20px" height="20px">   
                                    </span><span
                                        class="kt-menu__link-text">{{ __('admin/dashboard.dashboard') }}</span></a></li>

                            @foreach ($menu_1  as $menu_item)
                            @if (isset($menu_item->children))
                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                data-ktmenu-submenu-toggle="hover"><a href="javascript:;"
                                    class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-icon">
                                    <img src="https://t3halal.jaderplus.com/storage/app/settings/{{$menu_item->privileg}}.png" width="20px" height="20px">   

                                    </span><span
                                        class="kt-menu__link-text">{{ __('admin/dashboard.' . $menu_item->privileg) }}</span><i
                                        class="kt-menu__ver-arrow la la-angle-left"></i></a>
                                <div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
                                    <ul class="kt-menu__subnav">
                                        @foreach ($menu_item->children as $menu_item_child)
                                        @if (isset($menu_item_child->children))
                                        <!-- nested -->
                                        @else
                                        <li class="kt-menu__item " aria-haspopup="true"><a
                                                href="{{ route($menu_item_child->route ?? 'admin.home', $menu_item_child->route_parameters) }}"
                                                class="kt-menu__link ">
                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                                                <!-- <img src="https://t3halal.jaderplus.com/storage/app/settings/{{$menu_item_child->privileg}}.png" width="20px" height="20px">    -->

                                                <span
                                                    class="kt-menu__link-text">{{ $menu_item_child->title }}</span></a>
                                            {{-- to manage title for multi lang --}}
                                            {{-- {{ __('admin/dashboard.' . $menu_item_child->privileg)  }} --}}
                                        </li>
                                        @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                            @else
                            <li class="kt-menu__item " aria-haspopup="true"><a
                                    href="{{ route($menu_item->route, $menu_item->route_parameters) }}"
                                    class="kt-menu__link ">
                                    <!-- <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i> -->
                                    <img src="https://t3halal.jaderplus.com/storage/app/settings/{{$menu_item_child->privileg}}.png" width="20px" height="20px">   

                                    <span  class="kt-menu__link-text">{{ $menu_item->title }}</span></a></li>
                            @endif
                            @endforeach

                        </ul>
                    </div>
                </div>

                <!-- end:: Aside Menu -->
            </div>

            <!-- end:: Aside -->
            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

                <!-- begin:: Header -->
                <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">


                    <!-- begin:: Header Menu -->
                    <button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i
                            class="la la-close"></i></button>
                    <div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">

                    </div>


                    <!-- end:: Header Menu -->
                        @php
                        $notification_count= \App\Models\Notification::where(['user_reciever_id'=>0,'read_at'=>null])->count();
                        @endphp
                    <!-- begin:: Header Topbar -->
                    <div class="kt-header__topbar">
                        <div class="kt-header__topbar-item kt-header__topbar-item--search dropdown"
                            id="kt_quick_search_toggle">
                            <div  id="clear_notification" class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">

                                <span
                                    class="kt-header__topbar-icon">
                                    <i @if ($notification_count > 0) style="color:red" @endif style="margin:5px ;" id="notifications_bell" class="fas fa-bell"></i>
                                    <span @if ($notification_count > 0) style="color:red;font-weight: 700;background-color: #f2f3f8;padding: 8px;border: 1px solid transparent;BORDER-RADIUS: 72PX;MARGIN: auto;" @endif  style="font-size: 15px;font-weight: 800;background-color: #f2f3f8;padding: 8px;border: 1px solid transparent;BORDER-RADIUS: 72PX;MARGIN: auto;" id="notifications_counter"  >{{$notification_count}}</span>
                                </span>
                            </div>
                            <div
                                class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-lg">
                                <form>
                                    <div class="kt-notification kt-margin-t-10 kt-margin-b-10 kt-scroll"
                                        id="mnu_notifications" data-scroll="true" data-height="300"
                                        data-mobile-height="200">

                                        @foreach(\App\Models\Notification::where(['user_reciever_id'=>0])->orderBy('id','desc')->limit(10)->get()
                                        as $notification)
                                        <a class="kt-notification__item" href="{{$notification->link}}">
                                            <div class="kt-notification__item-details">
                                                <div class="kt-notification__item-title">{{ __('messages.'.$notification->data,$notification->params)}}</div>
                                            </div>
                                        </a>
                                        @endforeach

                                    </div>
                                </form>
                            </div>
                        </div>

                        <!--begin: Notifications -->
                        {{-- <div class="kt-header__topbar-item dropdown">
                            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="30px,0px"
                                aria-expanded="true">
                                <span class="kt-header__topbar-icon kt-pulse kt-pulse--brand">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1"
                                        class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect id="bound" x="0" y="0" width="24" height="24" />
                                            <path
                                                d="M2.56066017,10.6819805 L4.68198052,8.56066017 C5.26776695,7.97487373 6.21751442,7.97487373 6.80330086,8.56066017 L8.9246212,10.6819805 C9.51040764,11.267767 9.51040764,12.2175144 8.9246212,12.8033009 L6.80330086,14.9246212 C6.21751442,15.5104076 5.26776695,15.5104076 4.68198052,14.9246212 L2.56066017,12.8033009 C1.97487373,12.2175144 1.97487373,11.267767 2.56066017,10.6819805 Z M14.5606602,10.6819805 L16.6819805,8.56066017 C17.267767,7.97487373 18.2175144,7.97487373 18.8033009,8.56066017 L20.9246212,10.6819805 C21.5104076,11.267767 21.5104076,12.2175144 20.9246212,12.8033009 L18.8033009,14.9246212 C18.2175144,15.5104076 17.267767,15.5104076 16.6819805,14.9246212 L14.5606602,12.8033009 C13.9748737,12.2175144 13.9748737,11.267767 14.5606602,10.6819805 Z"
                                                id="Combined-Shape" fill="#000000" opacity="0.3" />
                                            <path
                                                d="M8.56066017,16.6819805 L10.6819805,14.5606602 C11.267767,13.9748737 12.2175144,13.9748737 12.8033009,14.5606602 L14.9246212,16.6819805 C15.5104076,17.267767 15.5104076,18.2175144 14.9246212,18.8033009 L12.8033009,20.9246212 C12.2175144,21.5104076 11.267767,21.5104076 10.6819805,20.9246212 L8.56066017,18.8033009 C7.97487373,18.2175144 7.97487373,17.267767 8.56066017,16.6819805 Z M8.56066017,4.68198052 L10.6819805,2.56066017 C11.267767,1.97487373 12.2175144,1.97487373 12.8033009,2.56066017 L14.9246212,4.68198052 C15.5104076,5.26776695 15.5104076,6.21751442 14.9246212,6.80330086 L12.8033009,8.9246212 C12.2175144,9.51040764 11.267767,9.51040764 10.6819805,8.9246212 L8.56066017,6.80330086 C7.97487373,6.21751442 7.97487373,5.26776695 8.56066017,4.68198052 Z"
                                                id="Combined-Shape" fill="#000000" />
                                        </g>
                                    </svg>
                                    <span class="kt-pulse__ring"></span>
                                    <!-- notify -->
                                     <x-pusher.notify />

                                    <x-firebase.notify />
                                </span>


                                <!-- Use dot badge instead of animated pulse effect: -->
                                <span
                                    class="kt-badge kt-badge--dot kt-badge--notify kt-badge--sm kt-badge--brand"></span>

                            </div>
                            <div
                                class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-lg">
                                <form>
                                    <div class="kt-notification kt-margin-t-10 kt-margin-b-10 kt-scroll"
                                        id="mnu_notifications" data-scroll="true" data-height="300"
                                        data-mobile-height="200">



                                    </div>
                                </form>
                            </div>
                        </div> --}}


                        <!--begin: User Bar -->
                        <div class="kt-header__topbar-item kt-header__topbar-item--user">
                            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
                                <div class="kt-header__topbar-user">
                                    <span class="kt-header__topbar-username kt-hidden-mobile"></span>
                                    <span
                                        class="kt-header__topbar-username kt-hidden-mobile">{{ auth()->user()->name }}</span> <!--. auth()->user()->CurrentGenderTitle-->
                                    <span class="kt-header__topbar-welcome kt-hidden-mobile"> </span>

                                    {{-- <img class="kt-hidden" alt="Pic" src="./assets/media/users/300_25.jpg" /> --}}
                                    <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                                    {{-- <span
                                        class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">.</span>
                                    --}}
                                </div>
                            </div>
                            <div
                                class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">


                                <!--begin: Navigation -->
                                <div class="kt-notification">

                                    <div class="kt-notification__custom kt-space-between">
                                        <a href="{{ route('admin.logout') }}"
                                            onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                                            class="btn btn-label btn-label-brand btn-sm btn-bold">
                                            {{ __('auth.logout') }}</a>

                                            <a  class="btn btn-label btn-label-brand btn-sm btn-bold" href="{{route('admin.profile.edit')}}">@lang('words.profile')</a>
                                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                        </form>

                                        <!-- Roles -->
                                        @foreach (app('userRoles') as $userRole)
                                        @if ($userRole->id != $currentRole->id)
                                        <form action="{{ route('admin.roles.set_current_role') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name='id' value="{{ $userRole->id }}">
                                            <button type="submit"
                                                class="btn btn-label btn-label-brand btn-sm btn-bold">{{ $userRole->title }}</button>
                                        </form>
                                        @endif
                                        @endforeach

                                        <!-- <a href="demo1/custom/user/login-v2.html" target="_blank" class="btn btn-clean btn-sm btn-bold">Upgrade Plan</a> -->
                                    </div>
                                </div>

                                <!--end: Navigation -->
                            </div>
                        </div>

                        <!--end: User Bar -->
                    </div>

                    <!-- end:: Header Topbar -->
                </div>

                <!-- end:: Header -->
                <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content"
                    style="direction: {{ $dir }} ;">

                    <!-- begin:: Content Head -->
                    <div class="kt-subheader  kt-grid__item" id="kt_subheader">
                        <div class="kt-container  kt-container--fluid ">
                            <div class="kt-subheader__main">
                                <h3 class="kt-subheader__title">{{ __($pageTitle ?? '') }}
                                    <!-- from AdminController (base for all admin Controllers ) -->
                                </h3>
                                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                                <span class="kt-subheader__desc">{{ $recordsCount ?? '' }}</span>
                                <span class="kt-subheader__desc">{{ $subTitle ?? '' }}</span>

                                <!-- <a href="#" class="btn btn-label-warning btn-bold btn-sm btn-icon-h kt-margin-l-10">  Add New </a> -->
                                <div class="kt-input-icon kt-input-icon--right kt-subheader__search kt-hidden">
                                    <input type="text" class="form-control" placeholder="Search order..."
                                        id="generalSearch">
                                    <span class="kt-input-icon__icon kt-input-icon__icon--right">
                                        <span><i class="flaticon2-search-1"></i></span>
                                    </span>
                                </div>
                            </div>



                        </div>
                    </div>

                    <!-- end:: Content Head -->

                    <!-- begin:: Content -->
                    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid"
                        style="direction: {{ $dir }} ;">

                        <x-FlashAlert />
                        <!--Begin::Dashboard 1-->
                        @yield('content')



                        <!--End::Dashboard 1-->
                    </div>

                    <!-- end:: Content -->
                </div>


                <!-- begin:: Footer -->
                <div class="kt-footer  kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop" id="kt_footer">
                    <div class="kt-container  kt-container--fluid ">
                        <div class="kt-footer__copyright">
                            2020&nbsp;&copy;&nbsp;<a href="https://qrc.com.sa" target="_blank" class="kt-link">جميع
                                الحقوق محفوظة لمؤسسة رمز الاستجابة</a>
                        </div>
                        <div class="kt-footer__menu">
                            {{-- <a href="http://keenthemes.com/metronic" target="_blank"
                                class="kt-footer__menu-link kt-link">About</a>
                            <a href="http://keenthemes.com/metronic" target="_blank"
                                class="kt-footer__menu-link kt-link">Team</a>
                            <a href="http://keenthemes.com/metronic" target="_blank"
                                class="kt-footer__menu-link kt-link">Contact</a> --}}
                        </div>
                    </div>
                </div>


                <!-- end:: Footer -->
            </div>
        </div>
    </div>

    <!-- end:: Page -->



    <!-- end::Quick Panel -->

    <!-- begin::Scrolltop -->
    <div id="kt_scrolltop" class="kt-scrolltop">
        <i class="fa fa-arrow-up"></i>
    </div>

    <!-- end::Scrolltop -->





    @include('admin.layouts.scripts')


    <!--end::Page Scripts -->
</body>

<script type="module">

  // Import the functions you need from the SDKs you need

  import { initializeApp } from "https://www.gstatic.com/firebasejs/9.6.11/firebase-app.js";

  import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.6.11/firebase-analytics.js";

  // TODO: Add SDKs for Firebase products that you want to use

  // https://firebase.google.com/docs/web/setup#available-libraries


  // Your web app's Firebase configuration

  // For Firebase JS SDK v7.20.0 and later, measurementId is optional

  const firebaseConfig = {

    apiKey: "AIzaSyAbap-EaZl1sVGfyQDW7dvoVh__DKGMrKE",

    authDomain: "kafoo-5a4e8.firebaseapp.com",

    projectId: "kafoo-5a4e8",

    storageBucket: "kafoo-5a4e8.appspot.com",

    messagingSenderId: "132630262869",

    appId: "1:132630262869:web:b2830b612d1176c79350dc",

    measurementId: "G-38VCQXFXFP"

  };


  // Initialize Firebase

  const app = initializeApp(firebaseConfig);

  const analytics = getAnalytics(app);

</script>

<!-- end::Body -->

</html>
