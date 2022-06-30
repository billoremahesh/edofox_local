<?php

namespace App\Controllers;

use \App\Models\LoginModel;
use \App\Models\AdminModel;

class Login extends BaseController
{

    /*******************************************************/
    /**
     * Login UI
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function index()
    {
        $data['title'] = "Edofox Admin - Login";
        $data['validation'] =  \Config\Services::validation();
        echo view('forms/login', $data);
    }
    /*******************************************************/



    /*******************************************************/
    /**
     * Admin Validate Login Using Token
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */

    public function admin_validate_login($token)
    {
        $session = session();
        $LoginModel = new LoginModel();
        if ($login_data = $LoginModel->validate_admin_login_details($token)) {
            $this->set_user_session_data($login_data);
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $this->userActivity->log('logged_in', $log_info);

            // Add Last Login Datetime
            $AdminModel = new AdminModel();
            $AdminModel->update_last_login($login_data['id']);
            return redirect()->to(base_url('/home'));
        } else {
            // Log Message
            $log_info =  [
                'token' =>  $this->request->getVar('token'),
                'ip_address' =>  get_client_ip()
            ];
            log_message('notice', 'User with token {token} tried logged into the system from {ip_address} but does not match records', $log_info);
            $session->setFlashdata('toastr_error', 'Invalid login credentials or account deleted or disabled');
            $data['validation'] = $this->validator;
            return redirect()->to(base_url('/login'))->withInput($data);
        }
    }

    /*******************************************************/


    /*******************************************************/
    /**
     * Logout
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    function user_logout()
    {
        $session = session();
        $userType = session()->get('user_type');
        if ($userType == "super_admin") :
            $session->destroy();
            $session->setFlashdata('toastr_success', 'Logged out successfully.');
            return redirect()->to(base_url('/login/super_admin'));
        else :
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $this->userActivity->log('logout', $log_info);
            $session->destroy();
            $session->setFlashdata('toastr_success', 'Logged out successfully.');
            return redirect()->to(base_url('/login'));
        endif;

    }
    /*******************************************************/


    /*******************************************************/
    /**
     * Set User Session Data
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    protected function set_user_session_data($data)
    {
        $session_data = array(
            'login_id' => encrypt_string($data['id']),
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'profile_img_url' => $data['profile_img_url'],
            'perms' => array(),
            'admin_token' => encrypt_string($data['admin_token']),
            'EdofoxAdminLoggedIn' => true,
            'admin_token_decrypted' => $data['admin_token'],
            'instituteID' => encrypt_string($data['institute_id']),
            'classroom_mapped_arr' => ""
        );
        $session = session();
        $session->set($session_data);
        return true;
    }
    /*******************************************************/



    /*******************************************************/
    /**
     * Super Admin Login
     * Added By @RushikeshB
     */
    function super_admin()
    {
        $data['title'] = "Edofox Super Admin - Login";
        $data['validation'] =  \Config\Services::validation();
        echo view('forms/super_admin_login.php', $data);
    }
    /*******************************************************/



    /*******************************************************/
    /**
     * For super admin login validate
     * Added By @RushikeshB
     */
    public function super_admin_login_validate()
    {

        $result = $this->validate([
            'username' => 'required|string|min_length[5]|max_length[25]',
            'password' => 'required|min_length[8]|max_length[80]'
        ]);
        if (!$result) {
            $this->super_admin();
        } else {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');

            $LoginModel = new LoginModel();
            if ($LoginModel->validate_super_admin_login($username, $password)) {
                $result = $LoginModel->get_super_admin_login_details($username);
                $session_data = array(
                    'username' => $username,
                    'email' =>  $result['email'],
                    'login_id' => encrypt_string($result['id']),
                    'user_type' => 'super_admin',
                    'name' => 'Edofox',
                    'mobile' => '',
                    'profile_img_url' => "",
                    'perms' => array(),
                    'admin_token' => "Edofox",
                    'instituteID' => encrypt_string("1"),
                    'classroom_mapped_arr' => "",
                    'instituteName' => 'Edofox',
                    'logo_path' => '',
                    'isSuperAdminLoggedIn' => true,
                    'unread_activity_count' => 0,
                    'admin_token' => encrypt_string('super_admin')
                );
                //  Set Session
                session()->set($session_data);
                return redirect()->to(base_url('/home'));
            } else {
                session()->setFlashdata('error', 'Invalid username or password.');
                return redirect()->to(base_url('/login/super_admin'));
            }
        }
    }
    /*******************************************************/
}
