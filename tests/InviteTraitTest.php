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
    // public function setUp(): void
    // {
    //     parent::setUp();
    //     // auth()->login($this->user);
    // }

    /** @test */
    public function get_team()
    {
        $invite = factory(TeamInvite::class)->create();
        $this->assertInstanceOf(Team::class, $invite->team);
    }

    /** @test */
    public function get_user()
    {
        $user = factory(User::class)->create();
        $invite = factory(TeamInvite::class)->create([
            'email' => $user->email
        ]);
        $this->assertInstanceOf(User::class, $invite->user);
        $this->assertEquals($user->email, $invite->email);
    }

    /** @test */
    public function get_inviter()
    {
        $inviter = factory(User::class)->create();
        $invite = factory(TeamInvite::class)->create([
            'user_id' => $inviter->getKey()
        ]);
        $this->assertInstanceOf(User::class, $invite->inviter);
        $this->assertEquals($inviter->getKey(), $invite->user_id);
    }
}
