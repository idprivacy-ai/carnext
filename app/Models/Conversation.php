<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $connection = 'secondary_db';
    protected $table = 'conversations';
    //protected $primaryKey = 'conversation_id';
    public $timestamps = false; // if your table doesn't have timestamps columns

    protected $casts = [
        'conversation_id' => 'string',
    ];
}
