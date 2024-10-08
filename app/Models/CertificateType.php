<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $attributes = [
        'description' => 'N/A'
    ];

    public function classification()
    {
        return $this->belongsTo(CertificateClassification::class, 'certificate_classification_id');
    }
}
