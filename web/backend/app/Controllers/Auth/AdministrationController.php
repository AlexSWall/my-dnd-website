<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Helpers\FrontEndDataUtilities;

class AdministrationController extends Controller
{
	public function index($request, $response)
	{
		return FrontEndDataUtilities::getEntryPointResponse(	$this->view, $response, 'administration' );
	}
}