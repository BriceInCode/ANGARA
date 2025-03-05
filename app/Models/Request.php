<?php

namespace App\Models;

use App\Enums\GenderType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'session_id',
        'request_number',
        'document_type',
        'request_reason',
        'civil_center_reference',
        'birth_act_number',
        'birth_act_creation_date',
        'declaration_by',
        'authorized_by',
        'first_name',
        'last_name',
        'gender',
        'birth_date',
        'birth_place',
        'father_name',
        'father_birth_date',
        'father_birth_place',
        'father_profession',
        'mother_name',
        'mother_birth_date',
        'mother_birth_place',
        'mother_profession',
    ];

    protected $casts = [
        'gender' => GenderType::class,
        'birth_date' => 'datetime',
        'father_birth_date' => 'datetime',
        'mother_birth_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }
}
