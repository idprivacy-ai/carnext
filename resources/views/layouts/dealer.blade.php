<!DOCTYPE html>
<html lang="en">
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta name="csrf-token" content="{{ csrf_token() }}"/>
       
        <title>@yield('title', 'Buy New & Used Cars Online | Affordable Prices & Best Deals on Cars')</title>
        <meta name="description" content="@yield('meta_description', 'Find the best deals on new and used cars online. Browse our extensive inventory of certified pre-owned vehicles, affordable prices, and top car brands. Shop now!')">
        <meta property="og:title" content="@yield('title', 'Buy New & Used Cars Online | Affordable Prices & Best Deals on Cars')" />
        <meta property="og:description" content="@yield('meta_description', 'Find the best deals on new and used cars online. Browse our extensive inventory of certified pre-owned vehicles, affordable prices, and top car brands. Shop now!')" />
        <link rel="stylesheet" href="{{asset('assets/fonts/fontawesome/css/all.min.css') }}">
        <link rel="icon" href="{{asset('favicon.jpeg') }}" type="image/jpeg">
        <link rel="shortcut icon" href="{{asset('favicon.jpeg') }}" type="image/jpeg">
        <meta name="google-site-verification" content="jhZXMaIvED3Jyf-ZZ-HqJ4pxeySm3aqooG1n6AdjI7U" />
    <!-- Css -->
        <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{asset('assets/css/owl.carousel.min.css') }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="{{asset('assets/css/gallary.css') }}">
        <link rel="stylesheet" href="{{asset('assets/css/style.css') }}">
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="https://revlab.blob.core.windows.net/scripts/identityScript.js"></script>
        <script>  (async function () { new Image().src = `https://smart-pixl.com/12783/00001_carnext.autos_SMART.GIF?ref=${encodeURIComponent(window.location.href)}`; })(); </script>
        <!-- Meta Pixel Code -->

<script>

!function(f,b,e,v,n,t,s)

{if(f.fbq)return;n=f.fbq=function(){n.callMethod?

n.callMethod.apply(n,arguments):n.queue.push(arguments)};

if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';

n.queue=[];t=b.createElement(e);t.async=!0;

t.src=v;s=b.getElementsByTagName(e)[0];

s.parentNode.insertBefore(t,s)}(window, document,'script',

'https://connect.facebook.net/en_US/fbevents.js');

fbq('init', '439550295547349');

fbq('track', 'PageView');

</script>

<noscript><img height="1" width="1" style="display:none"

src=https://www.facebook.com/tr?id=439550295547349&ev=PageView&noscript=1

/></noscript>

<!-- End Meta Pixel Code -->
<!--  Hotjar Tracking Code for  -->
<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:5047838,hjsv:6};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
</script>
        <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-2EP84S4Z1Y"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-2EP84S4Z1Y');
</script>
      
        <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5J6S8TFP');</script>
<!-- End Google Tag Manager -->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5112323336088202"
     crossorigin="anonymous"></script>
      <script async src="https://www.googletagmanager.com/gtag/js?id=AW-931280000"></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'AW-931280000'); </script>
      <script>gtag('config', 'AW-931280000');</script>

    </head>
    <body>
    <style>
    .pac-container {
        z-index: 10000 !important;
    }
</style>
        <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5J6S8TFP"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
            @include('template.dealers.include.header')
            @include('template.dealers.include.popup')
            @include('template.dealers.include.cartpopup')
       
            @yield('content')

            @include('template.dealers.include.footer')
            
            <script src="{{asset('assets/js/popper.min.js') }}"></script>
            <script src="{{asset('assets/js/bootstrap.bundle.min.js') }}"></script>
            <script src="{{asset('assets/js/owl.carousel.min.js') }}"></script>
            <script src="{{asset('assets/js/offcanvas.js') }}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
            <script src="{{asset('assets/js/script.js') }}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" integrity="sha512-37T7leoNS06R80c8Ulq7cdCDU5MNQBwlYoy1TX/WUsLFC2eYNqtKlV0QjH7r8JpG/S0GUMZwebnVFLPd6SU5yg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script src="{{asset('assets/js/common.js') }}"></script>
            <script src="{{asset('assets/js/share.js') }}"></script>
            <script src="{{asset('assets/js/gallary.js') }}"></script>
            <script src="{{asset('assets/js/cart.js') }}"></script>
            <script defer src='https://data.processwebsitedata.com/cscripts/iST8UxZ3iB-57de8fc4.js'></script>

            <script src="https://revlab.blob.core.windows.net/scripts/identityScript.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // Check if there's a hash in the URL
                    const hash = window.location.hash;

                    if (hash) {
                        // Get the corresponding tab element
                        const tab = document.querySelector(`[href="${hash}"]`);

                        if (tab) {
                            // Activate the tab
                            tab.click();
                        }
                    }
                });

            </script>
            @stack('after-scripts')
            <!-- Start of HubSpot Embed Code -->

            <script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-
            scripts.com/46524199.js"></script>
          

            <!-- End of HubSpot Embed Code -->
    </body>
</html>