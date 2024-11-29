<?php 
namespace App\Http\Controllers\Admin;

use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $conversations = Conversation::orderBy('request_timestamp', 'desc')
            ->paginate(10);
           // dd( $conversations );
          ;

        return view('template.admin.conversations', compact('conversations'));
        #return view('template.admin.conversations', compact('conversations'));
    }
}

