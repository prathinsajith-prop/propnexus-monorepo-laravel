<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BixoProductPortals extends Model
{
    use SoftDeletes;
    protected $table = 'bixo_product_portals';
    protected $guarded = [];
}
