<?php

namespace App\Policies;

use App\Models\DiscussionTopic;
use App\Models\User;

class DiscussionTopicPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->can('DiscussionTopic:viewAny')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DiscussionTopic $discussionTopic): bool
    {
        if ($user->can('DiscussionTopic:view')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->can('DiscussionTopic:create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DiscussionTopic $discussionTopic): bool
    {
        // Can't edit when topic is closed.
        if (! $discussionTopic->open) {
            return false;
        }

        if ($user->can('DiscussionTopic:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DiscussionTopic $discussionTopic): bool
    {
        if ($user->can('DiscussionTopic:delete')) {
            return true;
        }
    }
}
