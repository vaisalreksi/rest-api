<?php

namespace App\Models\Setup;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
  protected $table = "audit_trail";
  public $timestamps = true;
  protected $primaryKey = 'id';
  // protected $with = ['userRole'];

  protected $fillable = [
       'module',
       'no_ref',
       'action',
       'users_id'
  ];

}
