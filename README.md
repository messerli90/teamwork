# Teamwork - 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/messerli90/teamwork.svg?style=flat-square)](https://packagist.org/packages/messerli90/teamwork)
[![Build Status](https://img.shields.io/travis/messerli90/teamwork/master.svg?style=flat-square)](https://travis-ci.org/messerli90/teamwork)
[![Quality Score](https://img.shields.io/scrutinizer/g/messerli90/teamwork.svg?style=flat-square)](https://scrutinizer-ci.com/g/messerli90/teamwork)
[![Total Downloads](https://img.shields.io/packagist/dt/messerli90/teamwork.svg?style=flat-square)](https://packagist.org/packages/messerli90/teamwork)

Teamwork adds User - Team association with an invite system to your Laravel App

## Installation

You can install the package via composer:

```bash
composer require messerli90/teamwork
```

## Configuration

To publish Teamwork's configuration file, run:

```bash
php artisan vendor:publish --provider="Messerli90\Teamwork\TeamworkServiceProvider" --tag=config
```

This will create `config/teamwork.php`. The default configuration should work just fine for you, but if you need to change the table / model names you should do that here. The config also supplies an array of possible roles a teammember can have, which can be changed.

### Migrations

Run the `migration` command to generate all tables needed for Teamwork. **If your users or teams are not stored in `users` and `teams` tables be sure to modify the the `config/teamwork.php` configuration.**

```bash
php artisan migrate
```

After the migration, 2 new tables will be created:

* team_user -- pivot table that stores a many-to-many relation between teams and their (users) members. Also includes a 'roles' column, defaults to: member.
* team_invites -- stores pending invites between teams and email addresses.

## Usage

### Teamwork Facade

You can invite a User to a Team either by passing the User model, or an email address

```php
Teamwork::inviteToTeam($user, $team, callable $success)
```

Check if the given user or email address has a pending invite for the provided Team

```php
Teamwork::hasPendingInvite($user, $team) // bool
```

Get instance of Invite model from accept / deny token

```php
Teamwork::getInviteFromAcceptToken($token)
Teamwork::getInviteFromDenyToken($token)
```

Accept / Deny Invite

```php
Teamwork::acceptInvite(TeamInvite $invite)
Teamwork::denyInvite(TeamInvite $invite)
```


### Team - HasMembers trait

Create your own Team model and add the `HasMembers`. Trait adds relation for `invites`, `users`, `owner`.

```php
<?php
use Messerli90\Teamwork\HasMembers;

class Team extends Model {
    use HasMembers;
}
```

Determine if a User is part of Team

```php
$team = App\Team::find(1);
$user = App\User::find(2);

$team->hasUser($user); // bool
```

### User - HasTeams trait

Add the `HasTeams` trait to your User model. Trait adds relation for `teams`, `ownedTeams`, `invites`.

```php
<?php
use Messerli90\Teamwork\HasTeams;

class User extends Authenticatable {
    use HasTeams;
}
```

Check if User owns any team, or provided team

```php
$user = App\User::find(1);

// Owns ANY team
$user->ownsTeam(); // bool

// Owns provided team
$team = App\Team::find(1);
$user->ownsTeam($team); // bool
```

Attach User to Team

```php
$user = App\User::find(1);
$team = App\Team::find(1);
$role = 'member' // Default allowed roles: member, owner, admin, moderator

$user->attachTeam($team, $role);
```

Detach User from Team
```php
$user = App\User::find(1);
$team = App\Team::find(1);

$user->detachTeam($team);
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email michaelamesserli@gmail.com instead of using the issue tracker.

## Credits

- [Michael Messerli](https://github.com/messerli90)
- [All Contributors](../../contributors)

### Special Thanks
This package used [mpociot/teamwork](https://github.com/mpociot/teamwork/) as a starting point.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).

