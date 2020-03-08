<?php

namespace Messerli90\Teamwork\Tests\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Messerli90\Teamwork\Traits\HasTeams;

class User extends Authenticatable
{
    use HasTeams;
}
