<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyPermissionUser extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function permission(){
        return $this->belongsTo(CompanyPermission::class, 'company_permission_id');
    }
}
