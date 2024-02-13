<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('goal.{id}', function ($user, $id){
    return (int) $user->id === (int) $id;
});

Broadcast::channel('goal.updated.{id}', function ($goal, $id){
    return (int) \App\Models\Goal::find($id)?->id ===  (int) $goal->id;
});

Broadcast::channel('goal.deleted', function ($goal, $id){
   return true;
});
