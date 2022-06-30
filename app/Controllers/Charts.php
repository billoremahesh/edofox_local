<?php

namespace App\Controllers;

use \App\Models\TestsModel;
use \App\Models\StudentLoginSessionsModel;

class Charts extends BaseController
{

    public function index()
    {
    }


    /**
     * Generate Weekly Test Count Chart- async call
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function weekly_tests_chart()
    {
        $data['title'] = "Weekly Tests";
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        if ($instituteID == 1) {
            $instituteID = "";
        }
        $TestsModel = new TestsModel();
        $data['weekly_tests_count_data'] = $TestsModel->weekly_tests_count($instituteID);
        echo view('async/charts/weekly_tests_chart.php', $data);
    }
    /*******************************************************/


    /**
     * Generate Weekly Student logins Chart- async call
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function weekly_student_logins_chart()
    {
        $data['title'] = "Student Logins";
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        if ($instituteID == 1) {
            $instituteID = "";
        }
        $StudentLoginSessionsModel = new StudentLoginSessionsModel();
        $data['weekly_student_logins'] = $StudentLoginSessionsModel->students_login_counts($instituteID);
        echo view('async/charts/weekly_student_logins_chart.php', $data);
    }
    /*******************************************************/
}
