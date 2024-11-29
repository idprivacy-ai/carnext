@extends('layouts.admin')
@section('content')
<input type="hidden" id="seturl" value="">
<!-- Page Content Start -->
<div class="position-relative">
    <div class="row">
        <div class="col col-xl-2 col-lg-12 col-md-12 col-12 page_title">
            <div class="page_title mb-3 mb-xl-0">
                <h2>All Dealers</h2>
            </div>
        </div>
        <div class="col col-xl-10 col-lg-12 col-md-12 col-12">
            <div class="position-relative">
                <div class="row gx-1 justify-content-end"> 
                    <div class="col col-lg-8 col-md-12 col-12"> 
                        <form id="userFilter" method="GET" class="row gx-1">
                            <label class="col col-lg-4 col-md-4 col-12 d-flex align-items-center">
                                From:&nbsp;<input type="date" id="dealer-from" name="from" class="form-control form-control-sm me-md-1" value="{{Request::get('from')}}">
                            </label>
                            <label class="col col-lg-4 col-md-4 col-12 d-flex align-items-center">
                                To:&nbsp;<input type="date" id="dealer-to" name="to" class="form-control form-control-sm" value="{{Request::get('to')}}"> 
                            </label>
                            <div class="col col-lg-2 col-md-2 col-6">
                                <button type="submit" class="btn btn_theme w-100" value="Filter">Submit</button>
                            </div>
                            <div class="col col-lg-2 col-md-2 col-6">
                                <a href="{{ route('dealerlist.index') }}" class="btn btn_theme w-100">Reset</a>
                            </div>
                        </form>
                    </div>
                    <div class="col col-lg-2 col-md-4 col-6"> 
                        <button id="downloadCSV" class="btn btn_dark w-100 text-nowrap">Download CSV</button>
                    </div>
                    <div class="col col-lg-2 col-md-4 col-6"> 
                        <a class="btn btn_dark w-100" data-bs-toggle="modal" data-bs-target="#importcsv" value="Upload CSV">Upload CSV</a>
                    </div>
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

    <div class="tab_box">
        <!-- Nav pills -->
        <ul class="nav nav-pills underline_tabs border-bottom mt-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="dealerlisttab" data-bs-toggle="pill" href="#dealerList">All Accounts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="pill" href="#dealerForm">Add Dealer Contact</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <!-- All Dealers -->
            <div id="dealerList" class="tab-pane active">
                <div class="position-relative table-responsive pt-2" style="min-height: 300px;">
                    <table class="table table-bordered w-100 dataTable" id="dealer_tabl">
                        <thead>
                            <tr>
                                <td>#</td>
                                <td>Name</td>
                                <td>Dealership Group</td>
                                <td>Email</td>
                                <td>Phone Number</td>
                                <td>City</td>
                                <td>Date</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <!-- Dealer Form Tab -->
            <div id="dealerForm" class="tab-pane fade">
                <div class="row justify-content-center mt-3">
                    <div class="col col-xl-7 col-lg-9 col-md-12 col-12">
                        <div class="position-relative mb-3">
                            <form action="{{ route('dealerlist.store') }}" id="dealerregisterForm" method="post" enctype="multipart/form-data">
                                @csrf    
                                <div class="tab_box_form border-0 bg_yellow rounded">
                                    <div class="row gx-2 mb-3">
                                        <div class="col col-12">
                                            <h6 class="mb-3">Personal Contact info</h6>
                                        </div>
                                        <div class="col col-md-6 col-12">
                                            <input type="text" id="dealer-first_name" name="first_name" placeholder="First Name" value="" class="form-control required">
                                        </div>
                                        <div class="col col-md-6 col-12">
                                            <input type="text" id="dealer-last_name" name="last_name" placeholder="Last Name" value="" class="form-control required">
                                        </div>
                                        <div class="col col-12">
                                            <input type="text" id="dealer-email" name="email" placeholder="Email" value="" class="form-control required" autocomplete="false">
                                        </div>
                                        <div class="col col-md-6 col-12">
                                            <input type="password" id="dealer-password" name="password" placeholder="Password" value="" class="form-control required">
                                        </div>
                                        <div class="col col-md-6 col-12">
                                            <input type="password" id="dealer-confirm_password" name="confirm_password" placeholder="Confirm Password" value="" class="form-control required">
                                        </div>
                                    </div>

                                    <div class="row gx-2">
                                        <div class="col col-12">
                                            <h6 class="mb-3">Add Dealership</h6>
                                        </div>
                                        <div class="col col-md-6 col-12">
                                            <input type="text" id="dealer-designation" name="designation" placeholder="Title" value="" class="form-control required">
                                        </div>
                                        <div class="col col-md-6 col-12">
                                            <input type="text" id="dealer-dealership_group" name="dealership_group" placeholder="Dealership Group Name" value="" class="form-control required">
                                        </div>
                                        <div class="col col-md-6 col-12">
                                            <input type="text" id="dealer-phone_number" name="phone_number" placeholder="Contact Number" value="" class="form-control required">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col col-md-12 col-12">
                                        <div class="position-relative text-center mt-3">
                                            <button type="submit" id="dealerSubmit" name="submit" class="btn btn_theme">Submit</button> 
                                        </div>
                                    </div> 
                                </div> 
                            </form>
                            <div id="errorMessages" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Dealer Modal -->
