@extends('layouts.dealer')
 
@section('content')
<!-- Home Banner -->
<section class="home_banner dealer_banner position-relative" id="home_banner">
    <div class="home_banner_bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col col-xl-8 col-lg-7 col-md-12 col-12 d-flex align-items-center">
                    <div class="banner_hero_text">
                        <h1>Find Your <span class="br"></span><span>Next</span> Car on <span class="br"></span>Car<span>Next</span>.Autos by <span class="br"></span>Using <span>Next</span>Gen AI</h1>
                    </div>
                </div>
                <div class="col col-xl-4 col-lg-5 col-md-8 col-12 d-flex align-items-center">
                    <div class="banner_search_car dealer_form">
                        <form action="{{ route('dealer.requestDemo') }}" id="request_demo">
                            @csrf
                            <!-- Tabs -->
                            <div class="position-relative text-center dealer_form_head">
                                <p><b>Request a demo</b></p>
                                <p>To learn about CarNext, please complete this form and we will contact you.</p>
                            </div>

                            <!-- Form -->
                            <div class="banner_search_form">
                                <div class="row">
                                    <div class="col col-12">
                                        <input id="dealership_name" name="dealership_name" type="text" class="form-control required" placeholder="Dealership Name">
                                    </div>
                                    <div class="col col-6">
                                        <input  id="first_name" name="first_name"  type="text" class="form-control required" placeholder="First Name">
                                    </div>
                                    <div class="col col-6">
                                        <input id="last_name" name="last_name"  type="text" class="form-control required" placeholder="Last Name">
                                    </div>
                                    <div class="col col-12">
                                        <input id="phone" name="phone"  type="text" class="form-control required" placeholder="Phone">
                                    </div>
                                    <div class="col col-12">
                                        <input id="email" name="email"  type="text" class="form-control required" placeholder="Work Email">
                                    </div>
                                    <div class="col col-12">
                                        <input id="website" name="website"  type="text" class="form-control required" placeholder="Dealership Website">
                                    </div>
                                    <div class="col col-12">
                                        <div class="position-relative text-center">
                                            <button type="button" class="btn btn_theme">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="home_banner_car">
            <img src="{{ asset('assets/images/car-silver.png') }}" alt="car">
        </div>
    </div>
</section>

<!-- Dealer About -->
<div class="dealer_about position-relative section_padding pb-0 bg_light">
    <div class="container">
        <div class="row">
            <div class="col col-12">
                <div class="position-relative">
                    <p>CarNext Autos is streamlining the car shopping process by harnessing the power of AI-driven intent-based search. By focusing on understanding what customers are looking for and tailoring their experience accordingly, the journey becomes smoother and more personalized. Revolutionizing how people discover, research, and purchase vehicles will lead to a more efficient and satisfying car buying experience for consumers and drive additional sales for our dealer body.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dealer Main Features -->
<div class="dealer_main_features position-relative section_padding bg_light">
    <div class="container">
        <div class="row">
            <div class="col col-12">
                <div class="section_heading">
                    <h2>CarNext Autos drives results for dealers by focusing on the following:</h2>
                </div>
            </div>
        </div>
        <div class="row g-0 align-items-center">
            <div class="col col-lg-4 col-12">
                <div class="featurebx_b featurebx_b_le">
                    <i class="fa-solid fa-robot"></i>
                    <h3>AI Features</h3>
                    <p>CarNext Autos provides an unified car shopping journey leveraging AI-Driven intent based search, research and purchase their NEXT vehicle. Join the CarNext Autos experience.</p>
                </div>
            </div>
            <div class="col col-lg-4 col-12">
                <div class="featurebx_o">
                    <i class="fa-solid fa-users"></i>
                    <h3>Private Party</h3>
                    <p>Identify the specific used car inventory inventory that you are searching for by leveraging CarNext Autos. Avoid the auctions and go straight to the consumer interested in selling their vehicle.</p>
                </div>
            </div>
            <div class="col col-lg-4 col-12">
                <div class="featurebx_b featurebx_b_ri">
                    <i class="fa-solid fa-dollar-sign"></i>
                    <h3>Pricing and ROI</h3>
                    <p>Providing dealer with the most cost-effective opportunity to showcase, inventory, provide AI tools for consumers and drive loads. 30-day out without setup fees.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials -->
<div class="position-relative about_testimonials dealer_testimonials">
    <div class="row">
        <div class="col col-12">
            <div class="testimonials_main">
                <div class="container">
                    <div class="row">
                        <div class="col col-lg-9 col-md-8 col-12">
                            <div class="position-relative">
                                <div class="section_heading">
                                    <h2>Testimonials</h2>
                                </div>
                                <div class="testimonials_slider_main">
                                    <div class="owl-carousel owl-theme testimonials_slider circular_nav">
                                        <div class="item">
                                            <div class="testimonial_slide">
                                                <h5><span>"</span>It will be great to see a marketplace where I can shop across multiple dealers and compare cars without needing to switch sites. This will save time and make car buying more efficient. <span>”</span></h5>
                                                <p>- Beth Coyne </p>
                                            </div>
                                        </div>
                                        <!--div class="item">
                                            <div class="testimonial_slide">
                                                <h5>Tom Jones, General Manager of Brooklyn Mitsubishi is excited to join the CarNext Autos revolution. Jones stated, <i>“I love the innovation being brought to the market by the CarNext team”. “How could I turn down an opportunity like CarNext?”</i></h5>
                                            </div>
                                        </div-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonials_car">
                        <img src="{{ asset('assets/images/about-car.png') }}" alt="Car" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  

