<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('chat.{user1Id}-{user2Id}', function ($user, $user1Id, $user2Id) {
    return (int)$user->id === (int)$user1Id || (int)$user->id === (int)$user2Id;
});
