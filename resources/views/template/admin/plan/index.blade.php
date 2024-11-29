@extends('layouts.admin')
@section('content')
<input type="hidden" id="seturl" value="">
<!-- Page Content Start -->
<div class="admin_inner_content bg-white">
    <div class="row">
        <div class="page_title">
            <h2>Plan</h2>
            <div class="page_title_btn">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#cratePlan">Add Plan</button>
            </div>
        </div>
    </div>
    <hr>

    <div class="row">
        
        <div class="col col-12">
            <div class="position-relative table-responsive">
                <table class="table table-bordered w-100 dataTable" id="plan_tabl">
                    <thead>
                        <tr>
                            <td>Plan</td>
                            <td>Stripe Id</td>
                            <td>Amount</td>
                            <td>Period</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                    @foreach($plan as $key =>$value)
                        <tr>
                            <td>{{ $value->name}}</td>
                            <td>{{ $value->stripe_plan_id}}</td>
                            <td>{{ $value->price}}</td>
                            <td>{{ $value->interval}}</td>
                            <td>
                                <div class="btn_group">
                                <button class="btn btn-outline-secondary btn-sm" onclick="setEditData('{{ $value->id }}', '{{ $value->name }}', `{{ $value->description }}`, '{{ $value->price }}', '{{ $value->interval }}')" data-bs-toggle="modal" data-bs-target="#editPlan">Edit</button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="seturl('{{ route('admin.plan.delete',['plan_id'=>$value->id]) }}')" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</button>
                                </div>  
                            </td> 
                        </tr>

                    @endforeach
                    
                </table>
            </div>
        </div>
    </div>
</div>
       

<!-- Create Patient -->
<div class="modal fade" id="cratePlan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createAppointmentModalLabel">Add Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Appointment Form -->
                    <form id="addplan" method="POST" action="{{ route('admin.plan.add') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Name:</label>
                            <input type="text" class="form-control" id="name" name="name" value="">
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description:</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">Price:</label>
                            <input type="text" class="form-control" id="price" name="price" placeholder="9.99">
                        </div>
                        
                        <div class="mb-3">
                            <label for="interval" class="form-label">Invoice Interval:</label>
                            <select class="form-control" id="interval" name="interval" >
                                <option value="month">Monthly</option>
                                <option value="day">Daily</option>
                                <option value="quater">Quaterly</option>
                                <option value="year">Yearly</option>
                            </select>
                           
                        </div>
                        <button type="submit" class="btn btn-primary">Create Plan</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Plan -->
<div class="modal fade" id="editPlan" tabindex="-1" aria-labelledby="editPlanLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPlanLabel">Edit Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Edit Plan Form -->
                <form id="editPlanForm" method="POST" action="{{ route('admin.plan.update')}}">
                    @csrf
                   
                    
                    <input type="hidden" id="editPlanId" name="id">

                    <div class="mb-3">
                        <label for="editName" class="form-label">Name:</label>
                        <input type="text" class="form-control required" id="editName" name="name">
                    </div>

                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description:</label>
                        <textarea class="form-control required" id="editDescription" name="description"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="editPrice" class="form-label">Price:</label>
                        <input type="text" class="form-control required" id="editPrice" name="price" placeholder="9.99">
                    </div>

                    <div class="mb-3">
                        <label for="editInterval" class="form-label">Invoice Interval:</label>
                        <select class="form-control required" id="editInterval" name="interval">
                            <option value="month">Monthly</option>
                            <option value="quarter">Quarterly</option>
                            <option value="year">Yearly</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Plan</button>
                </form>
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

<!-- Approve -->
<div  class="modal fade" id="approve" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header border-0">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pb-4">
            <div class="position-relative text-center">
                <i class="fa-regular fa-circle-check mb-3 text-success"></i>
                <h5 class="mb-4">Are you sure? You want to Approve?</h5>
                <div class="position-relative text-center">                 
                    <button  type="button" class="btn btn-success" onclick="actionmethod()">Yes</a>
                    <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>


   
@endsection
@section('script')
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
function setEditData(id, name, description, price, interval) {
    $('#editPlanId').val(id);
    $('#editName').val(name);
    //$('#editDescription').val(description);
    CKEDITOR.instances['editDescription'].setData(description);
    $('#editPrice').val(price);
    $('#editInterval').val(interval);

    // Set the form action URL dynamically
   // $('#editPlanForm').attr('action', '/admin/plan/' + id);
}
CKEDITOR.replace('description');
    CKEDITOR.replace('editDescription');

 mydatatable = $('#plan_tabl').DataTable();
   
    $('#addplan').on('submit', function (event) {
            
        event.preventDefault();
          if($(this).valid()){
            for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
            var submitButton = $(this).find('[type="submit"]');
                submitButton.prop('disabled', true).text('Please wait...');

            // Serialize form data
                var formData = $(this).serialize();
            
                url =$(this).attr('action');
                runajax(url, formData, 'post', '', 'json', function(output) {
                        // var output = JSON.parse(res);
                        submitButton.prop('disabled', false).text('Submit');
                      //window.location.reload();
                
                })
            }
    });

    $('#editPlanForm').on('submit', function (event) {
        event.preventDefault();
        if ($(this).valid()) {
            var submitButton = $(this).find('[type="submit"]');
            submitButton.prop('disabled', true).text('Please wait...');
            for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
            var formData = $(this).serialize();
            var url = $(this).attr('action');

            runajax(url, formData, 'post', '', 'json', function (output) {
                submitButton.prop('disabled', false).text('Update Plan');
                if (output.success) {
                   //window.location.reload();
                } else {
                    alert('Failed to update the plan');
                }
            });
        }
    });


   
     
 
    function actionmethod( )
    {

        actionsurl = $('#seturl').val();
    
        runajax(actionsurl, '', 'get', '', 'json', function(output) {
                
                console.log(output);
                if (output.success) 
                {
                    $('.modal').modal('hide');
                  
                    mydatatable.ajax.reload();

                    
                    
                }else{
                    
                    
                        
                }
            }); 

    }

    function setId(id,first_name,last_name,email,phone){
        $('#patient_id').val(id);
        $('#pre_first_name').val(first_name);
        $('#pre_last_name').val(last_name);
        $('#pre_email').val(email);
        $('#pre_phone').val(phone);
    }

    function seturl(url )
    {
        $('#seturl').val(url);

    }

</script>

@endsection
