<?php

namespace App\Controllers;


use \App\Models\InstituteModel;
use \App\Models\AdminModel;
use \App\Models\SuperAdminModel;

class Institutes extends BaseController
{


    /*****************************************************************
     * #################### SUPER ADMIN CODE END HERE ###############
     *****************************************************************/
    public function index()
    {
        // Check permission to view Super Admin
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }
        $postData['super_admin_role'] = session()->get('super_admin_role');
        $postData['login_id'] = decrypt_cipher(session()->get('login_id'));
        $data['title'] = "Institutes";
        $InstituteModel = new InstituteModel();
        $institute_tests_data = $InstituteModel->fetch_all_institutes($postData);
        $data['institutes_data'] = json_encode($institute_tests_data);
        return view('super_admin/institutes/overview', $data);
    }
    /*******************************************************/




    /**
     * Optimized Institutes Json
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function optimized_institute_list()
    {
        // Check permission to view Super Admin Dashboard
        if (!isAuthorizedSuperAdmin()) {
            $formatted_data['incomplete_results'] = false;
            $formatted_data['items'] = array();
            $cnt = 0;
            $formatted_data['total_count'] = $cnt;
            echo json_encode($formatted_data);
        } else {
            $post_data['search'] = $this->request->getVar('search');
            $InstituteModel = new InstituteModel();
            $result = $InstituteModel->search_institutes($post_data);
            print_r($result);
        }
    }
    /*******************************************************/


    /**
     * Institute Classrooms
     *
     * @param [type] $institute_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function institute_classrooms($institute_id)
    {
        $InstituteModel = new InstituteModel();
        $result = $InstituteModel->institute_classrooms($institute_id);
        print_r($result);
    }
    /*******************************************************/


    /**
     * Add New Institute - Super Admin Module
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_institute()
    {
        // Check permission to view Super Admin Dashboard
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Add Institute";
        $SuperAdminModel = new SuperAdminModel();
        $data['sales_team'] = $SuperAdminModel->fetch_super_admins();
        return view('super_admin/institutes/add_institute', $data);
    }
    /*******************************************************/



    public function get_newly_created_institute_id()
    {
        $InstituteModel = new InstituteModel();
        $newly_created_institute_id =  $InstituteModel->get_newly_created_institute_id();
        echo encrypt_string($newly_created_institute_id);
    }



    /**
     * Update Institute Details - Super Admin Module
     *
     * @param [type] $institute_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_institute($institute_id, $redirect = "institutes")
    {
        // Check permission to view Super Admin Dashboard
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Update Institute";
        $data['institute_id'] = $institute_id;
        $data['redirect'] = $redirect;
        $data['validation'] =  \Config\Services::validation();
        $InstituteModel = new InstituteModel();
        $data['institute_data'] = $InstituteModel->get_institute_details(decrypt_cipher($institute_id));
        $SuperAdminModel = new SuperAdminModel();
        $data['sales_team'] = $SuperAdminModel->fetch_super_admins();
        $data['timezones'] = $InstituteModel->get_timezones();
        return view('super_admin/institutes/update_institute', $data);
    }
    /*******************************************************/



    /**
     * Upgrade Institue - Super Admin Module
     *
     * @param [type] $institute_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function upgrade_institute($institute_id, $redirect = "institutes")
    {
        // Check permission to view Super Admin Dashboard
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Upgrade Institute";

        $data['institute_id'] = decrypt_cipher($institute_id);
        $data['redirect'] = $redirect;
        $data['validation'] =  \Config\Services::validation();
        $InstituteModel = new InstituteModel();
        $data['institute_data'] = $InstituteModel->get_institute_details(decrypt_cipher($institute_id));
        return view('super_admin/institutes/upgrade_institute', $data);
    }
    /*******************************************************/





    /**
     * Super Admin Access to Old Edofox Admin Panel
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function old_admin_indirect_login($institute_id)
    {
        // Check permission to view Super Admin Dashboard
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "External Login";
        $AdminModel = new AdminModel();
        $admin_token = $AdminModel->get_admin_token(decrypt_cipher($institute_id));
        $data['external_url'] = 'https://test.edofox.com/test_operations/login_validate.php?universal_token=' . $admin_token;
        echo view('modals/institutes/external_login', $data);
    }


    /**
     * Super Admin Access to New Edofox Admin Panel
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function new_admin_indirect_login($institute_id)
    {
        // Check permission to view Super Admin Dashboard
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "External Login";
        $AdminModel = new AdminModel();
        $admin_token = $AdminModel->get_admin_token(decrypt_cipher($institute_id));
        $data['external_url'] = base_url() . '/login/admin_validate_login/' . $admin_token;
        echo view('modals/institutes/external_login', $data);
    }

    /*****************************************************************
     * #################### SUPER ADMIN CODE END HERE ###############
     *****************************************************************/



    /**
     * Update institute Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_institute_details_submit()
    {

        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'institute_name' => ['label' => 'Institute Name', 'rules' => 'required|string|min_length[1]|max_length[120]'],
            'contact' => ['label' => 'contact', 'rules' => 'required|string|min_length[1]|max_length[20]'],
            'email' => ['label' => 'Email', 'rules' => 'required|string|min_length[1]|max_length[120]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect));
        } else {
            $data = $this->request->getVar();
            $data['institute_id'] = decrypt_cipher($data['institute_id']);
            $InstituteModel = new InstituteModel();
            if ($InstituteModel->update_institute_details($data)) {
                $session->setFlashdata('toastr_success', 'Updated institute details successfully.');
                return redirect()->to(base_url($redirect));
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing data.');
                return redirect()->to(base_url($redirect));
            }
        }
    }
    /*******************************************************/


    /*******************************************************/
    /**
     * Upload Insitute Logo - Modal
     * Added By @RushikeshB
     */
    public function update_logo($institute_id, $redirect = 'settings')
    {
        $data['title'] = "Update Institute Logo";
        $data['institute_id'] = $institute_id;
        $data['redirect'] = $redirect;
        $data['validation'] =  \Config\Services::validation();
        $InstituteModel = new InstituteModel();
        $data['institute_details'] = $InstituteModel->get_institute_details(decrypt_cipher($institute_id));
        echo view('modals/institutes/update_institute_logo', $data);
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * Claim WhatsApp Credits
     * Added By @RushikeshB
     */
    public function claim_whatsapp_credits()
    {
        $data['institute_id'] = $this->request->getVar('institute_id');
        $data['institute_name'] = $this->request->getVar('institute_name');
        $data['whatsapp_credits'] = $this->request->getVar('whatsapp_credits');
        $InstituteModel = new InstituteModel();
        $InstituteModel->update_institute_details($data);
        echo "Success";
    }
    /*******************************************************/
}
