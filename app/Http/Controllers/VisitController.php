<?php
 
namespace App\Http\Controllers;
 
use App\Models\Lead;
use App\Models\User;
use App\Models\Dealer;
use App\Models\Favourite;

use App\Models\DealerSource;
use Illuminate\View\View;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str;
;
use App\Http\Traits\ApiResponseTrait;
use  Illuminate\Http\Response;
use Validator;
use Illuminate\Validation\Rule;
use App\Jobs\SendLeadNotification;
use App\Jobs\SendLeadNotificationExpired;

use App\Services\MarketcheckApiClient;

 
class VisitController extends Controller
{
    use ApiResponseTrait;
   
    protected $marketcheckApiClient;

    public function __construct(MarketcheckApiClient $marketcheckApiClient)
    {
        $this->marketcheckApiClient = $marketcheckApiClient;
    }

    public function createUser($request){
        $validator = Validator::make($request->all(), [
            
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                
                'email',
            
            ],
            'phone_number' => [
                'required',
                'numeric',
                'digits:10',
               
            ],
            
        ]);
        if ($validator -> fails()){
            $errors = $validator -> errors();
            return ['status'=> 0, 'data'=>$errors]; 
        }
        $finduser = User::where('email', $request->email)->first();
        if(!$finduser){
            $finduser = User::where('phone_number', $request->phone_number)->first();
        }

        if(!$finduser){
           $finduser =  User::create(['dial_code'=>'+91','first_name'=>$request['first_name'],'last_name'=>$request['last_name'],
           'email'=>$request['email'],'phone_number'=>$request['phone_number'],'zip_code'=>$request['zip_code'] ]);
        }
        return ['status'=> 1, 'data'=>$finduser]; 
        

    }
    /**
     * Make Lead on the vehicle by authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function visitStore(Request $request)
    {
        $vid_id = $request->vid; 
        $user = Auth::user();
       
        if(!$user){
            $data  = $this->createUser($request);
            //dd($data);
            if($data['status']==0){
                return $this->respondWithError('Validation Error',$data['data'],Response::HTTP_OK);
            }
            $user =  $data['data'];
        }
        if($user){
            $store_id = NULL;
            $vehicle = $this->marketcheckApiClient->getListing($vid_id);
            if($vehicle){
                $dealer_external_id = $vehicle['dealer']['id'];
                $dealer_source = $vehicle['dealer']['website'];
                $dealersource =DealerSource::where('source',$dealer_source)->first(); 
                
                if($dealersource){
                    $dealer =Dealer::where('source',$dealersource->dealer_id)->first(); 
               
                    $store_id = $dealersource['id'];
                    if($dealer){
                        $dealer_id = $dealer['id']?? 0;
                        Lead::where('dealer_source', $dealer_source)->where('dealer_id',0)->update(['dealer_id' => $dealer_id]);
                        DealerSource::where('source', $dealer_source)->where('dealer_id',0)->update(['dealer_id' => $dealer_id]);
                    }else{
                        $dealer_id = $dealersource['dealer_id']?? 0;
                    }  
                }else{
                    $dealer_id = 0;
                    $dealersource = DealerSource::create(['dealer_id'=>$dealer_id,'source'=>$dealer_source,'external_dealer_id'=>$dealer_external_id]);
                    Lead::where('dealer_source', $dealer_source)->where('dealer_id',0)->update(['dealer_id' => $dealer_id]);
                }

                $data = ['user_id'=>$user->id,'vid'=>$vehicle['id'],'vin'=>$vehicle['vin'],'dealer_external_id'=>$dealer_external_id,
                'dealer_source'=>$dealer_source,'dealer_id'=>$dealer_id,'viewed'=>0,'store_id' => $store_id,];

                $lead =Lead::where(['user_id'=>$user->id,'vid'=>$vehicle['id']])->first();
                if(!$lead){
                   
                    $lead = Lead::create($data);
                    if($dealer_id){
                        $currentDate = now();
                        $subscriptionEndDate = $dealersource['cancelled_at'];
                        if($dealersource['is_subscribed']){
                            
                            if (!empty($dealersource['cancelled_at']) && $currentDate->gt($dealersource['cancelled_at'])) {
                                SendLeadNotificationExpired::dispatch($user, $vehicle, $dealersource);
                               
                            }else{
                                $lead->update(['viewed'=>1]);
                                SendLeadNotification::dispatch($user, $vehicle, $dealersource);
                            }
                        }else{
                            $leadcount =Lead::where(['dealer_source'=>$dealer_source])->count();
                            if($leadcount<=5){
                                $lead->update(['viewed'=>1]);
                                SendLeadNotification::dispatch($user, $vehicle, $dealersource);
                            }else{
                                SendLeadNotificationExpired::dispatch($user, $vehicle, $dealersource);
                            }
                        }

                    }else{
                        $dealer = $vehicle['dealer'];
                        $dealer['first_name'] = $dealer['name'];
                        $dealer['dealership_name'] = $dealer['name'];
                        $dealer['phone'] = $dealer['phone']??'';
                        $dealer['email'] = $dealer['seller_email']??'seller@yopmail.com';
                        //$dealer['email'] ='ravikathait01@gmail.com';
                        $leadcount =Lead::where(['dealer_source'=>$dealer_source])->count();
                        if($leadcount<=5){
                            $lead->update(['viewed'=>1]);
                            if($dealer['email']) SendLeadNotification::dispatch($user, $vehicle, $dealer);
                        }else{
                            if($dealer['email']) SendLeadNotificationExpired::dispatch($user, $vehicle, $dealer);
                        }
                        
                    }
                }
            }
            return $this->respondWithSuccess('Lead Detail successfully',$lead,Response::HTTP_OK);
        }else{
            return $this->respondWithError('User Login successfully',$user,Response::HTTP_OK);
        }
    }

   

    public function getlifestyle(){
        
    }


    public function listVisit(Request $request)
    {
        $user = Auth::user();
        $lead =Lead::where(['user_id'=>$user->id])->get()->pluck('vin')->toArray();
        
        $vinlist = implode(',',$lead);
        $params['vin'] = $vinlist;
        if($lead)
            $vehiclelist = $this->marketcheckApiClient->searchActiveCars($params);
        else
            $vehiclelist['listings']=[];
        return view('template.users.interest', compact('vehiclelist'));
    }

    public function favouriteList(Request $request)
    {
        $user = Auth::user();
        $lead =Favourite::where(['user_id'=>$user->id])->get()->pluck('vin')->toArray();
       
        $vinlist = implode(',',$lead);
        if($lead){
            $params['vin'] = $vinlist;
            $vehiclelist = $this->marketcheckApiClient->searchActiveCars($params);
        }else{
            $vehiclelist['listings'] =[];
        }
       
        
        #dd($vehiclelist);
        return view('template.users.favourite', compact('vehiclelist'));
    }


    public function makeFavouite(Request $request){
        $user = Auth::user();
        if($user){
            $fav = Favourite::where(['user_id'=>$user->id, 'vin'=>$request->vin])->first();
            if(!$fav){
                $status =1;
                $fav = Favourite::create(['user_id'=>$user->id, 'vin'=>$request->vin,'vid'=>$request->vid]);
            }else{
                $status =0;
                $fav->delete();
            }
            return $this->respondWithSuccess('User Login successfully',['status'=>$status],Response::HTTP_OK);
        }
        return $this->respondWithError('User Login successfully',$user,Response::HTTP_OK);
    }

    
}

