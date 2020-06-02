<?php

namespace App\Models\Setup;

use Illuminate\Database\Eloquent\Model;

class DBVersion extends Model
{
    protected $table = 'db_version';
    protected $fillable = [];
    protected $hidden = [];
    protected $guarded = [];

}
