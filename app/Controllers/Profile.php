<?php

namespace App\Controllers;

use \App\Models\AdminModel;
use RuntimeException;

class Profile extends BaseController
{

    public function index()
    {
        $data['title'] = "Profile";
        $data['instituteID'] = session()->get('instituteID');
        $data['profile_id'] = session()->get('login_id');
        $data['redirect'] = 'profile';
        $profileID = decrypt_cipher(session()->get('login_id'));
        $AdminModel = new AdminModel();
        $data['profile_details'] = $AdminModel->get_profile_details($profileID);
        $data['admin_token'] = decrypt_cipher(session()->get('admin_token'));
        return view('pages/profile/admin/overview', $data);
    }



    /**
     * Update Profile Information
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_profile_info()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'profile_id' => ['label' => 'Profile ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $AdminModel = new AdminModel();
            if ($AdminModel->update_profile_details($data)) {
                $session->setFlashdata('toastr_success', 'Profile updated successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/


    /**
     * Profile Photo Modal
     *
     * @param [type] $profile_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_profile_photo($profile_id, $redirect = 'profile')
    {
        $data['title'] = "Update Profile Photo";
        $data['redirect'] = $redirect;
        $data['profile_id'] = $profile_id;
        $data['institute_id'] = session()->get('instituteID');
        echo view('modals/profile/update_profile_photo', $data);
    }
    /*******************************************************/
}
