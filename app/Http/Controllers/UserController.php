<?php
 
namespace App\Http\Controllers;
 
use App\Models\User;
use Illuminate\View\View;
use App\Jobs\SendWelcomeEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Str;
use App\Services\SnsService;
use App\Services\AuthService;
use App\Http\Traits\ApiResponseTrait;
use  Illuminate\Http\Response;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

 
class UserController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display the dashboard for the authenticated user.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(): View
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Return the dashboard view with the authenticated user data
        return view('template.users.dashboard', compact('user'));
    }

    /**
     * Update the profile of the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'profile_pic' => 'nullable|image|max:2048',
          
            'email' => [
                'required',
                
                'email',
                Rule::unique('users')->ignore(Auth::id()),
            ],
            'phone_number' => [
                'required',
                
                'numeric',
                'digits:10',
                Rule::unique('users')->ignore(Auth::id()),
            ],
            
        ]);
        if ($validator -> fails()){
            $errors = $validator -> errors();
            return $this->respondWithError('Validation Message',$errors ,Response::HTTP_OK);   
        }
        $validatedData =$request->all();
        $exitsemail =Auth::user()->email;
       //dd( $validatedData );
       $user = Auth::user();
        $user->first_name = $validatedData['first_name'];
        $user->last_name = $validatedData['last_name'];
        if ($request->has('email')) {
            $user->email = $validatedData['email'];
        }
        
        // Update phone number only if provided
        if ($request->has('phone_number')) {
            $user->phone_number = $validatedData['phone_number'];
        }
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $path = $file->store('profile_pics', 's3'); // Store the file in the 'profile_pics' directory on S3
            $user->profile_pic = Storage::disk('s3')->url($path);
           
        }
      
        $user->save();
        if(!$exitsemail){
            SendWelcomeEmailJob::dispatch($user);
        }
        return $this->respondWithSuccess('Profile updated successfully!',$user,Response::HTTP_OK);
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
        $user = Auth::user();
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
        $user = Auth::user();
       
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $path = $file->store('profile_pics', 's3'); // Store the file in the 'profile_pics' directory on S3
    
            // Update the user's profile picture URL
            $user->profile_pic = Storage::disk('s3')->url($path);
           
        }
        
        $user->save();
        return $this->respondWithSuccess('Profile updated successfully!',$user,Response::HTTP_OK);
    }

    /**
     * Logout the authenticated user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::logout(); 
        return redirect()->route('home'); 
    }
}
