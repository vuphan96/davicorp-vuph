<?php
#app/Models/ShopEmailTemplate.php
namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class ShopEmailTemplate extends Model
{
    public $timestamps = false;
    public $table      = SC_DB_PREFIX .'shop_email_template';
    protected $guarded = [];

}
