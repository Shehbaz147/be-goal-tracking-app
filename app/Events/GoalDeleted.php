<?php

namespace App\Events;

use App\Models\Goal;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GoalDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;



    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('goal.deleted');
    }


    public function broadcastAs(): string
    {
        return "GoalDeleted";
    }
}
