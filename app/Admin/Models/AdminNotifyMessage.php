<?php

namespace App\Admin\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class AdminNotifyMessage extends Model
{
  protected $table = SC_DB_PREFIX . 'messages';
  protected $primaryKey = 'id';
  protected $guarded = [];

  public static function htmlToPlainText($str){
    $str = str_replace('&nbsp;', ' ', $str);
    $str = html_entity_decode($str, ENT_QUOTES | ENT_COMPAT , 'UTF-8');
    $str = html_entity_decode($str, ENT_HTML5, 'UTF-8');
    $str = html_entity_decode($str);
    $str = htmlspecialchars_decode($str);
    $str = strip_tags($str);

    return $str;
  }
}