<?php

namespace Modules\Business\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Settings\Concerns\HasSettings;

class Business extends Model
{
    use HasSettings;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
