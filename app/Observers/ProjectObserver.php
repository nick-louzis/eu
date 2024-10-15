<?php

namespace App\Observers;

use App\Models\Post;
use App\Models\User;
use Barryvdh\Debugbar\Facades\Debugbar;
use Filament\Notifications\Notification;
use App\Notifications\ProjectNotification;
use Filament\Notifications\Actions\Action;

class ProjectObserver
{

    /**
     * Handle the Post "created" event.
     */
    public function created(Post $project): void
    {
        // A notification will be sent to admins that a new project has been created
        // Get them Users with isAdmin = true and notify them
        // if the coordinator is one of them admins, ** no need to send, exclude him **
        $authorId = auth()->user()->id;
        
        $admins = User::where('isAdmin', true)
        ->where('id', '!=', $authorId)
        ->get();
        
        foreach ($admins as $admin) {
            Notification::make()
                ->title("Νέο Πρόγραμμα: $project->title")
                ->success()
                ->actions([
                    Action::make('view')
                        ->button()
                        ->url("/admin/posts/$project->id/edit", shouldOpenInNewTab: true),
                ])
                ->sendToDatabase($admin, isEventDispatched: true);
        }
        
        
        // Another notification goes to the project coordinator if hes not the userLoggedIn
        if(($project->coordinatorable_id === $authorId && $project->coordinatorable_type === "App\Models\User") || ( $project->coordinatorable_type === "App\Models\Partner")) {
            return;
        }
        
        Notification::make()
            ->title("Είσαι Συντονιστής στο Νέο Πρόγραμμα: $project->title")
            ->info()
            ->actions([
                Action::make('view')
                    ->button()
                    ->url("/admin/posts/$project->id/edit", shouldOpenInNewTab: true),
            ])
            ->sendToDatabase($project->coordinatorable, isEventDispatched: true);
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $project): void
    {
        $authorId = auth()->user()->id;
        Debugbar::info("Reaching here at least $authorId");
        $admins = User::where('isAdmin', true)
        ->where('id', '!=', $authorId)
        ->get();

        foreach ($admins as $admin) {
            Debugbar::info($admin);
            Notification::make()
                ->title("A project has been updated")
                ->sendToDatabase($admin);
        }

        
        // Another notification goes to the project coordinator if hes not the userLoggedIn
        if( $project->coordinatorable_id === $authorId && $project->coordinatorable_type === "App\Models\User") {
            return;
        }
        
        Notification::make()
            ->title('Saved successfully')
            ->sendToDatabase($project->coordinatorable);
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        //
    }
}
