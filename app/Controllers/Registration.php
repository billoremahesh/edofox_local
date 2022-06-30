<?php

namespace App\Controllers;

use \App\Models\InstituteModel;
use \App\Models\StudentModel;

class Registration extends BaseController
{

    public function index()
    {
        return redirect()->to(base_url('/login'));
    }
    /*******************************************************/




    /**
     * Signup Form
     *
     * @param [type] $instituteID
     * @param string $classroom_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function signup($instituteID, $classroom_id = "")
    {
        $data['title'] = "Edofox - Signup";
        $InstituteModel = new InstituteModel();
        $data['institute_data'] = $InstituteModel->get_institute_details(decrypt_cipher($instituteID));
        if ($classroom_id != "") {
            $data['classroom_id'] = decrypt_cipher($classroom_id);
        } else {
            $data['classroom_id'] = $classroom_id;
        }
        echo view('forms/signup', $data);
    }
    /*******************************************************/




    /**
     * SignUp Response
     *
     * @param [type] $token
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function signup_response($token)
    {
        $data['title'] = "Edofox - Signup Response";
        $data['token'] = $token;
        $StudentModel = new StudentModel();
        $data['student_data'] = $StudentModel->fetch_student_data($token);
        echo view('forms/signup_response', $data);
    }
    /*******************************************************/
}
