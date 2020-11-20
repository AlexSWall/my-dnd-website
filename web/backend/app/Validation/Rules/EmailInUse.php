<?php

namespace App\Validation\Rules;

use App\Models\User;
use Respect\Validation\Rules\AbstractRule;

class EmailInUse extends AbstractRule
{
	public function validate($input)
	{
		return ( !is_null( User::retrieveUserByEmail($input) ) );
	}
}