<?php
 
namespace App\Http\Controllers\Dealer;
 
use App\Models\dealer;
use App\Models\Visit;
use App\Models\DealerSource;
use App\Models\AssignDealer;

use Spatie\Permission\Models\Role;
use App\Models\Role as dealerRole;
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
use Spatie\Permission\Models\Permission;
use App\Mail\DealerEmployeeCredentials;
use App\Mail\DealerNewPasswordNotification;
use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display the dashboard for the authenticated user.
     *
     * @return \Illuminate\Contracts\View\View
     */

   
    /**
     * Display the  for the Add Role user.
     *
     * @return \Illuminate\Contracts\View\View
    */
    public function index(Request $request): View
    {
        // Retrieve the authenticated user

        $mainDealer = app('mainDealer');
        $parentId = app('parentId');
        $dealer = Auth::guard('dealer')->user();
        $roles = Role::where('dealer_id', $parentId)->get();
        $source =app('storeList');
        $allstore = $source->pluck('id')->toArray();
       
        if($dealer->parent_id==0)
            $employees = Dealer::with(['roles', 'assignSource.source'])->where('parent_id', $parentId)->get();
        else{
             $employees = Dealer::with(['roles', 'assignSource.source'])
                ->whereHas('assignSource', function($query) use ($source,$allstore) {
                    $query->whereIn('dealer_source_id', $allstore);
                })
                ->get();
                
        }

        return view('template.dealers.employee',compact('dealer','employees','roles','source','parentId','mainDealer','parentId'));
    }

    public function storeEmployee(Request $request)
    {
        $parentId = app('parentId');
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:dealers',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,id',
        ]);
    
        
        if ($validator->fails()) {
            $errors = $validator -> errors();
            return $this->respondWithError('Validation Message',$errors ,Response::HTTP_OK); 
        }

        $dealer = Auth::guard('dealer')->user();

        $employee = new Dealer();

        $employee->first_name = $request->first_name;
        $employee->last_name = $request->last_name;
        $employee->email = $request->email;
        $employee->password = Hash::make($request->password);
        $employee->parent_id = $parentId;
        $employee->source = $dealer->source;
        $employee->save();
        foreach ($request->sources as $source_id) {
            AssignDealer::create([
                'dealer_id' => $employee->id,
                'dealer_source_id' => $source_id,
                'role_id' => $request->role,
            ]);
        }
    

        $role = Role::find($request->role);
        Mail::to($employee->email)->send(new DealerEmployeeCredentials($employee, $request->password));
        $employee->assignRole($role);
        return $this->respondWithSuccess('Employee Added successfully. ', $employee, Response::HTTP_OK);
        
    }

    public function updateEmployee(Request $request)
    {
        $id =$request->id;
        $parentId = app('parentId');
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:dealers,email,' . $id,
            'role' => 'required|exists:roles,id',
        ]);
    
        
        if ($validator->fails()) {
            $errors = $validator -> errors();
            return $this->respondWithError('Validation Message',$errors ,Response::HTTP_OK); 
        }

        $employee = Dealer::findOrFail($id);
        $employee->first_name = $request->first_name;
        $employee->last_name = $request->last_name;
        $employee->email = $request->email;
        $employee->save();

        $role = Role::find($request->role);
        $employee->syncRoles($role);
        foreach ($request->sources as $source_id) {
            AssignDealer::create([
                'dealer_id' => $employee->id,
                'dealer_source_id' => $source_id,
                'role_id' => $request->role,
            ]);
        }
        return $this->respondWithSuccess('Employee updated successfully. ', $employee, Response::HTTP_OK);
    }


   


    /**
     * Display the  for the Edit Role user.
     *
     * @return \Illuminate\Contracts\View\View
    */
    public function dealerStore(Request $request): View
    {
        $dealer = Auth::guard('dealer')->user();
        //$dealer->source = 'willmarcars.com';
        $stores = DealerSource::where('dealer_id', $dealer->id)->get();
        $params = ['rows'=>10,'start'=>0,'source'=>$dealer->source];
        return view('template.dealers.store',compact('dealer','stores'));
    }


    /**
     * Display the  for the Add Role user.
     *
     * @return \Illuminate\Contracts\View\View
    */
    public function dealerRole(Request $request): View
    {
        // Retrieve the authenticated user
        $dealer = Auth::guard('dealer')->user();
        //$dealer->source = 'willmarcars.com';
        $parentId = app('parentId');
        $roles = Role::where('dealer_id', $parentId)->with('permissions')->get();
      
        $permissions = [
            'manage employee',
            'manage role',
            'manage store',
            'view lead',
            'View Store Vehicles',
            'manage payment',
            'View Analytics'
        ];
        
        return view('template.dealers.role',compact('dealer','roles','permissions','parentId'));
    }
     /**
     * update the add role 
     *
     * @return \Illuminate\Contracts\View\View
    */

    public function addDealerRole(Request $request)
    {
        $dealer = Auth::guard('dealer')->user();
        //$dealer->source = 'willmarcars.com';
        $parentId = app('parentId');
        $request['role_name']=  $request['role_name'].'_'.$parentId;
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:255',
            'dealer_id' => 'required|exists:dealers,id',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // Custom validation to check if role already exists for dealer guard
        $validator->after(function ($validator) use ($request,$parentId) {
            if (Role::where('name', $request->role_name)
                    ->where('guard_name', 'dealer')
                    ->where('dealer_id', $parentId)
                    ->exists()) {
                $validator->errors()->add('role_name', 'A role with this name already exists for the dealer.');
            }
        });

        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->respondWithError('Validation Message', $errors, Response::HTTP_OK);
        }

        // Retrieve the dealer
        $dealer = Dealer::find($request->dealer_id);

        // Create the role for the dealer
      
            $role = DealerRole::create([
                'name' => $request->role_name,
                'guard_name' => 'dealer',
                'dealer_id' => $parentId,
            ]);
          
            $role->syncPermissions($request->permissions);

            return $this->respondWithSuccess('Role created successfully.', $role, Response::HTTP_OK);
       
    }


    public function updateRole(Request $request)
    {
        $parentId = app('parentId');
        $request['role_name']=  $request['role_name'].'_'.$parentId;
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
            'role_name' => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (Role::where('name', $request->role_name)
                    ->where('guard_name', 'dealer')
                    ->where('dealer_id', $parentId)
                    ->where('id', '!=', $request->role_id) // exclude the current role
                    ->exists()) {
                $validator->errors()->add('role_name', 'A role with this name already exists for the dealer.');
            }
        });

        $role = Role::find($request->role_id);
        $role->name = $request->role_name;
        $role->save();

        $role->syncPermissions($request->permissions);

        return $this->respondWithSuccess('Role Updated successfully. ', $role, Response::HTTP_OK);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dealer_id' => 'required|exists:dealers,id',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors()
            ]);
        }

        $dealer = Dealer::find($request->dealer_id);
        $dealer->password = Hash::make($request->password);
        $dealer->save();
        Mail::to($dealer->email)->send(new DealerNewPasswordNotification($dealer, $request->password));

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.'
        ]);
        
       
    }

    public function deleteRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
           
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors()
            ]);
        }
        $role = Role::find($request->role_id);
        $role->delete();
        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.'
        ]);
        
       
    }

    public function deleteEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dealer_id' => 'required|exists:dealers,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors()
            ]);
        }

        $dealer = Dealer::find($request->dealer_id);
        $dealer->delete();
        return response()->json([
            'success' => true,
            'message' => 'Dealer Deleted successfully.'
        ]);
        
       
    }
}

