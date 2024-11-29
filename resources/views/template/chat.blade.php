<div class="position-relative">
    <div class="chatboxO">
        <div class="banner_chatbox_o">
            <a class="chatboxO_a">
                <span class="banner_chatbox_text">Ask a question with ANISA</span>
                <span class="banner_chatbox_icon"><i class="fa-regular fa-magnifying-glass"></i></span>
            </a>
        </div>
    </div>

    <div class="chatbx_main">

     <!-- Car Details Card -->
            <div class="chatbx_secondary chatbx_car_details">
                <div class="chatbx_head">
                    <a class="btn_short"><i class="fa-solid fa-arrow-right-long-to-line"></i></a>
                </div>

                <!-- Car Details -->
                <!-- <div class="chatbx_window"> -->
                <div class="position-relative">
                    <div class="chatbx_car_details">
                        <img src="./assets/images/newjeep.jpg" id="image_car_detail" alt="car">
                        <div class="position-relative chatbx_cardetails_content">
                            <div class="position-relative text-center">
                                <h3 id="image_car_title" class="car_title">2022 Jeep Compass</h3>
                                <div class="chatbx_cardetails_loc">
                                    <i class="fa-solid fa-location-dot me-2"></i><span id="dealer_car_location">Oneida NY</span>
                                </div>
                            </div>
                            <div class="position-relative chatbx_cardetails_specs">
                                <ul class="row" id="car_attribute" >
                                    <?php  
                                        for ($x = 1; $x <= 6; $x++) {
                                    ?>
                                    <li class="col col-6">
                                        <div class="position-relative chatbx_cardetails_spec">
                                            <i class="fa-regular fa-square-check"></i>
                                            <div class="position-relative d-inline-block">
                                                <p><small>Body Type</small></p>
                                                <p>Sport utility</p>
                                            </div>
                                        </div>
                                    </li>
                                    <?php  
                                        }
                                    ?>
                                </ul>
                            </div>
                            <div class="chatbx_cardetails_price">
                                <h5><small>Dealer's Price</small>&nbsp;&nbsp;<span id="dealer_car_price">$40000</span></h5>
                            </div>
                            <div class="chatbx_cardetails_btn">
                                <a href=""  id="full_detail_href" target="_blank" class="btn_chatbx_fill w-100 mb-1">See Full Details</a>
                                <a class="btn_chatbx_fill w-100 chatbxreq_show_btn">Request Info</a>
                            </div>
                        </div>
                    </div>

                    <!-- Request Info Form -->
                    <div class="chatbx_req_info">
                        <div class="position-relative chatbx_req_info_inn">
                            <div class="chatbx_head position-relative pt-0 px-0">
                                <h5 class="mb-0">Request Information</h5>
                                <a class="chatbxreq_show_btn ms-auto"><i class="fa-solid fa-chevron-down"></i></a>
                            </div>
                            <div class="row">
                                <div class="col col-12">
                                    <div class="details_req_info">
                                        <form action="" id="validaterequest">
                                            <p><span>Hello, my name is</span>
                                            <input type="hidden" name="vid" id="vid"  value="" >
                                            <input type="text" name="first_name" id="first_name" value="{{ auth()->user()->first_name ??'' }}" placeholder="First Name" class="req_in_border required req_in_wsm">
                                            <input type="text"  name="last_name" id="last_name" value="{{ auth()->user()->last_name ??'' }}" placeholder="Last Name" class="req_in_border required req_in_wsm">
                                            <span>and I'm interested in this</span> 
                                            <span><b class="car_title"> s</b>. I'm in the</span>&nbsp;
                                            <input type="text"  placeholder="Zip code" name="zip_code" id="zip_code" class="req_in_border required req_in_wsm">
                                            <span>area. You can reach me by email at</span>&nbsp;
                                            <input type="text" name="email"  placeholder="Email"  value="{{ auth()->user()->email ??'' }}" class="req_in_border required email w-100">
                                            <span>or by phone at</span>&nbsp;
                                            <input type="text" name ="phone_number" placeholder="Phone" value="{{ auth()->user()->phone_number??'' }}" class="req_in_border required">
                                            <span>Thank you!</span></p>

                                            <a class="btn_chatbx_fill w-100 mt-2" onclick="saverequest()">Send</a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        <!-- Main Card -->
        <div class="chatbx_primary">
            <!-- Header -->
            <div class="chatbx_head">
                <img src="{{ asset('assets/images/anisa/ANISA_AI.png') }}" alt="img">
                <a class="full_screen ms-auto"><i class="fa-solid fa-expand"></i></a>
                <a class="mini_a ms-3"><i class="fa-solid fa-minus"></i></a>
                <a class="ms-3 chatboxO_a"><i class="fa-solid fa-xmark"></i></a>
            </div>

            <!-- Chat Window -->
            <div class="chatbx_window">
                <!-- Scrollable Chat Box -->
                <div class="chatbx_response">
                    <div class="chatbx_msg_l"><small>Hello, my name is <b>ANISA</b>, your AI auto concierge.How can I help you?</small></div>
                </div>
                <!-- Loader -->
                <div class="chatbx_msg_loader" id="loader" style="display:none;">
                    <div class="chatbx_msg_l chatbx_msg_loader_inn">
                        <small>Typing</small><div class="dot-pulse"></div>
                    </div>
                </div>
                
            </div>

            <!-- Close Confirmation -->
            <div class="chatbx_close_conf" style="display:none;">
                <div class="chatbx_close_msg">
                    <p><small>Are you sure? this action will delete your previous conversion?</small></p>
                    <div class="chatbx_closemsg_btns">
                        <a class="btn btn_chatbx chatbx_close_yes">Yes</a>
                        <a class="btn btn_chatbx chatbx_close_no">No</a>
                    </div>
                </div>
            </div>

            <!-- Search Input + Btn -->
            <div class="chatbx_input">
                <div class="input-group align-items-center flex-nowrap">
                    <input type="text" class="form-control mb-0 border-0" id="userInput" placeholder="Type any questions here...">
                    <a id="sendBtn" class="px-2"><i class="fa-solid fa-paper-plane-top"></i></a>
                </div>
                <small id="charCount" class="text-muted">0/2000</small>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="chattreqInfoConfirmationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-body">
            <div class="position-relative logout_confirmation_modal">
                <button type="button" class="btn-close modal_close_btn" data-bs-dismiss="modal" aria-label="Close"></button>

                <div class="row">
                    <div class="col col-12">
                        <div class="position-relative">
                            <div class="modal_heading">
                                <h3>Confirm</h3>
                            </div>

                            <div class="position-relative logout_icon_text">
                            <i class="fa-light fa-circle-check text-success"></i>
                                <h5>Your Request has been shared with respective dealer.</h5>
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
@push('after-scripts')
<script>
    $(document).on('click', '.chatbx_cars_slides .owl-next', function() {
        if ($('.chatbx_main').hasClass('collapsed')) {
            $('.chatbx_main').removeClass('collapsed');
        }
    });

    $(document).on('click', '.chatbx_cars_slides .owl-prev', function() {
        if ($('.chatbx_main').hasClass('collapsed')) {
            $('.chatbx_main').removeClass('collapsed');
        }
    });

    function initializeChatbot() {

        if ($('.chatbx_main').hasClass('fullScreen') == false) {
            var chatbxWindowHeight = $('.chatbx_primary').innerHeight() - ($('.chatbx_head').innerHeight() + $('.chatbx_input').innerHeight());
            $('.chatbx_window').css({'min-height': chatbxWindowHeight, 'max-height': chatbxWindowHeight});
            $('.chatbx_primary').innerWidth('350px');
            $('body').css('overflow', 'unset');
        } else {
            var chatbxWindowHeight = $(window).height() - ($('.chatbx_primary .chatbx_head').innerHeight() + $('.chatbx_primary .chatbx_input').innerHeight());
            $('.chatbx_window').css({'min-height': chatbxWindowHeight, 'max-height': chatbxWindowHeight});
            $('.chatbx_window').css('overflow-y', 'auto');
            $('body').css('overflow', 'hidden');

            if($(window).width() < 991 && $(window).width() > 600){
                var primaryWidth = $('.chatbx_main').innerWidth() / 1.6;
            } else if($(window).width() < 600){
                var primaryWidth = $('.chatbx_main').innerWidth();
            } else{
                var primaryWidth = $('.chatbx_main').innerWidth() / 2;
            }
            $('.chatbx_primary').innerWidth(primaryWidth);

        }

        if ($('.chatbx_main').hasClass('fullScreen') && $(window).width() > 1500) {
            $('.chatbx_cars_slides').trigger('destroy.owl.carousel');
            $('.chatbx_cars_slides').owlCarousel({
                items: 4,
                loop: true,
                margin: 10,
                dots: false,
                nav: true,
                navText: ["<i class='fa-solid fa-angle-left'></i>","<i class='fa-solid fa-angle-right'></i>"]
            });
        } else if ($('.chatbx_main').hasClass('fullScreen') && $(window).width() > 991) {
            $('.chatbx_cars_slides').trigger('destroy.owl.carousel');
            $('.chatbx_cars_slides').owlCarousel({
                items: 3,
                loop: true,
                margin: 10,
                dots: false,
                nav: true,
                navText: ["<i class='fa-solid fa-angle-left'></i>","<i class='fa-solid fa-angle-right'></i>"]
            });
        } else if ($('.chatbx_main').hasClass('fullScreen') && $(window).width() > 600) {
            $('.chatbx_cars_slides').trigger('destroy.owl.carousel');
            $('.chatbx_cars_slides').owlCarousel({
                items: 2,
                loop: true,
                margin: 10,
                dots: false,
                nav: true,
                navText: ["<i class='fa-solid fa-angle-left'></i>","<i class='fa-solid fa-angle-right'></i>"]
            });                            
        } else {
            $('.chatbx_cars_slides').trigger('destroy.owl.carousel');
            $('.chatbx_cars_slides').owlCarousel({
                items: 1,
                loop: true,
                margin: 10,
                dots: false,
                nav: true,
                navText: ["<i class='fa-solid fa-angle-left'></i>","<i class='fa-solid fa-angle-right'></i>"]
            });                            
        }

        // chat box cars slider
        // console.log('static');
        // $('.chatbx_cars_slides').owlCarousel({
        //     items: 1,
        //     loop: true,
        //     margin: 20,
        //     autoplay: false,
        //     autoplayTimeout: 3500,
        //     autoplayHoverPause: true,
        //     responsiveClass: false,
        //     dots: false,
        //     nav: true,
        //     navText: ["<i class='fa-solid fa-angle-left'></i>","<i class='fa-solid fa-angle-right'></i>"]
        // });
    }

    function resetchatbx() {
        $('body').css('overflow', 'unset');
        $('.chatbx_main').removeClass('collapsed');
        $('.chatbx_main').removeClass('fullScreen');
        $('.chatbx_req_info').removeClass('chatbxreq_show');
    }

    // Call the function to initialize chatbot on page load
    $(document).ready(function() {
        initializeChatbot();

        $('.chatboxO_a').click(function(e) {
           
            if($('.chatbx_main').hasClass('chat_open')){
                $('.chatbx_window').css('overflow-y', 'hidden');
                $('.chatbx_main').removeClass('collapsed');
                $('.chatbx_close_conf').css('display', 'block'); 
                  
            } else {
                $('.chatbx_main').addClass('chat_open');
            }
            hidePopup()    
            // $('.chatbx_main').toggleClass('chat_open');
            // resetchatbx();
            // initializeChatbot();
        });
        $('.chatbx_close_yes').click(function(e) {
            $('.chatbx_close_conf').css('display', 'none');        
            $('.chatbx_window').css('overflow-y', 'auto');
            $('.chatbx_main').removeClass('chat_open');
            $('.chatbx_response').html(` <div class="chatbx_msg_l"><small>Hello, my name is ANISA, your AI automotive concierge.</small></div>`);
             conversation_id = '';
             context = '';
             lastUserInput = '';
             lastFormData = {};
            resetchatbx();
            initializeChatbot();
        });
        $('.chatbx_close_no').click(function(e) {
            $('.chatbx_close_conf').css('display', 'none');        
            $('.chatbx_window').css('overflow-y', 'auto');
        });

        $('body').on('click', '.btn_expand', function(e) {
            vehicle  = JSON.parse($(this).attr('data_attr'));
            href  = ($(this).attr('data_href'));
            $('#full_detail_href').attr('href', href);
            console.log(vehicle);
            var image = vehicle?.['media']?.['photo_links']?.[0] ?? '';
            var title = `${vehicle?.['build']?.['year'] ?? ''} ${vehicle?.['build']?.['make'] ?? ''} ${vehicle?.['build']?.['model'] ?? ''}`;
            var location = `${vehicle?.['dealer']?.['city'] ?? ''} ${vehicle?.['dealer']?.['state'] ?? ''}`;
            var price = vehicle?.['price'] ? `$${Math.floor(vehicle['price']).toLocaleString('en-US')}` : 'N/A';


            $('#image_car_detail').attr('src', image);
            $('.car_title').html(title);
            $('#dealer_car_location').html(location);
            $('#dealer_car_price').html(price);
            $('#car_attribute').html('');
            $('#vid').val(vehicle?.id ?? '');

            var vehicle_detail = '';

            vehicle_detail += `<li class="col col-6">
                <div class="position-relative chatbx_cardetails_spec">
                    <i class="fa-regular fa-square-check"></i>
                    <div class="position-relative d-inline-block">
                        <p><small>Body Type</small></p>
                        <p>${vehicle?.['build']?.['body_type'] ?? ''}</p>
                    </div>
                </div>
            </li>`;

            vehicle_detail += `<li class="col col-6">
                <div class="position-relative chatbx_cardetails_spec">
                    <i class="fa-regular fa-square-check"></i>
                    <div class="position-relative d-inline-block">
                        <p><small>Trim</small></p>
                        <p>${vehicle?.['build']?.['trim'] ?? ''}</p>
                    </div>
                </div>
            </li>`;

            vehicle_detail += `<li class="col col-6">
                <div class="position-relative chatbx_cardetails_spec">
                    <i class="fa-regular fa-square-check"></i>
                    <div class="position-relative d-inline-block">
                        <p><small>Engine</small></p>
                        <p>${vehicle?.['build']?.['engine'] ?? ''}</p>
                    </div>
                </div>
            </li>`;

            vehicle_detail += `<li class="col col-6">
                <div class="position-relative chatbx_cardetails_spec">
                    <i class="fa-regular fa-square-check"></i>
                    <div class="position-relative d-inline-block">
                        <p><small>Transmission</small></p>
                        <p>${vehicle?.['build']?.['transmission'] ?? ''}</p>
                    </div>
                </div>
            </li>`;

            vehicle_detail += `<li class="col col-6">
                <div class="position-relative chatbx_cardetails_spec">
                    <i class="fa-regular fa-square-check"></i>
                    <div class="position-relative d-inline-block">
                        <p><small>Fuel Type</small></p>
                        <p>${vehicle?.['build']?.['fuel_type'] ?? ''}</p>
                    </div>
                </div>
            </li>`;

            vehicle_detail += `<li class="col col-6">
                <div class="position-relative chatbx_cardetails_spec">
                    <i class="fa-regular fa-square-check"></i>
                    <div class="position-relative d-inline-block">
                        <p><small>Drive Train</small></p>
                        <p>${vehicle?.['build']?.['drivetrain'] ?? ''}</p>
                    </div>
                </div>
            </li>`;

            $('#car_attribute').html(vehicle_detail);

            $('.chatbx_main').addClass('collapsed');
        });
        // collapse sections
      

        $('.btn_short').click(function(e) {
            $('.chatbx_main').removeClass('collapsed');
        });

        $('.chatbxreq_show_btn').click(function(e) {
            $('.chatbx_req_info').toggleClass('chatbxreq_show');
        });

        $('.full_screen').click(function(e) {
            $('.chatbx_main').toggleClass('fullScreen');
            initializeChatbot();
        });

        // Minimize
        $('.mini_a').click(function(e) {
            if($('.chatbx_main').hasClass('chat_open')){       
                $('.chatbx_main').removeClass('chat_open');
            } else {
                $('.chatbx_main').addClass('chat_open');
            }
            // if($('.chatbx_main').hasClass('fullScreen')){
            //     $('.chatbx_main').removeClass('fullScreen');
            //     initializeChatbot();
            // }    
            // $('.chatbx_main').toggleClass('minimize');    
        });
    });

    var conversation_id = '';
    var context = '';
    var lastUserInput = '';
    var lastFormData = {};
    var resendBtn = '<a id="resendBtn" class="px-2"><i class="fa-solid fa-redo"></i></a>';
    
    $(document).ready(function() {
        $('#sendBtn').on('click', function() {
            if (!$(this).hasClass('chatbx_disable')) {
                sendUserMessage();
            }
        });

        $('#resendBtn').on('click', function() {
            resendLastMessage();
        });

        $('#userInput').on('keypress', function(e) {
            var userInput = $(this).val();
            $('#charCount').text(userInput.length + '/2000');

            if (userInput.length > 2000) {
                e.preventDefault();
                alert('Query should not be more than 2000 characters.');
            }

            if (e.which === 13) { // Enter key pressed
                if (!$('#sendBtn').hasClass('chatbx_disable')) {
                    sendUserMessage();
                }
            }
        });
    });


        function sendUserMessage() {
            var userInput = $('#userInput').val().trim();

            if (userInput === '') {
                return;
            }

            if (userInput.length > 2000) {
                alert('Query should not be more than 2000 characters.');
                return;
            }

            lastUserInput = userInput;

            // Append user's message to chatbox
            $('.chatbx_response').append('<div class="chatbx_msg_r"><small>' + userInput + '</small><span class="ch_time">' + getCurrentTime() + '</span></div>');
            $('#userInput').val('');
            $('#charCount').text('0/2000');

            // Show the loader
            $('#loader').show();
            $('#sendBtn').addClass('chatbx_disable');
            formData = {
                "request": userInput,
                "context": context,
                "conversation_id": conversation_id,
            };

            lastFormData = formData;

            url = '{{route("chat")}}';
            chatajax(url, formData, 'post', '', 'json', function(response) {
                $('#loader').hide();
               
                if (response) {
                    conversation_id = response.conversation_id;
                    if(response.rawoutput !='Thank you for the question. This topic is beyond my training with providing support for automotive shoppers. I suggest using different resources. Are you interested in purchasing a new or pre-owned vehicle?')
                    context += response.rawoutput;

                    // Parse and display the response in the chatbox
                    var responseText = formatApiResponse(response);
                    var html = response.html;

                    $('.chatbx_response').append('<div class="chatbx_msg_l"><small>' + responseText + '</small><span class="ch_time">' + getCurrentTime() + '</span></div>');
                    var carhtml = '';
                    if (html) {
                        carhtml = `<div class="chatbx_car_card_main">
                            <div class="owl-carousel owl-theme chatbx_cars_slides circular_nav">` + html + `</div>
                            <span class="ch_time">` + getCurrentTime() + `</span></div>`;
                        $('.chatbx_response').append(carhtml);
                    }
                    if (carhtml) {
                        $('.chatbx_cars_slides').trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
                        if ($('.chatbx_main').hasClass('fullScreen') && $(window).width() > 1500) {
                            $('.chatbx_cars_slides').trigger('destroy.owl.carousel');
                            $('.chatbx_cars_slides').owlCarousel({
                                items: 4,
                                loop: true,
                                margin: 10,
                                dots: false,
                                nav: true,
                                navText: ["<i class='fa-solid fa-angle-left'></i>","<i class='fa-solid fa-angle-right'></i>"]
                            });
                        } else if ($('.chatbx_main').hasClass('fullScreen') && $(window).width() > 991) {
                            $('.chatbx_cars_slides').trigger('destroy.owl.carousel');
                            $('.chatbx_cars_slides').owlCarousel({
                                items: 3,
                                loop: true,
                                margin: 10,
                                dots: false,
                                nav: true,
                                navText: ["<i class='fa-solid fa-angle-left'></i>","<i class='fa-solid fa-angle-right'></i>"]
                            });
                        } else if ($('.chatbx_main').hasClass('fullScreen') && $(window).width() > 600) {
                            $('.chatbx_cars_slides').trigger('destroy.owl.carousel');
                            $('.chatbx_cars_slides').owlCarousel({
                                items: 2,
                                loop: true,
                                margin: 10,
                                dots: false,
                                nav: true,
                                navText: ["<i class='fa-solid fa-angle-left'></i>","<i class='fa-solid fa-angle-right'></i>"]
                            });                            
                        } else {
                            $('.chatbx_cars_slides').trigger('destroy.owl.carousel');
                            $('.chatbx_cars_slides').owlCarousel({
                                items: 1,
                                loop: true,
                                margin: 10,
                                dots: false,
                                nav: true,
                                navText: ["<i class='fa-solid fa-angle-left'></i>","<i class='fa-solid fa-angle-right'></i>"]
                            });                            
                        }
                        $('.chatbx_cars_slides').addClass('owl-carousel owl-loaded');
                    }
                    console.log($('.chatbx_window')[0].scrollHeight);
                    setTimeout(function() {
                        $('.chatbx_window').scrollTop($('.chatbx_window')[0].scrollHeight);
                    }, 100);
                    $('.chatbx_window').scrollTop  = $('.chatbx_window')[0].scrollHeight;
                    $('.chatbx_window').scrollTop($('.chatbx_window')[0].scrollHeight);
                } else {
                    displayErrorMessage('Failed to fetch data from the API');
                }
                $('.chatbx_response').scrollTop($('.chatbx_response')[0].scrollHeight);
            });
        }

       

        function formatApiResponse(response) {
            if (response && response.response) {
                let data;
                    try {
                        data = JSON.parse(response.response);
                    } catch (e) {
                        console.error('Failed to parse response as JSON:', e);
                        return '<p>'+response.rawoutput+'</p>';
                    }

                    if (!data || typeof data !== 'object') {
                        return '<p>'+response.rawoutput+'</p>';
                    }
           
           
                console.log(data);
                let formattedResponse = '';

                if (data.heading) {
                    formattedResponse += `<h5>${data.heading}</h5>`;
                }

                if (data.subheading) {
                    formattedResponse += `<h6>${data.subheading}</h6>`;
                }

                if (data.paragraphs && data.paragraphs.length > 0) {
                    data.paragraphs.forEach(paragraph => {
                        formattedResponse += `<p>${paragraph}</p>`;
                    });
                }

                if (data.list && data.list.length > 0) {
                    formattedResponse += '<ul>';
                    data.list.forEach(item => {
                        formattedResponse += `<li>${item}</li>`;
                    });
                    formattedResponse += '</ul>';
                }

                return formattedResponse;
            }else{
                return response.rawoutput;
            }
            //return 'No response received from the API.';
            return 'No response received from the API.';
        }

       

       
  
