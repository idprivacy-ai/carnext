<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Slug;
use App\Jobs\SendDealerVerificationEmail;
use App\Mail\DealerNewPasswordNotification;
use App\Mail\AdminDealerCredentialMail;
use Illuminate\Support\Facades\Hash;
use App\Models\Dealer;
use App\Models\DealerSource;
use App\Models\Role;
use App\Models\Lead;
use App\Models\Visit;
use App\Models\DealerSocialAccount;
use Illuminate\Support\Facades\Http;
use App\Services\MarketcheckApiClient;

class AdminDealerController extends Controller
{
    use ApiResponseTrait;

    protected $marketcheckApiClient;

    public function __construct(MarketcheckApiClient $marketcheckApiClient)
    {
        $this->marketcheckApiClient = $marketcheckApiClient;
    }
    /**
     * Display a listing of dealers.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function dealerlist(Request $request)
    {
       // $dealer = Dealer::all();
       $admin = Auth::guard('admin')->user();
       
        if ($admin->can('manage stores','admin')) {
           
            $dealerIds = $admin->managedDealers()->pluck('dealer_id')->toArray();
            //dd($dealerIds);
            // If the user has permission, retrieve the dealer IDs they can manage
            if($dealerIds)
                $dealer = Dealer::where('parent_id', 0)->whereIn('id', $dealerIds)->get();
            else
                $dealer = Dealer::where('parent_id', 0)->get();
            
        } else {
           
            $dealer = Dealer::where('parent_id', 0)->get();
        }
        return view('template.admin.dealerlist', compact('dealer'));
    }

    /**
     * Display the list of all dealers.
     *
     * @return \Illuminate\View\View
     */
    public function alldealers()
    {
        return view('template.admin.alldealers');
    }

    /**
     * Register a new dealer.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dealerRegister(Request $request)
    {
        $data = $request->all();

        // Validate the request data
        $validator = Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:dealers',
            'password' => 'required|string|min:8|same:confirm_password',
            'confirm_password' => 'required|string|min:8',
            'phone_number' => 'required|numeric|digits:10|unique:dealers',
            'dealership_group' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail)   {
                    $exists = \App\Models\Dealer::whereRaw("LOWER(dealership_group) = LOWER(?)", [$value])
                        ->exists();
                  
                    if ($exists) {
                        $fail('The ' . $attribute . ' has already been taken.');
                    }
                },
            ],
            'designation' => 'required|string|max:255'
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->respondWithError('Validation Message', $errors, Response::HTTP_OK);
        }

        // Create a new dealer
        $dealer = Dealer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'dealership_group' => $request->dealership_group,
            'designation' => $request->designation,
        ]);

        // Assign admin role to the dealer
        $this->createAdminRole($dealer);
        $dealer->markEmailAsVerified();
        // Send email verification notification
        //$dealer->sendEmailVerificationNotification();
        Mail::to($dealer->email)->send(new AdminDealerCredentialMail($dealer,$request->password));

        // Log in the dealer
        //Auth::guard('dealer')->login($dealer);

        // Return success response
        return $this->respondWithSuccess('User registered successfully. Please verify your email.', $dealer, Response::HTTP_OK);
    }

    /**
     * Create admin role for a dealer.
     *
     * @param Dealer $dealer
     */
    public function createAdminRole($dealer)
    {
        // Create a new role for the dealer
       
        $role =Role::where('name', 'admin_' . $dealer->id)->first();
        if(!$role){
            $role = Role::create([
                'name' => 'admin_' . $dealer->id,
                'guard_name' => 'dealer',
                'dealer_id' => $dealer->id,
            ]);
        }
        $dealer->assignRole('admin_' . $dealer->id);
        // Define the permissions for the role
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

        // Assign the permissions to the role
        $role->givePermissionTo($permissions);
    }

