<?php

namespace Messerli90\Teamwork\Tests;

use Messerli90\Teamwork\TeamInvite;
use Messerli90\Teamwork\Teamwork;
use Messerli90\Teamwork\Tests\Models\Team;
use Messerli90\Teamwork\Tests\Models\User;

class TeamworkTest extends TestCase
{
    protected $user;
    protected $teamwork;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        auth()->login($this->user);
        $this->teamwork = new Teamwork();
    }

    /** @test */
    public function invite_user_with_user_object()
    {
        $tTeam = factory(Team::class)->create();
        $tUser = factory(User::class)->create();

        $this->assertEquals(0, TeamInvite::count());
        $this->teamwork->inviteToTeam($tUser, $tTeam);
        $this->assertEquals(1, TeamInvite::count());

        $tInvite = TeamInvite::first();
        $this->assertEquals($tUser->email, $tInvite->email);
        $this->assertEquals('invite', $tInvite->type);
    }

    /** @test */
    public function invite_user_with_email()
    {
        $tTeam = factory(Team::class)->create();
        $tUser = factory(User::class)->create();

        $this->assertEquals(0, TeamInvite::count());
        $this->teamwork->inviteToTeam($tUser->email, $tTeam);
        $this->assertEquals(1, TeamInvite::count());

        $tInvite = TeamInvite::first();
        $this->assertEquals($tUser->email, $tInvite->email);
    }

    /** @test */
    public function invite_user_with_team_as_array()
    {
        $tTeam = factory(Team::class)->create();
        $tUser = factory(User::class)->create();

        $this->assertEquals(0, TeamInvite::count());
        $this->teamwork->inviteToTeam($tUser, $tTeam->toArray());
        $this->assertEquals(1, TeamInvite::count());

        $tInvite = TeamInvite::first();
        $this->assertEquals($tUser->email, $tInvite->email);
    }

    /** @test */
    public function invite_user_with_team_as_string()
    {
        $tTeam = factory(Team::class)->create();
        $tUser = factory(User::class)->create();

        $this->assertEquals(0, TeamInvite::count());
        $this->teamwork->inviteToTeam($tUser, $tTeam->getKey());
        $this->assertEquals(1, TeamInvite::count());

        $tInvite = TeamInvite::first();
        $this->assertEquals($tUser->email, $tInvite->email);
    }

    /** @test */
    public function create_request_to_join_team()
    {
        $tTeam = factory(Team::class)->create();
        $tUser = factory(User::class)->create();

        $this->assertEquals(0, TeamInvite::count());
        $this->teamwork->inviteToTeam($tUser, $tTeam->getKey(), 'request');
        $this->assertEquals(1, TeamInvite::count());

        $tInvite = TeamInvite::first();
        $this->assertEquals('request', $tInvite->type);
    }

    /** @test */
    public function checks_if_user_has_pending_invite()
    {
        $tTeam = factory(Team::class)->create();
        $tUserWith = factory(User::class)->create();
        $tUserWithout = factory(User::class)->create();
        $this->teamwork->inviteToTeam($tUserWith, $tTeam);

        $hasInvite = $this->teamwork->hasPendingInvite($tUserWith, $tTeam);
        $hasNotInvite = $this->teamwork->hasPendingInvite($tUserWithout, $tTeam);

        $this->assertTrue($hasInvite);
        $this->assertFalse($hasNotInvite);
    }

    /** @test */
    public function returns_pending_invite_for_user()
    {
        $tTeam = factory(Team::class)->create();
        $tUser = factory(User::class)->create();
        $this->teamwork->inviteToTeam($tUser, $tTeam);

        $tInvite = $this->teamwork->getPendingInvite($tUser, $tTeam);

        $this->assertEquals($tUser->email, $tInvite->email);
    }

    /** @test */
    public function gets_invite_from_accept_token()
    {
        $tTeam = factory(Team::class)->create();
        $tUser = factory(User::class)->create();
        $this->teamwork->inviteToTeam($tUser, $tTeam);
        $tInvite = TeamInvite::first();

        $invite = $this->teamwork->getInviteFromAcceptToken($tInvite->accept_token);

        $this->assertEquals($tInvite->id, $invite->id);
        $this->assertEquals($tInvite->accept_token, $invite->accept_token);
    }

    /** @test */
    public function gets_invite_from_deny_token()
    {
        $tTeam = factory(Team::class)->create();
        $tUser = factory(User::class)->create();
        $this->teamwork->inviteToTeam($tUser, $tTeam);
        $tInvite = TeamInvite::first();

        $invite = $this->teamwork->getInviteFromDenyToken($tInvite->deny_token);

        $this->assertEquals($tInvite->id, $invite->id);
        $this->assertEquals($tInvite->deny_token, $invite->deny_token);
    }

    /** @test */
    public function add_user_to_team_after_accepting_invite()
    {
        $tTeam = factory(Team::class)->create();
        $tUser = factory(User::class)->create();
        $this->teamwork->inviteToTeam($tUser, $tTeam);
        $tInvite = TeamInvite::first();

        $this->assertEquals(1, $tTeam->users()->count());
        $this->teamwork->acceptInvite($tInvite);
        $this->assertEquals(2, $tTeam->users()->count());
        $this->assertEquals(0, TeamInvite::count());
    }

    /** @test */
    public function remove_invite_after_denying_invite()
    {
        $tTeam = factory(Team::class)->create();
        $tUser = factory(User::class)->create();
        $this->teamwork->inviteToTeam($tUser, $tTeam);
        $tInvite = TeamInvite::first();
        $this->assertEquals(1, TeamInvite::count());

        $this->teamwork->denyInvite($tInvite);

        $this->assertEquals(0, TeamInvite::count());
    }
}
