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
use App\Mail\ChangeDealerPasswordMail;
use App\Mail\AdminDealerCredentialMail;
use Illuminate\Support\Facades\Hash;
use App\Models\Dealer;
use App\Models\DealerSource;

use App\Models\Transaction;

class TransactionController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of dealers.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $dealer = Dealer::where('parent_id',0)->where('dealership_group','!=' ,NULL)->get();;
        $stores = DealerSource::where('dealer_id','!=',0)->get();

        return view('template.admin.transaction',compact('stores','dealer'));
    }

   
    public function data(Request $request)
    {
        $request->merge(['page' => (($request->input('start') / $request->input('length')) + 1)]);
        $request->merge(['perPage' => $request->input('length')]);
        $this->perPage = $request->input('length', 3);
        $input = $request->all();
        //dd($input,$this->perPage);
        $data = Transaction::makeQuery($input)->paginate( $this->perPage);

        return response()->json([
            'data' => $data->items(),
            'draw' => $request->draw,
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total(),
        ]);
    }

    public function downloadCSV(Request $request)
        {
            $input['start_date'] = $request->input('start_date');
            $input['end_date']  = $request->input('end_date');
            $input['parent_id']  = 0;

            // Get dealer data based on date range
             $data = Transaction::query()->makeQuery($input)->get();

            // Prepare CSV data
            $csvData = $data->map(function($transaction) {
                return [
                    'ID' => $transaction->id,
                    'Transaction Date' => $transaction->created_at->format('Y-m-d'),
                    'Dealership Name' => $transaction->store->dealership_name,
                    'Dealership Group' => $transaction->dealer->dealership_group,
                    'Subscription Start Date' => $transaction->subscription_start_date,
                    'Subscription End Date' => $transaction->subscription_end_date,
                    'Amount' => $transaction->total_amount,
                    'Coupon Amount' => $transaction->coupon_amount,
                    'Subscription Type' => $transaction->tranaction_type == 1 ? 'Manual' : 'Default',
                ];
            });

            // Define CSV header
            $csvHeader = [
                'ID','Stripe Subscripiton Id', 'Transaction Date','Dealership Name', 'Dealership Group', 'Subscription Start Date', 'Subscription End Date', 'Amount', 'Coupon Amount','Subscription Type'
            ];

            // Create CSV file
            $filename = "transaction_" . now()->format('Ymd_His') . ".csv";
            $handle = fopen($filename, 'w');
            fputcsv($handle, $csvHeader);

            fclose($handle);

            // Return CSV file for download and delete after sending
            return response()->download($filename)->deleteFileAfterSend(true);
        
        }

        public function add(Request $request)
        {
            
    
            $validator = Validator::make($request->all(), [
                'store_id' => 'required|exists:dealer_source,id',
                'total_amount' => 'required|numeric',
               
            ]);
            if ($validator->fails()) {
                $errors = $validator -> errors();
                return $this->respondWithError('Validation Message',$errors ,Response::HTTP_OK); 
            }
            $transaction = Transaction::create([
                'dealer_id' => DealerSource::find($request->store_id)->dealer_id,
                'store_id' => $request->store_id,
                'total_amount' => $request->total_amount??NULL,
                'coupon_amount' => $request->coupon_amount??NULL,
                'coupon_code' => $request->coupon_code??NULL,
                'subscription_start_date' => $request->subscription_start_date??NULL,
                'subscription_end_date' => $request->subscription_end_date??NULL,
                'transaction_type' => 1,
            ]);
    
            return response()->json(['success' => true]);
        }
    
        public function update(Request $request)
        {
           
            $validator = Validator::make($request->all(), [
                'transaction_id' => 'required|exists:transactions,id',
                'store_id' => 'required|exists:dealer_sources,id',
                'total_amount' => 'required|numeric',
                'coupon_amount' => 'required|numeric',
                'coupon_code' => 'required|string',
                'subscription_start_date' => 'required|date',
                'subscription_end_date' => 'required|date',
                'transaction_type' => 'required|in:0,1',
            ]);
            if ($validator->fails()) {
                $errors = $validator -> errors();
                return $this->respondWithError('Validation Message',$errors ,Response::HTTP_OK); 
            }
    
            $transaction = Transaction::find($request->transaction_id);
            $transaction->update([
                'store_id' => $request->store_id,
                'total_amount' => $request->total_amount,
                'coupon_amount' => $request->coupon_amount,
                'coupon_code' => $request->coupon_code,
                'subscription_start_date' => $request->subscription_start_date,
                'subscription_end_date' => $request->subscription_end_date,
                'transaction_type' => $request->subscription_type,
            ]);
    
            return response()->json(['success' => true]);
        }
    
        public function delete(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'transaction_id' => 'required|exists:transactions,id',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['success' => false, 'data' => $validator->errors()]);
            }
    
            Transaction::find($request->transaction_id)->delete();
    
            return response()->json(['success' => true]);
        }
   

   
    
}
