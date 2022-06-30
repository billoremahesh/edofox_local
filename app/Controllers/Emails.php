<?php

namespace App\Controllers;

class Emails extends BaseController
{
    public function index()
    {
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Emails";
        return view('super_admin/emails/overview', $data);
    }

}

?>