<div class="modal fade" id="viewDealer" tabindex="-1" aria-labelledby="viewDealerLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="position-relative float-start w-100 px-lg-5 py-lg-4 p-3">
            <h5 class="modal-title text_primary mb-3"><b>View Account Details</b></h5>

            <!-- Form -->
            <form class="gx-3 gy-2">
                @csrf    
                <div class="row gx-2 mb-3">
                    <div class="col col-12">
                        <h6 class="mb-3">Contact Details</h6>
                    </div>
                    <div class="col col-md-6 col-12">
                        <input type="text" id="view-dealer-first_name" name="first_name" placeholder="First Name" value="" class="form-control required" disabled>
                    </div>
                    <div class="col col-md-6 col-12">
                        <input type="text" id="view-dealer-last_name" name="last_name" placeholder="Last Name" value="" class="form-control required" disabled>
                    </div>
                    <div class="col col-12">
                        <input type="text" id="view-dealer-email" name="email" placeholder="Email" value="" class="form-control required" disabled>
                    </div>
                </div>

                <div class="row gx-2">
                    <div class="col col-12">
                        <h6 class="mb-3">Dealership Details</h6>
                    </div>
                    <div class="col col-md-6 col-12">
                        <input type="text" id="view-dealer-designation" name="designation" placeholder="Title" value="" class="form-control required" disabled>
                    </div>
                    <div class="col col-md-6 col-12">
                        <input type="text" id="view-dealer-dealership_group" name="dealership_group" placeholder="Dealership Group Name" value="" class="form-control required" disabled>
                    </div>
                    <div class="col col-md-6 col-12">
                        <input type="text" id="view-dealer-phone_number" name="phone_number" placeholder="Contact Number" value="" class="form-control required" disabled>
                    </div>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Dealer Modal -->
