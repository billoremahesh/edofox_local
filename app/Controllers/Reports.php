<?php

namespace App\Controllers;

use \App\Models\LiveSessionsModel;
use \App\Models\StudentLoginSessionsModel;


class Reports extends BaseController
{

    public function index()
    {
        // Log Activity 
        $this->activity->page_access_activity('Reports', '/reports');
        $data['title'] = "Reports";
        return view('pages/reports/overview', $data);
    }


    /**
     * Live Lecture Analysis
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function live_lectures_analysis()
    {
        $data['title'] = "Live Lectures Analysis";
        $data['instituteID'] = session()->get('instituteID');
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        // Activity Log
        $log_info =  [
            'username' =>  session()->get('username'),
            'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
            'uri' => "Live Lectures Analysis",
            'admin_id' =>  decrypt_cipher(session()->get('login_id'))
        ];
        $this->userActivity->log('page_access', $log_info);
        $LiveSessions = new LiveSessionsModel();
        $data['live_sessions_data'] = $LiveSessions->fetch_live_lectures($instituteID);
        return view('pages/reports/live_lectures_analysis', $data);
    }


    /**
     * Student Device Tracker
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_device_tracker()
    {
        $data['title'] = "Student Device Tracker";
        // Log Activity 
        $this->activity->page_access_activity('Student Device Tracker', '/reports/student_device_tracker');
        return view('pages/reports/student_device_tracker', $data);
    }


    /**
     * Student Live Login Sessions
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_login_sessions()
    {
        // Log Activity 
        $this->activity->page_access_activity('Active student sessions', '/reports/student_login_sessions');
        $data['title'] = "Active student sessions";
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        return view('pages/reports/student_login_sessions', $data);
    }

    /**
     * Load Student Sessions
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function load_student_sessions()
    {
        $postData = object_to_array($this->request->getVar());
        $StudentLoginSessionsModel = new StudentLoginSessionsModel();
        $classrooms_data = $StudentLoginSessionsModel->student_live_login_sessions($postData);
        echo json_encode($classrooms_data);
    }
    /*******************************************************/
}
