<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ChatController;

use App\Http\Controllers\Admin\AdminStoreController;
use App\Http\Controllers\Admin\AdminEmployeeController;
use App\Http\Controllers\Admin\AdminDealerController;
use App\Http\Controllers\Admin\ConversationController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\AdsController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PlanController ;
use App\Http\Controllers\Admin\ProfileController; 
use App\Http\Controllers\Admin\AdminRoleController; 
use App\Http\Controllers\Admin\SettingController; 


use App\Http\Controllers\Admin\AdminLoginController ;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\FormBuildersController;
use App\Http\Controllers\Admin\FormsController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;

use App\Http\Middleware\EnsureDealerEmailVerified;
use App\Http\Middleware\checkStoreCount;
use App\Http\Controllers\Dealer\LoginController as DealerLogin;
use App\Http\Controllers\Dealer\DealerController ;
use App\Http\Controllers\Dealer\VerificationController ;
use App\Http\Controllers\Dealer\SubscriptionController; 
use App\Http\Controllers\Dealer\EmployeeController; 
use App\Http\Controllers\Dealer\StoreController; 

Route::get('/robots.txt', function() {
   // return response("User-agent: *\nDisallow: /", 200)
               // ->header('Content-Type', 'text/plain');
});
Route::post('stripe/webhook', [WebhookController::class, 'handleWebhook']);
Route::get('login', [LoginController::class, 'index'])->name('login');
/* */
Route::get('/facetbykeyword', [VehicleController::class, 'dependentKeyword'])->name('dependentKeyword');

Route::post('/user/login', [LoginController::class, 'userLogin'])->name('userlogin');
Route::post('/register', [LoginController::class, 'userRegister'])->name('userregister');
Route::post('/forgotpassword', [LoginController::class, 'forgotPassword'])->name('userforgotPassword');
//Route::post('/resetpassword', [LoginController::class, 'resetPassword'])->name('userresetpassword');
Route::get('/email/verify/{id}/{hash}', [LoginController::class, 'verify'])->name('verification.userverify');
Route::post('/user-password/reset', [LoginController::class, 'resetPassword'])->name('userpassword.update');

/* */
Route::get('dealer/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('login', [VehicleController::class, 'index'])->name('login');
Route::get('call_sms', [VehicleController::class, 'callSms'])->name('call_sms');
Route::post('generate-otp', [LoginController::class, 'sendOtp'])->name('generateOtp');
Route::post('login', [LoginController::class, 'login'])->name('validateOtp');
Route::get('/', [VehicleController::class, 'index'])->name('home');
Route::get('user-reset/{token}', [VehicleController::class, 'index'])->name('user.password.reset');
Route::get('/vehicle', [VehicleController::class, 'searchActiveCars'])->name('vechile');
Route::get('/dealer/vehicle', [VehicleController::class, 'dealerVehicle'])->name('source.vechile');
Route::get('/vehicle-detail', [VehicleController::class, 'getVehicleDetail'])->name('vechile_detail');
Route::get('/vechile_item', [VehicleController::class, 'getCarbytabbased'])->name('vechile.facet');
Route::get('make_favourite', [VisitController::class, 'makeFavouite'])->name('makeFavouite');
Route::get('vin_visit', [VisitController::class, 'visitStore'])->name('visitStore');

Route::post('chat', [ChatController::class, 'index'])->name('chat');
Route::get('/social-login/{provider}/callback',[LoginController::class,'providerCallback']);
Route::get('/social-auth/{provider}',[LoginController::class,'redirectToProvider'])->name('social.redirect');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [UserController::class, 'index'])->name('dashboard');
    Route::post('/user_change-password', [LoginController::class, 'changePassword'])->name('user.changePassword');
    Route::post('updateAddress', [UserController::class, 'updateAddress'])->name('updateAddress');
    Route::post('updateProfile', [UserController::class, 'updateProfile'])->name('updateProfile');
   // Route::get('vin_visit', [VisitController::class, 'visitStore'])->name('visitStore');
    Route::get('vin_visit_list', [VisitController::class, 'listVisit'])->name('listVisit');
   
    Route::get('favourite', [VisitController::class, 'favouriteList'])->name('favourite');
    Route::get('logout', [UserController::class, 'logout'])->name('logout');
    Route::post('updateProfilepic', [UserController::class, 'updateProfilepic'])->name('updateProfilepic');

});

