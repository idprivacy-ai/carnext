@php
    if(in_array($item['id'], $fav)) {
        $favclass ='fas';
    }else{
        $favclass ='far';
    }
@endphp

<div class="car_card {{$class}}">
    <div class="like_share_icon">
        <!-- Toggle Favorite Button -->
        <div class="form-check fevCheck">
            <input type="checkbox" class="form-check-input" id="btn-check_{{ $item['id'] }}"  onclick="makeFavourite(this,'{{ $item['id'] }}','{{ $item['vin'] }}','{{ route('makeFavouite') }}')">
            <label class="form-check-label" for="btn-check_{{ $item['id'] }}"><i class="{{$favclass}} fa-heart" id="heart-icon_{{ $item['id'] }}"></i></label>
        </div>

        <!-- Share Icon -->
        <div class="share_icon">
          
            <i class="fa-regular fa-share-from-square"  onclick="showSharePopup(`{{ $item['id'] }}`, '{{ route('vechile_detail',['id'=>$item['id']]) }}', '{{ $item['build']['year'] ?? '' }} {{ $item['build']['make'] ?? '' }} {{ $item['build']['model'] ?? '' }}')"></i>
           
        </div>
    </div>

    <a href="{{ route('vechile_detail',['id'=>$item['id']]) }}" class="car_card_main">
        <div class="car_card_img">          
            <div class="bg-span"></div>                  
            <img src="{{ $item['media']['photo_links'][0] ??'' }}" alt="Car Image">
        </div>
        <div class="car_card_info">
            <div class="car_title_price">
                <h4>{{ $item['build']['year'] ??'' }} {{ $item['build']['make'] ??'' }} {{ $item['build']['model']??'' }} {{ $item['build']['trim'] ??'' }} {{ $item['build']['drivetrain'] ??'' }}</h4>
                <div class="location_price">
                    <div class="car_loc">
                        <i class="fa-solid fa-location-dot me-2"></i><span>{{$item['dealer']['city'] ??''}} {{$item['dealer']['state'] ??''}}</span>
                    </div>
                    @if(isset($item['price']))
                        <h5>${{ number_format($item['price']) }}</h5>
                    @else
                        <h5>N/A</h5>
                    @endif
            
                </div>
                
            </div>
            <hr>
            <div class="car_spec">
                <span class="car_spec_year">
                    @if(isset($item['miles']))
                        @if(($item['miles']< 10)&&  (isset($item['inventory_type']) && $item['inventory_type']=='used' ))
                           N/A
                        @else
                        {{ number_format($item['miles']) }}
                        @endif
                   
                    @else
                       N/A
                    @endif Miles
                </span>
                <div class="car_spec_info">
                    <span>
                        
                        {{ $item['build']['engine'] ?? 'N/A'  }} 
                    </span>&nbsp;|&nbsp;<span>{{ $item['build']['body_type'] ?? 'N/A'  }}</span>
                </div>
            </div>
        </div>
    </a>
</div>
@push ('after-scripts')

@endpush