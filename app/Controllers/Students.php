<?php

namespace App\Controllers;

use \App\Models\InstituteModel;
use \App\Models\StudentModel;
use \App\Models\ClassroomModel;
use \App\Models\PackagesModel;


class Students extends BaseController
{

    public function index()
    {
        // Log Activity 
        $this->activity->page_access_activity('Your Students', '/students');
        $data['title'] = "Your Students";
        $data['instituteID'] = session()->get('instituteID');
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $InstituteModel = new InstituteModel();
        $data['institute_details'] = $InstituteModel->get_institute_details($instituteID);
        $data['decrypt_institute_id'] = $instituteID;
        $ClassroomModel = new ClassroomModel();
        $data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID);
        return view('pages/students/overview', $data);
    }


    /**
     * Student Data - JSON ASYNC CALL
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_list()
    {
        // POST data
        $postData = $this->request->getVar();
        $postData['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $InstituteModel = new InstituteModel();
        $postData['institute_details'] = $InstituteModel->get_institute_details($postData['instituteID']);
        // Get data
        $StudentModel = new StudentModel();
        $students_data = $StudentModel->get_students_data($postData);
        echo json_encode($students_data);
    }
    /*******************************************************/


    /**
     * Update Student Password
     *
     * @param string $student_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_password(string $student_id, $redirect = 'students')
    {
        $data['title'] = "Update Student Password";
        $data['redirect'] = $redirect;
        $data['instituteID'] = session()->get('instituteID');
        $instituteID = decrypt_cipher($data['instituteID']);
        // Activity Log
        $log_info =  [
            'username' =>  session()->get('username'),
            'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
            'uri' => "Update Student Password",
            'admin_id' =>  decrypt_cipher(session()->get('login_id'))
        ];
        $this->userActivity->log('page_access', $log_info);
        // Get data
        $StudentModel = new StudentModel();
        $decrypted_student_id = decrypt_cipher($student_id);
        $data['student_details'] = $StudentModel->get_student_details($decrypted_student_id, $instituteID);
        return view('pages/students/update_password', $data);
    }
    /*******************************************************/



    /**
     * Student Classrooms
     *
     * @param string $student_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function students_classroom(string $student_id, $redirect = 'students')
    {
        $data['title'] = "STUDENT'S CLASSROOMS";
        $data['redirect'] = $redirect;
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        // Activity Log
        $log_info =  [
            'username' =>  session()->get('username'),
            'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
            'uri' => "EDIT STUDENT'S CLASSROOMS",
            'admin_id' =>  decrypt_cipher(session()->get('login_id'))
        ];
        $this->userActivity->log('page_access', $log_info);
        // Get data
        $StudentModel = new StudentModel();
        $data['student_id'] = $student_id;
        $decrypted_student_id = decrypt_cipher($student_id);
        $data['studentID'] = $decrypted_student_id;
        $data['instituteID'] = session()->get('instituteID');
        $data['student_details'] = $StudentModel->student_details($decrypted_student_id);
        $data['student_classrooms'] = $StudentModel->get_student_classrooms($decrypted_student_id, $instituteID);
        return view('pages/students/students_classroom', $data);
    }
    /*******************************************************/




    /**
     * Update Student Classroom
     *
     * @param [type] $stu_pkg_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_student_classroom($stu_pkg_id)
    {

        $data['stu_pkg_id'] = $stu_pkg_id;
        $decrypted_stu_pkg_id = decrypt_cipher($stu_pkg_id);
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $StudentModel = new StudentModel();
        $data['student_package_data'] = $StudentModel->fetch_student_package_data($decrypted_stu_pkg_id);
        $data['student_details'] = $StudentModel->student_details($data['student_package_data']['student_id']);
        $data['title'] = 'Update Classroom';
        $PackagesModel = new PackagesModel();
        $data['packages_data'] = $PackagesModel->get_packages_list($instituteID);
        return view('modals/students/update_student_classroom', $data);
    }
    /*******************************************************/





    /*******************************************************/
    /**
     * Update Student Classroom
     * @return Void
     * @author PrachiP
     * @since 2021/10/19
     */
    public function update_student_classroom_submit()
    {
        $session = session();
        $result = $this->validate([
            'payment_status' => ['label' => 'Payment Status', 'rules' => 'required|string']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url('Students/students_classroom/' . $this->request->getVar('student_id')))->withInput();
        } else {
            $data = $this->request->getVar();
            $StudentModel = new StudentModel();
            if ($StudentModel->update_student_classroom($data)) {
                $session->setFlashdata('toastr_success', 'Student Classroom Updated.');
                return redirect()->to(base_url('Students/students_classroom/' . $this->request->getVar('student_id')));
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
                return redirect()->to(base_url('Students/students_classroom/' . $this->request->getVar('student_id')));
            }
        }
    }
    /*******************************************************/





    /*******************************************************/
    /**
     * Delete Student Classroom
     * @return View
     * @author PrachiP
     * @since 2021/10/19
     */
    public function delete_classroom_modal($student_id, $stu_pkg_id, $institute_id)
    {
        $data['title'] = "Delete the Classroom";
        $data['stu_pkg_id'] = $stu_pkg_id;
        $data['institute_id'] = $institute_id;
        $data['student_id'] = $student_id;
        return view('modals/students/delete_student_classroom', $data);
    }
    /*******************************************************/



    /**
     * Signup Modal
     *
     * @param [type] $institute_id
     * @param string $classroom_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_signup_modal($institute_id, $classroom_id = "")
    {
        $data['title'] = "Share sign up link with students";
        $data['instituteID'] = $institute_id;
        $data['classroom_id'] = $classroom_id;
        echo view('modals/register/signup', $data);
    }
    /*******************************************************/



    /*******************************************************/
    /**
     * Delete Student Classroom submit
     * @return Void
     * @author PrachiP
     * @since 2021/10/19
     */
    public function delete_student_classroom_submit()
    {
        $session = session();
        $result = $this->validate([
            'stu_pkg_id' => ['label' => 'Student Package id', 'rules' => 'required']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url('Students/students_classroom/' . $this->request->getVar('student_id')))->withInput();
        } else {
            $data = $this->request->getVar();
            $student_id = $this->request->getVar('student_id');
            $StudentModel = new StudentModel();
            if ($StudentModel->delete_student_classroom($data)) {
                $session->setFlashdata('toastr_success', 'Student Classroom Updated.');
                return redirect()->to(base_url('students/students_classroom/' . $student_id));
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
                return redirect()->to(base_url('students/students_classroom/' . $student_id));
            }
        }
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * Add Student Classroom
     * @return View
     * @author PrachiP
     * @since 2021/10/19
     */
    public function add_classroom_modal($student_id, $institute_id)
    {
        $data['title'] = "Add New Classroom";
        $data['student_id'] = $student_id;
        $data['institute_id'] = $institute_id;
        $ClassroomModel = new ClassroomModel();
        $data['classroom_list'] = $ClassroomModel->fetch_all_classrooms(decrypt_cipher($institute_id));
        return view('modals/students/add_student_classroom', $data);
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * Add Student Classroom Submit
     * @return Void
     * @author PrachiP
     * @since 2021/10/19
     */
    public function add_student_classroom_submit()
    {
        $session = session();
        $result = $this->validate([
            'add_package_id' => ['label' => 'Package id', 'rules' => 'required']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url('Students/students_classroom/' . $this->request->getVar('student_id')))->withInput();
        } else {
            $data = $this->request->getVar();
            $StudentModel = new StudentModel();
            if ($StudentModel->add_student_classroom($data)) {
                $session->setFlashdata('toastr_success', 'Student Classroom Added Successfully.');
                return redirect()->to(base_url('Students/students_classroom/' . $this->request->getVar('student_id')));
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
                return redirect()->to(base_url('Students/students_classroom/' . $this->request->getVar('student_id')));
            }
        }
    }
    /*******************************************************/




    /**
     * Student Performance Report
     *
     * @param [type] $student_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function performance_report($student_id)
    {
        $data['graph_script'] = true;
        $data['title'] = "Performance Report";
        $data['redirect'] = 'students';
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        // Get data
        $StudentModel = new StudentModel();
        $data['student_id'] = $student_id;
        $decrypted_student_id = decrypt_cipher($student_id);
        $data['instituteID'] = session()->get('instituteID');
        $data['decrypted_instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $data['student_details'] = $StudentModel->get_student_details($decrypted_student_id, $instituteID);
        return view('pages/students/performance_report', $data);
    }

    public function attendance_performance_report($student_id)
    {
        $data['graph_script'] = true;
        $data['title'] = "Attendance Report";
        $data['redirect'] = 'students';
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        // Get data
        $StudentModel = new StudentModel();
        $data['student_id'] = $student_id;
        $decrypted_student_id = decrypt_cipher($student_id);
        $data['instituteID'] = session()->get('instituteID');
        $data['decrypted_instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $data['student_details'] = $StudentModel->get_student_details($decrypted_student_id, $instituteID);
        return view('pages/students/attendance_performance_report', $data);
    }
    /*******************************************************/



    /**
     * Get performance data - Async Call
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_performance_data()
    {

        // this cookie required to acces student result page as it is folder in which codeingter session not accessble
        setcookie('newadmincookie', true, time() + (86400 * 30), "/"); // 86400 = 1 day
        setcookie('admintoken', session()->get('admin_token_decrypted'), time() + (86400 * 30), "/"); // 86400 = 1 day
        // POST data
        $student_id = $this->request->getVar('student_id');
        $institute_id = $this->request->getVar('institute_id');
        $data['performance_report_type'] = $this->request->getVar('type');

        $data['institute_id'] = $institute_id;
        $data['student_id'] = $student_id;
        $data['startTime'] = $this->request->getVar('startTime');
        $data['endTime'] = $this->request->getVar('endTime');
        // Get data
        $StudentModel = new StudentModel();
        $data['student_details'] = $StudentModel->get_student_details($student_id, $institute_id);
        return view('async/students/student_performance_data', $data);
    }

    public function student_attendance_performance_data()
    {

        // this cookie required to acces student result page as it is folder in which codeingter session not accessble
        setcookie('newadmincookie', true, time() + (86400 * 30), "/"); // 86400 = 1 day
        setcookie('admintoken', session()->get('admin_token_decrypted'), time() + (86400 * 30), "/"); // 86400 = 1 day
        // POST data
        $student_id = $this->request->getVar('student_id');
        $institute_id = $this->request->getVar('institute_id');
        $data['performance_report_type'] = $this->request->getVar('type');

        $data['institute_id'] = $institute_id;
        $data['student_id'] = $student_id;
        $data['startTime'] = $this->request->getVar('startTime');
        $data['endTime'] = $this->request->getVar('endTime');
        // Get data
        $StudentModel = new StudentModel();
        $result= $StudentModel->get_attend_student_details($student_id, $institute_id); 
       
        // $data['student_details']=$result['records'];
        $data['exam']=$result['exam']; 
        $data['reqular_session']=$result['reqular_session'];  
        $data['attendance_list']=$result['attendance_list'];  
        $data['subject_attendanc_present']=$result['subject_attendanc_present'];
        $data['subject_attend_abs']=$result['subject_attend_abs'];
        $data['abset_tem']=$result['abset_tem'];
        return view('async/students/student_attendance_performance_data', $data);
    }
    /*******************************************************/



    /**
     * Get Student token Data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function load_student_tokens()
    {
        // POST data
        $postData = $this->request->getVar();
        $postData['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        // Get data
        $StudentModel = new StudentModel();
        $students_data = $StudentModel->student_tokens($postData);
        echo json_encode($students_data);
    }
    /*******************************************************/


    /**
     * Modals 
     */

    /**
     * Add Student Modal
     *
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_student_modal($redirect = 'students')
    {
        $data['title'] = "Add Student";
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $data['redirect'] = $redirect;
        $ClassroomModel = new ClassroomModel();
        $data['all_classrooms_array'] = $ClassroomModel->fetch_all_classrooms($data['instituteID']);
        echo view('modals/students/add', $data);
    }
    /*******************************************************/


    /**
     * Update student details modal
     *
     * @param string $student_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_student_details_modal(string $student_id, $redirect = 'students')
    {
        $data['title'] = "Update Student Details";
        $data['instituteID'] = session()->get('instituteID');
        $data['student_id'] = $student_id;
        $data['redirect'] = $redirect;
        $instituteID = decrypt_cipher($data['instituteID']);
        $ClassroomModel = new ClassroomModel();
        $data['all_classrooms_array'] = $ClassroomModel->fetch_all_classrooms($instituteID);
        $StudentModel = new StudentModel();
        $decrypted_student_id = decrypt_cipher($student_id);
        $data['student_details'] = $StudentModel->get_student_details($decrypted_student_id, $instituteID);
        echo view('modals/students/update', $data);
    }
    /*******************************************************/

    /**
     * Import Student View
     *
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function import_bulk_students($redirect = '/students')
    {
        $data['title'] = "Import students with Excel";
        $data['instituteID'] = session()->get('instituteID');
        $data['redirect'] = $redirect;
        $instituteID = decrypt_cipher($data['instituteID']);
        $data['decryptedinstituteID'] = decrypt_cipher($data['instituteID']);
        $ClassroomModel = new ClassroomModel();
        $data['all_classrooms_array'] = $ClassroomModel->fetch_all_classrooms($instituteID);
        echo view('pages/students/import', $data);
    }
    /*******************************************************/


    /**
     * Send Notifications to Students
     *
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_notification_modal($redirect = 'students')
    {
        $data['title'] = "Send SMS/Email notification";
        $data['instituteID'] = session()->get('instituteID');
        $data['redirect'] = $redirect;
        $instituteID = decrypt_cipher($data['instituteID']);
        $data['decryptedinstituteID'] = decrypt_cipher($data['instituteID']);
        $ClassroomModel = new ClassroomModel();
        $data['all_classrooms_array'] = $ClassroomModel->fetch_all_classrooms($instituteID);
        echo view('modals/students/notification', $data);
    }
    /*******************************************************/


    /**
     * Disable/ Unable Student
     *
     * @param string $student_id
     * @param string $block_type
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function disable_student_modal(string $student_id, string $block_type, $redirect = 'students')
    {
        $data['title'] = strtoupper($block_type . " student");
        $data['student_id'] = $student_id;
        $data['block_type'] = $block_type;
        $data['redirect'] = $redirect;
        $decrypted_student_id = decrypt_cipher($student_id);
        $decrypted_institute_id = decrypt_cipher(session()->get('instituteID'));
        // Get data
        $StudentModel = new StudentModel();
        $data['student_details'] = $StudentModel->get_student_details($decrypted_student_id, $decrypted_institute_id);
        echo view('modals/students/disable', $data);
    }
    /*******************************************************/

    /**
     * Delete Student Modal
     *
     * @param string $student_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_student_modal(string $student_id, $redirect = 'students')
    {
        $data['title'] = "Delete Profile";
        $data['student_id'] = $student_id;
        $decrypted_student_id = decrypt_cipher($student_id);
        $decrypted_institute_id = decrypt_cipher(session()->get('instituteID'));
        // Get data
        $StudentModel = new StudentModel();
        $data['student_details'] = $StudentModel->get_student_details($decrypted_student_id, $decrypted_institute_id);
        $data['redirect'] = $redirect;
        echo view('modals/students/delete', $data);
    }
    /*******************************************************/





    /**
     * End of Modals
     */






    /**
     * Submit Methods
     */

    /**
     * Delete Student Submit 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_student_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'student_id' => ['label' => 'Student ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $StudentModel = new StudentModel();
            if ($StudentModel->delete_student($data)) {
                $session->setFlashdata('toastr_success', 'Student deleted successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/



    /**
     * Delete Student Live Login Session
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_student_live_login_session($live_session_id, $redirect = "/reports/student_login_sessions")
    {
        $session = session();
        $StudentModel = new StudentModel();
        if ($StudentModel->delete_student_live_login_session(decrypt_cipher($live_session_id))) {
            $session->setFlashdata('toastr_success', 'Deleted student active login session');
        } else {
            $session->setFlashdata('toastr_error', 'Error in processing.');
        }
        return redirect()->to(base_url($redirect));
    }
    /*******************************************************/

      /**
     * Delete Multiple Student Live Login Sessions
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_student_live_login_sessions()
    {
        $session = session();
        $redirect = "/reports/student_login_sessions";
        $live_session_ids = $this->request->getVar('live_session_ids');
        $StudentModel = new StudentModel();
        if ($StudentModel->delete_student_live_login_session($live_session_ids)) {
            $session->setFlashdata('toastr_success', 'Deleted student active login session');
        } else {
            $session->setFlashdata('toastr_error', 'Error in processing.');
        }
        return redirect()->to(base_url($redirect));
    }
    /*******************************************************/

    /**
     * Update Student Details Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_student_details_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'student_id' => ['label' => 'Student ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $StudentModel = new StudentModel();
            if ($StudentModel->update_student_details($data)) {
                $session->setFlashdata('toastr_success', 'Student details updated successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/



    /**
     * Disable Student Profile - Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function disable_student_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'student_id' => ['label' => 'Student ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $StudentModel = new StudentModel();
            $block_type = $this->request->getVar('block_type');
            $blockText = 'disabled';
            if ($block_type != 'disable') {
                $blockText = 'unblocked';
            }
            if ($StudentModel->disable_student($data)) {
                $session->setFlashdata('toastr_success', "Student $blockText successfully");
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/


    /**
     * Delete Student Token
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_student_token_submit($token)
    {
        $session = session();
        $redirect = '/reports/student_device_tracker';
        $data['token'] = $token;
        $StudentModel = new StudentModel();

        if ($StudentModel->disable_student_token($data)) {
            $session->setFlashdata('toastr_success', 'Student token deleted successfully.');
        } else {
            $session->setFlashdata('toastr_error', 'Error in processing.');
        }
        return redirect()->to(base_url($redirect));
    }
    /*******************************************************/


    /**
     * End of Submit Methods
     */
}
