<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $fillable = [
        'user_id', 'menu_id', 'order_id', 'rating', 'comment', 'is_approved'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    protected static function booted()
    {
        $updateMenuRating = function ($review) {
            $menu = $review->menu;
            if ($menu) {
                // Hanya hitung ulasan yang di-approve
                $approvedReviews = self::where('menu_id', $menu->id)
                    ->where('is_approved', true)
                    ->get();

                $menu->reviews_count = $approvedReviews->count();
                $menu->rating_avg = $approvedReviews->count() > 0 
                    ? round($approvedReviews->avg('rating'), 2) 
                    : 0;
                $menu->save();
            }
        };

        static::saved($updateMenuRating);
        static::deleted($updateMenuRating);
    }
}