<div class="modal fade" id="editDealer" tabindex="-1" aria-labelledby="editDealerLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="position-relative float-start w-100 px-lg-5 py-lg-4 p-3">
            <h5 class="modal-title text_primary mb-3"><b>Edit Account Details</b></h5>

            <!-- Form -->
            <form action="{{ route('dealerlist.update') }}" id="editdealerform" class="gx-3 gy-2">
                @csrf    
                <input type="hidden" id="edit-dealer-id" name="dealer_id">
                <div class="row gx-2 mb-3">
                    <div class="col col-12">
                        <h6 class="mb-3">Contact Details</h6>
                    </div>
                    <div class="col col-md-6 col-12">
                        <input type="text" id="edit-dealer-first_name" name="first_name" placeholder="First Name" value="" class="form-control required">
                    </div>
                    <div class="col col-md-6 col-12">
                        <input type="text" id="edit-dealer-last_name" name="last_name" placeholder="Last Name" value="" class="form-control required">
                    </div>
                    <div class="col col-12">
                        <input type="text" id="edit-dealer-email" name="email" placeholder="Email" value="" class="form-control required">
                    </div>
                </div>

                <div class="row gx-2">
                    <div class="col col-12">
                        <h6 class="mb-3">Dealership Details</h6>
                    </div>
                    <div class="col col-md-6 col-12">
                        <input type="text" id="edit-dealer-designation" name="designation" placeholder="Title" value="" class="form-control required">
                    </div>
                    <div class="col col-md-6 col-12">
                        <input type="text" id="edit-dealer-dealership_group" name="dealership_group" placeholder="Dealership Group Name" value="" class="form-control required">
                    </div>
                    <div class="col col-md-6 col-12">
                        <input type="text" id="edit-dealer-phone_number" name="phone_number" placeholder="Contact Number" value="" class="form-control required">
                    </div>
                </div>

                <div class="row gx-2">
                    <div class="col col-6">
                        <button data-bs-dismiss="modal" class="btn btn-outline-primary w-100 mt-3">Cancel</button> 
                    </div> 
                    <div class="col col-6">
                        <button type="submit" id="submitStore" name="submit" class="btn btn_theme w-100 mt-3">Update Details</button> 
                    </div> 
                </div> 
            </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="position-relative float-start w-100 px-lg-5 py-lg-4 p-3">
            <h5 class="modal-title text_primary mb-3"><b>Change Password</b></h5>

            <!-- Form -->
            <form action="{{ route('dealerlist.changepassword') }}" id="changePasswordForm" class="gx-3 gy-2">
                @csrf    
                <input type="hidden" id="change-password-dealer-id" name="dealer_id">
                <div class="row gx-2 mb-3">
                    <div class="col col-12">
                        <input type="password" id="change-password-password" name="password" placeholder="New Password" class="form-control required">
                    </div>
                    <div class="col col-12">
                        <input type="password" id="change-password-confirm" name="password_confirmation" placeholder="Confirm Password" class="form-control required">
                    </div>
                </div>

                <div class="row gx-2">
                    <div class="col col-6">
                        <button data-bs-dismiss="modal" class="btn btn-outline-primary w-100 mt-3">Cancel</button> 
                    </div> 
                    <div class="col col-6">
                        <button type="submit" id="submitPasswordChange" name="submit" class="btn btn_theme w-100 mt-3">Change Password</button> 
                    </div> 
                </div> 
            </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <button type="button" class="btn btn-danger" onclick="actionmethod()">Yes</a>
                        <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import CSV Modal -->
<div class="modal fade" id="importcsv" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="dealerHeading" class="modal-title">Import Dealer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="errormsgdiv alert alert-danger" style="display:none;"></div>
                <div class="successmsgdiv alert alert-success" style="display:none;"></div>
                <form action="{{ route('dealers.import') }}" id="importdealer" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label class="form-label">Upload Csv</label>
                                <a href="{{ asset('assets/js/dealer.csv') }}" target="_blank"> Sample csv</a>
                                <input type="file" id="csv" class="form-control required" name="csv">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-12">
                            <button type="submit" id="importsubmit" class="btn btn-primary" value="create">Submit</button>
                        </div>
                    </div>  
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script>
var dealer ='';
var myurl = '{{route('dealerlist.tableData')}}';

$(function () {
    $('#createDealer').click(function () {
        $('#dealerSubmit').val("create-dealer");
        $('#dealer_id').val('');
        $('#dealerForm').trigger("reset");
        $('#dealerHeading').html("Create Dealer");
        $('#dealerModal').modal('show');
    });

    $('body').on('click', '.editDealer', function () {
        var dealer_id = $(this).data('id');
        $.get("{{ route('dealerlist.edit') }}?dealer_id=" + dealer_id, function (data) {
            $('#dealerHeading').html("Edit Dealer");
            $('#dealerSubmit').val("edit-dealer");
            $('#edit-dealer-id').val(data.id);
            $('#edit-dealer-first_name').val(data.first_name);
            $('#edit-dealer-last_name').val(data.last_name);
            $('#edit-dealer-email').val(data.email);
            $('#edit-dealer-designation').val(data.designation);
            $('#edit-dealer-dealership_group').val(data.dealership_group);
            $('#edit-dealer-phone_number').val(data.phone_number);
            $('#editDealer').modal('show');
        });
    });

    $('body').on('click', '.viewDealer', function () {
        var dealer_id = $(this).data('id');
        $.get("{{ route('dealerlist.edit') }}?dealer_id=" + dealer_id, function (data) {
            $('#view-dealer-first_name').val(data.first_name);
            $('#view-dealer-last_name').val(data.last_name);
            $('#view-dealer-email').val(data.email);
            $('#view-dealer-designation').val(data.designation);
            $('#view-dealer-dealership_group').val(data.dealership_group);
            $('#view-dealer-phone_number').val(data.phone_number);
            $('#viewDealer').modal('show');
        });
    });

    $('body').on('click', '.changePassword', function () {
        var dealer_id = $(this).data('id');
        $('#change-password-dealer-id').val(dealer_id);
        $('#changePasswordModal').modal('show');
    });

    // Date validation
    $('#userFilter').on('submit', function(event) {
        event.preventDefault();
        
        var fromDate = new Date($('#dealer-from').val());
        var toDate = new Date($('#dealer-to').val());
        var today = new Date();
        
        if (fromDate >= toDate) {
            alert('From date should be less than To date.');
            return false;
        }
        
        if (toDate > today) {
            alert('To date should not be greater than today.');
            return false;
        }
        
        this.submit();
    });

});

