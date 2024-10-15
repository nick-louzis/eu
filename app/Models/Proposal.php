<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Partner;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'submission_date',
        'description',
        'coordinatorable_id',
        'coordinatorable_type',
        'attachments',
        'approved',
    ];

    protected $casts = [
        'attachments' => 'array', // Automatically cast attachments to array when retrieving from the DB
    ];

    // Define relationships

    /**
     * Get the coordinator (either User or Partner)
     */
    public function coordinatorable()
    {
        return $this->morphTo();
    }

    /**
     * Get the categories associated with the proposal.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_proposal', 'proposal_id', 'category_id')
                    ->withTimestamps();
    }

    /**
     * Get the partners associated with the proposal.
     */
    public function partners()
    {
        return $this->belongsToMany(Partner::class, 'proposal_partner', 'proposal_id', 'partner_id')->withPivot(['order', 'assignments'])
                    ->withTimestamps();
    }

    public function userCanEdit()
    {
        $user = auth()->user();

        
        $isCoordinator = $this->coordinatorable_type === 'App\Models\User' 
                         && $this->coordinatorable_id === $user->id;

        // The user can toggle if they are an admin or coordinator
        return [
            'isAdmin' => $user->isAdmin,
            'isCordinator' => $isCoordinator
        ];
    }



}
