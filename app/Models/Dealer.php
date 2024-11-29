<?php

namespace App\Models;
use App\Notifications\VerifyEmail;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Cashier\Billable;
use App\Notifications\DealerResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;

class Dealer extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable,Billable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'dealers';
    protected $fillable = [
        'phone_number',
        'dial_code',
        'designation',
        'first_name',
        'last_name',
        'email',
        'adfmail',
        'source',
        'social_account',
        'dealership_name',
        'email_verified_at',
        'password',
        'parent_id',
        'dealership_group'

       // 'adfemail'
       
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    public function getEmailVerificationUrlAttribute()
    {
        return URL::temporarySignedRoute(
            'verification.verify', // The route name that handles verification
            now()->addMinutes(60), // Link expiration time
            [
                'id' => $this->getKey(), // Dealer ID
                'hash' => sha1($this->getEmailForVerification()), // Hash of the email
            ]
        );
    }

    public function storelist(){
        return $this->hasMany(DealerSource::class,'dealer_id','id');
    }


    public function socialAccounts(){
        return $this->hasMany(DealerSocialAccount::class,'dealer_id','id');
    }

    public function assignSource(){
        return $this->hasMany(AssignDealer::class,'dealer_id','id');
    }


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new DealerResetPasswordNotification($token));
    }


    public function scopeMakeQuery($query, $data)
    {

        $model = $this->query();
       
        if($this->checkExistinArray('id',$data))  $model = $model->where('id',$data['id']);
        if($this->checkExistinArray('parent_id',$data))  $model = $model->where('parent_id',$data['parent_id']);
        if($this->checkExistinArray('dealership_group',$data))  $model = $model->where('dealership_group',$data['dealership_group']);
        if ($this->checkExistinArray('search', $data)) {
            $searchValue = $data['search']['value'];
          
            if($searchValue){
                
                $model = $model->where(function ($query) use ($searchValue) {
                    $query->where('first_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('last_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('email', 'like', '%' . $searchValue . '%')
                    ->orWhere('dealership_group', 'like', '%' . $searchValue . '%');
                   
                });

            }
        }

        if($this->checkExistinArray('start_date',$data))  $model = $model->where('start_date','>=',$data['start_date']);
        if($this->checkExistinArray('end_date',$data))  $model = $model->where('end_date','>=',$data['end_date']);
        if ($this->checkExistinArray('order', $data)) {
            $orderColumn = $data['order'][0]['column'];
            $orderDirection = $data['order'][0]['dir'];
    
            $orderColumns = ['id', 'name', 'email', 'phone_number', 'city', 'state', 'address', 'zip_code', 'created_at'];
    
            $orderBy = isset($orderColumns[$orderColumn]) ? $orderColumns[$orderColumn] : 'id';
    
            $model = $model->orderBy($orderBy, $orderDirection);
        }else{
            $model = $model->orderBy('id', 'desc');
        }
        
        return $model;
       
    }

    public function checkExistinArray($key,$data,$dafault=false)
    {
       return  isset($data[$key]) && $data[$key]!=''?true:$dafault;
    }
}
