@extends('layouts.admin')
@section('content')
<input type="hidden" id="seturl" value="">
<!-- Page Content Start -->
 
<div class="admin_inner_content bg-white">
    <div class="row">
        <div class="col col-xl-2 col-lg-12 col-md-12 col-12 page_title">
            <div class="page_title mb-3 mb-xl-0">
                <h2>Total Leads</h2>
            </div>
        </div>
        <div class="col col-xl-10 col-lg-12 col-md-12 col-12">
            <div class="position-relative">
                <div class="row gx-1 justify-content-end"> 
                    <div class="col col-lg-10 col-md-10 col-12"> 
                        <form action="" id="filter-form" method="GET" class="row gx-1">
                            <label class="col col-lg-4 col-md-4 col-12 d-flex align-items-center">
                                From:&nbsp;<input type="date" name="from" class="form-control form-control-sm me-md-1" value="{{Request::get('from')}}">
                            </label>
                            <label class="col col-lg-4 col-md-4 col-12 d-flex align-items-center">
                                To:&nbsp;<input type="date" name="to" class="form-control form-control-sm" value="{{Request::get('to')}}">
                            </label>
                            <div class="col col-lg-2 col-md-2 col-6">
                                <button type="submit" class="btn btn_theme w-100">Filter</button>
                            </div>
                            <div class="col col-lg-2 col-md-2 col-6">
                                <a href="{{ route('lead.index') }}" class="btn btn_theme w-100">Reset</a>
                            </div>
                        </form>
                    </div>
                    <div class="col col-lg-2 col-md-2 col-6"> 
                        <button type="button" id="downloadCSV" class="btn btn_dark w-100 text-nowrap dwn_csv_btn mt-0">Download CSV</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        
        <div class="col col-12">
            <div class="position-relative table-responsive">
                <table class="table table-bordered w-100 dataTable" id="contact_tabl">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>VIN</td>
                            <td>First Name</td>
                            <td>Last Name</td>
                            <td>Dealer Name</td>
                            <td>Dealer Phone</td>
                            <td>Website</td>
                            <td>Date</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
</div>

 <!-- Show -->
 <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="adHeading" class="modal-title">See Information about contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                        <div class="row align-items-center">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Vin</h6>
                            </div>
                            <div class="col-sm-9 text-secondary fs-6" id="contact-vin"></div>
                        </div>
                        <hr>
                        <div class="row align-items-center">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Dealer Ship Name</h6>
                            </div>
                            <div class="col-sm-9 text-secondary fs-6" id="contact-dealername"></div>
                        </div>
                        <hr>
                        <div class="row align-items-center">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Dealer Phone</h6>
                            </div>
                            <div class="col-sm-9 text-secondary fs-6" id="contact-dealerphone"></div>
                        </div>
                        <hr>
                        <div class="row align-items-center">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Dealer Email</h6>
                            </div>
                            <div class="col-sm-9 text-secondary fs-6" id="contact-dealeremail"></div>
                        </div>
                        <hr>
                        <div class="row align-items-center">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Dealer Phone</h6>
                            </div>
                            <div class="col-sm-9 text-secondary fs-6" id="contact-phone"></div>
                        </div>
                        <hr>
                        <div class="row align-items-center">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Dealer Source</h6>
                            </div>
                            <div class="col-sm-9 text-secondary fs-6" id="contact-website"></div>
                        </div>
                        <hr>
                      
                        <div class="row align-items-center">
                            <div class="col-sm-3">
                                <h6 class="mb-0">User Name</h6>
                            </div>
                            <div class="col-sm-9 text-secondary fs-6" id="contact-user_name"></div>
                        </div>
                        <hr>
                      

                        <hr>
                        <div class="row align-items-center">
                            <div class="col-sm-3">
                                <h6 class="mb-0">User Phone</h6>
                            </div>
                            <div class="col-sm-9 text-secondary fs-6" id="contact-userphone"></div>
                        </div>
                        <hr>
                        <div class="row align-items-center">
                            <div class="col-sm-3">
                                <h6 class="mb-0">User Email</h6>
                            </div>
                            <div class="col-sm-9 text-secondary fs-6" id="contact-usereremail"></div>
                        </div>
                        <hr>
                       
                        <div class="row align-items-center">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Created At</h6>
                            </div>
                            <div class="col-sm-9 text-secondary fs-6" id="contact-created_at"></div>
                        </div>
                        <hr>
                       
                
                </div>
            </div>
        </div>
    </div>
</div>

 <!-- Delete -->
<div  class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header border-0">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pb-4">
            <div class="position-relative text-center">
                <i class="fa-regular fa-circle-xmark fa-4x mb-3 text-danger"></i>
                <h5 class="mb-4">Are you sure? You want to delete?</h5>
                <div class="position-relative text-center">                    
                    <button  type="button" class="btn btn-danger" onclick="actionmethod()">Yes</a>
                    <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script>
