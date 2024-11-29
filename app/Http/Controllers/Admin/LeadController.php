<?php
namespace App\Http\Controllers\Admin;
 
use App\Models\Lead;
use Illuminate\View\View;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Str;
use App\Services\SnsService;
use App\Services\AuthService;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Response;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Home;
use App\Models\Ads;
use App\Models\Contact;
use App\Models\RequestDemo;
use App\Models\Post;
 
class LeadController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display the setting page for the authenticated admin.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(): View
    {
        return view('template.admin.lead');
    }

    /**
     * Update the Website Setting .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   

    public function leadTableData(Request $request)
    {
        $request->merge(['page' => (($request->input('start')/ $request->input('length'))+1)]);
        $request->merge(['perPage' => $request->input('length')]);
      
        $this->perPage = $request->input('length', 3);
        $input= $request->all(); 
      
        $data = Lead::query()->makeQuery($input)->paginate( $this->perPage );
          
        return  response()->json([
            'data' => $data->items(),
            'draw' => $request->input('draw'), // Add draw parameter for security (optional)
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total(),
        ]);
    }

    public function leadlistView(Request $request){
        $lead = Lead::query()->makeQuery(['id' =>$request->id])->first();
        return response()->json($lead);

    }

    public function leadDownload(Request $request){
        // Get the filtered data
        $query = Lead::makeQuery($request->all());

        $leads = $query->get();

        // Define the CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="leads.csv"',
        ];

        // Define the columns you want to export
        $columns = [
            'ID', 'VIN', 'First Name', 'Last Name','Phone','Email', 'Dealer Name', 'Dealer Phone', 'Website', 'Date'
        ];

        // Create a callback function to generate the CSV
        $callback = function() use ($leads, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($leads as $lead) {
                $data = [
                    $lead->id,
                    $lead->vin,
                    $lead->user->first_name,
                    $lead->user->last_name,
                    $lead->user->phone_number,
                    $lead->user->email,
                    $lead->dealer ? $lead->dealer->first_name . ' ' . $lead->dealer->last_name : '',
                    $lead->dealer ? $lead->dealer->phone_number : '',
                    $lead->dealer ? $lead->dealer->source : '',
                    $lead->created_at->format('Y-m-d H:i:s')
                ];
                fputcsv($file, $data);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);

    }
}
