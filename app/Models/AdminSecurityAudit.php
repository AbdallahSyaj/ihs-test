<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSecurityAudit extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'actor_admin_id',
        'subject_admin_id',
        'action',
        'ip_address',
    ];
}
