<?php

namespace Messerli90\Teamwork;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Messerli90\Teamwork\Events\UserInvitedToTeam;

class Teamwork
{
    /**
     * Get the currently authenticated user or null.
     * @return Authenticatable|null
     */
    public function user()
    {
        return Auth::user();
    }

    /**
     * Invite an email adress to a team.
     * Either provide a email address or an object with an email property.
     *
     * @param string|Model $user
     * @param Model $team
     * @param callable $success
     * @throws \Exception
     */
    public function inviteToTeam($user, $team, $type = 'invite', callable $success = null)
    {
        if (is_null($team)) {
            throw new \Exception('No Team provided when attempting to invite user');
        } elseif (is_object($team)) {
            $team = $team->getKey();
        } elseif (is_array($team)) {
            $team = $team["id"];
        }

        if (is_object($user) && isset($user->email)) {
            $email = $user->email;
        } elseif (is_string($user)) {
            $email = $user;
        } else {
            throw new \Exception('The provided object has no "email" attribute and is not a string.');
        }

        if (!in_array($type, ['invite', 'request'])) {
            throw new \Exception('The provided type is invalid. Should be either "invite" or "request"');
        }

        $invite               = app(Config::get('teamwork.invite_model'));
        $invite->user_id      = $this->user()->getKey();
        $invite->team_id      = $team;
        $invite->type         = $type;
        $invite->email        = $email;
        $invite->accept_token = md5(uniqid(microtime()));
        $invite->deny_token   = md5(uniqid(microtime()));
        $invite->save();

        if (!is_null($success)) {
            // TODO: Create invite
            event(new UserInvitedToTeam($invite));
            return $success($invite);
        }
    }

    /**
     * Checks if the given user or email address has a pending invite for the
     * provided Team
     *
     * @param Model|string $user
     * @param Model|integer $team
     * @return bool
     */
    public function hasPendingInvite($user, $team)
    {
        if (is_object($user)) {
            $email = $user->email;
        } elseif (is_string($user)) {
            $email = $user;
        }

        if (is_object($team)) {
            $team = $team->getKey();
        }

        return DB::table(Config::get('teamwork.team_invites_table'))->where('email', $email)->where('team_id', $team)->exists();
    }

    /**
     * Checks if the given user or email address has a pending invite for the
     * provided Team
     *
     * @param Model|string $user
     * @param Model|integer $team
     * @return bool
     */
    public function getPendingInvite($user, $team)
    {
        if (is_object($user)) {
            $email = $user->email;
        } elseif (is_string($user)) {
            $email = $user;
        }

        if (is_object($team)) {
            $team = $team->getKey();
        }

        return app(Config::get('teamwork.invite_model'))->where('email', $email)->where('team_id', $team)->first();
    }

    /**
     * Get instance of Invite model from accept token
     *
     * @param string $token
     * @return TeamInvite
     */
    public function getInviteFromAcceptToken($token)
    {
        return app(Config::get('teamwork.invite_model'))::where('accept_token', $token)->first();
    }

    /**
     * @param TeamInvite $invite
     */
    public function acceptInvite(TeamInvite $invite)
    {
        $invite->user->attachTeam($invite->team);
        $invite->delete();
    }

    /**
     * @param string $token
     * @return TeamInvite
     */
    public function getInviteFromDenyToken($token)
    {
        return app(Config::get('teamwork.invite_model'))::where('deny_token', $token)->first();
    }

    /**
     * @param TeamInvite $invite
     */
    public function denyInvite(TeamInvite $invite)
    {
        $invite->delete();
    }
}
