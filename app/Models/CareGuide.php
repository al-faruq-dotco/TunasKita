<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareGuide extends Model
{
    protected $fillable = ['guide_category_id', 'user_id', 'title', 'content', 'image'];

    public function category()
    {
        return $this->belongsTo(GuideCategory::class, 'guide_category_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}