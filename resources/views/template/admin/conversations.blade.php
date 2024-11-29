@extends('layouts.admin')
@section('content')

<style>
    .dataTable thead tr th {
        background-color: #f8f9fa !important;
        font-weight: 500;
    }
    .logs_tble .sm\:hidden{
        display: none !important;
    }
    .logs_tble svg{
        width: 22px;
    }
    .logs_tble nav.flex.items-center.justify-between .sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between{
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .logs_tble nav p{
        margin-bottom: 0;
    }
    .logs_tble nav div span a{
        display: inline-block;
        text-decoration: unset;
        color: #666;
    }
    .logs_tble nav div span a:hover, .logs_tble nav div span a:focus, .logs_tble nav div span span.cursor-default{
        color: var(--primary);
    }
    .logs_tble nav div span a.px-4, .logs_tble nav div span span.cursor-default{
        padding-left: 14px !important;
        padding-right: 14px !important;
    }

    @media (max-width: 991px){
        .logs_tble nav.flex.items-center.justify-between .sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between{
            display: block;
            margin-top: 12px;
        }
        .logs_tble nav div span a {
    margin-bottom: 4px;
}
        .logs_tble nav p{
            margin-bottom: 12px;
        }
    }
</style>


<input type="hidden" id="seturl" value="">
<!-- Page Content Start -->
<div class="admin_inner_content bg-white">
    <div class="row">
        <div class="page_title">
            <h2>Anisa Conversations</h2>
            <div class="page_title_btn">
                <div class="ms-auto d-md-flex align-items-end"> 
                    <!--form id="userFilter" method="GET">
                        <label>
                           From:<input type="date" id="user-form" name="from" class="form-control form-control-sm" value="{{Request::get('from')}}"> 
                        </label>
                        <label>
                            To:<input type="date" id="user-to" name="to" class="form-control form-control-sm" value="{{Request::get('to')}}"> 
                        </label>
                        <button type="submit" class="btn btn-primary" value="Filter">Submit</button>
                        <a href="{{ route('userlist.index') }}" class="btn btn-danger">Reset</a>
                    </form>
                    <form id="userDownload" method="GET" class="dwn_csv">
                        <input type="hidden" name="export" class="form-control" value=""> 
                        <input type="hidden" name="ids" class="form-control export-ids"> 
                        <input type="hidden" name="from" class="form-control" value="{{Request::get('from')}}"> 
                        <input type="hidden" name="to" class="form-control" value="{{Request::get('to')}}"> 
                        <input type="submit" class="btn btn-dark mb-2 ms-1" value="Download CSV">
                    </form-->
                </div> 
            </div>
        </div>
    </div>
    @if(session()->has('success'))
        <div class="row">
            <div class="alert alert-success alert-dismissible text-white" role="alert">
                <span class="text-sm">{{ Session::get('success') }}</span>
                <button type="button" class="btn-close text-lg py-3 opacity-10"
                    data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    @endif
    <hr>

    <div class="row">
        
        <div class="col col-12">
            <div class="position-relative table-responsive logs_tble">
                <table class="table table-bordered w-100 dataTable" id="user_tabl">
                <input type="hidden" name="export" class="form-control" value="export"> 
                    <thead>
                        <tr>
                           
                            <th>Conversation ID</th>
                            <th>Conversation Count</th>
                        
                            <th>Request Timestamp</th>
                            <th>Request</th>
                            <th>Response</th>
                            <th>Additional </th>
                            <th>API </th>
                          
                            <th>Context</th>
                            <th>Followup ID</th>
                          
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($conversations as $key => $conversation)
                            <tr>
                               
                                <td>{{ $conversation->conversation_id }}</td>
                                <td>{{ $conversation->total }}</td>
                                <td>{{ $conversation->request_timestamp }}</td>
                                <td>{{ $conversation->request }}</td>
                                <td>{{ $conversation->response }}</td>
                                <td>{{ $conversation->additional_info }}</td>
                                <td>{{ $conversation->api_url }}</td>
                              
                                <td>{{ $conversation->context }}</td>
                                <td>{{ $conversation->followup_id }}</td>
                             
                            </tr>
                        @endforeach
                    </tbody>
                   
                </table>
                {{ $conversations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection