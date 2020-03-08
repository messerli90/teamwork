<?php

namespace Messerli90\Teamwork\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Messerli90\Teamwork\Events\UserJoinedTeam;
use Messerli90\Teamwork\Events\UserLeftTeam;

/**
 *
 */
trait HasTeams
{
    /**
     * Many-to-Many relations with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Config::get('teamwork.team_model'), Config::get('teamwork.team_user_table'), 'user_id', 'team_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * @return mixed
     */
    public function ownedTeams()
    {
        return $this->teams()->wherePivot('role', 'owner');
        // return $this->teams()->where(Config::get('team_owner_column'), $this->getKey());
    }

    /**
     * One-to-Many relation with the invite model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invites()
    {
        return $this->hasMany(Config::get('teamwork.invite_model'), 'email', 'email');
    }

    /**
     * Boot the user model
     * Attach event listener to remove the many-to-many records when trying to delete
     * Will NOT delete any records if the user model uses soft deletes.
     *
     * @return void|bool
     */
    public static function bootHasTeams()
    {
        static::deleting(function (Model $user) {
            if (!method_exists(Config::get('teamwork.user_model'), 'bootSoftDeletes')) {
                $user->teams()->sync([]);
            }
            return true;
        });
    }

    /**
     * @param $team
     * @return mixed
     */
    protected function retrieveTeamId($team)
    {
        if (is_object($team)) {
            $team = $team->getKey();
        }
        if (is_array($team) && isset($team["id"])) {
            $team = $team["id"];
        }
        return $team;
    }

    /**
     * Returns if the user owns any team or given team
     *
     * @param Model|string|null $team
     * @return bool
     */
    public function ownsTeam($team = null)
    {
        if ($team) {
            return $this->teams()->where('id', $this->retrieveTeamId($team))->where("owner_id", $this->getKey())->exists();
        }

        return $this->teams()->where("owner_id", "=", $this->getKey())->exists();
    }

    /**
     * Alias to eloquent many-to-many relation's attach() method.
     *
     * @param mixed $team
     * @param string $role
     * @param array $pivotData
     * @return $this
     */
    public function attachTeam($team, $role = null, $pivotData = [])
    {
        $team = $this->retrieveTeamId($team);

        $role = $role ?: 'member';
        if ($role && !in_array($role, Config::get('teamwork.member_roles'))) {
            throw new \Exception('Provided role is invalid. Allowed roles: ' . Config::get('teamwork.member_roles'));
        }
        $pivotData['role'] = $role;

        $this->teams()->syncWithoutDetaching([
            $team => $pivotData
        ]);

        event(new UserJoinedTeam($this, $team));

        if ($this->relationLoaded('teams')) {
            $this->load('teams');
        }

        return $this;
    }

    /**
     * Alias to eloquent many-to-many relation's detach() method.
     *
     * @param mixed $team
     * @return $this
     */
    public function detachTeam($team)
    {
        $team = $this->retrieveTeamId($team);
        $this->teams()->detach($team);

        event(new UserLeftTeam($this, $team));

        if ($this->relationLoaded('teams')) {
            $this->load('teams');
        }

        return $this;
    }

    /**
     * Attach multiple teams to a user
     *
     * @param mixed $teams
     * @return $this
     */
    public function attachTeams($teams)
    {
        foreach ($teams as $team) {
            $this->attachTeam($team);
        }
        return $this;
    }

    /**
     * Detach multiple teams from a user
     *
     * @param mixed $teams
     * @return $this
     */
    public function detachTeams($teams)
    {
        foreach ($teams as $team) {
            $this->detachTeam($team);
        }
        return $this;
    }
}