Route::prefix('dealer')->group(function () {
    
    Route::get('/', [DealerLogin::class, 'index'])->name('dealer.index');
    Route::get('user-reset/{token}', [DealerLogin::class, 'index'])->name('dealer.reset');
    Route::get('/login', [DealerLogin::class, 'loginpage'])->name('dealer.login');
    Route::get('/social-login/{provider}/callback',[DealerLogin::class,'providerCallback']);
    Route::get('/social-auth/{provider}',[DealerLogin::class,'redirectToProvider'])->name('dealer.social.redirect');
    
    Route::post('/loginemail', [DealerLogin::class, 'userLogin'])->name('dealerlogin');
    Route::post('/register', [DealerLogin::class, 'dealerRegister'])->name('dealerregister');
    Route::post('/register_preview', [DealerLogin::class, 'dealerRegisterPreview'])->name('dealerregister_preview');
    Route::post('/forgotpassword', [DealerLogin::class, 'forgotPassword'])->name('dealerforgotPassword');
    Route::post('/dealer-password/reset', [DealerLogin::class, 'resetPassword'])->name('dealerpassword.update');

    // Route for generating OTP for dealer login
    Route::post('/generate-otp', [DealerLogin::class, 'sendOtp'])->name('dealer.generateOtp');
    Route::post('/request-demo', [DealerLogin::class, 'requestDemo'])->name('dealer.requestDemo');
    
    Route::post('/login', [DealerLogin::class, 'login'])->name('dealer.validateOtp');

    Route::middleware(['auth:dealer'])->group(function () {


        Route::post('/user_change-password', [LoginController::class, 'changePassword'])->name('dealer.changePassword');
        Route::get('/sendverify', [DealerController::class, 'sendVerification'])->name('dealer.sendverify');
        Route::get('/logout', [DealerController::class, 'logout'])->name('dealer.logout');
        Route::get('/dealerverification', [DealerController::class, 'logout'])->name('dealer.verification');
        Route::post('/updateAddress', [DealerController::class, 'updateAddress'])->name('dealer.updateAddress');
        Route::post('/updateProfile', [DealerController::class, 'updateProfile'])->name('dealer.updateProfile');
        Route::post('/updateProfilepic', [DealerController::class, 'updateProfilepic'])->name('dealer.updateProfilepic');
        Route::get('/profile', [DealerController::class, 'profile'])->name('dealer.profile');
        Route::get('/sendverification', [DealerController::class, 'sendVerification'])->name('dealer.sendverify');
       
        Route::middleware(EnsureDealerEmailVerified::class)->group(function () {

            Route::prefix('/store')->group(function () {
                Route::post('/cart', [StoreController::class, 'cartPopup'])->name('dealer.cart');
                Route::post('/save-cart', [StoreController::class, 'saveCart'])->name('dealer.save_cart');
                Route::post('/apply-coupon', [DealerController::class, 'applyCoupon'])->name('dealer.apply_coupon');
                Route::post('/move-to-cart', [DealerController::class, 'moveToCart'])->name('dealer.move_to_cart');
                Route::get('/', [StoreController::class, 'index'])->name('dealer.stores');
                Route::post('/', [StoreController::class, 'saveStore'])->name('dealer.addstore');
                Route::post('/update',[StoreController::class, 'updateStore'])->name('dealer.updateStore');
                Route::get('/add_payment_method', [StoreController::class, 'addPaymentMethod'])->name('dealer.store.add_payment_method');
                Route::post('/subscribe', [StoreController::class, 'processPayment'])->name('dealer.store.processPayment');
                Route::post('/create_cancel_request', [StoreController::class, 'createRequest'])->name('dealer.store.create.request');
            });
            Route::middleware(checkStoreCount::class)->group(function () {
               
                Route::prefix('/employee')->group(function () {
                    Route::get('/', [EmployeeController::class, 'index'])->name('dealer.employee');
                    Route::post('/', [EmployeeController::class, 'storeEmployee'])->name('dealer.store.employee');
                    Route::post('/update',[EmployeeController::class, 'updateEmployee'])->name('dealerupdate.employee');
                    Route::post('/changepassword',[EmployeeController::class, 'changePassword'])->name('dealer.changePassword');
                    Route::get('/delete', [EmployeeController::class, 'deleteEmployee'])->name('dealer.delete.staff');
                    
                });
                
                
            

                Route::prefix('/role')->group(function () {
                    Route::get('/', [EmployeeController::class, 'dealerRole'])->name('dealer.role');
                    Route::post('/', [EmployeeController::class, 'adddealerRole'])->name('adddealer.role');
                    Route::post('/update',[EmployeeController::class, 'updateRole'])->name('dealerupdate.role');
                    Route::get('/delete', [EmployeeController::class, 'deleteRole'])->name('dealer.delete.role');
                    
                });



                Route::get('/cancelsubscription', [SubscriptionController::class, 'cancelSubscription'])->name('dealer.cancelsubscription');
                Route::get('/subscription', [SubscriptionController::class, 'index'])->name('dealer.subscription');
                Route::get('/payment_method', [SubscriptionController::class, 'myPaymentMethod'])->name('dealer.payment_method');
                Route::post('/updatecard', [SubscriptionController::class, 'updateCard'])->name('dealer.update.card');
                Route::get('/add_payment_method', [SubscriptionController::class, 'addPaymentMethod'])->name('dealer.add_payment_method');
                Route::post('/checksubscription', [SubscriptionController::class, 'checkPlan'])->name('subscribe.checkPlan');
                Route::post('/subscribe', [SubscriptionController::class, 'processSubscriptionAjax'])->name('subscribe.process.ajax');
                Route::get('/dashboard', [DealerController::class, 'index'])->name('dealer.dashboard');
                Route::get('/verify', [DealerController::class, 'profile'])->name('dealer.verify');
                //Route::get('/subscription', [DealerController::class, 'subscription'])->name('dealer.subscription');
                Route::get('/myvehicle', [DealerController::class, 'myvehicle'])->name('dealer.myvehicle');
                Route::get('/mylead', [DealerController::class, 'mylead'])->name('dealer.mylead');
                Route::get('/car_detail', [DealerController::class, 'carDetail'])->name('dealer.car_detail');
                Route::get('/myleadcar', [DealerController::class, 'myleadCar'])->name('dealer.myleadcar');
                Route::get('/lead_detail', [DealerController::class, 'leadDetail'])->name('dealer.lead_detail');
            });
        });
    });
});

