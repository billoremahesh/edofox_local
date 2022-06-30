<?php

namespace App\Controllers;

use \App\Models\SuperAdminModel;

class Support extends BaseController
{
    public function index()
    {
    }

    public function account_manager()
    {
        // Log Activity 
        $this->activity->page_access_activity('account_manager', '/support/account_manager');
        $data['title'] = "Account Manager";
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $SuperAdminModel = new SuperAdminModel();
        $data['account_manager_data'] = $SuperAdminModel->fetch_institute_account_manager($instituteID);
        return view('pages/support/account_manager', $data);
    }
}
