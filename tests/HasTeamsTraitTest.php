<?php

namespace Messerli90\Teamwork\Tests;

use Messerli90\Teamwork\TeamInvite;
use Messerli90\Teamwork\Teamwork;
use Messerli90\Teamwork\Tests\Models\Team;
use Messerli90\Teamwork\Tests\Models\User;

class HasTeamsTraitTest extends TestCase
{
    protected $user;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function user_belongs_to_many_teams()
    {
        factory(Team::class, 3)->create()->each(function ($team) {
            $this->user->attachTeam($team);
        });

        $this->assertCount(3, $this->user->teams);
        $this->assertInstanceOf(Team::class, $this->user->teams()->first());
    }

    /** @test */
    public function get_teams_owned_by_user()
    {
        factory(Team::class, 3)->create()->each(function ($team) {
            $this->user->attachTeam($team);
        });
        factory(Team::class)->create([
            'owner_id' => $this->user->getKey()
        ]);

        $this->assertCount(1, $this->user->ownedTeams);
        $this->assertInstanceOf(Team::class, $this->user->ownedTeams->first());
    }

    /** @test */
    public function user_can_have_many_invites()
    {
        factory(TeamInvite::class, 3)->create([
            'email' => $this->user->email
        ]);

        $this->assertCount(3, $this->user->invites()->get());
    }

    /** @test */
    public function check_if_user_owns_any_team()
    {
        factory(Team::class)->create([
            'owner_id' => $this->user->getKey()
        ]);
        $tNoTeamUser = factory(User::class)->create();

        $this->assertTrue($this->user->ownsTeam());
        $this->assertFalse($tNoTeamUser->ownsTeam());
    }

    /** @test */
    public function check_if_user_owns_a_specific_team()
    {
        $owned = factory(Team::class)->create([
            'owner_id' => $this->user->getKey()
        ]);
        $notOwned = factory(Team::class)->create();

        $this->assertTrue($this->user->ownsTeam($owned));
        $this->assertFalse($this->user->ownsTeam($notOwned));
    }

    /** @test */
    public function attaches_user_to_team()
    {
        $tTeam = factory(Team::class)->create();

        $this->assertCount(1, $tTeam->users()->get());
        $this->user->attachTeam($tTeam);
        $this->assertCount(2, $tTeam->users()->get());
    }

    /** @test */
    public function attaches_user_to_team_with_role()
    {
        $tTeam = factory(Team::class)->create();

        $this->user->attachTeam($tTeam, 'moderator');

        $member = $tTeam->users()->where('user_id', $this->user->getKey())->withPivot('role')->first();
        $this->assertEquals('moderator', $member->pivot->role);
    }

    /** @test */
    public function detaches_user_from_team()
    {
        $tTeam = factory(Team::class)->create();

        $this->user->attachTeam($tTeam);
        $this->assertCount(2, $tTeam->users()->get());

        $this->user->detachTeam($tTeam);
        $this->assertCount(1, $tTeam->users()->get());
    }

    /** @test */
    public function attaches_user_to_multiple_teams()
    {
        $tTeams = factory(Team::class, 3)->create();

        $this->user->attachTeams($tTeams);
        $this->assertCount(3, $this->user->teams()->get());
    }

    /** @test */
    public function detaches_user_from_multiple_teams()
    {
        $tTeams = factory(Team::class, 3)->create();

        $this->user->attachTeams($tTeams);
        $this->assertCount(3, $this->user->teams()->get());

        $this->user->detachTeams($tTeams);
        $this->assertCount(0, $this->user->teams()->get());
    }

    /** @test */
    public function user_can_switch_roles()
    {
        $tTeam = factory(Team::class)->create();

        $this->user->attachTeam($tTeam);
        $this->assertEquals(
            'member',
            $this->user->teams()->where('team_id', $tTeam->getKey())
                ->withPivot('role')->first()->pivot->role
        );

        $this->user->switchRole($tTeam, 'moderator');
        $this->assertEquals(
            'moderator',
            $this->user->teams()->where('team_id', $tTeam->getKey())
                ->withPivot('role')->first()->pivot->role
        );
    }
}