var contact ='';
var myurl = '{{route('lead.tableData')}}';

$(function () {

    $('body').on('click', '.showContact', function () {
        var contact_id = $(this).data('id');
        $.get("{{ route('lead.view') }}" +'?id=' + contact_id , function (data) {
            $('#showModal').modal('show');

            $('#contact-vin').text(data.vin ? data.vin : '');
            $('#contact-dealername').text(data.dealer ? (data.dealer.first_name + ' ' + data.dealer.last_name) : '');
            $('#contact-dealerphone').text(data.dealer ? data.dealer.phone_number : '');
            $('#contact-dealeremail').text(data.dealer ? data.dealer.email : '');
            $('#contact-user_name').text(data.user ? (data.user.first_name + ' ' + (data.user.last_name ?  data.user.last_name  :' ')) : '');
            $('#contact-userphone').text(data.user ? data.user.phone_number : '');
            $('#contact-usereremail').text(data.user ? data.user.email : '');
            $('#contact-website').text(data.dealer_source ? data.dealer_source : '');
            $('#contact-created_at').text(data.created_at ? data.created_at : '');

            
        });
    });

});
$('#filter-form').on('submit', function(e) {
        e.preventDefault();
        contact.ajax.reload();
    });
$('#download-csv').on('click', function() {
    var from = $('input[name="from"]').val();
    var to = $('input[name="to"]').val();
 
    var form = $('<form>', {
        action: '{{ route('lead.download') }}',
        method: 'GET',
        class: 'd-none'
    }).append($('<input>', { type: 'hidden', name: 'export', value: '' }))
       
        .append($('<input>', { type: 'hidden', name: 'from', value: from }))
        .append($('<input>', { type: 'hidden', name: 'to', value: to }));

    $('body').append(form);
    form.submit();
    form.remove();
});
function getQueryParams() {
        const params = new URLSearchParams(window.location.search);
        return {
            from: params.get('from'),
            to: params.get('to')
        };
    }

    // Set form values from query parameters
    const queryParams = getQueryParams();
    if (queryParams.from) {
        $('input[name="from"]').val(queryParams.from);
    }
    if (queryParams.to) {
        $('input[name="to"]').val(queryParams.to);
    }

contact= $('#contact_tabl').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
            url: '{{ route('lead.tableData') }}',
            type: 'GET',
            data: function(d) {
                d.from = $('input[name="from"]').val();
                d.to = $('input[name="to"]').val();
            },
            dataSrc: function(json) {
                return json.data.map(function(record) {
                    return {
                        id: record.id,
                        vin: record.vin,
                        first_name: record.user.first_name,
                        last_name: record.user.last_name,
                        dealer_name: record.dealer ? record.dealer.first_name + ' ' + record.dealer.last_name : '',
                        dealer_phone: record.dealer ? record.dealer.phone_number : '',
                        website: record.dealer_source ? record.dealer_source: '',
                        date: moment(record.created_at).format('DD-MM-YYYY HH:mm:ss'),
                        action: `<button class="btn btn-primary btn-sm showContact" data-id="${record.id}">Show</button> 
                                `
                    };
                });
            }
    },
    columns: [
            { data: 'id', name: 'id' },
            { data: 'vin', name: 'vin' },
            { data: 'first_name', name: 'first_name' },
            { data: 'last_name', name: 'last_name' },
            { data: 'dealer_name', name: 'dealer_name' },
            { data: 'dealer_phone', name: 'dealer_phone' },
            { data: 'website', name: 'website' },
            { data: 'date', name: 'date' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
    ],
    language: {
        paginate: {
            first: 'First',
            last: 'Last',
            next: '&rarr;',
            previous: '&larr;',
        },
        lengthMenu: 'Show <select>' +
            '<option value="1" selected>1</option>' +
            '<option value="2">2</option>' +
            '<option value="3">4</option>' +
            '<option value="4">6</option>' +
            '<option value="-1">All</option>' +
            '</select> records',
                /* "oLanguage": {
            "sLengthMenu": "Display _MENU_ records per page",
            // other language options
        },*/

        info: 'Showing _START_ to _END_ of _TOTAL_ records',
        infoFiltered: '(filtered from _MAX_ total records)',
        
    },
});

function actionmethod()
{
    actionsurl = $('#seturl').val();

    runajax(actionsurl, '', 'get', '', 'json', function(output) {
            
            console.log(output);
            if (output.success) 
            {
                $('.modal').modal('hide');
                contact.ajax.reload();

            }else{
                       
            }
    }); 
}

function seturl(url )
{
    $('#seturl').val(url);

}
</script>
@endsection
