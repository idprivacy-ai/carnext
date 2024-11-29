<?php

namespace App\Services;

use GuzzleHttp\Client;

use App\Models\ApiLog;
use Illuminate\Http\Request;


class MarketcheckApiClient
{
    protected $baseUrl = 'https://mc-api.marketcheck.com/v2/';
    protected $client;
    protected $api_key;
    public $ip;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->api_key = env('MARKETCHECK_API_KEY');
        $this->ip = $this->client_ip();
    }

    public function searchActiveCars($params)
    {
        $params['api_key'] = $this->api_key;
        $params['include_relevant_links'] = 'true';
        $params['photo_links'] = 'true';

        return $this->makeRequest('GET', 'search/car/active', $params);
    }

    public function searchFacets($params)
    {
        $params['api_key'] = $this->api_key;
        return $this->makeRequest('GET', 'search/car/active', $params);
    }

    public function getListing($vin)
    {
        $params['api_key'] = $this->api_key;
        return $this->makeRequest('GET', 'listing/car/' . $vin, $params);
    }

    public function getDealerSource($params)
    {
        $params['api_key'] = $this->api_key;
        return $this->makeRequest('GET', 'car/dealer/inventory/active', $params);
    }

    public function getPopularMakeModel($params, $home = 1)
    {
        $params['api_key'] = $this->api_key;
        $response = $this->makeRequest('GET', 'popular/cars/', $params);
        $body = $response;
        $popular = [];
        $k = 0;
        foreach ($body as $key => $value) {
            $row = ['sort_by' => 'dom', 'sort_order' => 'asc', 'dom_range' => '15-60'];
            $row['make'] = $value['make'];
            $row['car_type'] = $params['car_type'];
            $row['model'] = $value['model'];
            $row['api_key'] = $params['api_key'];

            $response = $this->makeRequest('GET', 'search/car/active', $row);
            $data = $response;
            $row['data'] = $data;
            $popular[] = $row;

            if ($k > 3) {
                break;
            }
            $k++;
        }
        return $popular;
    }

    public function getPopularMakeModelOnly($params, $home = 1)
    {
        $params['api_key'] = $this->api_key;
        $response = $this->makeRequest('GET', 'popular/cars/', $params);
        $body = $response;
        $popularbrand = ['make' => [], 'model' => []];
        $k = 0;
        foreach ($body as $key => $value) {
            if (!in_array($value['make'], $popularbrand['make'])) {
                $popularbrand['make'][] = $value['make'];
            }
            $popularbrand['car_type'] = $params['car_type'];
            if (!in_array($value['model'], $popularbrand['model'])) {
                $popularbrand['model'][] = $value['model'];
            }
            if ($k > 10) {
                break;
            }
            $k++;
        }
        return $popularbrand;
    }

    protected function makeRequest($method, $endpoint, $params)
    {
        $responsePayload = null;
        $statusCode = null;
        try {
            $response = $this->client->request($method, $this->baseUrl . $endpoint, ['query' => $params]);
            //$responsePayload = json_decode($response->getBody()->getContents(), true);
            //$statusCode = $response->getStatusCode();
        } catch (\Exception $e) {
            //$responsePayload = ['error' => $e->getMessage()];
            //$statusCode = $e->getCode();
        }

        $this->log($endpoint, $params, [], 'web');
        if(isset($response)){
            return $body =json_decode($response->getBody()->getContents(), true);
        }else{
            return ['num_found'=>0];
        }
       
    }

    public function log($endpoint, $requestPayload, $responsePayload, $call_from='web')
    {
        ApiLog::create([
            'endpoint' => $endpoint,
            'request_payload' => json_encode($requestPayload),
            'call_from'=>$call_from,
            'ip_address' => $this->ip
        ]);
    }

    public function get360image($vin)
    {
        // Base URL for the API
        $imagebaseurl = 'https://wa-detection-api.spincar.com/';
    
        // Fetch client ID and API key from environment variables
        $client = env('THREE_60_IMAGE_CLIENT_ID');
        $api_key = env('THREE_60_IMAGE_CLIENT_API');
    
        // Check if environment variables are set correctly
        if (!$client || !$api_key) {
            throw new \Exception('Client ID or API key not set in environment variables');
        }
    
        // Set the parameters
        $params['vin'] = $vin;
        $params['cid'] = $client;
    
        // Create the hash for authentication
        $hash = hash('sha512', $api_key . $client . $vin);
        $params['auth'] = $hash;
        $params['filter'] = 'y';
        $params['nospin200'] = 'y';
        
    
        try {
            // Make the API request
            $response = $this->client->request('GET', $imagebaseurl, ['query' => $params]);
            
            // Decode the response body
            $body = json_decode($response->getBody()->getContents(), true);
            
            return $body;
        } catch (\Exception $e) {
            // Catch and display any errors
            return false;
           
        }
    }

    public function myweburl($url)
    {
        //dd($url);
        //$params['api_key'] =  $this->api_key;
        $response = $this->client->request('GET', $url,[]);
        return $body = json_decode($response->getBody()->getContents(), true);
    }

    public function client_ip() {
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

}
