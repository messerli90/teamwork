<?php

namespace Messerli90\Teamwork\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

/**
 * Trait used by Team model
 */
trait HasMembers
{
    /**
     * Attach owner on creation
     *
     * @return void
     */
    public static function bootHasMembers()
    {
        static::created(function (Model $team) {
            $team->users()->sync([$team->owner_id => ['role' => 'owner']]);
            return true;
        });
    }

    /**
     * One-to-Many relation with the invite model
     * @return mixed
     */
    public function invites()
    {
        return $this->hasMany(Config::get('teamwork.invite_model'), 'team_id', 'id');
    }

    /**
     * Many-to-Many relations with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(Config::get('teamwork.user_model'), Config::get('teamwork.team_user_table'), 'team_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Has-One relation with the user model.
     * This indicates the owner of the team
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function owner()
    {
        $userModel   = Config::get('teamwork.user_model');
        $userKeyName = (new $userModel())->getKeyName();
        return $this->hasOne(Config::get('teamwork.user_model'), $userKeyName, Config::get('teamwork.team_owner_column'));
    }

    /**
     * Helper function to determine if a user is part of this team
     *
     * @param Model $user
     * @return bool
     */
    public function hasUser(Model $user)
    {
        return $this->users()->where($user->getKeyName(), $user->getKey())->exists();
    }
}
