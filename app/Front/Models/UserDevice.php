<?php

namespace App\Front\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $table = SC_DB_PREFIX .'device_of_admin_user';

    public function owner()
    {
        return $this->belongsTo(AdminUser::class, 'id', 'user_id');
    }
}
