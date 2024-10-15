<?php

namespace App\Models;

use App\Models\Post;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Partner extends Model
{
    use Notifiable;
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'tel',
        'postal_code',
        'country',
        'town',
        'website',
        'logo',
        'attachments',
        'fullname'
    ];

    protected static function boot()
    {
        parent::boot();

        // Automatically set fullname when user gets created
        static::creating(function ($partner) {
            $partner->fullname = "{$partner->firstname} {$partner->lastname}";
        });

        // Automatically set fullname when user gets updated
        static::updating(function ($partner) {
            $partner->fullname = "{$partner->firstname} {$partner->lastname}";
        });
    }
    

    public function partnerProposals() {
        return $this->belongsToMany(Proposal::class, 'proposal_partner', 'partner_id', 'proposal_id')->withTimestamps();
    }

    public function posts() {
        return $this->belongsToMany(Post::class, 'partner_post')->withPivot(['order'])->withTimestamps();
    }

    public function proposals() {
        return $this->morphMany(Proposal::class, 'coordinatorable');
    }

    public function postsCordinator() {
        return $this->morphMany(Post::class, 'coordinatorable');
    }
}
