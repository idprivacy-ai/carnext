@foreach($vehicle['listings'] as $key =>$item)

    
    <div class="item">
        <div class="chatbx_car_card">
            <img src="{{ $item['media']['photo_links'][0] ??'' }}" alt="car">
            <div class="chatbx_carcard_content">
                <h3>{{ $item['build']['year'] ??'' }} {{ $item['build']['make'] ??'' }} {{ $item['build']['model']??'' }} </h3>
                <div class="chatbx_carcard_loc_price">
                    <div class="chatbx_car_loc">
                        <i class="fa-solid fa-location-dot me-2"></i><span>{{$item['dealer']['city'] ??''}} {{$item['dealer']['state'] ??''}}</span>
                    </div>
                    @if(isset($item['price']))
                        <h5>${{ number_format($item['price']) }}</h5>
                    @else
                        <h5>N/A</h5>
                    @endif                                           
                </div>
            </div>
            <div class="chatbx_carcard_btns">
                <a class="btn_chatbx_card btn_expand" data_href="{{ route('vechile_detail',['id'=>$item['id']]) }}" data_attr= "{{ json_encode($item) }}">View Details</a>
                <a href="{{$weburl}}" target="_blank" class="btn_chatbx_card">Explore More</a>
            </div>
        </div>
    </div>
@endforeach