Route::prefix('admin')->group(function () {

    Route::get('/', [AdminLoginController::class, 'index'])->name('admin.login');
    Route::get('/forget', [PasswordResetLinkController::class, 'create'])
    ->name('password.request');

    Route::post('forget', [PasswordResetLinkController::class, 'store'])
    ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
    ->name('password.store');
    
    Route::post('/', [AdminLoginController::class, 'login'])->name('admin.submitLogin');

    Route::middleware('auth:admin')->group(function () {

        Route::prefix('/employee')->group(function () {
            Route::post('/check-role-permission', [AdminEmployeeController::class, 'checkRolePermission'])->name('admin.checkrole');;
            Route::get('/', [AdminEmployeeController::class, 'index'])->name('admin.employee');
            Route::get('/data', [AdminEmployeeController::class, 'employeeData'])->name('admin.staff.data');
            Route::post('/', [AdminEmployeeController::class, 'storeEmployee'])->name('admin.staff.save');
            Route::post('/update',[AdminEmployeeController::class, 'updateEmployee'])->name('admin.staff.update');
            Route::get('/delete', [AdminEmployeeController::class, 'deleteEmployee'])->name('admin.staff.delete');
            Route::post('/changepassword', [AdminEmployeeController::class, 'resetPassword'])->name('admin.staff.changePassword');
        });
        Route::prefix('/store')->group(function () {
            Route::get('/', [AdminStoreController::class, 'index'])->name('admin.stores');
            Route::post('/', [AdminStoreController::class, 'saveStore'])->name('admin.addstore');
            Route::post('/update',[AdminStoreController::class, 'updateStore'])->name('admin.updateStore');
            Route::get('/tableData', [AdminStoreController::class, 'storeTableData'])->name('admin.store.tableData');
            Route::get('get/', [AdminStoreController::class, 'viewStore'])->name('admin.store.view');
            Route::delete('/delete', [AdminStoreController::class, 'deleteStore'])->name('admin.deleteStore');
            Route::get('/downloadCSV', [AdminStoreController::class, 'downloadCSV'])->name('admin.store.downloadCSV');
            Route::post('/uploadCSV', [AdminStoreController::class, 'uploadCSV'])->name('admin.store.uploadCSV');
            Route::get('/cancel-subscription', [AdminStoreController::class, 'cancelSubscription'])->name('admin.cancelrequest.Subscription');
            Route::get('/cancellation-request', [AdminStoreController::class, 'cancelRequest'])->name('admin.cancellation.request.list');
            Route::get('/cancellation-request/data', [AdminStoreController::class, 'cancelRequestData'])->name('cancellation.request.data');
            Route::get('/cancellation-request/approve', [AdminStoreController::class, 'approveRequest'])->name('admin.request.approve');
            Route::get('/cancellation-request/reject', [AdminStoreController::class, 'rejectRequest'])->name('admin.request.reject');
            
        });

        Route::prefix('/role')->group(function () {
            Route::get('/', [AdminRoleController::class, 'role'])->name('admin.role');
            Route::post('/', [AdminRoleController::class, 'addadminRole'])->name('admin.role.add');
            Route::post('/update',[AdminRoleController::class, 'updateRole'])->name('admin.role.update');
            Route::get('/data',[AdminRoleController::class, 'roleData'])->name('adminrole.data');
            Route::get('/edit',[AdminRoleController::class, 'getData'])->name('admin.role.edit');
            Route::get('/delete',[AdminRoleController::class, 'deleteRole'])->name('admin.role.delete');
        });
        Route::prefix('/subscription')->group(function () {
            Route::get('/', [EmployeeController::class, 'dealerRole'])->name('admin.subscription');
          
        });
        Route::prefix('/transaction')->group(function () {
            Route::get('/', [TransactionController::class, 'index'])->name('admin.transaction');
            Route::get('/delete', [TransactionController::class, 'delete'])->name('admin.transaction.delete');
            Route::get('/data', [TransactionController::class, 'data'])->name('admin.transaction.data');
            Route::post('/', [TransactionController::class, 'add'])->name('admin.transaction.add');
            Route::post('/update', [TransactionController::class, 'update'])->name('admin.transaction.update');
            // In routes/web.php
            Route::get('/csv', [TransactionController::class, 'downloadCSV'])->name('admin.transaction.csv');

        });
        Route::get('/dashboard', [ProfileController::class, 'index'])->name('admin.dashboard');
        Route::get('/profile', [ProfileController::class, 'profile'])->name('admin.profile');
        Route::get('/editProfile', [ProfileController::class, 'editProfile'])->name('admin.editProfile');
        Route::post('/updateProfile', [ProfileController::class, 'updateProfile'])->name('admin.updateProfile');
        Route::post('/updatePassword', [ProfileController::class, 'updatePassword'])->name('admin.updatePassword');
        Route::get('/logout', [ProfileController::class, 'logout'])->name('admin.logout');

        Route::get('/log', [ConversationController::class, 'index'])->name('admin.log');
        Route::get('/logstore', [ConversationController::class, 'index'])->name('admin.log');

        Route::get('/userlist', [ProfileController::class, 'userlist'])->name('userlist.index');
        Route::get('/userlist/delete', [ProfileController::class, 'userlist_delete'])->name('userlist.delete');
        Route::post('/userlist/store', [ProfileController::class, 'userlist_store'])->name('userlist.store');
        Route::get('/userlist/tableData', [ProfileController::class, 'userlist_tableData'])->name('userlist.tableData');
        Route::get('/userlist/{id}/edit', [ProfileController::class, 'userlist_edit'])->name('userlist.edit');

       
        Route::prefix('/dealerlist')->group(function () {
            Route::post('/dealers/import', [AdminDealerController::class, 'import'])->name('dealers.import');
            
            Route::get('/loginasdealer', [AdminDealerController::class, 'loginAs'])->name('loginasdealer');
            Route::get('/alldealers', [AdminDealerController::class, 'alldealers'])->name('alldealers.index');
            Route::get('/', [AdminDealerController::class, 'dealerlist'])->name('dealerlist.index');
            Route::get('/delete', [AdminDealerController::class, 'dealerlist_delete'])->name('dealerlist.delete');
            Route::post('/register', [AdminDealerController::class, 'dealerRegister'])->name('dealerlist.store');
            Route::post('/update', [AdminDealerController::class, 'dealerUpdate'])->name('dealerlist.update');
            Route::post('/changepassword', [AdminDealerController::class, 'changepassword'])->name('dealerlist.changepassword');
            Route::get('/tableData', [AdminDealerController::class, 'dealerlist_tableData'])->name('dealerlist.tableData');
            Route::get('getdealer', [AdminDealerController::class, 'dealerlist_edit'])->name('dealerlist.edit');
            Route::post('/import', [AdminDealerController::class, 'import'])->name('dealers.import');
            Route::get('/downloadCSV', [AdminDealerController::class, 'downloadCSV'])->name('dealerlist.downloadCSV');

        });

        

        Route::post('/setting', [SettingController::class, 'updateSetting'])->name('admin.settingupdate');
        Route::post('/updateProfile', [ProfileController::class, 'updateProfile'])->name('admin.updateProfile');
        Route::get('/setting', [SettingController::class, 'index'])->name('admin.setting');

        Route::get('/plan', [PlanController::class, 'index'])->name('admin.plan.index');
        Route::post('/plan/add', [PlanController::class, 'storePlan'])->name('admin.plan.add');
        Route::post('/plan/update', [PlanController::class, 'updatePlan'])->name('admin.plan.update');
        Route::get('/plan/delete', [PlanController::class, 'deletePlan'])->name('admin.plan.delete');
       
        Route::get('/ads', [AdsController::class, 'index'])->name('ads.index');
        Route::get('/ads/{id}/edit', [AdsController::class, 'edit'])->name('ads.edit');
        Route::get('/ads/delete', [AdsController::class, 'delete'])->name('ads.delete');
        Route::post('/ads/store', [AdsController::class, 'store'])->name('ads.store');
        Route::get('/ads/tableData', [AdsController::class, 'tableData'])->name('ads.tableData');
        Route::get('/ads/disable', [AdsController::class, 'disable'])->name('ads.disable');
        Route::get('/ads/enable', [AdsController::class, 'enable'])->name('ads.enable');

        Route::get('/contact-us', [SettingController::class, 'contact_us'])->name('contact.index');
        Route::get('/contact-us/{id}/show', [SettingController::class, 'contact_us_show'])->name('contact.show');
        Route::get('/contact-us-delete/delete', [SettingController::class, 'contact_us_delete'])->name('contact.delete');;
        Route::get('/contact-us/tableData', [SettingController::class, 'contact_tableData'])->name('contact.tableData');


        Route::get('/request-demo', [SettingController::class, 'requestDemo'])->name('reqeust.index');
        Route::get('/request-demo/{id}/show', [SettingController::class, 'requestShow'])->name('request.show');
        Route::get('/request-demo/delete', [SettingController::class, 'requestDemoDelete'])->name('request.delete');;
        Route::get('/request-demo/tableData', [SettingController::class, 'requestDatatable'])->name('request.tableData');


        Route::get('/post', [PostController::class, 'index'])->name('post.index');
        Route::get('/post/{id}/edit', [PostController::class, 'edit'])->name('post.edit');
        Route::get('/post/delete', [PostController::class, 'delete'])->name('post.delete');
        Route::post('/post/store', [PostController::class, 'store'])->name('post.store');
        Route::get('/post/tableData', [PostController::class, 'tableData'])->name('post.tableData');
        Route::get('/post/publish', [PostController::class, 'publish'])->name('post.publish');
        Route::get('/post/draft', [PostController::class, 'draft'])->name('post.draft');
        Route::post('/post/upload', [PostController::class, 'uploadImage'])->name('post.upload');

        Route::get('/media', [MediaController::class, 'index'])->name('media.index');
        Route::get('/media/{id}/edit', [MediaController::class, 'edit'])->name('media.edit');
        Route::get('/media/delete', [MediaController::class, 'delete'])->name('media.delete');
        Route::post('/media/store', [MediaController::class, 'store'])->name('media.store');
        Route::get('/media/tableData', [MediaController::class, 'tableData'])->name('media.tableData');
        Route::get('/media/publish', [MediaController::class, 'publish'])->name('media.publish');
        Route::get('/media/draft', [MediaController::class, 'draft'])->name('media.draft');
        Route::post('/media/upload', [MediaController::class, 'uploadImage'])->name('media.upload');


        Route::get('/lead', [LeadController::class, 'index'])->name('lead.index');
        Route::get('/lead/delete', [LeadController::class, 'leadlist_delete'])->name('lead.delete');
        Route::get('/lead/view', [LeadController::class, 'leadlistView'])->name('lead.view');
       // Route::post('/lead/store', [LeadController::class, 'dealerlist_store'])->name('lead.store');
        Route::get('/lead/tableData', [LeadController::class, 'leadTableData'])->name('lead.tableData');
        Route::get('/lead/downloadlead', [LeadController::class, 'leadDownload'])->name('lead.download');
       // Route::get('/lead/{id}/edit', [LeadController::class, 'dealerlist_edit'])->name('lead.edit');

        Route::get('/pages', [PagesController::class, 'index'])->name('pages.index');
        Route::get('/pages/{id}/edit', [PagesController::class, 'edit'])->name('pages.edit');
        Route::get('/pages/delete', [PagesController::class, 'delete'])->name('pages.delete');
        Route::post('/pages/store', [PagesController::class, 'store'])->name('pages.store');
        Route::put('/pages/update/{id}', [PagesController::class, 'update'])->name('pages.update');
        Route::get('/pages/tableData', [PagesController::class, 'tableData'])->name('pages.tableData');
        Route::get('/pages/create', [PagesController::class, 'create'])->name('pages.create');
       // Route::get('/pages/draft', [MediaController::class, 'draft'])->name('media.draft');
       // Route::post('/pages/upload', [MediaController::class, 'uploadImage'])->name('media.upload');

        //Route::get('/pages/{id}/sections', [PagesController::class, 'sections'])->name('pages.sections');
    
        Route::get('/form-builder', [FormBuildersController::class, 'index'])->name('form-builder.index');
        Route::get('/form/tableData', [FormBuildersController::class, 'tableData'])->name('form.tableData');
    
        Route::view('/formbuilder', 'template.admin.FormBuilder.create');
    
        Route::post('/save-form-builder', [FormBuildersController::class, 'create']);
    
        //Route::delete('/form-delete/{id}', [FormBuildersController::class, 'destroy']);
        Route::get('/form-delete', [FormBuildersController::class, 'delete'])->name('form.delete');
    
        Route::view('/edit-form-builder/{id}', 'template.admin.FormBuilder.edit');
        Route::get('/get-form-builder-edit', [FormBuildersController::class, 'editData']);
        Route::post('/update-form-builder', [FormBuildersController::class, 'update']);
    
        Route::view('/read-form-builder/{id}', 'template.admin.FormBuilder.read');
        Route::get('/get-form-builder', [FormsController::class, 'read']);
        Route::post('/save-form-transaction', [FormsController::class, 'create'])->name('forms.create');

    });

});

Route::get('/blog', [SettingController::class, 'blog_list'])->name('blog_list');
Route::get('/blog/{slug}', [SettingController::class, 'blog'])->name('blogs');

Route::get('/media', [SettingController::class, 'media_list'])->name('media_list');
Route::get('/media/{slug}', [SettingController::class, 'media'])->name('media');

Route::get('/{slug}', [PageController::class, 'pages'])->name('frontend.page');

/*Route::get('/privacy-policy', function () {
    return redirect('/privacy-policy');
})->name('privacy');

Route::get('/term-and-conditions', function () {
    return redirect('/term-and-conditions');
})->name('term');*/

Route::get('/term-and-conditions', [PageController::class, 'term'])->name('term');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/about-us', [PageController::class, 'about'])->name('about');
Route::get('/faqs', [PageController::class, 'faq'])->name('faq');
Route::get('/contact-us', [PageController::class, 'contact'])->name('contactus');

Route::post('/contactus', [PageController::class, 'contact_us_store'])->name('contactus.post');


//require __DIR__.'/auth.php';