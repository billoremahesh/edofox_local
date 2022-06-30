<?php

namespace App\Controllers;

use \App\Models\SubjectsModel;
use \App\Models\ChaptersModel;

class Chapters extends BaseController
{
    public function index()
    {
    }

    /**
     * Chapter List - JSON AJAX Call
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function chapter_list()
    {
        // POST data
        $postData = object_to_array($this->request->getVar());
        $postData['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        // Get data
        $ChaptersModel = new ChaptersModel();
        $fetch_data = $ChaptersModel->filtered_chapters($postData);
        echo json_encode($fetch_data);
    }
    /*******************************************************/


    /**
     * Add chapter Modal
     *
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_chapter_modal($redirect = 'settings')
    {
        $data['title'] = "Add New Chapter";
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $SubjectsModel = new SubjectsModel();
        $data['subjects_list'] = $SubjectsModel->get_subjects($instituteID);
        $data['redirect'] = $redirect;
        echo view('modals/chapters/add', $data);
    }
    /*******************************************************/


    /**
     * Update chapter Modal
     *
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_chapter_modal($chapter_id, $redirect = 'settings')
    {
        $data['title'] = "Edit Chapter Details";
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $data['chapter_id'] = $chapter_id;
        $data['redirect'] = $redirect;
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $SubjectsModel = new SubjectsModel();
        $data['subjects_list'] = $SubjectsModel->get_subjects($instituteID);
        $chaptersModel = new chaptersModel();
        $data['chapter_details'] = $chaptersModel->get_chapter_details(decrypt_cipher($chapter_id));
        echo view('modals/chapters/edit', $data);
    }
    /*******************************************************/


    /**
     * Delete chapter
     *
     * @param [type] $chapter_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_chapter_modal($chapter_id, $redirect = 'settings')
    {
        $data['title'] = "Delete Chapter details";
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $data['chapter_id'] = $chapter_id;
        $data['redirect'] = $redirect;
        $chaptersModel = new chaptersModel();
        $data['chapter_details'] = $chaptersModel->get_chapter_details(decrypt_cipher($chapter_id));
        echo view('modals/chapters/delete', $data);
    }
    /*******************************************************/

    /**
     * Add chapter form submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_chapter_submit()
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
            $ChaptersModel = new ChaptersModel();
            if ($ChaptersModel->add_new_chapter($data)) {
                $session->setFlashdata('toastr_success', 'Added new chapter successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/



    /**
     * Update chapter details
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_chapter_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'chapter_id' => ['label' => 'chapter ID', 'rules' => 'required']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $ChaptersModel = new ChaptersModel();
            $data['chapter_id'] = decrypt_cipher($data['chapter_id']);
            if ($ChaptersModel->update_chapter($data)) {
                $session->setFlashdata('toastr_success', 'Chapter details updated successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/


    /**
     * Delete chapter Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_chapter_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'chapter_id' => ['label' => 'chapter ID', 'rules' => 'required']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $ChaptersModel = new ChaptersModel();
            $data['chapter_id'] = decrypt_cipher($data['chapter_id']);
            if ($ChaptersModel->delete_chapter($data)) {
                $session->setFlashdata('toastr_success', 'Chapter details deleted successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/
}
