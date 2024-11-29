<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormBuilder extends Model
{
    use HasFactory;
    protected $table = "cform_builders";
    protected $fillable = [
        'name',
        'page_id',
        'content',
    ];
    protected $casts = [
        'content' => 'array'
    ];

    public function scopeMakeQuery($query, $data)
    {

        $model = $this->query();
       
        if($this->checkExistinArray('id',$data))  $model = $model->where('id',$data['id']);
        if ($this->checkExistinArray('search', $data)) {
            $searchValue = $data['search']['value'];
          
            if($searchValue){
                
                $model = $model->where(function ($query) use ($searchValue) {
                    $query->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('created_at', 'like', '%' . $searchValue . '%');   
                });

            }
        }
    
        if ($this->checkExistinArray('order', $data)) {
            $orderColumn = $data['order'][0]['column'];
            $orderDirection = $data['order'][0]['dir'];
    
            $orderColumns = ['id', 'name', 'created_at'];
    
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
