<?php

namespace Messerli90\Teamwork\Tests;

use Messerli90\Teamwork\TeamInvite;
use Messerli90\Teamwork\Teamwork;
use Messerli90\Teamwork\Tests\Models\Team;
use Messerli90\Teamwork\Tests\Models\User;

class HasMembersTraitTest extends TestCase
{
    protected $team;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->team = factory(Team::class)->create();
    }

    /** @test */
    public function team_owner_gets_attached_as_user_on_team_creation()
    {
        $this->assertCount(1, $this->team->users);
    }

    /** @test */
    public function team_has_many_invites()
    {
        factory(TeamInvite::class, 3)->create([
            'type' => 'invite',
            'team_id' => $this->team->getKey()
        ]);
        factory(TeamInvite::class, 3)->create([
            'type' => 'request',
            'team_id' => $this->team->getKey()
        ]);

        $this->assertCount(3, $this->team->invites);
    }

    /** @test */
    public function team_has_many_requests()
    {
        factory(TeamInvite::class, 3)->create([
            'type' => 'invite',
            'team_id' => $this->team->getKey()
        ]);
        factory(TeamInvite::class, 3)->create([
            'type' => 'request',
            'team_id' => $this->team->getKey()
        ]);

        $this->assertCount(3, $this->team->requests);
    }

    /** @test */
    public function team_has_many_users()
    {
        factory(User::class, 3)->create()->each(function ($user) {
            $user->attachTeam($this->team);
        });

        $this->assertCount(4, $this->team->users);
    }

    /** @test */
    public function getting_users_includes_role()
    {
        $this->assertEquals('owner', $this->team->users->first()->pivot->role);
    }

    /** @test */
    public function team_has_owner()
    {
        $this->assertInstanceOf(User::class, $this->team->owner);

        $this->assertEquals($this->team->owner_id, $this->team->owner->getKey());
    }

    /** @test */
    public function checks_user_belongs_to_team()
    {
        $tUser = factory(User::class)->create();

        $tUser->attachTeam($this->team);

        $this->assertTrue($this->team->hasUser($tUser));
    }
}
