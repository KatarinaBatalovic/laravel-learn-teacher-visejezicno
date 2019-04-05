<!DOCTYPE html>
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
        
        @yield('seo-title')
        
        @include('frontend.layout.partials.head')

        @yield('custom-css')
        
    </head>

    <body class="c-layout-header-fixed c-layout-header-mobile-fixed">
        
        @include('frontend.layout.partials.header')

        <!-- BEGIN: PAGE CONTAINER -->
        <div class="c-layout-page">
           
            @include('frontend.layout.partials.breadcrumb')
            
            <!-- BEGIN: PAGE CONTENT -->
            <!-- BEGIN: BLOG LISTING -->
            <div class="c-content-box c-size-md">
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            
                            @yield('content')
                            
                        </div>
                        <div class="col-md-3">
                            
                            @include('frontend.layout.partials.sidebar')
                            
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: BLOG LISTING  -->
            <!-- END: PAGE CONTENT -->
        </div>
        <!-- END: PAGE CONTAINER -->
        
        @include('frontend.layout.partials.footer')
        
        @yield('custom-js')
        
    </body>

</html>