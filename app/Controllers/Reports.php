<?php

namespace App\Controllers;

use \App\Models\LiveSessionsModel;
use \App\Models\StudentLoginSessionsModel;
use \App\Models\InstituteScheduleModel;
use \App\Models\EmailSmsLogsModel;


class Reports extends BaseController
{

    public function index()
    {
        // Check Authorized User
		if (!isAuthorized("view_reports")) {
            $session = session();
            $session->setFlashdata('toastr_error', 'UnAuthorized access.');
			return redirect()->to(base_url('/home'));
		}
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

    /** student view attendance start */
    public function view_student_attendance()
    {
        // Log Activity 
        $this->activity->page_access_activity('View Student Monthly Attendance', '/reports/view_student_attendance');
        $data['title'] = "Attendance Report";
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        return view('pages/reports/view_student_attendance', $data);
    }

    public function fetch_student_attendance(){
        $post_data = $this->request->getVar();   
        $InstituteScheduleModel = new InstituteScheduleModel();
        $post_data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $class_attendance =$InstituteScheduleModel->student_attendance_details($post_data); 
        echo json_encode($class_attendance); 
    }

    public function fetch_student_custome_attendance(){
        $post_data = $this->request->getVar();   
        $InstituteScheduleModel = new InstituteScheduleModel();
        $post_data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $class_attendance =$InstituteScheduleModel->student_custom_attendance_details($post_data); 
        echo json_encode($class_attendance); 
    }

    public function view_student_day_attendance()
    { 
        // Log Activity 
        $this->activity->page_access_activity('View Student Daily Attendance', '/reports/view_student_day_attendance');
        $data['title'] = "View Student Daily Attendance";
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        return view('pages/reports/view_student_day_attendance', $data);
    }
    
    public function fetch_student_day_attendance(){
        $post_data = $this->request->getVar();   
        $InstituteScheduleModel = new InstituteScheduleModel();
        $post_data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $class_attendance =$InstituteScheduleModel->student_day_attendance_details($post_data); 
        echo json_encode($class_attendance); 
    }
    public function student_attendance_update(){
        $post_data = $this->request->getVar();  
        $InstituteScheduleModel = new InstituteScheduleModel();
        $post_data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $result =$InstituteScheduleModel->student_attendance_update($post_data); 
        return true;
    }
    /** student view attendance end */

    /**
     * Email/ SMS logs Report
     */
    public function email_sms_logs()
    {
        // Log Activity 
        $this->activity->page_access_activity('SMS Logs', '/reports/sms_logs');
        $data['title'] = "Email & SMS Report";
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        return view('pages/reports/email_sms_logs', $data);
    }
    /*******************************************************/


    /**
     * Load Email/ SMS Logs
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function load_email_sms_logs()
    {
        $postData = object_to_array($this->request->getVar());
        $EmailSmsLogsModel = new EmailSmsLogsModel();
        $email_sms_logs_data = $EmailSmsLogsModel->email_sms_logs($postData);
        echo json_encode($email_sms_logs_data);
    }
    /*******************************************************/
}
