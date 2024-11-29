<?php
 
namespace App\Http\Controllers;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use Illuminate\View\View;
use App\Models\Visit;
use App\Models\clickCallSms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use App\Http\Traits\ApiResponseTrait;
use App\Services\MarketcheckApiClient;
use Illuminate\Support\Facades\Cache;
use Stevebauman\Location\Facades\Location;
use App\Models\Ads;
use App\Models\Dealer;
use App\Models\DealerSource;

class VehicleController extends Controller
{
    use ApiResponseTrait;
    protected $marketcheckApiClient;

    public function __construct(MarketcheckApiClient $marketcheckApiClient)
    {
        $this->marketcheckApiClient = $marketcheckApiClient;
    }
    /**
     * Display the dashboard for the authenticated user.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request): View
    {
        $DealerSource = new DealerSource();
        #dd($request->token);
        $token = $request->token;
        //dd($token);
        $request->session()->forget('geolocation');
        $getAdress = $this->getAddress();
        $geolocationData = session('geolocation');
        $country = $geolocationData['country'];
        $state = $geolocationData['state'];
        $city = $geolocationData['cityName'];
        $zip = $geolocationData['zipCode'];
        $mylocation =['zip'=>$zip,'longitude'=>$geolocationData['longitude'],'latitude'=>$geolocationData['latitude'],'country' =>$country,'radius'=>50];
        $extraparam =['country'=>'US'];
        $range = ['0-10000', '10000-20000', '20000-30000', '30000-40000', '40000-1000000000'];
        foreach ($range as $key => $value) {
            $cacheKey = 'active_cars_' . $value;
            $startrange = Cache::remember($cacheKey, now()->addDay(), function () use ($value,$extraparam)  {
                $param =array_merge($extraparam,['price_range' => $value, 'rows' => 10, 'start' => 0]);
               
                return $this->marketcheckApiClient->searchActiveCars($param);
            });
          
            break;
            
        }
        
        $params['start'] = 0;
        $params['rows'] = 0;
        $params['facets'] = 'make';
        $params['make']= 'Toyota,Honda,Ford,Chevrolet,BMW,Mercedes-Benz,Subaru,Jeep,Volkswagen';


        $allbrand =   Cache::remember('all_brands', now()->addDay(),function () { 
            $all = $this->marketcheckApiClient->searchFacets(['facets'=>'make|0|1000,model|0|50']);
            usort($all['facets']['make'], function ($a, $b) {
                return strcmp($a['item'], $b['item']);
            });
            return $all;
            
        });


        $brandlist = Cache::remember('facets_brand_list_specific' , now()->addDay(), function () use ($params,$extraparam) {
            $params =array_merge($params,$extraparam);
            
            $brand= $this->marketcheckApiClient->searchFacets($params);

            return $returnbrand = $this->changeOrder($brand,explode(',',$params['make']),'make');
           
        });
       
        #dd($geolocationData);
        $nearbyDealers =[];
        if(!empty($geolocationData['latitude'])){
            $nearbyDealers = $DealerSource->getNearByDealer($geolocationData['latitude'], $geolocationData['longitude'],25);
            // dd($geolocationData,$nearbyDealers);
            if($nearbyDealers)
                $nearbyDealers['data']  = $this->marketcheckApiClient->getDealerSource(['rows'=>20,'start'=>0,'source'=>$nearbyDealers['source']]);
        }
        $params =[];
        $params['start'] = 0;
        $params['rows'] = 0;
        $params['facets'] = 'body_type';
        $params['body_type']= 'SUV,Sedan,Pickup,Minivan,Coupe,Hatchback,Convertible';
        
        $bodytypelist = Cache::remember('facets_body_list_specific', now()->addDay(), function () use ($params,$extraparam) {
            $params =array_merge($params,$extraparam);
               
            $brand= $this->marketcheckApiClient->searchFacets($params);

            return $returnbrand = $this->changeOrder($brand,explode(',',$params['body_type']),'body_type');
        });
        $body_type = ['SUV', 'Sedan', 'Pickup', 'Minivan', 'Coupe','Hatchback','Convertible'];
        $body_types_data=[];
        
        //foreach ($body_type as $key => $value) {
            $cacheKey = 'active_cars_SUV';

            $body_types_data = Cache::remember($cacheKey, now()->addDay(), function () use ($value,$extraparam) {
                $params =array_merge(['body_types' => 'SUV', 'rows' => 10, 'start' => 0],$extraparam);
               
                return $this->marketcheckApiClient->searchActiveCars($params);
            });
            
          
        //}
        
        $newpopular = Cache::remember('popular_new_cars', now()->addDay(), function () use($extraparam) {
            $params =array_merge(['car_type' => 'new'],$extraparam);
               
            return $this->marketcheckApiClient->getPopularMakeModel(['car_type' => 'new']);
        });
        
        $usedpopular = Cache::remember('popular_used_cars', now()->addDay(), function () use($extraparam) {
            $params =array_merge(['car_type' => 'used'],$extraparam);
            return $this->marketcheckApiClient->getPopularMakeModel(['car_type' => 'used']);
        });
        
        $brand = ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'BMW','Mercedes-Benz','Subaru','Jeep','Volkswagen'];
        foreach ($brand as $key => $value) {
            $cacheKey = 'active_cars_' . $value;
            #dd( $cacheKey );
            $firstbrand = Cache::remember($cacheKey, now()->addDay(), function () use ($value,$extraparam) {
                $params =array_merge(['make' => $value, 'rows' => 10, 'start' => 0],$extraparam);
                return $this->marketcheckApiClient->searchActiveCars($params);
            });
            break;
           
        }

        
        $latest_car = Cache::remember('latest_cars', now()->addDay(), function () use ($extraparam) {
            $params =array_merge(['sort_by' => 'dom', 'sort_order' => 'asc', 'dom_range'=>'15-60','rows' => 10, 'start' => 0],$extraparam);
            return $this->marketcheckApiClient->searchActiveCars($params);
        });
       
       
        
        $slot1 = Ads::where('page','home-page')->where('slot',1)->where('enabled',true)->first();
        $slot2 = Ads::where('page','home-page')->where('slot',2)->where('enabled',true)->first();
        $slot3 = Ads::where('page','home-page')->where('slot',3)->where('enabled',true)->first();
        
        $lifestyle = Cache::remember('lifestyle_data', now()->addDay(), function() {
            return $this->getAllLifeStyle();
        });
        
        // Cache the popular new car parameters for 60 minutes
        $popularnewparam = Cache::remember('popular_new_car_params', now()->addDay(), function() {
            return $this->marketcheckApiClient->getPopularMakeModelOnly(['car_type' => 'new']);
        });
        
        // Cache the popular used car parameters for 60 minutes
        $popularusedparam = Cache::remember('popular_used_car_params', now()->addDay(), function() {
            return $this->marketcheckApiClient->getPopularMakeModelOnly(['car_type' => 'used']);
        });
       
        return view('template.users.index', compact('allbrand','body_types_data','startrange','bodytypelist','firstbrand','range','latest_car',
        'brandlist','newpopular','usedpopular','slot1','slot2','slot3','lifestyle','popularnewparam','popularusedparam','getAdress','mylocation','token','nearbyDealers'));
    }

    /**
     * Get user location by IP
     *
     * @param  \Illuminate\Http\Request  $ip
     * @return address
     */


