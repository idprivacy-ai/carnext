<?php
 
namespace App\Http\Controllers;

use Illuminate\View\View;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Traits\ApiResponseTrait;
use  Illuminate\Http\Response;
use App\Services\MarketcheckApiClient;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Location\Facades\Location;

class ChatController extends Controller
{

    protected $marketcheckApiClient;

    public function __construct(MarketcheckApiClient $marketcheckApiClient)
    {
        $this->marketcheckApiClient = $marketcheckApiClient;
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

    public function index(Request $request)
    {
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json'
        ];
        $ipAddress = $this->get_client_ip();
       // $ipAddress  ='104.174.125.138';
        // Decode the JSON payload from the request
        $bodyData = [
            'request' => $request->input('request'),
            'context' => $request->input('context', ''),
            'conversation_id' => $request->input('conversation_id', ''),
            'ip'=>$ipAddress
        ];

       
        $geolocationData = session('geolocation');
        if(!isset($geolocationData['zip']) || empty($geolocationData['zip'])){
            
            $geolocationData = $this->getAddress();;
        }
        $country = $geolocationData['country']??'';
        $state = $geolocationData['state']??'';
        $city = $geolocationData['cityName']??'';
        $zip = $geolocationData['zipCode']??'';
        $mylocation =['zip'=>$zip,'longitude'=>$geolocationData['longitude']??'','latitude'=>$geolocationData['latitude']??'','country' =>$country??'','radius'=>50];
       # dd($mylocation);
        $bodyData= array_merge($bodyData,   $mylocation);
        // Encode the data to JSON
        $body = json_encode($bodyData);
        $this->marketcheckApiClient->log(env('CHAT_BOT_SERVER_URL'), $body, [], 'chat');

        //dd($body);
        // Send the request using the post method of the Client class
        $res = $client->post(env('CHAT_BOT_SERVER_URL'), [
            'headers' => $headers,
            'body' => $body
        ]);
        $response = json_decode($res->getBody()->getContents(),true);
        $weburl = str_replace('https://carnext.autos','https://www.carnext.autos/vehicle',$response['weburl']) ;
        if(isset($response['RecordFound']) && $response['RecordFound'] =='Yes'){
            $vehicle = $response['vehicle'];
            $html = view('template.chatcar', compact('vehicle','weburl'))->render();
            unset($vehicle);
            $response['html'] =$html;
        }else{
            
            $response['html'] ='';
            //unset($vehicle);
    
    
           
           
        }
        unset($response['apiurl']);
        return response()->json($response);
    }

    public function removeZipParameter($url) {
        // Parse the URL and its query string
        $parsed_url = parse_url($url);
        
        // Parse the query string into an associative array
        parse_str($parsed_url['query'], $query_params);
        
        // Remove the 'zip' parameter
        if(isset($query_params['zip']))
            unset($query_params['zip']);
        
        // Build the new query string without 'zip'
        $new_query_string = http_build_query($query_params);
        
        // Rebuild the URL with the new query string
        $new_url = $parsed_url['scheme'] . '://' . $parsed_url['host'] . $parsed_url['path'];
        
        if (!empty($new_query_string)) {
            $new_url .= '?' . $new_query_string;
        }
        
        return $new_url;
    }
    public function getAddress($input=[])
    {
        $geolocationData = session('geolocation');
       // dd( $geolocationData); 

        $ipAddress = $this->get_client_ip();
       
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
   
}