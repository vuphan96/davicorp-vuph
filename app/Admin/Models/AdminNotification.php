<?php

namespace App\Admin\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
  protected $table = SC_DB_PREFIX . 'admin_notification';
  protected $primaryKey = 'id';
  protected $guarded = [];

  public static function getAdminNotification() {
        return self::where('is_admin', 1);
  }
}

