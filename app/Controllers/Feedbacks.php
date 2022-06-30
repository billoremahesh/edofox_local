<?php

namespace App\Controllers;

use RuntimeException;
use \App\Models\FeedbacksModel;

class Feedbacks extends BaseController
{
    public function index()
    {
        // Check permission to view Super Admin Dashboard
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }

        $data['title'] = "Feedbacks";
        return view('pages/feedbacks/overview', $data);
    }


    /**
     * Load Feedback Data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function load_feedbacks()
    {
        $post_data = $this->request->getVar();
        $FeedbacksModel = new FeedbacksModel();
        $feedback_data = $FeedbacksModel->load_feedbacks($post_data);
        echo json_encode($feedback_data);
    }

    /**
     * Add Feedback 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add()
    {
        $data['title'] = "Add Feedback";
        $data['admin_id'] = session()->get('login_id');
        $data['redirect'] = "feedbacks/add";
        return view('pages/feedbacks/add', $data);
    }
    /*******************************************************/



    /**
     * Add Feedback Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_feedback_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'admin_id' => ['label' => 'Admin ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $data['admin_id'] = decrypt_cipher($this->request->getVar('admin_id'));
            $FeedbacksModel = new FeedbacksModel();
            if ($FeedbacksModel->add_feedback($data)) {
                $session->setFlashdata('toastr_success', 'Feedback added successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/
}
