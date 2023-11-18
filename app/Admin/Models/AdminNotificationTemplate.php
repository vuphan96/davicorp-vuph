<?php

namespace App\Admin\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class AdminNotificationTemplate extends Model
{
  protected $table = SC_DB_PREFIX . 'admin_notification_template';
  protected $primaryKey = 'id';
  protected $guarded = [];
}