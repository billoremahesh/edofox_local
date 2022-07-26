<?php

namespace App\Controllers;

use \App\Models\StaffModel;
use \App\Models\ClassroomModel;

class Staff extends BaseController
{
    /**
     * Staff Overview
     * 
     * @return void
     */
    public function index()
    {
        // Check Authorized User
		if (!isAuthorized("view_staff")) {
            $session = session();
            $session->setFlashdata('toastr_error', 'UnAuthorized access.');
			return redirect()->to(base_url('/home'));
		}
        // Log Activity 
        $this->activity->page_access_activity('Staff', '/staff');
        $data['title'] = "Staff";
        $data['instituteID'] = session()->get('instituteID');
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $StaffModel = new StaffModel();
        $data['staff_data'] = $StaffModel->fetch_all_staff($instituteID);
        return view('pages/staff/overview', $data);
    }


    /**
     * View Staff Details
     *
     * @param [type] $staff_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function view_details($staff_id)
    {
        $data['title'] = "Staff Details";
        $StaffModel = new StaffModel();
        $data['staff_details'] = $StaffModel->get_staff_details(decrypt_cipher($staff_id));
        return view('pages/staff/view_details', $data);
    }
    /*******************************************************/




    /**
     * Update Password
     *
     * @param [type] $staff_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_password($staff_id)
    {
        $data['title'] = "Update Password";
        $StaffModel = new StaffModel();
        $data['staff_details'] = $StaffModel->get_staff_details(decrypt_cipher($staff_id));
        return view('pages/staff/update_password', $data);
    }
    /*******************************************************/


    /**
     * Add Staff
     *
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add()
    {
        $data['title'] = "Add New Staff";
        $data['instituteID'] = session()->get('instituteID');
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $data['redirect'] = 'staff';
        $data['validation'] =  \Config\Services::validation();
        $ClassroomModel = new ClassroomModel();
        $data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID);
        echo view('pages/staff/add_new_staff', $data);
    }
    /*******************************************************/

    /**
     * Update Staff
     *
     * @param [Integer] $staff_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_staff($staff_id, $redirect = 'staff')
    {
        $data['title'] = "Update Staff Details";
        $data['staff_id'] = $staff_id;
        $data['redirect'] = $redirect;
        $data['validation'] =  \Config\Services::validation();
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $ClassroomModel = new ClassroomModel();
        $data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID);
        $StaffModel = new StaffModel();
        $data['staff_details'] = $StaffModel->get_staff_details(decrypt_cipher($staff_id));
        $data['staff_mapped_packages'] = $StaffModel->staff_mapped_packages(decrypt_cipher($staff_id));
        $roleperms = array();
        if (isset($data['staff_details']['perms']) and $data['staff_details']['perms'] != "") {
            $roleperms = str_replace("[", '', $data['staff_details']['perms']);
            $roleperms = str_replace("]", '', $roleperms);
            $roleperms = str_replace('"', '', $roleperms);
            $roleperms = explode(",", $roleperms);
        }
        $data['roleperms'] = $roleperms;
        echo view('pages/staff/update_staff_details', $data);
    }
    /*******************************************************/


    /**
     * Staff Modals 
     */

    /**
     * Delete Staff Modal
     *
     * @param [Integer] $staff_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_staff_modal($staff_id, $redirect = 'staff')
    {
        $data['title'] = "Delete Staff Details";
        $data['staff_id'] = $staff_id;
        $data['redirect'] = $redirect;
        $data['validation'] =  \Config\Services::validation();
        $StaffModel = new StaffModel();
        $data['staff_details'] = $StaffModel->get_staff_details(decrypt_cipher($staff_id));
        echo view('modals/staff/delete', $data);
    }
    /*******************************************************/



    /**
     * Submit Methods (Add, Edit, Delete)
     */



    /**
     * Update Staff Submit 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_staff_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'staff_id' => ['label' => 'Staff ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $StaffModel = new StaffModel();
            if ($StaffModel->update_staff($data)) {
                $session->setFlashdata('toastr_success', 'Staff updated successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/



    /**
     * Delete Staff Submit 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_staff_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'staff_id' => ['label' => 'Staff ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $StaffModel = new StaffModel();
            if ($StaffModel->delete_staff($data)) {
                $session->setFlashdata('toastr_success', 'Staff deleted successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/

    /**
     * End of Submit Methods
     */
}
