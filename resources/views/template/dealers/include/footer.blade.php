
<footer class="footer_section">
    <div class="container">
        <div class="footer_top">
            <div class="row">
                <div class="col col-lg-3 col-md-12 col-12">
                    <div class="footer_logo_dealer">
                        <div class="position-relative footer_logo">
                            @if(isset($setting['website_logo']) && !empty($setting['website_name'])) 
                                <img src="{{ $setting['website_logo'] }}" alt="{{ $setting['website_logo'] }}">
                            @else 
                                <img src="{{asset('assets/images/logo.png') }}" alt="Logo">
                            @endif
                        </div>
                        <!-- <div class="position-relative footer_dealer">
                            <a data-bs-toggle="modal" data-bs-target="{{ route('dealer.index') }}" class="btn"><i class="fa-solid fa-eye me-2"></i>Dealer</a>
                        </div> -->
                    </div>
                </div>
                <div class="col col-lg-6 col-md-12 col-12">
                    <div class="position-relative">
                        <div class="row">
                            <div class="col col-md-12 col-12">
                                <div class="footer_sec_head">
                                    <h5>Useful Links</h5>
                                </div>
                            </div>
                            <div class="col col-md-6 col-6">
                                <div class="useful_link_list">
                                    <ul>
                                        <li><a href="{{ route('vechile') }}?car_type=new">Explore New Cars</a></li>
                                        <li><a href="{{ route('vechile') }}?car_type=used">Explore Used Cars</a></li>
                                        <li><a href="{{ route('about') }}">About Us</a></li>
                                        <li><a href="{{ route('faq') }}">FAQ’s</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col col-md-6 col-6">
                                <div class="useful_link_list">
                                    <ul>
                                        <li><a href="{{ route('contactus') }}">Contact Us</a></li>
                                        <li><a href="{{ route('blog_list') }}">Blog</a></li>
                                        <li><a href="{{ route('media_list') }}">Press Release</a></li>
                                        <li><a href="{{ route('term') }}">Terms and Conditions</a></li>
                                        <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col col-lg-3 col-md-12 col-12">
                    <div class="footer_social_media">
                        <div class="footer_sec_head">
                            <h5>Connect with us</h5>
                        </div>
                        <div class="footer_social">
                            <ul>
                                @if(isset($setting['website_twitter']) && !empty($setting['website_twitter'])) 
                                    <li><a href="{{ $setting['website_twitter'] }}" class="tw"><i class="fa-brands fa-twitter   "></i></a></li>
                                @endif

                                @if(isset($setting['website_fb']) && !empty($setting['website_fb'])) 
                                    <li><a href="{{ $setting['website_fb'] }}" class="fb"><i class="fa-brands fa-facebook"></i></a></li>
                                @endif

                                @if(isset($setting['website_youtube']) && !empty($setting['website_youtube'])) 
                                    <li><a href="{{ $setting['website_youtube'] }}" class="fb"><i class="fa-brands fa-youtube"></i></a></li>
                                @endif
                                @if(isset($setting['website_linkedin']) && !empty($setting['website_linkedin'])) 
                                    <li><a href="{{ $setting['website_linkedin'] }}" class="ln"><i class="fa-brands fa-linkedin"></i></a></li>
                                @endif
                                
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="m-0">

        <!-- Copyright -->
        <div class="footer_copyright">
            <div class="row">
                <div class="col col-md-6 col-12">
                    <p>Copyright © {{date('Y')}} Carnext.autos</p>
                </div>
                <div class="col col-md-6 col-12 text-md-end">
                    <p>Design & developed by <a href="https://katharostechie.in/" target="_blank">Katharos Techie</a></p>
                </div>
            </div>
        </div>
    </div>
</footer>
<div id="loader-overlay" class="loader-overlay" style="display:none;">
    <div class="loader_inn">
        <i class="fa-duotone fa-tire"></i>
    </div>
</div>
@push ('after-scripts')

<script>
function viewCustomer(id){
    url = '{{ route("dealer.lead_detail") }}?id='+id;
    $('.modal').modal('hide');
    runajax(url, '', 'get', '', 'json', function(output) {
       
           $('#consumerDetailsModalbody').html(output.html);
           $('#consumerDetailsModal').modal('show');
    });
}
 function  viewCar(id){
   
    url = '{{ route("dealer.car_detail") }}?id='+id;
    $('.modal').modal('hide');
    runajax(url, '', 'get', '', 'json', function(output) {
       
           $('#cardetailbody').html(output.html);
           $('#carDetailsModal').modal('show');
            
          
  // Car slider with thumbnail
    var bigimage = $("#dealerbig");
    var thumbs = $("#dealerthumbs");
    //var totalslides = 10;
    var syncedSecondary = true;
  
    bigimage
      .owlCarousel({
      items: 1,
      slideSpeed: 2000,
      nav: false,
      autoplay: false,
      dots: false,
      loop: true,
      responsiveRefreshRate: 200,
    //   navText: [
    //     '<i class="fa fa-arrow-left" aria-hidden="true"></i>',
    //     '<i class="fa fa-arrow-right" aria-hidden="true"></i>'
    //   ]
    })
      .on("changed.owl.carousel", syncPosition);
  
    thumbs
      .on("initialized.owl.carousel", function() {
      thumbs
        .find(".owl-item")
        .eq(0)
        .addClass("current");
    })
      .owlCarousel({
        items: 5,
        dots: false,
        nav: true,
        navText: ["<i class='fa-solid fa-angle-left'></i>","<i class='fa-solid fa-angle-right'></i>"],
        smartSpeed: 200,
        slideSpeed: 500,
        slideBy: 4,
        margin: 10,
        responsiveRefreshRate: 100,
        responsiveClass: true,
        responsive: {
            0: {
                items: 3,
                margin: 6,
                nav: false
            },
            600: {
                items: 3,
                margin: 8,
            },
            1000: {
                items: 5,
                margin: 8,
            },
            1200: {
                items: 5
            }
        },
    })
      .on("changed.owl.carousel", syncPosition2);
  
    function syncPosition(el) {
      var count = el.item.count - 1;
      var current = Math.round(el.item.index - el.item.count / 2 - 0.5);
  
      if (current < 0) {
        current = count;
      }
      if (current > count) {
        current = 0;
      }
      thumbs
        .find(".owl-item")
        .removeClass("current")
        .eq(current)
        .addClass("current");
      var onscreen = thumbs.find(".owl-item.active").length - 1;
      var start = thumbs
      .find(".owl-item.active")
      .first()
      .index();
      var end = thumbs
      .find(".owl-item.active")
      .last()
      .index();
  
      if (current > end) {
        thumbs.data("owl.carousel").to(current, 100, true);
      }
      if (current < start) {
        thumbs.data("owl.carousel").to(current - onscreen, 100, true);
      }
    }
  
    function syncPosition2(el) {
      if (syncedSecondary) {
        var number = el.item.index;
        bigimage.data("owl.carousel").to(number, 100, true);
      }
    }
  
    thumbs.on("click", ".owl-item", function(e) {
      e.preventDefault();
      var number = $(this).index();
      bigimage.data("owl.carousel").to(number, 300, true);
    });

//   ==========

    }); 
    
}

</script>
<script>
    document.getElementById('keywordSelect').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex].text;
        var searchInput = document.getElementById('searchInput');
        searchInput.placeholder = selectedOption;
    });
</script>
@endpush