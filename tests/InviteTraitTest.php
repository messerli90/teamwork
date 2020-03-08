<?php

namespace Messerli90\Teamwork\Tests;

use Messerli90\Teamwork\TeamInvite;
use Messerli90\Teamwork\Tests\Models\Team;
use Messerli90\Teamwork\Tests\Models\User;

class InviteTraitTest extends TestCase
{
    protected $user;
    protected $invite;
    protected $team;
    protected $inviter;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->team = factory(Team::class)->create();
        $this->inviter = $this->team->owner;

        $this->invite               = new TeamInvite();
        $this->invite->team_id      = $this->team->getKey();
        $this->invite->user_id      = $this->inviter->getKey();
        $this->invite->email        = $this->user->email;
        $this->invite->type         = 'invite';
        $this->invite->accept_token = md5(uniqid(microtime()));
        $this->invite->deny_token   = md5(uniqid(microtime()));
        $this->invite->save();

        auth()->login($this->user);
    }

    /** @test */
    public function get_teams()
    {
        $this->assertEquals($this->team->getKey(), $this->invite->team->getKey());
    }

    /** @test */
    public function get_user()
    {
        $this->assertEquals($this->user->getKey(), $this->invite->user->getKey());
    }

    /** @test */
    public function get_inviter()
    {
        $this->assertEquals($this->inviter->getKey(), $this->invite->inviter->getKey());
    }
}
