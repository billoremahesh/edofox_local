<?php

namespace App\Controllers\API;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use \App\Models\UserActivityModel;
use \App\Models\LoginModel;

class Activity extends ResourceController
{

    use ResponseTrait;

    function index()
    {
        // Forbidden action
        return $this->failForbidden("Access denied");
    }


    /**
     * Set Session
     *
     * @param string $token
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    function set_session(string $token)
    {
        $LoginModel = new LoginModel();
        if ($admin_data = $LoginModel->validate_admin_login_details($token)) {
            $session_data = array(
                'login_id' => encrypt_string($admin_data['id']),
                'username' => $admin_data['username'],
                'name' => $admin_data['name'],
                'email' => $admin_data['email'],
                'mobile' => $admin_data['mobile'],
                'profile_img_url' => $admin_data['profile_img_url'],
                'perms' => array(),
                'admin_token' => encrypt_string($admin_data['admin_token']),
                'EdofoxAdminLoggedIn' => true,
                'instituteID' => encrypt_string($admin_data['institute_id']),
                'classroom_mapped_arr' => ""
            );
            $session = session();
            $session->set($session_data);
            return true;
        } else {
            return false;
        }
    }
    /*******************************************************/


    /**
     * Add User Log - API Call
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    function add_log()
    {
        session();
        $this->userActivity = new UserActivityModel();
        // This helper required for validation
        helper(['form', 'url']);

        $result = $this->validate([
            'token' => 'required|string|min_length[1]|max_length[80]',
            'activity' => 'required|string|min_length[1]|max_length[40]'
        ]);

        if (!$result) {
            // Generic failure response
            return $this->fail("Validation failed", 400);
        } else {
            $token = $this->request->getVar('token');
            $activity = $this->request->getVar('activity');
            if (!$this->set_session($token)) {
                return $this->failUnauthorized("Invalid token, user not authorized");
            }
            $log_info = $this->request->getVar();
            // Log Message
            $log_info['username'] =  session()->get('username');
            $log_info['institute_id'] =  decrypt_cipher(session()->get('instituteID'));
            $log_info['admin_id'] =  decrypt_cipher(session()->get('login_id'));
            $UserActivityModel = new UserActivityModel();
            if ($UserActivityModel->log($activity, $log_info)) {
                // Generic response method
                $data = array(
                    'message' => 'Added log successfully'
                );
                return $this->respond($data, 200);
            } else {
                // Generic failure response
                return $this->fail("Error in processing", 400);
            }
        }
    }
    /*******************************************************/
}
