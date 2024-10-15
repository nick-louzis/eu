<?php

namespace App\Models;

use App\Models\User;
use App\Observers\ProjectObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;



#[ObservedBy([ProjectObserver::class])]
class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'thumbnail',
        'title',
        'color',
        'slug',
        'category_id',
        'content',
        'tags',
        'published',
        'users_id',
        'description',
        'attachments',
        'submission_date',
        'coordinatorable_id',
        'coordinatorable_type',
    ];

    protected $casts = [
        'tags'=> 'array',
        'users_id' => 'array',
        'attachments'=>'array'
    ];

    public function coordinatorable()
    {
        return $this->morphTo();
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    // public function users(){
    //     return $this->belongsToMany(User::class,'post_user')->withPivot(['order'])->withTimestamps();
    // }

    public function partners(){
        return $this->belongsToMany(Partner::class,'partner_post')->withPivot(['order'])->withTimestamps();
    }

    public function getHasAttachmentsAttribute(){
        return !empty($this->attachments) && is_array($this->attachments) && count($this->attachments) > 0;
    }

    public function userCanEdit()
    {
        $user = auth()->user();

        
        $isCoordinator = $this->coordinatorable_type === 'App\Models\User' 
                         && $this->coordinatorable_id === $user->id;

        // The user can toggle if they are an admin or coordinator
        return [
            'isAdminLevel2' => $user->isAdmin <= 2,
            'isCordinator' => $isCoordinator
        ];
    }
}
