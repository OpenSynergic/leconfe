<?php

namespace Tests\Feature;

use App\Frontend\Website\Pages\InvitationRegister;
use App\Mail\Templates\VerifyUserEmail;
use App\Models\Conference;
use App\Models\Role;
use App\Models\ScheduledConference;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class InvitationRegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitation_registration_keeps_new_user_unverified_and_sends_verification_email(): void
    {
        Config::set('app.must_verify_email', true);
        Mail::fake();

        $conference = Conference::query()->create([
            'name' => 'Test Conference',
            'path' => 'test-conference',
        ]);

        $this->createInvitationRole($conference);

        $invitation = $this->createInvitation('invitee@example.com', 'invite-token-new-user', $conference);

        Livewire::test(InvitationRegister::class, ['token' => $invitation->token])
            ->set('given_name', 'Invitee')
            ->set('family_name', 'User')
            ->set('password', 'password12345')
            ->set('password_confirmation', 'password12345')
            ->set('privacy_statement_agree', true)
            ->call('register');

        $user = User::query()
            ->where('email', 'invitee@example.com')
            ->firstOrFail();

        $this->assertNull($user->email_verified_at);
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('user_invitations', [
            'id' => $invitation->id,
            'status' => 'accepted',
        ]);
        Mail::assertQueued(VerifyUserEmail::class, 1);
    }

    public function test_invitation_registration_redirects_existing_user_email_to_login(): void
    {
        Config::set('app.must_verify_email', true);
        Mail::fake();

        $conference = Conference::query()->create([
            'name' => 'Test Conference',
            'path' => 'test-conference',
        ]);

        $this->createInvitationRole($conference);

        $user = User::query()->create([
            'given_name' => 'Existing',
            'family_name' => 'User',
            'email' => 'existing@example.com',
            'password' => 'password12345',
        ]);

        $invitation = $this->createInvitation($user->email, 'invite-token-existing-user', $conference);

        Livewire::test(InvitationRegister::class, ['token' => $invitation->token])
            ->assertRedirect(route('livewirePageGroup.conference.pages.login', [
                'conference' => $conference->path,
            ]));

        $user->refresh();

        $this->assertNull($user->email_verified_at);
        $this->assertGuest();
        $this->assertDatabaseHas('user_invitations', [
            'id' => $invitation->id,
            'status' => 'pending',
        ]);
        Mail::assertNothingQueued();
    }

    public function test_scheduled_conference_invitation_registration_is_available_before_publish(): void
    {
        [$conference, $scheduledConference] = $this->createUnpublishedScheduledConferenceInvitationContext();
        $invitation = $this->createInvitation(
            'invitee@example.com',
            'scheduled-invite-token-new-user',
            $conference,
            $scheduledConference
        );

        $this->withoutVite()
            ->get($invitation->getRegisterUrl())
            ->assertOk()
            ->assertSee('Invitation Registration')
            ->assertDontSee(__('scheduled_conference.unpublished_description'));
    }

    public function test_scheduled_conference_invitation_can_be_accepted_before_publish(): void
    {
        [$conference, $scheduledConference] = $this->createUnpublishedScheduledConferenceInvitationContext();
        $user = User::factory()->create([
            'email' => 'invitee@example.com',
            'password' => Hash::make('password12345'),
        ]);

        $invitation = $this->createInvitation(
            $user->email,
            'scheduled-invite-token-existing-user',
            $conference,
            $scheduledConference
        );

        $this->actingAs($user)
            ->get($invitation->getAcceptUrl())
            ->assertRedirect(route('filament.scheduledConference.pages.dashboard', [
                'conference' => $conference->path,
                'serie' => $scheduledConference->path,
            ]));

        $this->assertDatabaseHas('user_invitations', [
            'id' => $invitation->getKey(),
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas(config('permission.table_names.model_has_roles', 'model_has_roles'), [
            'role_id' => Role::withoutGlobalScopes()
                ->where('name', 'Reviewer')
                ->where('conference_id', $conference->getKey())
                ->where('scheduled_conference_id', $scheduledConference->getKey())
                ->firstOrFail()
                ->getKey(),
            'model_type' => User::class,
            'model_id' => $user->getKey(),
            'conference_id' => $conference->getKey(),
            'scheduled_conference_id' => $scheduledConference->getKey(),
        ]);
    }

    public function test_scheduled_conference_invitation_existing_user_can_reach_login_before_publish(): void
    {
        [$conference, $scheduledConference] = $this->createUnpublishedScheduledConferenceInvitationContext();
        $user = User::factory()->create([
            'email' => 'existing-invitee@example.com',
            'password' => Hash::make('password12345'),
        ]);

        $invitation = $this->createInvitation(
            $user->email,
            'scheduled-invite-token-existing-user-login',
            $conference,
            $scheduledConference
        );

        $loginUrl = route('livewirePageGroup.scheduledConference.pages.login', [
            'conference' => $conference->path,
            'serie' => $scheduledConference->path,
        ]);

        $this->get($invitation->getAcceptUrl())
            ->assertRedirect($loginUrl);

        $this->withoutVite()
            ->get($loginUrl)
            ->assertOk()
            ->assertSee(__('general.login'))
            ->assertDontSee(__('scheduled_conference.unpublished_description'));
    }

    protected function createInvitationRole(Conference $conference, ?ScheduledConference $scheduledConference = null): void
    {
        Role::withoutGlobalScopes()->firstOrCreate([
            'name' => 'Reviewer',
            'guard_name' => 'web',
            'conference_id' => $conference->getKey(),
            'scheduled_conference_id' => $scheduledConference?->getKey() ?? 0,
        ]);
    }

    protected function createUnpublishedScheduledConferenceInvitationContext(): array
    {
        $conference = Conference::query()->create([
            'name' => 'Test Conference',
            'path' => 'test-conference',
        ]);

        $scheduledConference = ScheduledConference::query()->create([
            'conference_id' => $conference->getKey(),
            'title' => 'Draft Scheduled Conference',
            'path' => 'draft-scheduled-conference',
            'is_published' => false,
        ]);

        $this->createInvitationRole($conference, $scheduledConference);
        app()->setCurrentConferenceId($conference->getKey());
        app()->setCurrentScheduledConferenceId($scheduledConference->getKey());

        return [$conference, $scheduledConference];
    }

    protected function createInvitation(
        string $email,
        string $token,
        Conference $conference,
        ?ScheduledConference $scheduledConference = null
    ): UserInvitation {
        return UserInvitation::query()->create([
            'email' => $email,
            'role_name' => 'Reviewer',
            'token' => $token,
            'status' => 'pending',
            'conference_id' => $conference->getKey(),
            'scheduled_conference_id' => $scheduledConference?->getKey(),
        ]);
    }
}
