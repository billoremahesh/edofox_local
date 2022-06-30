<?php

namespace App\Controllers;

use \App\Models\SubjectsModel;
use \App\Models\ChaptersModel;

class Subjects extends BaseController
{
    public function index()
    {
    }



    /**
     * Load Subject Chapters
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function load_subject_chapters()
    {
        $subject_id =  sanitize_input($this->request->getVar('subject_id'));
        $ChaptersModel = new ChaptersModel();
        $result = $ChaptersModel->get_subject_chapters($subject_id, decrypt_cipher(session()->get('instituteID')));
        echo json_encode($result);
    }
    /*******************************************************/



    /**
     * Add Subject Modal
     *
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_subject_modal($redirect = 'settings')
    {
        $data['title'] = "Add New Subject";
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $data['redirect'] = $redirect;
        echo view('modals/subjects/add', $data);
    }
    /*******************************************************/


    /**
     * Update Subject Modal
     *
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_subject_modal($subject_id, $redirect = 'settings')
    {
        $data['title'] = "Edit subject details";
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $data['subject_id'] = $subject_id;
        $data['redirect'] = $redirect;
        $SubjectsModel = new SubjectsModel();
        $data['subject_details'] = $SubjectsModel->get_subject_detail(decrypt_cipher($subject_id));
        echo view('modals/subjects/edit', $data);
    }
    /*******************************************************/


    /**
     * Delete Subject
     *
     * @param [type] $subject_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_subject_modal($subject_id, $redirect = 'settings')
    {
        $data['title'] = "Delete subject details";
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $data['subject_id'] = $subject_id;
        $data['redirect'] = $redirect;
        $SubjectsModel = new SubjectsModel();
        $data['subject_details'] = $SubjectsModel->get_subject_detail(decrypt_cipher($subject_id));
        echo view('modals/subjects/delete', $data);
    }
    /*******************************************************/


    /**
     * Add Subject Form Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_subject_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'institute_id' => ['label' => 'Institute ID', 'rules' => 'required']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $SubjectsModel = new SubjectsModel();
            if ($SubjectsModel->add_new_subject($data)) {
                $session->setFlashdata('toastr_success', 'Added New Subject successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/



    /**
     * Update Subject Details
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_subject_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'subject_id' => ['label' => 'Subject ID', 'rules' => 'required']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $SubjectsModel = new SubjectsModel();
            $data['subject_id'] = decrypt_cipher($data['subject_id']);
            if ($SubjectsModel->update_subject($data)) {
                $session->setFlashdata('toastr_success', 'Subject details updated successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/


    /**
     * Delete Subject Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_subject_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'subject_id' => ['label' => 'Subject ID', 'rules' => 'required']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $SubjectsModel = new SubjectsModel();
            $data['subject_id'] = decrypt_cipher($data['subject_id']);
            if ($SubjectsModel->delete_subject($data)) {
                $session->setFlashdata('toastr_success', 'Subject details deleted successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/
}