    public function changeOrder($bodytypelist, $desiredOrder,$facet){
            $orderedResults = [];
            $makes = $bodytypelist['facets'][$facet];
            $orderMap = array_flip($desiredOrder);
            // Use usort to order based on the map
            usort($makes, function ($a, $b) use ($orderMap) {
                return $orderMap[$a['item']] <=> $orderMap[$b['item']];
            });
            $bodytypelist['facets'][$facet] = $makes;
            return $bodytypelist;
    }

    public function getAddress($input=[])
    {
        $geolocationData = session('geolocation');
       // dd( $geolocationData); 

        $ipAddress = $this->get_client_ip();
      // $ipAddress ='67.247.37.166';
        $user = Auth::user();
        //dd( $user); 
        if(isset($input['latitude']) && !empty($input['latitude']) && !empty($input['longitude'])){
            $geolocationData = [
                
                'latitude' => $input['latitude'],
                'longitude' => $input['longitude'],
                'country' => $input['country']??'',
                'zipCode'=>$input['zip']??'',
                'state' => '',
                'city' => '',
                'cityName'=>'',
            ];
            session(['geolocation' => $geolocationData]);
        }else if($user ){
            if($user['country']='United States') $country ='US';
            
            $geolocationData = [ 
                'latitude' => $user['latitude'],
                'longitude' => $user['longitude'],
                'state' => $user['state'],
                'city' => $user['city'],
                'cityName'=>$user['city'],
                'zipCode'=>$user['zip_code'],
                'country' => $country,
            ];

            if(!isset($geolocationData['latitude']) || empty($geolocationData['latitude']) ){
               
                $position = Location::get($ipAddress);
               
                if ($position) {
                    // Prepare data for storage in the session
                    $geolocationData = [
                        'ip' => $position->ip,
                        'latitude' => $position->latitude,
                        'longitude' => $position->longitude,
                        'country' => $position->countryCode,
                        'state' => $position->regionCode,
                        'city' => $position->areaCode,
                        'cityName'=>$position->cityName,
                        'zipCode'=>$position->zipCode,
                        //'country' => 'US',
                    ];
                }else{
                    $geolocationData = [
                        'ip' => $ipAddress,
                        'latitude' => '',
                        'longitude' => '',
                        'country' => '',
                        'state' => '',
                        'city' => '',
                        'cityName'=>'',
                        'zipCode'=>'',
                        //'country' => 'US',
                    ];
                }
            }
          //  dd('inside',$geolocationData);
        } else{
          
            // Get the client's IP address from the request object
           if(!isset($geolocationData['latitude']) || empty($geolocationData['latitude']) ){
               
                $position = Location::get($ipAddress);
               
                if ($position) {
                    // Prepare data for storage in the session
                    $geolocationData = [
                        'ip' => $position->ip,
                        'latitude' => $position->latitude,
                        'longitude' => $position->longitude,
                        'country' => $position->countryCode,
                        'state' => $position->regionCode,
                        'city' => $position->areaCode,
                        'cityName'=>$position->cityName,
                        'zipCode'=>$position->zipCode,
                        //'country' => 'US',
                    ];
                }else{
                    $geolocationData = [
                        'ip' => $ipAddress,
                        'latitude' => '',
                        'longitude' => '',
                        'country' => '',
                        'state' => '',
                        'city' => '',
                        'cityName'=>'',
                        'zipCode'=>'',
                        //'country' => 'US',
                    ];
                }
            }
        }
      
        if($geolocationData['country']!='US'){
            $geolocationData = [
               
                'ip' => $ipAddress,
                'latitude' => '',
                'longitude' => '',
                'country' => '',
                'state' => '',
                'city' => '',
                'cityName'=>'',
                'zipCode'=>'',
            ];
        
            
        } 
      
        session(['geolocation' => $geolocationData]);
        
        return $geolocationData;
    }
    

    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    /**
     * Search active cars based on the provided parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function explodeSubarrays(&$array) {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            
        }
    }
    
    public function searchActiveCars(Request $request)
    {
        // Retrieve parameters from the request
        $price_range = ['0-10000', '10000-20000', '20000-30000', '30000-40000', '40000-50000','50000-60000','60000-70000','70000-1000000000'];
        $input =  $request->all();
        if(isset($input['year_range'])&& !empty($input['year_range'])) {
            $allyear = explode('-',$input['year_range']);
            for($i = $allyear[0] ;$i <= $allyear[1]; $i++)
            $input['year'][] = $i;
        }
        if((isset($input['min']) && !empty($input['min'])) && (isset($input['max'])&& !empty($input['max']))){
            $input['price_range'] = $input['min'].'-'.$input['max'];
        }else if((isset($input['min']) && !empty($input['min'])) && !(isset($input['max'])&& !empty($input['max']))){
            $input['price_range'] = $input['min'].'-10000000';
        }else if(!(isset($input['min']) && !empty($input['min'])) && (isset($input['max'])&& !empty($input['max']))){
            $input['price_range'] ='0-'.$input['max'];
        }
        if(isset($input['radius']) && $input['radius'] >500){
            $input['radius'] =500;
        }
        $exploded =$input;
        unset($exploded['zip']);
        $getAdress = $this->getAddress($input);
        
        $this->explodeSubarrays($exploded);
        $params = $exploded;
       
        
        $params['start'] =0;
        $params['rows'] =0;
        
        if(empty($request->life_style)){
            unset($_REQUEST['life_style']);
            unset($input['popular']);
        }
        if(empty($request->popular)){
            unset($_REQUEST['popular']);
            unset($input['life_style']);
        }
       
        $checking= ['life_style','popular','car_type','make','year','model','trim','transmission','body_type','fuel_type','interior_color','exterior_color','powertrain_type','high_value_features','seating_capacity','engine'];
        $checking= ['life_style','popular','make','year','model'];
        $keysToCheck = array_keys($input);
        $keysExist = !empty(array_intersect($keysToCheck, $checking));
        $colorData = config('constants.COLOR');
        $inputValues = [
            'latitude' => $input['latitude'] ?? null,
            'longitude' => $input['longitude'] ?? null,
            'radius' => $input['radius'] ?? null
        ];
        $inputparam = ['rows'=>0,'start'=>0,'facets'=>'seating_capacity,car_type,year|0|1000,make|0|1000,model|0|1000,trim|0|1000,drivetrain|0|1000,transmission|0|1000,cylinders|0|1000,fuel_type|0|1000,body_type|0|1000,vehicle_type|0|1000,doors|0|1000,engine|0|1000,interior_color|0|1000,exterior_color|0|1000,powertrain_type|0|1000,high_value_features|0|1000'];
        $inputparam = array_filter($inputValues) + $inputparam;
        $finalvalue = $this->marketcheckApiClient->searchFacets($inputparam);
        
        if(($keysExist)){
           
            $search =[];
           
            $checking= ['life_style','popular','make','year','model'];
            $inputparam = ['rows'=>0,'start'=>0];
            $inputparam['facets']='car_type|0|1000,make|0|1000,year|0|1000,model|0|1000,body_type|0|1000,fuel_type|0|1000,trim|0|1000,transmission|0|1000,interior_color|0|1000,exterior_color|0|1000,powertrain_type|0|1000,drivetrain|0|1000,high_value_features|0|1000,seating_capacity|0|1000,engine|0|1000,cylinders0|1000';
           
            foreach($checking as $key =>$value){
                    
                $inputValues = [
                    'year' => $exploded['year'] ?? null,
                    'latitude' => $input['latitude'] ?? null,
                    'longitude' => $input['longitude'] ?? null,
                    'radius' => $input['radius'] ?? null,
                    'car_type' => $exploded['car_type'] ?? null,
                    'make' => $exploded['make'] ?? null,
                    'fuel_type' => $exploded['fuel_type'] ?? null,
                    'body_type' => $exploded['body_type'] ?? null,
                    'trim' => $exploded['trim'] ?? null,
                    'transmission' => $exploded['transmission'] ?? null,
                    'interior_color' => $exploded['interior_color'] ?? null,
                    'exterior_color' => $exploded['exterior_color'] ?? null,
                    'powertrain_type' => $exploded['powertrain_type'] ?? null,
                    'high_value_features' => $exploded['high_value_features'] ?? null,
                    'seating_capacity' => $exploded['seating_capacity'] ?? null,
                    'drivetrain' => $exploded['drivetrain'] ?? null,
                    'engine' => $exploded['engine'] ?? null,
                    'model' => $exploded['model'] ?? null,
                ];  

                if(isset($_REQUEST['life_style'])  && !empty($_REQUEST['life_style'])){
                    $lifestyle = config('constants.LIFESTYLE.'.$_REQUEST['life_style']);
                    $inputValues = array_merge($inputValues, $lifestyle);
                    $params = array_merge($params, $lifestyle);
                }
                if(isset($_REQUEST['popular']) && !empty($_REQUEST['popular'])){
                    $popular =  $this->marketcheckApiClient->getPopularMakeModelOnly(['car_type' =>$_REQUEST['car_type']]);
                    $inputValues = array_merge($inputValues, $popular);
                    $params = array_merge($params, $popular);
                }
               
                if(isset($input[$value])){
                    unset($inputValues[$value]);
                    $inputValues[$value] =null;
                    $inputparam = array_filter($inputValues) + $inputparam;
                    unset($inputparam[$value]);
                    $allother = $this->marketcheckApiClient->searchFacets($inputparam );
                    
                    $find =0;
                    $search[$value] = $allother['facets'][$value]??[];
                    $searchitem =$input[$value];
                    if(!is_array($input[$value])){
                        $searchitem = explode(',',$input[$value]);
                    }
                
                    foreach($search[$value] as $row){
                    
                        if(in_array($row['item'],$searchitem)){
                            $find =1;
                            break;
                        }
                    }

                    if($find==0){
                        foreach($searchitem  as $key =>$row){
                            $search[$value][]=['item'=>$row,'count'=>0];
                        }
                    
                    }
                }else{
                    $inputparam = array_filter($inputValues) + $inputparam;
                    $allother = $this->marketcheckApiClient->searchFacets($inputparam );   
                    foreach($allother['facets'] as $key2 =>$row){
                        if(in_array($key2,$checking )){
                            if($key2 == $key){
                                continue;
                            }else{
                                $finalvalue['facets'][$key2] = $row;
                            }
                        }
                    }
                }
            }
            
          
            foreach($search as $key =>$value){
                $finalvalue['facets'][$key] = $value;
               
            }
            $inputparam = ['rows'=>0,'start'=>0,'facets'=>'make|0|1000'];
            if(!isset($finalvalue)){
                $inputparam = ['rows'=>0,'start'=>0,'facets'=>'seating_capacity,car_type,year|0|1000,make|0|1000,model|0|1000,trim|0|1000,drivetrain|0|1000,transmission|0|1000,cylinders|0|1000,fuel_type|0|1000,body_type|0|1000,vehicle_type|0|1000,doors|0|1000,engine|0|1000,interior_color|0|1000,exterior_color|0|1000,powertrain_type|0|1000,high_value_features|0|1000'];
            }
            
            $inputValues = [
                'car_type' => $input['car_type'] ?? null,
                'latitude' => $input['latitude'] ?? null,
                'longitude' => $input['longitude'] ?? null,
                'radius' => $input['radius'] ?? null
            ];
            if(!isset($finalvalue)){
                $inputparam = array_filter($inputValues) + $inputparam;
                $finalvalue = $this->marketcheckApiClient->searchFacets($inputparam );
            }
            
            $params['start'] =(isset($input['page'])?($input['page']*30 -30):0) ;
            $params['rows'] =30;
            //dd($params);
            if(isset($_REQUEST['Latest']) && !empty($_REQUEST['Latest'])){
                $params = array_merge($params, ['dom', 'sort_order' => 'asc', 'dom_range'=>'30-60']);
            }elseif(!isset($_REQUEST['sort_by'])){
                $input['sort_by'] ='price';
                $input['sort_order'] ='asc';
                $params = array_merge($params, ['sort_by' =>'price', 'sort_order' => 'asc']);
            }elseif(isset($_REQUEST['sort_by']) && empty($_REQUEST['sort_by'])){
                $input['sort_by'] ='price';
                $input['sort_order'] ='asc';
                $params = array_merge($params, ['sort_by' =>'price', 'sort_order' => 'asc']);
            }
            $vehiclelist = $this->marketcheckApiClient->searchActiveCars($params);

        }else{
            $inputValues = [
                'latitude' => $input['latitude'] ?? null,
                'longitude' => $input['longitude'] ?? null,
                'radius' => $input['radius'] ?? null
            ];
            /*$inputparam = ['rows'=>0,'start'=>0,'facets'=>'seating_capacity,car_type,year|0|1000,make|0|1000,model|0|1000,trim|0|1000,drivetrain|0|1000,transmission|0|1000,cylinders|0|1000,fuel_type|0|1000,body_type|0|1000,vehicle_type|0|1000,doors|0|1000,engine|0|1000,interior_color|0|1000,exterior_color|0|1000,powertrain_type|0|1000,high_value_features|0|1000'];
            $inputparam = array_filter($inputValues) + $inputparam;
            $finalvalue = $this->marketcheckApiClient->searchFacets($inputparam);*/
            
