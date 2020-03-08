<?php

namespace Messerli90\Teamwork\Tests;

use Messerli90\Teamwork\Teamwork;
use Messerli90\Teamwork\Tests\Models\Team;
use Messerli90\Teamwork\Tests\Models\User;

class HasTeamsTraitTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        //$this->user = factory(User::class)->create();

        // $this->invite               = new TeamInvite();
        // $this->invite->team_id      = $this->team->getKey();
        // $this->invite->user_id      = $this->team->owner_id;
        // $this->invite->email        = $this->user->email;
        // $this->invite->type         = 'invite';
        // $this->invite->accept_token = md5(uniqid(microtime()));
        // $this->invite->deny_token   = md5(uniqid(microtime()));
        // $this->invite->save();
    }

    /** @test */
    public function get_teams()
    {
        $tUser = factory(User::class)->create();
        $tTeam = factory(Team::class)->create();
        $tUser->attachTeam($tTeam);

        $this->assertCount(1, $tUser->teams);
        $this->assertEquals($tTeam->getKey(), $tUser->teams->first()->getKey());
    }

    /** @test */
    public function get_owned_teams()
    {
        $tTeam = factory(Team::class)->create();
        $tOwner = $tTeam->owner;

        $this->assertCount(1, $tOwner->ownedTeams);
    }

    /** @test */
    public function get_invites()
    {
        $tTeam = factory(Team::class)->create();
        $tUser = factory(User::class)->create();
        auth()->login($tTeam->owner);

        $teamwork = new Teamwork;
        $teamwork->inviteToTeam($tUser, $tTeam);

        $this->assertCount(1, $tUser->invites);
    }

    /** @test */
    public function owns_any_team()
    {
        $tTeam = factory(Team::class)->create();
        $tOwner = $tTeam->owner;
        $tUser = factory(User::class)->create();

        $this->assertTrue($tOwner->ownsTeam());
        $this->assertFalse($tUser->ownsTeam());
    }

    /** @test */
    public function attaches_user_to_team()
    {
        $tTeam = factory(Team::class)->create();
        $tUser = factory(User::class)->create();

        $this->assertCount(1, $tTeam->users()->get());
        $tUser->attachTeam($tTeam);
        $this->assertCount(2, $tTeam->users()->get());
    }

    /** @test */
    public function attaches_user_to_team_with_role()
    {
        $tTeam = factory(Team::class)->create();
        $tUser = factory(User::class)->create();

        $this->assertCount(1, $tTeam->users()->get());
        $tUser->attachTeam($tTeam, 'moderator');

        $member = $tTeam->users()->where('user_id', $tUser->getKey())->withPivot('role')->first();
        $this->assertCount(2, $tTeam->users()->get());
        $this->assertEquals('moderator', $member->pivot->role);
    }

    /** @test */
    public function detaches_user_from_team()
    {
        $tTeam = factory(Team::class)->create();
        $tUser = factory(User::class)->create();

        $tUser->attachTeam($tTeam);
        $this->assertCount(2, $tTeam->users()->get());

        $tUser->detachTeam($tTeam);
        $this->assertCount(1, $tTeam->users()->get());
    }

    /** @test */
    public function attaches_user_to_multiple_teams()
    {
        $tTeams = factory(Team::class, 3)->create();
        $tUser = factory(User::class)->create();

        $tUser->attachTeams($tTeams);
        $this->assertCount(3, $tUser->teams()->get());
    }

    /** @test */
    public function detaches_user_from_multiple_teams()
    {
        $tTeams = factory(Team::class, 3)->create();
        $tUser = factory(User::class)->create();

        $tUser->attachTeams($tTeams);
        $this->assertCount(3, $tUser->teams()->get());

        $tUser->detachTeams($tTeams);
        $this->assertCount(0, $tUser->teams()->get());
    }
}