var $multigroupselect = $('#multigroupselect');
$multigroupselect.select2({
    placeholder: 'Select Dealer Group',
    templateSelection: function() {
        const selectedCount = $multigroupselect.select2('data').length;
        return selectedCount + ' selected';
    },
    templateResult: function(data) {
        return data.text;
    }
});

$multigroupselect.on('change', function() {
    const selectedCount = $multigroupselect.select2('data').length;
    $('#multigroupselect').next('.select2-container').find('.select2-selection__rendered').text(selectedCount + ' selected');
});

var $multinameselect = $('#multinameselect');
$multinameselect.select2({
    placeholder: 'Select Dealer Name',
    templateSelection: function() {
        const selectedCount = $multinameselect.select2('data').length;
        return selectedCount + ' selected';
    },
    templateResult: function(data) {
        return data.text;
    }
});

$multinameselect.on('change', function() {
    const selectedCount = $multinameselect.select2('data').length;
    $('#multinameselect').next('.select2-container').find('.select2-selection__rendered').text(selectedCount + ' selected');
});

dealer = $('#dealer_tabl').DataTable({
    processing: true,
    serverSide: true,
    order: [[0, 'desc']], 
    ajax: {
        url: '{{route('dealerlist.tableData')}}?source=dealer',
        type: 'GET',
        data: function (d) {
            d.start_date = $('#dealer-from').val();
            d.end_date = $('#dealer-to').val();
        }
    },
    columns: [
        { data: 'id', name: 'id' },
        {
            data: null,
            orderable: false,
            render: function (data, type, row) {
                return (row.first_name || '') + ' ' + (row.last_name || '');
            }
        },
        { data: 'dealership_group', name: 'dealership_group', orderable: false, },
        { data: 'email', name: 'email', orderable: false },
        {
            data: null,
            orderable: false,
            render: function (data, type, row) {
                return (row.dial_code || '') + ' ' + (row.phone_number || '');
            }
        },
        { data: 'city', name: 'city', orderable: false },
        {
            data: 'created_at',
            orderable: false,
            name: 'created_at',
            render: function (data, type, row) {
                return data ? moment(data).format('DD-MM-YYYY HH:mm:ss') : '';
            }
        },
        {
            data: null,
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
                deleteurl = `{{ route('dealerlist.delete') }}?dealer_id=` + row.id;
                
                var editButton = `<a class="dropdown-item editDealer" data-id="` + row.id + `">Edit</a>`;
                var deleteButton = `<a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="seturl('` + deleteurl + `')">Delete</a>`;
                var viewButton = `<a class="dropdown-item viewDealer" data-id="` + row.id + `">View</a>`;
                var changePasswordButton = `<a class="dropdown-item changePassword" data-id="` + row.id + `">Change Password</a>`;
                var loginas = `<a target="_blank" class="dropdown-item " href="{{ route('loginasdealer') }}?dealer=` + row.id + `">Login As Dealer</a>`;
                return  ` <div class="table_action dropdown">
                            <a class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></a>
                            <ul class="dropdown-menu">
                                <li>` + editButton + '</li><li>' + viewButton + '</li><li>' + changePasswordButton + '</li><li>' + deleteButton + `</li><li>` + loginas + `</li>
                            </ul>
                        </div>`;
            },
        }
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
        info: 'Showing _START_ to _END_ of _TOTAL_ records',
        infoFiltered: '(filtered from _MAX_ total records)',
    }
});

