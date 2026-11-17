<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuideCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function guides()
    {
        return $this->hasMany(CareGuide::class);
    }
}