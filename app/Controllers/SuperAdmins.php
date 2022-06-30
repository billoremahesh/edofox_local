<?php

namespace App\Controllers;

use \App\Models\SuperAdminModel;

class SuperAdmins extends BaseController
{
    public function index()
    {
        // Log Activity 
        $this->activity->page_access_activity('Team Members Overview', '/SuperAdmins');
        $data['title'] = "Team Members";
        $SuperAdminModel = new SuperAdminModel();
        $data['sales_team'] = $SuperAdminModel->fetch_super_admins();
        return view('super_admin/teams/overview', $data);
    }


    public function add()
    {
        // Log Activity 
        $this->activity->page_access_activity('Add Team Member', '/SuperAdmins/add');
        $data['title'] = "Add Team Member";
        $data['redirect'] = "SuperAdmins";
        return view('super_admin/teams/add', $data);
    }


    public function update(string $staff_id)
    {
        // Log Activity 
        $this->activity->page_access_activity('Update Team Member Details', '/SuperAdmins/add');
        $data['title'] = "Update Team Member Details";
        $data['redirect'] = "SuperAdmins";
        $data['staff_id'] = $staff_id;
        $SuperAdminModel = new SuperAdminModel();
        $data['staff_details'] = $SuperAdminModel->get_super_admin_details(decrypt_cipher($staff_id));
        $roleperms = array();
        if (isset($data['staff_details']['access_perms']) and $data['staff_details']['access_perms'] != "") {
            $roleperms = str_replace("[", '', $data['staff_details']['access_perms']);
            $roleperms = str_replace("]", '', $roleperms);
            $roleperms = str_replace('"', '', $roleperms);
            $roleperms = explode(",", $roleperms);
        }
        $data['roleperms'] = $roleperms;
        return view('super_admin/teams/update', $data);
    }


    /**
     * Modals 
     */

    /**
     * Delete Modal
     *
     * @param string $staff_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_super_admin_details_modal(string $staff_id, string $redirect = 'SuperAdmins')
    {
        $data['title'] = "Delete Team Member Details";
        $data['staff_id'] = $staff_id;
        $data['redirect'] = $redirect;
        $SuperAdminModel = new SuperAdminModel();
        $data['staff_details'] = $SuperAdminModel->get_super_admin_details(decrypt_cipher($staff_id));
        echo view('modals/super_admin/delete', $data);
    }
    /*******************************************************/



    /**
     * Start of Submit Methods
     */

    /**
     * Add Submit Method
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_super_admin_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'name' => ['label' => 'name', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $SuperAdminModel = new SuperAdminModel();
            if ($SuperAdminModel->add_super_admin($data)) {
                $session->setFlashdata('toastr_success', 'Team Member added successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/



    /**
     * Update Submit Method
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_super_admin_details_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'name' => ['label' => 'name', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $SuperAdminModel = new SuperAdminModel();
            if ($SuperAdminModel->update_super_admin_details($data)) {
                $session->setFlashdata('toastr_success', 'Team Member details updated successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/


    /**
     * Delete Submit Method
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_super_admin_details_submit()
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
            $SuperAdminModel = new SuperAdminModel();
            if ($SuperAdminModel->delete_super_admin_details($data)) {
                $session->setFlashdata('toastr_success', 'Team Member details deleted successfully.');
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
