<?php

namespace Messerli90\Teamwork\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Messerli90\Teamwork\Traits\HasMembers;

class Team extends Model
{
    use HasMembers;
}
