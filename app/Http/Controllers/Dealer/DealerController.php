<?php
 
namespace App\Http\Controllers\Dealer;
 
use App\Models\dealer;
use App\Models\Visit;
use App\Models\clickCallSms;
use Illuminate\View\View;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use App\Services\SnsService;
use App\Services\AuthService;
use App\Http\Traits\ApiResponseTrait;
use  Illuminate\Http\Response;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Services\MarketcheckApiClient;

class DealerController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display the dashboard for the authenticated user.
     *
     * @return \Illuminate\Contracts\View\View
     */

    protected $marketcheckApiClient;

    public function __construct(MarketcheckApiClient $marketcheckApiClient)
    {
        $this->marketcheckApiClient = $marketcheckApiClient;
    }

    public function dashboard(Request $request){
        $dealer = Auth::guard('dealer')->user();
        //$dealer->source = 'willmarcars.com';
        
        
        $params = ['rows'=>10,'start'=>0,'source'=>$dealer->source];
        $vehiclelist = $this->marketcheckApiClient->getDealerSource($params);

        $query = Lead::with('user')->where('dealer_source', $dealer->source);

        $subscription = $dealer->subscription('default');
       
        if ($subscription) {
            if ($request->filled('vin')) {
                $query->where('vin', $request->vin);
            }
            if (!$subscription->canceled()) {
                $leads = $query->orderBy('id', 'desc')->get(5);
            } else {
                $currentDate = now();
                $subscriptionEndDate = $subscription->ends_at;
                if ($currentDate->lt($subscriptionEndDate)) {
                    $leads = $query->where('created_at', '<=', $subscriptionEndDate)->orderBy('id', 'desc')->get(5);
                }
            }
        } else {
            $leads = $query->orderBy('id', 'asc')->take(5)->get();
        }

    }

    public function index(Request $request): View
    {
        $dealer = Auth::guard('dealer')->user();
        $mainDealer = app('mainDealer');
        $storeList = app('storeList');
        $parentId = app('parentId');
        if(isset($request['source']) && !empty($request['source'])  && $request['source'] !='All')
        {   $allstore =[$request['source']];
            $params = ['rows'=>10,'start'=>0,'source'=>$request['source']];
        }else{
            $allstore = $storeList->pluck('source')->toArray();
            $stores = implode(',',$allstore);
            $params = ['rows'=>10,'start'=>0,'source'=>$stores];
        }
        $vehiclelist = $this->marketcheckApiClient->getDealerSource($params);
        $query = Lead::with('user')->whereIn('dealer_source', $allstore);
        $totalCount = $query->count();
        $subscription = $dealer->subscription('default');
        $leads = $query->orderBy('id', 'desc')->take(5)->get();
        $vehiclecount = $vehiclelist['num_found'];
        $visitCount = Visit::whereIn('source',$allstore)->count() ;

        $vinArray = $leads->pluck('vin')->all();
        $vinList = implode(',', $vinArray);
        $params = [
            'start' =>0,
            'rows' => 30,
            'vin' => $vinList
        ];
        $vehicleList = count($vinArray) > 0
            ? $this->marketcheckApiClient->searchActiveCars($params)
            : ['listings' => [], 'num_found' => 0];

        // Get VINs from the vehicle listings
        $listingVins = collect($vehicleList['listings'])->pluck('vin')->all();

        // Filter leads to include only those with VINs in the listings
        $filteredLeads = $leads->filter(function ($lead) use ($listingVins) {
            return in_array($lead->vin, $listingVins);
        });

        foreach ($vehicleList['listings'] as $listing) {
            $filteredLeads->where('vin', $listing['vin'])->each(function ($lead) use ($listing) {
                $lead->additional_data = $listing;
            });
        }

        $paginator = new LengthAwarePaginator(
            $filteredLeads->forPage(0, 30)->values(),
            $filteredLeads->count(),
            5,
            1,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        return view('template.dealers.dashboard', compact('vehiclecount','visitCount','totalCount','paginator','mainDealer'));
    }

     /**
     * Display the dashboard for the authenticated user.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function profile(): View
    {
        $user = Auth::guard('dealer')->user();
        //$mainDealer = app('mainDealer');
        return view('template.dealers.profile', compact('user'));
    }

    /**
     * Update the profile of the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::guard('dealer')->user();
        $id = $user->id;
        $data = $request->all();
        if (!empty($data['source']) && !preg_match('/^https?:\/\//i', $data['source'])) {
            $request->source = preg_replace('/^www\./', '', $request->source);
            $request->source =   $data['source'] = 'https://' . $data['source'];
        }

       
        $validator = Validator::make( $data, [
            
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            //'dealership_name'=>'required|string|max:255',
            'dealership_group'=>'required|string|max:255',
            'phone_number' => [
                'required',
                
                'numeric',
                'digits:10',
                Rule::unique('dealers')->ignore(Auth::id()),
            ],
            'designation' => 'required|string|max:255',
            
            'email' => [
                'bail',
                'required',
                'email',
                function ($attribute, $value, $fail) use ($request, $id) {
                   
                    $query = \App\Models\Dealer::where('email', $value)  ;                        
                    if ($id) {
                        $query->where('id', '!=', $id);
                    }
                    if ($query->exists()) {
                        $fail('The ' . $attribute . ' is already registered with a verified dealer.');
                    }
                },
            ],
           
            
        ]);
        if ($validator -> fails()){
            $errors = $validator -> errors();
            return $this->respondWithError('Validation Message',$errors ,Response::HTTP_OK);   
        }
        $validatedData =$request->all();
        $user = Auth::guard('dealer')->user();
        $user->first_name = $validatedData['first_name'];
        //$user->dealership_name = $validatedData['dealership_name'];
        $user->dealership_group = $validatedData['dealership_group'];
        $user->phone_number = $validatedData['phone_number'];
        $user->designation = $validatedData['designation'];
        $user->last_name = $validatedData['last_name'];
        $resend=0;
      
        if($user->email != $request->email) {
            $user->email_verified_at = null;
            $resend =1;
         
        }
        
       
        if ($request->has('phone_number')) {
            $user->phone_number = $validatedData['phone_number'];
        }
        if ($request->has('email')) {
            $user->email = $validatedData['email'];
        }
        if ($request->has('adfemail')) {
           
            $user->adfemail = $request->adfemail;
        }
       
        
        
        $user->save();
        //$this->createAdminRole($user);
        if($resend){
            $user->sendEmailVerificationNotification();
        }
        return $this->respondWithSuccess('Profile updated successfully!',$user,Response::HTTP_OK);
    }

    public function createAdminRole($dealer){
        $role = Role::create([
            'name' =>'admin_'. $dealer->id,
            'guard_name' => 'dealer',
            'dealer_id' => $dealer->id,
        ]);
        $permissions = [
            'manage employee',
            'manage role',
            'manage store',
            'manage subscription',
            'view lead',
            'View Store Vehicles',
            'manage payment',
            'View Analytics'
        ];
        $role->givePermissionTo($permissions);
    }

    public function sendVerification()
    {
        $dealer = Auth::guard('dealer')->user();

        // Ensure the dealer exists and is not already verified
        if ($dealer && !$dealer->hasVerifiedEmail()) {
            $dealer->sendEmailVerificationNotification();

            // Redirect to the dealer profile page with a success message
            return redirect()->route('dealer.profile')->with('success', 'Verification email sent successfully. Please check your email to verify your account.');
        }

        // Redirect back if the dealer is already verified or no dealer found
        return redirect()->route('dealer.profile')->with('error', 'No action needed or already verified.');
    }



 
    /**
     * Update the profile of the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'address' => 'required|string',
            'zip_code' => 'required',
            'city' => 'required',
            'city' => 'required',
            
            
        ]);
        if ($validator -> fails()){
            $errors = $validator -> errors();
            return $this->respondWithError('Validation Message',$errors ,Response::HTTP_OK);   
        }
        $validatedData =$request->all();
        $user = Auth::guard('dealer')->user();
        $user->address = $validatedData['address'];
        $user->address2 = $validatedData['address2'];
        $user->city = $validatedData['city'];
        $user->zip_code = $validatedData['zip_code'];
        $user->state = $validatedData['state'];
        $user->country = $validatedData['country'];
        $user->latitude = $validatedData['latitude'];
        $user->longitude = $validatedData['longitude'];
       
        $user->save();
        return $this->respondWithSuccess('Address updated successfully!',$user,Response::HTTP_OK);
    }

     /**
     * Update the profile of the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfilepic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            
            'profile_pic' => 'required|image|max:2048',
           
            
        ]);
        if ($validator -> fails()){
            $errors = $validator -> errors();
            return $this->respondWithError('Validation Message',$errors ,Response::HTTP_OK);   
        }
        $validatedData =$request->all();
        $user = Auth::guard('dealer')->user();
       
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $path = $file->store('profile_pics', 's3'); // Store the file in the 'profile_pics' directory on S3
    
            // Update the user's profile picture URL
            $user->profile_pic = Storage::disk('s3')->url($path);
           
        }
        
        $user->save();
        return $this->respondWithSuccess('Profile updated successfully!',$user,Response::HTTP_OK);
    }

    public function myvehicle(Request $request)
    {
        $input = $request->all();
        $dealer = Auth::guard('dealer')->user();
        $params = $input;
        //$dealer->source ='willmarcars.com';

        $storeList = app('storeList');
        $parentId = app('parentId');
        if(isset($request['source']) && !empty(($request['source'])  && $request['source'] !='All'))
        {   $allstore =[$request['source']];
            $stores = $request['source'];
            $params = ['rows'=>10,'start'=>0,'source'=>$request['source']];
        }else{
            $allstore = $storeList->pluck('source')->toArray();
            $stores = implode(',',$allstore);
            $params = ['rows'=>10,'start'=>0,'source'=>$stores];
        }

        if($allstore){
          
            if(isset($input['search']) && !empty($input['search'])){
               $params[$input['keywordterm']] =$input['search'];
            }
            $params[] = array_filter($params) ;
            $params['start'] =(isset($input['page'])?($input['page']*30 -30):0) ;
            $params['rows'] =30;
            
            $vehiclelist = $this->marketcheckApiClient->getDealerSource($params);
            if(!isset($vehiclelist['listings'])){
                $vehiclelist['listings'] =[];
                $vehiclelist['num_found'] =0;
            }
        }else{
            $vehiclelist['listings'] =[];
            $params['start'] =(isset($input['page'])?($input['page']*30 -30):0) ;
            $vehiclelist['num_found'] =0;
        }
        $lead =Lead::select('vid', 'vin')
        ->selectRaw('COUNT(vin) as vin_count')
        ->whereIn('dealer_source',$allstore )
        ->groupBy('vid', 'vin')->get();

        $call =clickCallSms::select('vid', 'vin')
        ->selectRaw('COUNT(vin) as vin_count')
        ->whereIn('source',$allstore )
        ->where('type','call' )
        ->groupBy('vid', 'vin')->get();

        $sms =clickCallSms::select('vid', 'vin')
        ->selectRaw('COUNT(vin) as vin_count')
        ->whereIn('source',$allstore )
        ->where('type','sms' )
        ->groupBy('vid', 'vin')->get();

        $storedvehiclecount =[];
        $storedcallvehiclecount =[];
        $storedsmsvehiclecount =[];
        foreach($lead as $key =>$value){
            $storedvehiclecount[$value['vin']]= $value['vin_count'];
        }
        foreach($sms as $key =>$value){
            $storedsmsvehiclecount [$value['vin']]= $value['vin_count'];
        }
        foreach($call as $key =>$value){
            $storedcallvehiclecount[$value['vin']]= $value['vin_count'];
        }

        $leadvisit =Visit::select('vid', 'vin')
        ->selectRaw('COUNT(vin) as vin_count')
        ->whereIn('source',$allstore )
        ->groupBy('vid', 'vin')->get();
        $visitvehicleCount =[];
        foreach($leadvisit as $key =>$value){
            $visitvehicleCount[$value['vin']]= $value['vin_count'];
        }
        //dd($storedvehiclecount);

        foreach($vehiclelist['listings'] as $key =>$value){
           // if($value['vin'] $)
        }
        $paginator = new LengthAwarePaginator(
            $vehiclelist['listings'],
            $vehiclelist['num_found'],
            30,
            $params['start']*30,
            ['path' => request()->url(), 'query' => request()->query()]
        );
       

        //foreach($)
        #dd($ve hiclelist);
        return view('template.dealers.my-vehicles', compact('paginator','input','storedvehiclecount','visitvehicleCount','storedcallvehiclecount','storedsmsvehiclecount'));
    }

    public function getLead(){

    }


    public function myleadCar(Request $request)
    {
        $input = $request->all();
        $dealer = Auth::guard('dealer')->user();
        $params = $input;
        //$dealer->source ='willmarcars.com';
        $storeList = app('storeList');
        $parentId = app('parentId');
        if(isset($request['source']) && (!empty($request['source'])  && $request['source'] !='All'))
        {   $allstore =[$request['source']];
            $stores = $request['source'];
            $params = ['rows'=>10,'start'=>0,'source'=>$request['source']];
        }else{
            $allstore = $storeList->pluck('source')->toArray();
            $stores = implode(',',$allstore);
            $params = ['rows'=>10,'start'=>0,'source'=>$stores];
        }

        if($allstore){

            $lead =Lead::select('vid', 'vin')
            ->selectRaw('COUNT(vin) as vin_count')
            ->whereIn('dealer_source', $allstore  )
            ->groupBy('vid', 'vin')
            ->paginate(30);
            $vin_array =[];
            $myvin =[];
            foreach($lead  as $item){
                $vin_array[$item->vin] = $item->vin_count;
                $myvin[] = $item->vin;
            }
            if($myvin){

            
                $vinlist = implode(',',$myvin);
                $params['start'] =(isset($input['page'])?($input['page']*30 -30):0) ;
                $params = ['rows'=>0,'start'=>0,'source'=>$dealer->source];
                if(isset($input['search']) && !empty($input['search'])){
                    $params[$input['keywordterm']] =$input['search'];
                }
                $params['rows'] =30;
                $params['vin'] = $vinlist;
                $vehiclelist = $this->marketcheckApiClient->searchActiveCars($params);
            }else{
                $vehiclelist['listings'] =[];
                $vehiclelist['num_found'] =0;
            }
            
        }else{
                $vehiclelist['listings'] =[];
                $vehiclelist['num_found'] =0;
            }
        $paginator = new LengthAwarePaginator(
            $vehiclelist['listings'],
            $vehiclelist['num_found'],
            30,
            (isset($input['page'])?($input['page']*30 -30):0),
            ['path' => request()->url(), 'query' => request()->query()]
        );
       
        return view('template.dealers.my-lead-car', compact('paginator','input','vin_array'));
    }

    public function carDetail(Request $request){
        $vin_id = $request->id;
        $vehicle = $this->marketcheckApiClient->getListing($vin_id);
        $params['vins'] = $vehicle['vin'];
        $params['match'] ='year,make,model,trim';
        $params['stats'] ='price,miles,dom';
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
        $html = view('template.dealers.car', compact('vehicle','similiarcar','organized_features'))->render();
        return response()->json(['html' => $html]);
       
    }

    public function leadDetail(Request $request){
        $dealer = Auth::guard('dealer')->user();
        $lead = Lead::with('user')->where('id', $request->id)->first();
        $subscription = $dealer->subscription('default');
        $user = $lead->user;
        $vin_id = $lead->vid;
        $vehicle = $this->marketcheckApiClient->getListing($vin_id);
        //dd($subscription);
        $html = view('template.dealers.leadDetail', compact('lead','user','vehicle','subscription'))->render();
        
        return response()->json(['html' => $html,'viewed'=>$lead->viewed]);
       
    }



    public function mylead(Request $request)
    {
        $input = $request->all();
        $dealer = Auth::guard('dealer')->user();
        //$dealer->source = 'willmarcars.com';
        $storeList = app('storeList');
        $parentId = app('parentId');
        if(isset($request['source']) && (!empty($request['source'])  && $request['source'] !='All'))
        {   $allstore =[$request['source']];
            $stores = $request['source'];
            $params = ['rows'=>10,'start'=>0,'source'=>$request['source']];
        }else{
            $allstore = $storeList->pluck('source')->toArray();
            $stores = implode(',',$allstore);
            $params = ['rows'=>10,'start'=>0,'source'=>$stores];
        }
        $query = Lead::with('user')->whereIn('dealer_source', $allstore);

        $subscription = $dealer->subscription('default');
        /*
        if ($subscription) {
            if ($request->filled('vin')) {
                $query->where('vin', $request->vin);
            }
            
            if (!$subscription->canceled()) {
                // Trigger mail with vehicle and user detail

                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = $request->input('start_date');
                    $endDate = $request->input('end_date');
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
                $leads = $query->orderBy('id', 'desc')->paginate(30);
            } else {
                $currentDate = now();
                $subscriptionEndDate = $subscription->ends_at;
                if ($currentDate->lt($subscriptionEndDate)) {
                    $leads = $query->where('created_at', '<=', $subscriptionEndDate)->orderBy('id', 'desc')->paginate(30);
                }
            }
        } else {
            $leads = $query->orderBy('id', 'asc')->take(5)->get();
        }*/
        if ($request->filled('vin')) {
            $query->where('vin', $request->vin);
        }
        if ($request->filled('start_date')) {
            $startDate = $request->input('start_date');
            $query->where('created_at', '>=',$startDate);
        }
        if ($request->filled('end_date')) {
            $end_date = $request->input('end_date');
            $query->where('created_at', '<=',$end_date);
        }
        
        
        
        $leads = $query->orderBy('id', 'desc')->get();
        $vinArray = $leads->pluck('vin')->all();
        $vinList = implode(',', $vinArray);
        $params = [
            'start' => (($input['page'] ?? 1) * 30) - 30,
            'rows' => 30,
            'vin' => $vinList
        ];
        if (isset($input['search']) && !empty($input['search'])) {
            $params[$input['keywordterm']] = $input['search'];
        }

        // Prepare parameters for API call.
        $vehicleList = count($vinArray) > 0
            ? $this->marketcheckApiClient->searchActiveCars($params)
            : ['listings' => [], 'num_found' => 0];

        // Get VINs from the vehicle listings
        $listingVins = collect($vehicleList['listings'])->pluck('vin')->all();

        // Filter leads to include only those with VINs in the listings
        $filteredLeads = $leads->filter(function ($lead) use ($listingVins) {
            return in_array($lead->vin, $listingVins);
        });

        foreach ($vehicleList['listings'] as $listing) {
            $filteredLeads->where('vin', $listing['vin'])->each(function ($lead) use ($listing) {
                $lead->additional_data = $listing;
            });
        }

        // Paginate the filtered leads
        $page = $input['page'] ?? 1;
        $perPage = 30;
        $paginator = new LengthAwarePaginator(
            $filteredLeads->forPage($page, $perPage)->values(),
            $filteredLeads->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('template.dealers.my-leads', [
            'paginator' => $paginator,
            'input' => $input,
            'subscription'=>$subscription
        ]);


    }
    
    /**
     * Logout the authenticated user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::guard('dealer')->logout(); 
        return redirect()->route('dealer.index'); 
    }
}