function displayErrorMessage(message) {
        $('.chatbx_response').append('<div class="chatbx_msg_l"><small>' + message + '</small><span class="ch_time">' + getCurrentTime() + '</span></div>' + resendBtn);
        $('.chatbx_response').scrollTop($('.chatbx_response')[0].scrollHeight);

        $('#resendBtn').on('click', function() {
            resendLastMessage();
        });
}
function resendLastMessage() {
    if (lastUserInput !== '') {
        $('#userInput').val(lastUserInput);
        sendUserMessage();
    }
}
function getCurrentTime() {
    var now = new Date();
    return now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();
}
function saverequest()
    {

        url = '{{ route("visitStore") }}';
        formData = $('#validaterequest').serialize();
        if($('#validaterequest').valid()){
            runajax(url, formData, 'get', '', 'json', function(output) {
                if (output.success) 
                {
                    
                    $('#chattreqInfoConfirmationModal').modal('show');
                    gtag('event', 'conversion', {
                            'send_to': 'AW-931280000/WhSpCPfnpdcBEIDpiLwD',
                            'transaction_id': output.data.id // You can pass a transaction ID here if you have one
                        });
                    
                }else{
                    triggerobj = $(obj); 
                    $('#userLoginModal').modal('show');
                        
                }
            }); 
        }
}
</script>
@endpush