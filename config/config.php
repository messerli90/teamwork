<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | This is the User model used by Teamwork.
    |
    */
    'user_model' => App\User::class,

    /*
    |--------------------------------------------------------------------------
    | Users Table
    |--------------------------------------------------------------------------
    |
    | This is the users table name used by Teamwork.
    |
    */
    'users_table' => 'users',

    /*
    |--------------------------------------------------------------------------
    | Team Model
    |--------------------------------------------------------------------------
    |
    | This is the Team model used by Teamwork to create correct relations.  Update
    | the team if it is in a different namespace.
    |
    */
    'team_model' => App\Team::class,

    /*
    |--------------------------------------------------------------------------
    | Teams Table
    |--------------------------------------------------------------------------
    |
    | This is the teams table name used by Teamwork.
    |
    */
    'teams_table' => 'teams',

    /*
    |--------------------------------------------------------------------------
    | Team Owner Column
    |--------------------------------------------------------------------------
    |
    | This is the column on the Teams Table with a relation to the User Model
    | that owns it.
    |
    */
    'team_owner_column' => 'owner_id',

    /*
    |--------------------------------------------------------------------------
    | User Foreign Key
    |--------------------------------------------------------------------------
    |
    | Foreign key for User on Teamwork's team_user Table (Pivot)
    |
    */
    'user_foreign_key' => 'id',

    /*
    |--------------------------------------------------------------------------
    | Member Pivot Table
    |--------------------------------------------------------------------------
    |
    | This is the team_user table used by Teamwork to save assigned teams to the
    | database.
    |
    */
    'team_user_table' => 'team_user',

    /*
    |--------------------------------------------------------------------------
    | Member Roles
    |--------------------------------------------------------------------------
    | The 'owner' role will be included automatically
    | These are the available roles a user can have within a team. You can use
    | use these roles for your policies.
    |
    | Role at index[0] will be the default
    |
    */
    'member_roles' => ['member', 'admin', 'moderator'],

    /*
    |--------------------------------------------------------------------------
    | Teamwork Team Invite Model
    |--------------------------------------------------------------------------
    |
    | This is the Team Invite model used by Teamwork to create correct relations.
    | Update the team if it is in a different namespace.
    |
    */
    'invite_model' => Messerli90\Teamwork\TeamInvite::class,

    /*
    |--------------------------------------------------------------------------
    | Teamwork team invites Table
    |--------------------------------------------------------------------------
    |
    | This is the team invites table name used by Teamwork to save sent/pending
    | invitation into teams to the database.
    |
    */
    'team_invites_table' => 'team_invites',
];