$('#dealerregisterForm').on('submit', function(event) {
    event.preventDefault();
    
    if ($(this).valid()) {
        var submitButton = $(this).find('[type="submit"]');
        submitButton.prop('disabled', true).text('Please wait...');
        
        var formData = $(this).serialize();
        var url = $(this).attr('action');
        
        runajax(url, formData, 'post', '', 'json', function(output) {
            submitButton.prop('disabled', false).text('Submit');
            
            if (output.success) {
                $('#dealerModal').modal('hide');
                
                window.location.href ='{{ route('dealerlist.index') }}'
               
                $('#dealerregisterForm')[0].reset();
            } else {
                for (var key in output.data) {
                    var existvalue = $('#dealer-' + key).val();
                    
                    // Adding a unique method for each error dynamically
                    jQuery.validator.addMethod(key + "error", function(value, element) {
                        return this.optional(element) || value !== existvalue;
                    }, jQuery.validator.format(output.data[key][0]));
                    
                    var inputField = jQuery('#dealer-' + key);
                    inputField.addClass(key + "error");
                    inputField.valid();
                }
            }
        });
    }
});


$('#importdealer').on('submit', function (event) {
    event.preventDefault();
    if ($(this).valid()) {
        var submitButton = $(this).find('[type="submit"]');
        submitButton.prop('disabled', true).text('Please wait...');
        var formData = new FormData($('#importdealer')[0]);
        var url = $(this).attr('action');
        uploadajax(url, formData, 'post', '', 'json', function (output) {
            if (output.success) {
                $('.successmsgdiv').html(output.message).show();
                $('#importcsv').modal('hide');
                dealer.ajax.reload();
            } else {
                var errorsHtml = '<ul>';
                $.each(output.errors, function (index, errorObj) {
                    // Add row information to the error message
                    errorsHtml += '<li>Row ' + errorObj.row + ':<ul>';
                    $.each(errorObj.errors, function (field, messages) {
                        $.each(messages, function (i, message) {
                            errorsHtml += '<li>' + message + '</li>';
                        });
                    });
                    errorsHtml += '</ul></li>';
                });
                errorsHtml += '</ul>';
                
                // Display the error messages
                $('.errormsgdiv').html(errorsHtml).show();
            }

            submitButton.prop('disabled', false).text('Submit');
        });
    }
});

function actionmethod() {
    var actionsurl = $('#seturl').val();
    runajax(actionsurl, '', 'get', '', 'json', function (output) {
        if (output.success) {
            $('.modal').modal('hide');
            dealer.ajax.reload();
        }
    });
}

function seturl(url) {
    $('#seturl').val(url);
}

$('#editdealerform').on('submit', function(event) {
    event.preventDefault();
    if ($(this).valid()) {
        var submitButton = $(this).find('[type="submit"]');
        submitButton.prop('disabled', true).text('Please wait...');
        var formData = $(this).serialize();
        var url = $(this).attr('action');
        runajax(url, formData, 'post', '', 'json', function(output) {
            submitButton.prop('disabled', false).text('Submit');
            if (output.success) {
                $('#editDealer').modal('hide');
                dealer.ajax.reload();
                $('#editdealerform')[0].reset();
            } else {
                for (var key in output.data) {
                    existvalue = $('#edit-dealer-' + key).val();
                    jQuery.validator.addMethod(key + "error", function(value, element) {
                        return this.optional(element) || value !== existvalue;
                    }, jQuery.validator.format(output.data[key][0]));
                    jQuery('#edit-dealer-' + key).addClass(key + "error");
                    jQuery('#edit-dealer-' + key).valid();
                }
            }
        });
    }
});

$('#changePasswordForm').on('submit', function(event) {
    event.preventDefault();
    if ($(this).valid()) {
        var submitButton = $(this).find('[type="submit"]');
        submitButton.prop('disabled', true).text('Please wait...');
        var formData = $(this).serialize();
        var url = $(this).attr('action');
        runajax(url, formData, 'post', '', 'json', function(output) {
            submitButton.prop('disabled', false).text('Change Password');
            if (output.success) {
                $('#changePasswordModal').modal('hide');
                dealer.ajax.reload();
            } else {
                var errorsHtml = '<ul>';
                $.each(output.errors, function (key, error) {
                    errorsHtml += '<li>' + error + '</li>';
                });
                errorsHtml += '</ul>';
                $('#errorMessages').html(errorsHtml).show();
            }
        });
    }
});

$('#userFilter').on('submit', function(event) {
    event.preventDefault();
    dealer.draw();
});

$('#downloadCSV').on('click', function() {
    let from = $('#dealer-from').val();
    let to = $('#dealer-to').val();
    let url = `{{ route('dealerlist.downloadCSV') }}?start_date=${from}&end_date=${to}`;
    window.location.href = url;
});

</script>
@endsection
