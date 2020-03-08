<?php

namespace Messerli90\Teamwork\Traits;

use Illuminate\Support\Facades\Config;

/**
 * Trait used by TeamInvite model
 */
trait InviteTrait
{
    /**
     * Has-One relations with the team model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function team()
    {
        return $this->hasOne(Config::get('teamwork.team_model'), 'id', 'team_id');
    }

    /**
     * Has-One relations with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user()
    {
        return $this->hasOne(Config::get('teamwork.user_model'), 'email', 'email');
    }

    /**
     * Has-One relations with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function inviter()
    {
        return $this->hasOne(Config::get('teamwork.user_model'), 'id', 'user_id');
    }
}