            $params['start'] =(isset($input['page'])?($input['page']*30 -30):0) ;
            $params['rows'] =30;
            if(isset($_REQUEST['Latest']) && !empty($_REQUEST['Latest'])){
                $params = array_merge($params, ['dom', 'sort_order' => 'asc', 'dom_range'=>'30-60']);
            }elseif(!isset($_REQUEST['sort_by'])){
                $input['sort_by'] ='price';
                $input['sort_order'] ='asc';
                $params = array_merge($params, ['sort_by' =>'price', 'sort_order' => 'asc']);
            }elseif(isset($_REQUEST['sort_by']) && empty($_REQUEST['sort_by'])){
                $input['sort_by'] ='price';
                $input['sort_order'] ='asc';
                $params = array_merge($params, ['sort_by' =>'price', 'sort_order' => 'asc']);
            }
            $vehiclelist = $this->marketcheckApiClient->searchActiveCars($params);
           
        }
        $paginator = new LengthAwarePaginator(
            $vehiclelist['listings'],
            ($vehiclelist['num_found']>1500 ? 1500 : $vehiclelist['num_found']) ,
            30,
            ($params['start']+30)/30,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $slot1 = Ads::where('page','car-list-page')->where('slot',1)->where('enabled',true)->first();
        $slot2 = Ads::where('page','car-list-page')->where('slot',2)->where('enabled',true)->first();
        $slot3 = Ads::where('page','car-list-page')->where('slot',3)->where('enabled',true)->first();

        #dd($finalvalue);
        return view('template.users.vehicle', compact('finalvalue','input','paginator','colorData','price_range','slot1','slot2','slot3'));
    }

    /**
     * Display the car data by make,price_range,body_type
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function getCarbytabbased(Request $request)
    {
        $key = key(request()->query());
        $param = $request->input();
        $param['start']=0;
        $param['rows']=10;
        $value = $param[$key];
        $geolocationData = session('geolocation');
        if(!empty($geolocationData)){
            $geolocationData = $this->getAddress();;
        }
        $country = $geolocationData['country']??'';
        $state = $geolocationData['state']??'';
        $city = $geolocationData['cityName']??'';
        $zip = $geolocationData['zipCode']??'';
        $mylocation =['zip'=>$zip,'longitude'=>$geolocationData['longitude']??'','latitude'=>$geolocationData['latitude']??'','country' =>$country??'','radius'=>50];
        //$mylocation =['zip'=>$zip,'longitude'=>$geolocationData['longitude'],'latitude'=>$geolocationData['latitude'],'radius'=>50];
        $extraparam =['country'=>$country];
        if($key == 'source'){
            $cacheKey = 'active_cars_' . $value;
            $latest_car = Cache::remember($cacheKey, now()->addDay(), function () use ($param,$extraparam) {
              
                $param['start']=0;
                $param['rows']=10;
              
                return $this->marketcheckApiClient->getDealerSource($param);
                
            });
          
        }else if($key == 'life_style'){
            $cacheKey = 'active_cars_' . $value;
            $param = config('constants.LIFESTYLE.'.$value);
            Cache::forget($cacheKey);
            #dd($key,$param,$cacheKey );
            $latest_car = Cache::remember($cacheKey, now()->addDay(), function () use ($param,$extraparam) {
              
                $param['start']=0;
                $param['rows']=10;
                return $this->marketcheckApiClient->searchActiveCars($param);
                
            });
          
        }else{
            $cacheKey = 'active_cars_' . $value;
            $latest_car = Cache::remember($cacheKey, now()->addDay(), function () use ($param,$extraparam) {
                $params = array_merge($param);
                return $this->marketcheckApiClient->searchActiveCars($params);
            });
        }
        if($key == 'source'){
            $link= '  <a href="'. route('source.vechile',array_merge(['name'=> $param['source_name'] ,$key=>$value ])).' " class="btn btn_theme ">View  All </a>';
        }
        else if($key == 'price_range'){
            
                $link= '  <a href="'. route('vechile',array_merge($mylocation,[$key=>$value ])).' " class="btn btn_theme ">View  All </a>';
        }else  if($key == 'life_style'){
           // $link= '  <a href="'. route('vechile', config('constants.LIFESTYLE.'.$value)).' " class="btn btn_theme ">View  All </a>';
           $link= '  <a href="'. route('vechile',array_merge($mylocation,['life_style'=>$value])).' " class="btn btn_theme ">View  All </a>';
        }else{
           
            $link= '  <a href="'. route('vechile',array_merge($mylocation,[$key.'[]'=>$value ])).' " class="btn btn_theme ">View All </a>';
        }
        $html = view('template.users.include.item', compact('latest_car'))->render();
        return response()->json(['html' => $html,'total_number'=>$link]);
        
    }

    public function getPopularBrand(){
        return $popularmake = $this->getPopularMakeModelOnly();

    }

    public function getLifestyle($lifestyle ='FAMILY'){
        $param = config('constants.LIFESTYLE.'.$lifestyle);
        $param['start']=0;
        $param['rows']=10;
        return $this->marketcheckApiClient->searchActiveCars($param);
    }

    public function getAllLifeStyle(){
        $lifestyle = config('constants.LIFESTYLE');
        $row =[];
        foreach($lifestyle as $key =>$item){
            
            $row[$key] =  $this->marketcheckApiClient->searchActiveCars($item);
            $row[$key]['param'] = $item;
        }
       
        return $row;
    }

    

     /**
     * Get listing details for a vehicle based on the VIN and VID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $vin
     * @return \Illuminate\Contracts\View\View
     */
    public function getVehicleDetail(Request $request)
    {
       
        $vin_id = $request->id;
        $vehicle = $this->marketcheckApiClient->getListing($vin_id);
       # dd($vehicle);
        $user = Auth::user();
        if($user){
            $user_id = $user->id;
        }
        // $getAdress = $this->getAddress();
        $params['vins'] = $vehicle['vin'];
        $params['match'] ='year,make,model,trim';
        $params['stats'] ='price,miles,dom';

         $geolocationData = session('geolocation');
         //dd($geolocationData);
         $geolocationData = session('geolocation');
        if(!empty($geolocationData['longitude']) && !empty($geolocationData['latitude'])){
            $geolocationData = $this->getAddress();;
        }
        $country = $geolocationData['country']??'US';
        $state = $geolocationData['state']??'';
        $city = $geolocationData['cityName']??'';
        $zip = $geolocationData['zipCode']??'';
        $extraparam =['longitude'=>$geolocationData['longitude']??'','latitude'=>$geolocationData['latitude']??'','radius'=>50];
        //$extraparam =['country'=>$country];
        $params = array_filter(array_merge($params, $extraparam));
        //dd($params);
        $similiarcar = $this->marketcheckApiClient->searchActiveCars($params);
        $organized_features = [];
        if(isset($vehicle['extra']['high_value_features'])){
            foreach ($vehicle['extra']['high_value_features'] as $feature) {
                $category = $feature['category'];
                $description = $feature['description'];
                // Initialize category array if it doesn't exist
                if (!isset($organized_features[$category])) {
                    $organized_features[$category] = [];
                }
                // Append the description to the category array
                $organized_features[$category][] = $description;
            }
        }

        $ads = Ads::first();

        $ip = $this->get_client_ip();
        if($vehicle['dealer']['website']){
            $dealer_source=$vehicle['dealer']['website'];
            $dealersource =DealerSource::where('source',$dealer_source)->first(); 
            $store_id =NULL;
            if($dealersource){
                $dealer =Dealer::where('id',$dealersource->dealer_id)->first(); 
                $vehicle['dealer']['internal_id'] = $dealersource['dealer_id']??'0';
                $store_id = $dealersource['store_id']??NULL;
                $vehicle['dealer']['profile_pic'] = $dealer['profile_pic'] ??'';
                $vehicle['dealer']['name'] =$dealersource['dealership_name'] ;
                $vehicle['dealer']['phone'] =$dealersource['phone'] ;
                $vehicle['dealer']['street'] =$dealersource['address']?$dealersource['address']: $vehicle['dealer']['street'] ;
                $vehicle['dealer']['city'] =$dealersource['city'] ?$dealersource['city']: $vehicle['dealer']['city'] ;;
                $vehicle['dealer']['state'] =$dealersource['state'] ?$dealersource['state']: $vehicle['dealer']['state'] ;;
                $vehicle['dealer']['zip'] =$dealersource['zip_code'] ?$dealersource['zip_code']: $vehicle['dealer']['zip'] ;;
                $vehicle['dealer']['call_track_number'] =  $dealersource['call_tracking_number'] ?$dealersource['call_tracking_number']: $dealersource['phone']  ;;
                $vehicle['dealer']['call_track_sms'] =  $dealersource['call_track_sms'] ?$dealersource['call_track_sms']: '12182748696' ;;
            }
           
        }
       # dd($dealersource , $vehicle);
        Visit::create(['vid'=>$vehicle['id']??'NULL','vin'=>$vehicle['vin']??'NULL','source'=>$vehicle['dealer']['website']??'NULL','store_id' => $store_id,'dealer_id'=>$vehicle['dealer']['internal_id']??'NULL','user_id'=> $user_id??'NULL','ip' =>$ip]);
        $three60_image_url ='';
        $three60_image = $this->marketcheckApiClient->get360image($vehicle['vin']);
        if($three60_image['status'] =='ok'){
            $three60_image_url =  $three60_image['url'];
        }
        return view('template.users.vehicleDetail', compact('vehicle','similiarcar','organized_features','ads','three60_image_url'));
    }

    /**
     * Search active cars based on the provided parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchActiveCarsAjax(Request $request)
    {
        // Retrieve parameters from the request
        $params = $request->all();
        $response = $this->marketcheckApiClient->searchActiveCars($params);

       
        return $response->json(); 
    }

    /**
     * Search facets based on the provided parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchFacets(Request $request)
    {
        // Retrieve parameters from the request

        
        $params = $request->all();

        // Make a request to search facets
        $response = $this->marketcheckApiClient->searchFacets($params);

        // Process the response as needed
        return $response->json(); // Example: Return JSON response
    }

    public function dealerVehicle(Request $request){
        $input = $request->all();
        $price_range = ['0-10000', '10000-20000', '20000-30000', '30000-40000', '40000-50000','50000-60000','60000-70000','70000-1000000000'];
        $input['price-range'] = (isset($input['price_range'])?($input['price_range']):NULL) ;
        $params =$input;

        unset($params['price_range']);
       
        $params = array_filter( $params);
        $params['start'] =(isset($input['page'])?($input['page']*30 -30):0) ;
        $params['rows'] =30;
        $exploded =$input;
        unset($exploded['zip']);
        $checking= ['make','year','model'];
        if(isset($_REQUEST['source']) && !empty($_REQUEST['source'])){
            $params = array_merge($params, ['source'=>$_REQUEST['source']]);
            
            
        }else{
            $params = array_merge($params, ['source'=>'montrosekia.com']);
        }
          
        
        $keysToCheck = array_keys($input);
        $keysExist = !empty(array_intersect($keysToCheck, $checking));

        $inputparam = ['rows'=>0,'start'=>0,'facets'=>'seating_capacity,car_type,year|0|1000,make|0|1000,model|0|1000,trim|0|1000,drivetrain|0|1000,transmission|0|1000,cylinders|0|1000,fuel_type|0|1000,body_type|0|1000,vehicle_type|0|1000,doors|0|1000,engine|0|1000,interior_color|0|1000,exterior_color|0|1000,powertrain_type|0|1000,high_value_features|0|1000'];
        $inputparam = array_filter($params) + $inputparam;
        $finalvalue = $this->marketcheckApiClient->searchFacets($inputparam);
        
        if(($keysExist)){
           
            $search =[];
           
           
            $inputparam = ['rows'=>0,'start'=>0];
            $inputparam['facets']='car_type|0|1000,make|0|1000,year|0|1000,model|0|1000,body_type|0|1000,fuel_type|0|1000,trim|0|1000,transmission|0|1000,interior_color|0|1000,exterior_color|0|1000,powertrain_type|0|1000,drivetrain|0|1000,high_value_features|0|1000,seating_capacity|0|1000,engine|0|1000,cylinders0|1000';
           
            foreach($checking as $key =>$value){
                    
                $inputValues = [
                    'year' => $exploded['year'] ?? null,
                    'latitude' => $input['latitude'] ?? null,
                    'longitude' => $input['longitude'] ?? null,
                    'radius' => $input['radius'] ?? null,
                    'car_type' => $exploded['car_type'] ?? null,
                    'make' => $exploded['make'] ?? null,
                    'fuel_type' => $exploded['fuel_type'] ?? null,
                    'body_type' => $exploded['body_type'] ?? null,
                    'trim' => $exploded['trim'] ?? null,
                    'transmission' => $exploded['transmission'] ?? null,
                    'interior_color' => $exploded['interior_color'] ?? null,
                    'exterior_color' => $exploded['exterior_color'] ?? null,
                    'powertrain_type' => $exploded['powertrain_type'] ?? null,
                    'high_value_features' => $exploded['high_value_features'] ?? null,
                    'seating_capacity' => $exploded['seating_capacity'] ?? null,
                    'drivetrain' => $exploded['drivetrain'] ?? null,
                    'engine' => $exploded['engine'] ?? null,
                    'model' => $exploded['model'] ?? null,
                ];  

               
               
                if(isset($input[$value])){
                    unset($inputValues[$value]);
                    $inputValues[$value] =null;
                    $inputparam = array_filter($inputValues) + $inputparam;
                    unset($inputparam[$value]);
                    $allother = $this->marketcheckApiClient->searchFacets($inputparam );
                    
                    $find =0;
                    $search[$value] = $allother['facets'][$value]??[];
                    $searchitem =$input[$value];
                    if(!is_array($input[$value])){
                        $searchitem = explode(',',$input[$value]);
                    }
                
                    foreach($search[$value] as $row){
                    
                        if(in_array($row['item'],$searchitem)){
                            $find =1;
                            break;
                        }
                    }

                    if($find==0){
                        foreach($searchitem  as $key =>$row){
                            $search[$value][]=['item'=>$row,'count'=>0];
                        }
                    
                    }
                }else{
                    $inputparam = array_filter($inputValues) + $inputparam;
                    $allother = $this->marketcheckApiClient->searchFacets($inputparam );   
                    foreach($allother['facets'] as $key2 =>$row){
                        if(in_array($key2,$checking )){
                            if($key2 == $key){
                                continue;
                            }else{
                                $finalvalue['facets'][$key2] = $row;
                            }
                        }
                    }
                }
            }
            
          
            foreach($search as $key =>$value){
                $finalvalue['facets'][$key] = $value;
               
            }
            $inputparam = ['rows'=>0,'start'=>0,'facets'=>'make|0|1000'];
            if(!isset($finalvalue)){
                $inputparam = ['rows'=>0,'start'=>0,'facets'=>'seating_capacity,car_type,year|0|1000,make|0|1000,model|0|1000,trim|0|1000,drivetrain|0|1000,transmission|0|1000,cylinders|0|1000,fuel_type|0|1000,body_type|0|1000,vehicle_type|0|1000,doors|0|1000,engine|0|1000,interior_color|0|1000,exterior_color|0|1000,powertrain_type|0|1000,high_value_features|0|1000'];
            }
            
            $inputValues = [
                'car_type' => $input['car_type'] ?? null,
                'latitude' => $input['latitude'] ?? null,
                'longitude' => $input['longitude'] ?? null,
                'radius' => $input['radius'] ?? null
            ];
            if(!isset($finalvalue)){
                $inputparam = array_filter($inputValues) + $inputparam;
                $finalvalue = $this->marketcheckApiClient->searchFacets($inputparam );
            }
            
            $params['start'] =(isset($input['page'])?($input['page']*30 -30):0) ;
            $params['rows'] =30;
            //dd($params);
            if(isset($_REQUEST['Latest']) && !empty($_REQUEST['Latest'])){
                $params = array_merge($params, ['dom', 'sort_order' => 'asc', 'dom_range'=>'30-60']);
            }elseif(!isset($_REQUEST['sort_by'])){
                $input['sort_by'] ='price';
                $input['sort_order'] ='asc';
                $params = array_merge($params, ['sort_by' =>'price', 'sort_order' => 'asc']);
            }elseif(isset($_REQUEST['sort_by']) && empty($_REQUEST['sort_by'])){
                $input['sort_by'] ='price';
                $input['sort_order'] ='asc';
                $params = array_merge($params, ['sort_by' =>'price', 'sort_order' => 'asc']);
            }
            
            $vehiclelist = $this->marketcheckApiClient->getDealerSource($params);

        }else{
           
            $params['start'] =(isset($input['page'])?($input['page']*30 -30):0) ;
            $params['rows'] =30;
            if(isset($_REQUEST['Latest']) && !empty($_REQUEST['Latest'])){
                $params = array_merge($params, ['dom', 'sort_order' => 'asc', 'dom_range'=>'30-60']);
            }elseif(!isset($_REQUEST['sort_by'])){
                $input['sort_by'] ='price';
                $input['sort_order'] ='asc';
                $params = array_merge($params, ['sort_by' =>'price', 'sort_order' => 'asc']);
            }elseif(isset($_REQUEST['sort_by']) && empty($_REQUEST['sort_by'])){
                $input['sort_by'] ='price';
                $input['sort_order'] ='asc';
                $params = array_merge($params, ['sort_by' =>'price', 'sort_order' => 'asc']);
            }

            $vehiclelist = $this->marketcheckApiClient->getDealerSource($params);
           
        }
        $paginator = new LengthAwarePaginator(
            $vehiclelist['listings'],
            ($vehiclelist['num_found']>1500 ? 1500 : $vehiclelist['num_found']) ,
            30,
            ($params['start']+30)/30,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        $slot1 = Ads::where('page','car-list-page')->where('slot',1)->where('enabled',true)->first();
        $slot2 = Ads::where('page','car-list-page')->where('slot',2)->where('enabled',true)->first();
        $slot3 = Ads::where('page','car-list-page')->where('slot',3)->where('enabled',true)->first();
        $colorData = config('constants.COLOR');
        return view('template.users.dealer', compact('finalvalue','input','paginator','colorData','price_range','slot1','slot2','slot3'));
        #dd($finalvalue);
      
        return view('template.users.dealer', compact('price_range','paginator','slot1','slot2','slot3'));
    }

    public function callSms(Request $request){
        $vin_id = $request->vid; 
        $action = $request->action;
        $vehicle = $this->marketcheckApiClient->getListing($vin_id);
      
        $ip = $this->get_client_ip();
        if($vehicle['dealer']['website']){
            $dealer_source=$vehicle['dealer']['website'];
            $dealersource =DealerSource::where('source',$dealer_source)->first(); 
            $store_id =NULL;
            if($dealersource){
                $dealer =Dealer::where('id',$dealersource->dealer_id)->first(); 
                $vehicle['dealer']['internal_id'] = $dealersource['dealer_id']??'0';
                $store_id = $dealersource['store_id']??NULL;
                $vehicle['dealer']['profile_pic'] = $dealer['profile_pic'] ??'';
                $vehicle['dealer']['name'] =$dealersource['dealership_name'] ;
                $vehicle['dealer']['phone'] =$dealersource['phone'] ;
                $vehicle['dealer']['street'] =$dealersource['address']?$dealersource['address']: $vehicle['dealer']['street'] ;
                $vehicle['dealer']['city'] =$dealersource['city'] ?$dealersource['city']: $vehicle['dealer']['city'] ;;
                $vehicle['dealer']['state'] =$dealersource['state'] ?$dealersource['state']: $vehicle['dealer']['state'] ;;
                $vehicle['dealer']['zip'] =$dealersource['zip_code'] ?$dealersource['zip_code']: $vehicle['dealer']['zip'] ;;
                $vehicle['dealer']['call_track_number'] =  $dealersource['call_track_number'] ?$dealersource['call_track_number']: '' ;;
            }
           
        }

        clickCallSms::create(['vid'=>$vehicle['id']??'NULL','vin'=>$vehicle['vin']??'NULL','source'=>$vehicle['dealer']['website']??'NULL','store_id' => $store_id,'dealer_id'=>$vehicle['dealer']['internal_id']??'NULL','user_id'=> $user_id??'NULL','ip' =>$ip,'type'=>$action]);
        return response()->json([]);
    }

    public function dependentKeyword(Request $request){
        $allmodel =   Cache::remember('all_brands_model_'.$request->make, now()->addDay(),function ()use ($request) { 
            $param = [];
            $all = $this->marketcheckApiClient->searchFacets(['facets'=>'model|0|1000','make'=>$request['make'],'rows'=>0, 'start'=>0]);
          
            return $all;
            
        });
        return response()->json($allmodel);
        
    }

   
}
