<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
     public function update(User $user, Event $event)
    {
        return $event->user_id == $user->id
            ? Response::allow()
            : Response::deny('Not your event');
    }

    public function delete(User $user, Event $event) {
        return $event->user_id == $user->id;
    }
}
