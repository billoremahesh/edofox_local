<?php

namespace App\Controllers;


use \App\Models\NoticesModel;

class Notices extends BaseController
{

    public function index()
    {
        // Check permission to view Super Admin
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }

        $data['title'] = "Notices";
        return view('pages/notices/overview', $data);
    }

    /**
     * Load Notices 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function load_notices()
    {
        // POST data
        $postData = object_to_array($this->request->getVar());
        // Get data
        $NoticesModel = new NoticesModel();
        $notices_data = $NoticesModel->get_all_notices($postData);
        echo json_encode($notices_data);
    }
    /*******************************************************/


    /**
     * View Notice 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function view_notice($notice_id)
    {
        $data['title'] = "View Notice Details";
        $NoticesModel = new NoticesModel();
        $data['notice_data'] = $NoticesModel->get_notice_details(decrypt_cipher($notice_id));
        return view('pages/notices/view_notice', $data);
    }
    /*******************************************************/


    /**
     * Modals 
     */

    /**
     * Add Notice 
     *
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_notice_modal($redirect = 'notices')
    {
        $data['title'] = "Add Notice";
        $data['redirect'] = $redirect;
        echo view('modals/notices/add', $data);
    }
    /*******************************************************/

    /**
     * Update Notice 
     *
     * @param [Integer] $notice_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_notice_modal($notice_id, $redirect = 'notices')
    {
        $data['title'] = "Update Notice";
        $data['notice_id'] = $notice_id;
        $data['redirect'] = $redirect;
        $NoticesModel = new NoticesModel();
        $data['notice_details'] = $NoticesModel->get_notice_details(decrypt_cipher($notice_id));
        echo view('modals/notices/edit', $data);
    }
    /*******************************************************/


    /**
     * Disable Notice 
     *
     * @param [Integer] $notice_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_notice_modal($notice_id, $redirect = 'notices')
    {
        $data['title'] = "Delete Notice";
        $data['notice_id'] = $notice_id;
        $data['redirect'] = $redirect;
        $NoticesModel = new NoticesModel();
        $data['notice_details'] = $NoticesModel->get_notice_details(decrypt_cipher($notice_id));
        echo view('modals/notices/delete', $data);
    }
    /*******************************************************/



    /**
     * Submit Methods 
     */


     /**
     * Add Notice Submit 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_notice_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'title' => ['label' => 'title', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $NoticesModel = new NoticesModel();
            if ($NoticesModel->add_notice($data)) {
                $session->setFlashdata('toastr_success', 'Notice added successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/


     /**
     * Update  Notice Submit 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_notice_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'notice_id' => ['label' => 'Notice ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $NoticesModel = new NoticesModel();
            if ($NoticesModel->update_notice_details($data)) {
                $session->setFlashdata('toastr_success', 'Notice details updated successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/

    /**
     * Delete Notice Submit 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_notice_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'notice_id' => ['label' => 'Notice ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $NoticesModel = new NoticesModel();
            if ($NoticesModel->disable_notice($data)) {
                $session->setFlashdata('toastr_success', 'Notice deleted successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/
}