<!-- Dealer Main Features -->
<div class="dealer_main_features position-relative section_padding">
    <div class="container">
        <div class="row">
            <div class="col col-12">
                <div class="section_heading">
                    <h2>Subscription Plan</h2>
                </div>
            </div>
        </div>

        <div class="position-relative subscription_plans">
            <div class="row justify-content-center">
            @php 
            $href = ($dealer && $dealer->id) ? route('dealer.subscription') : 'javascript:;';
            @endphp
                @foreach ($plans as $plan)
                           
                    @php 
                    $discount =0;
                        if($plan->name=='Monthly'){
                            $name ='/Monthly';
                        }
                        if($plan->name=='Quarterly'){
                            $name = '/Quarterly' ;
                            $discount = 599*3;
                        }
                        if($plan->name=='Free'){
                            $name = '' ;
                        }
                    @endphp

                    <div class="col col-lg-3 col-md-4 col-12">
                        <div class="subs_card">
                            <!-- Plan -->
                            <div class="subs_plan_head">
                                <h2>{{ $plan->name }}</h2>
                            </div>
                            <!-- Description -->
                            <div class="subs_plan_desc">
                              
                            </div>
                            <hr>
                            <!-- Features -->
                            <div class="subs_plan_features featureCollapse" style="height: 340px;">
                                <div class="subs_plan_features_inn" >
                                {!! $plan->description !!}
                                </div>
                                <a class="toggleButton">More..</a>
                            </div>
                            <!-- Price -->
                            <div class="subs_plan_price">
                                <h3>${{ number_format($plan->price) }}<span class="mx-2">@if($discount>0)<del>${{ number_format(( $discount ),0) }}</del> @endif</span><span>{{$name}}</span></h3>
                            </div>
                            <!-- Button and Subscription Info -->
                            @if ($plan->price>0)
                              {{--   @if ($subscription && $subscription->stripe_price == $plan->stripe_price_id)
                                    @if (!$subscription->canceled())
                                       If the subscription is active and not canceled 
                                    
                                        <a href="javascript:;" onclick="cancelpopup('{{ route('dealer.add_payment_method', ['plan_id' => $plan->id]) }}')" class="btn btn_theme w-100">Cancel Now</a>
                                        <p class="small text-muted pt-2 text-center w-100 mb-0">
                                            Next Payment on: {{ Carbon::createFromTimestamp($subscription->asStripeSubscription()->current_period_end)->toFormattedDateString() }}
                                        </p>
                                    @else
                                      
                                    
                                        <a href="javascript:;" onclick="showpopup('{{ route('dealer.add_payment_method', ['plan_id' => $plan->id]) }}','{{$plan->id}}')" class="btn btn_theme w-100">Renew Now</a>
                                        <p class="small text-muted pt-2 text-center w-100 mb-0">
                                            Ends on: {{ $subscription->ends_at->toFormattedDateString() }}
                                        </p>
                                    @endif
                                @else --}}
                                @if($dealer && $dealer->id)
                                    <a href="{{  $href}}"  class="btn btn_theme w-100">Subscribe Now</a>
                                @else
                                     <a href="{{  $href}}" data-bs-toggle="modal" data-bs-target="#userLoginModal"  class="btn btn_theme w-100">Subscribe Now</a>
                                @endif
                                    {{--  @endif--}}
                            @else
                                @if($dealer && $dealer->id)
                                    <a href="{{  $href}}"  class="btn btn_theme w-100">Subscribe Now</a>
                                @else
                                <a href="{{  $href}}" data-bs-toggle="modal" data-bs-target="#userLoginModal"  class="btn btn_theme w-100">Subscribe Now</a>
                                @endif
                          
                            @endif
                            
                        </div>
                    </div>

                    @endforeach
              
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reqInfoConfirmationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-body">
            <div class="position-relative logout_confirmation_modal">
                <button type="button" class="btn-close modal_close_btn" data-bs-dismiss="modal" aria-label="Close"></button>

                <div class="row">
                    <div class="col col-12">
                        <div class="position-relative">
                            
                            <div class="position-relative logout_icon_text">
                            <i class="fa-light fa-circle-check text-success"></i>
                                <h5>Your Request has been shared successfully.</h5>
                            </div>        
                            
                            <div class="position-relative text-center mb-3">
                                <a data-bs-dismiss="modal" class="btn btn_secondary_light">Ok</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        </div>
    </div>
</div>
@endsection

@push ('after-scripts')
<script>
    $('#request_demo button').click(function(e){
    e.preventDefault();
    $('#request_demo button').text('Please wait...');
    $('#request_demo button').attr('disabled','disabled');
    
    if($('#request_demo').valid())
    {    
        url = '{{route('dealer.requestDemo')}}';

        var formData = new FormData($('#request_demo')[0]);
        
        uploadajax(url, formData, 'post', '', 'json', function(output) {
            // var output = JSON.parse(res);
            $('#request_demo button').text('Save Address ');
            $('#request_demo button').removeAttr('disabled');
            if (output.success) 
            {
                $('#request_demo')[0].reset();	
                
               
                $('#reqInfoConfirmationModal').modal('show');
                
            }else{
                
                for (var key in output.data){
                
                    existvalue= $('#'+key).val();
                    jQuery.validator.addMethod(key+"error", function(value, element) {
                        return this.optional(element) || value !== existvalue;
                    }, jQuery.validator.format(output.data[key][0]));
                    $('#'+key).addClass(key+"error");
                    jQuery('#'+key).valid();
                }
                    
            }
        }); 
    }else{
        $('#request_demo button').text('Save Address');
        $('#request_demo button').removeAttr('disabled');
    }
});
</script>
@endpush