    /**
     * Update an existing dealer's information.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dealerUpdate(Request $request)
    {
        $data = $request->all();
        $dealer = Dealer::find($request->dealer_id);
        $dealerId = $request->dealer_id; 
        // Validate the request data
       
        $validator = Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:dealers,email,' . $dealer->id,
            'phone_number' => 'required|numeric|digits:10|unique:dealers,phone_number,' . $dealer->id,
            'dealership_group' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($dealerId) {
                    $exists = \App\Models\Dealer::whereRaw("LOWER(dealership_group) = LOWER(?)", [$value])
                        ->where('id', '!=', $dealerId)
                        ->exists();
                  
                    if ($exists) {
                        $fail('The ' . $attribute . ' has already been taken.');
                    }
                },
            ],
            'designation' => 'required|string|max:255'
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->respondWithError('Validation Message', $errors, Response::HTTP_OK);
        }

        // Update dealer's information
        $dealer->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'dealership_group' => $request->dealership_group,
            'designation' => $request->designation,
        ]);
        $this->createAdminRole($dealer);
        // Return success response
        return $this->respondWithSuccess('Dealer updated successfully.', $dealer, Response::HTTP_OK);
    }

    /**
     * Change the password of a dealer.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $data = $request->all();

        // Validate the request data
        $validator = Validator::make($data, [
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->respondWithError('Validation Message', $errors, Response::HTTP_OK);
        }

        // Find the dealer and update the password
        $dealer = Dealer::find($request->dealer_id);
        Mail::to($dealer->email)->send(new DealerNewPasswordNotification($dealer, $request->password));

      
        $dealer->update([
            'password' => Hash::make($request->password),
        ]);

        // Return success response
        return $this->respondWithSuccess('Password updated successfully.', null, Response::HTTP_OK);
    }

    /**
     * Get dealer details for editing.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dealerlist_edit(Request $request)
    {
        $dealer_id = $request->get('dealer_id');
        $dealer = Dealer::find($dealer_id);
        return response()->json($dealer);
    }

    /**
     * Get dealer data for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dealerlist_tableData(Request $request)
    {
        $request->merge(['page' => (($request->input('start') / $request->input('length')) + 1)]);
        $request->merge(['perPage' => $request->input('length')]);
        $this->perPage = $request->input('length', 3);
        $input = $request->all();
        $input['parent_id'] = 0;
        $admin = Auth::guard('admin')->user();

        if ($admin->hasPermissionTo('manage stores')) {
            // If the user has permission, retrieve the dealer IDs they can manage
            $dealerIds = $admin->managedDealers()->pluck('dealer_id')->toArray(); // Assuming managedDealers is a relation or method returning the IDs
           
            if( $dealerIds)
                $data = Dealer::query()->makeQuery($input)->whereIn('id', $dealerIds)->paginate($this->perPage);
            else
                $data = Dealer::query()->makeQuery($input)->paginate($this->perPage);
        } else {
            // If no permission, just query the data normally
            $data = Dealer::query()->makeQuery($input)->paginate($this->perPage);
        }

        // Get paginated dealer data
       // $data = Dealer::query()->makeQuery($input)->paginate($this->perPage);

        // Return data for DataTables
        return response()->json([
            'data' => $data->items(),
            'draw' => $request->input('draw'),
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total(),
        ]);
    }

    /**
     * Delete a dealer.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dealerlist_delete(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'dealer_id' => 'required|exists:dealers,id',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Delete the dealer
        $dealersource = DealerSocialAccount::where('dealer_id', $request->dealer_id)->first();
        if($dealersource){
            DealerSocialAccount::where('dealer_id', $request->dealer_id)->delete();
        }
        DealerSource::where('dealer_id', $request->dealer_id)->update(['dealer_id' => 0]);
        Lead::where('dealer_id', $request->dealer_id)->update(['dealer_id' => 0]);
        Visit::where('dealer_id', $request->dealer_id)->update(['dealer_id' => 0]);
        
        // Check if any dealers have the given dealer_id as their parent_id before deleting them
        if (Dealer::where('parent_id', $request->dealer_id)->exists()) {
            Dealer::where('parent_id', $request->dealer_id)->delete();
        }
        
        // Finally, delete the dealer itself
        Dealer::find($request->dealer_id)->delete();

        // Return success response
        return response()->json(['success' => true, 'message' => 'Store deleted successfully.']);
    }

    /**
     * Download dealer data as a CSV file.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function downloadCSV(Request $request)
    {
        $input['start_date'] = $request->input('start_date');
        $input['end_date']  = $request->input('end_date');
        $input['parent_id']  = 0;

        // Get dealer data based on date range
        $data = Dealer::query()->makeQuery($input)->get();

        // Prepare CSV data
        $csvData = $data->map(function($dealer) {
            return [
                'ID' => $dealer->id,
                'Name' => $dealer->first_name . ' ' . $dealer->last_name,
                'Dealership Group' => $dealer->dealership_group,
                'Email' => $dealer->email,
                'Phone Number' => $dealer->phone_number,
                'City' => $dealer->city,
                'Date' => $dealer->created_at->format('Y-m-d H:i:s'),
            ];
        });

        // Define CSV header
        $csvHeader = [
            'ID', 'Name', 'Dealership Group', 'Email', 'Phone Number', 'City', 'Date'
        ];

        // Create CSV file
        $filename = "dealers_" . now()->format('Ymd_His') . ".csv";
        $handle = fopen($filename, 'w');
        fputcsv($handle, $csvHeader);

        // Add data to CSV file
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        // Return CSV file for download and delete after sending
        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function loginAs(Request $request){
        $id = $request->dealer; 
        $dealer = Dealer::where('id',$id)->first();
        Auth::guard('dealer')->login($dealer);
        return  redirect()->route('dealer.dashboard');
    }

    public function import(Request $request)
    {
        if ($request->hasFile('csv')) {
            $file = $request->file('csv');
            $data = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_shift($data); // remove the header row
            
            $errors = [];
            foreach ($data as $index => $row) {
                $row = array_combine($header, $row);

                // Validate dealer data
               

              
                if(!isset($row['password'])){
                    $row['password'] = '12345678';
                }
                // Create Dealer
                $dealer =  Dealer::where('email', $row['email'])->first();
                if(!$dealer){
                    $dealer = Dealer::create([
                        'first_name' => $row['first_name'],
                        'last_name' => $row['last_name'],
                        'email' => $row['email'],
                        'phone_number' => $row['phone_number'],
                        'password' => Hash::make($row['password']),
                        'dealership_group' => $row['dealership_group'],
                        'designation' => $row['designation'],
                    ]);


                    // Assign admin role to the dealer and mark email as verified
                    $this->createAdminRole($dealer);
                    $dealer->markEmailAsVerified();
                    Mail::to($dealer->email)->send(new AdminDealerCredentialMail($dealer, $row['password']));
                }

                // Validate store data
                $storeValidator = Validator::make($row, [
                    'dealership_name' => 'required|string|max:255',
                    'source' => [
                        'required',
                        'string',
                        function($attribute, $value, $fail) {
                            $value = preg_replace('/^(https?:\/\/)?(www\.)?/', '', $value);
                            if (DealerSource::where('dealer_id', '!=', 0)
                                            ->where('source', strtolower($value))
                                            ->exists()) {
                                $fail('The source ' . $value . ' has already been taken.');
                            }
                          
                           
                            $host = config('app.api_host');
                            $apiKey = config('app.api_key');
        
                            $params = ['rows' => 0, 'start' => 0, 'source' => $value];
                            try {
                                $vehiclelist = $this->marketcheckApiClient->getDealerSource($params);
                                if ($vehiclelist['num_found'] == 0) {
                                    $fail('No Vehicle available for this ' . $value);
                                }
                            } catch (\Exception $e) {
                                $fail('The source ' . $value . ' is not valid.');
                            }
        
                        }
                    ],
                    'adf_mail' => 'required|email|max:255',
                    'store_email' => 'required|email|max:255',
                    'store_phone' => 'required|max:10',
                    'subscribed' => 'required|in:1,0',
                    'address' => 'required|string|max:255',
                    'zip_code' => 'required|string|max:10',
                    'city' => 'required|string|max:255',
                    'subscription_price' => 'nullable|numeric',
                    'subscription_start_date' => 'nullable|date',
                    'subscription_end_date' => 'nullable|date',
                ]);
            

                if ($storeValidator->fails()) {
                    $errors[] = ['row' => $index + 1, 'errors' => $storeValidator->errors()];
                    continue;
                }
                $address = $this->getAddress($row['zip_code']);
                // Create Store
                DealerSource::create([
                    'dealer_id' => $dealer->id,
                    'dealership_name' => $row['dealership_name'],
                    'source' => strtolower(preg_replace('/^(https?:\/\/)?(www\.)?/', '', $row['source'])),
                    'adf_mail' => $row['adf_mail'],
                    'email' => $row['store_email'],
                    'phone' => $row['store_phone'],
                    'subscribed' => $row['subscribed'],
                    'address' => $row['address'],
                    'zip_code' => $row['zip_code'],
                    'city' => $address['city'],
                    'latitude' => $address['latitude'] ?? null,
                    'longitude' => $address['longitude'] ?? null,
                    'is_subscribed' => $data['is_subscribed'] ?? 0,
                    'is_manage_by_admin' => $data['is_manage_by_admin'] ?? 0,
                    'subscription_price' => $row['subscription_price'] ?? null,
                    'cancelled_at' => $row['subscription_end_date'] ?? null,
                    'call_tracking_number' => $row['call_tracking_number'] ?? null,
                    'call_track_sms' => $row['call_track_sms'] ?? null,
                ]);
            }

            if (!empty($errors)) {
                return response()->json(['success' => false, 'errors' => $errors], 200);
            }

            return response()->json(['success' => true, 'message' => 'Dealers and stores uploaded successfully'], 200);
        }

        return response()->json(['success' => false, 'message' => 'No CSV file uploaded'], 400);
    }

    public function getAddress($zipcode)
    {
        $adress['latitude'] =NULL;
        $adress['longitude'] =NULL;
        $adress['city'] =NULL;
        if (isset($zipcode)) {
            $geocodeResponse = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $zipcode,
                'key' => env('GOOGLE_PLACE_API'),
            ]);

            $geocodeData = $geocodeResponse->json();

            if (isset($geocodeData['results'][0])) {
                $location = $geocodeData['results'][0]['geometry']['location'];
                $adress['latitude'] = $location['lat'];
                $adress['longitude'] = $location['lng'];

                // Extract city from the response
                $addressComponents = $geocodeData['results'][0]['address_components'];
                foreach ($addressComponents as $component) {
                    if (in_array('locality', $component['types'])) {
                        $adress['city'] = $component['long_name'];
                        break;
                    }
                }
            }

        }
        return $adress;
    }
}
