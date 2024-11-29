<?php  for ($x = 0; $x < 10; $x++) {
     if(!isset($latest_car['listings'][$x]))
     {
        continue;
     }
   $item = $latest_car['listings'][$x] ;
     if(!isset($latest_car['listings'][$x]['media']['photo_links'][0]))
     {
        continue;
     }
    
    ?>

    <div class="item">
         @include('template.users.include.vehicle_item',['class'=>'','item'=>$item])
        <!--div class="car_card">
            <div class="like_share_icon">
               
                <div class="form-check fevCheck">
                    <input type="checkbox" class="form-check-input" id="btn-check" autocomplete="off">
                    <label class="form-check-label" for="btn-check"><i class="far fa-heart" id="heart-icon"></i></label>
                </div>

              
                <div class="share_icon">
                    <i class="fa-regular fa-share-from-square"></i>
                </div>
            </div>

            <a href="{{ route('vechile_detail',['id'=>$latest_car['listings'][$x]['id']]) }}" class="car_card_main">
                <div class="car_card_img">          
                    <div class="bg-span"></div>                  
                    <img src="{{ $latest_car['listings'][$x]['media']['photo_links'][0]?? 'No image' }}" alt="Car Image">
                </div>
                <div class="car_card_info">
                    <div class="car_title_price">
                        <h4>{{ $latest_car['listings'][$x]['build']['year']??'' }} {{ $latest_car['listings'][$x]['build']['make']??'' }} {{ $latest_car['listings'][$x]['build']['model']??'' }} {{ $latest_car['listings'][$x]['build']['trim']??'' }} {{ $latest_car['listings'][$x]['build']['drivetrain']??'' }}</h4>
                        @if(isset($latest_car['listings'][$x]['price']))
                            <h5>${{ number_format($latest_car['listings'][$x]['price']) }}</h5>
                        @else
                            <h5>N/A</h5>
                        @endif
                    </div>
                    <hr>
                    <div class="car_spec">
                        <span class="car_spec_year">{{ $latest_car['listings'][$x]['miles'] ?? 'N/A' }} Miles</span>
                        <div class="car_spec_info">
                            <span>{{ $latest_car['listings'][$x]['build']['engine'] ?? 'N/A' }} </span>&nbsp;|&nbsp;
                            <span>{{ $latest_car['listings'][$x]['build']['body_type'] ?? 'N/A' }}</span>
                            
                        </div>
                    </div>
                </div>
            </a>
        </div>-->
    </div>
<?php  } ?>