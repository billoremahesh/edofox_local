<?php

namespace App\Controllers;

use \App\Models\InstituteModel;
use \App\Models\SubjectsModel;


class Settings extends BaseController
{

    public function index()
    {
        $data['title'] = "Settings";
        $data['redirect'] = "settings";
        $data['instituteID'] = session()->get('instituteID');
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $InstituteModel = new InstituteModel();
        $data['institute_details'] = $InstituteModel->get_institute_details($instituteID);
        $data['timezones'] = $InstituteModel->get_timezones();
        $SubjectsModel = new SubjectsModel();
        $data['subjects'] = $SubjectsModel->get_subjects($instituteID);
        return view('pages/settings/overview', $data);
    }
}
