<?php

namespace Messerli90\Teamwork\Tests;

use Messerli90\Teamwork\TeamInvite;
use Messerli90\Teamwork\Teamwork;
use Messerli90\Teamwork\Tests\Models\Team;
use Messerli90\Teamwork\Tests\Models\User;

class HasMembersTraitTest extends TestCase
{
    protected $user;
    protected $team;
    protected $invite;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->team = factory(Team::class)->create();
        $this->user = factory(User::class)->create();

        $this->invite               = new TeamInvite();
        $this->invite->team_id      = $this->team->getKey();
        $this->invite->user_id      = $this->team->owner_id;
        $this->invite->email        = $this->user->email;
        $this->invite->type         = 'invite';
        $this->invite->accept_token = md5(uniqid(microtime()));
        $this->invite->deny_token   = md5(uniqid(microtime()));
        $this->invite->save();
    }

    /** @test */
    public function team_owner_gets_attached_as_user_on_team_creation()
    {
        $this->assertCount(1, $this->team->users);
    }

    /** @test */
    public function get_invites()
    {
        $invites = $this->user->invites()->get();
        $this->assertCount(1, $invites);
        // $this->assertEquals($this->invite->getKey(), $this->team->invites->first()->getKey());
    }

    /** @test */
    public function get_users()
    {
        $this->user->attachTeam($this->team);
        $this->assertCount(2, $this->team->users);
        // $this->assertEquals($this->user->getKey(), $this->team->users->first()->getKey());
    }

    /** @test */
    public function getting_users_includes_role()
    {
        $this->assertEquals('owner', $this->team->users->first()->pivot->role);
    }

    /** @test */
    public function get_owner()
    {
        $this->assertEquals($this->team->owner_id, $this->team->owner->getKey());
    }

    /** @test */
    public function check_user_belongs_to_team()
    {
        $this->user->attachTeam($this->team);
        $this->assertTrue($this->team->hasUser($this->user));
    }
}
