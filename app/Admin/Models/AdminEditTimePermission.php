<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;

class AdminEditTimePermission extends Model
{
    public $table = SC_DB_PREFIX . "admin_edit_time_permission";
    protected $guarded = [];
}
