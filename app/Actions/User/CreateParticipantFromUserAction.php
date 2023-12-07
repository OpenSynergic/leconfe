<?php

namespace App\Actions\User;

use App\Actions\Participants\ParticipantCreateAction;
use App\Models\Enums\UserRole;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateParticipantFromUserAction
{
    use AsAction;

    public function handle(User $user)
    {
        $userData = $user->toArray();

        $userData['meta'] = $user->meta()->get()
            ->mapWithKeys(
                fn ($meta) => [$meta->key => $meta->value]
            )
            ->toArray();

        $participant = Participant::where('email', $user->email)->first();

        if (! $participant) {
            $participant = ParticipantCreateAction::run($userData);
        }

        foreach ($user->getRoleNames() as $userRole) {
            // Only for Author, Reviewer and Editor
            $shouldCreateParticipant = match ($userRole) {
                UserRole::Author->value, UserRole::Reviewer->value, UserRole::Editor->value => true,
                default => false,
            };
            if (! $shouldCreateParticipant) {
                continue;
            }
            $position = ParticipantPosition::where('name', $userRole)->first();
            $participant->positions()->detach($position);
            $participant->positions()->attach($position);
        }

        return $participant;
    }
}
