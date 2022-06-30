<?php

namespace App\Controllers;


class Admin extends BaseController
{

	public function index()
	{
		$data['title'] = "Admin";
		return view('pages/activity_logs/activity_logs_overview', $data);
	}
	
}