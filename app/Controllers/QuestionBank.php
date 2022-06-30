<?php

namespace App\Controllers;

use \App\Models\SubjectsModel;
use \App\Models\ChaptersModel;
use \App\Models\TestsModel;
use \App\Models\QuestionModel;

class QuestionBank extends BaseController
{

    public function index()
    {
        // Log Activity 
        $this->activity->page_access_activity('Question Bank', '/questionBank');
        $data['title'] = "Manage Question Bank";
        $data['instituteID'] = session()->get('instituteID');
        $SubjectsModel = new SubjectsModel();
        $data['subjects_list'] = $SubjectsModel->get_subjects(decrypt_cipher($data['instituteID']));
        return view('pages/question_bank/overview', $data);
    }
    /*******************************************************/

    public function format_data()
    {
        $data['question_arr']  = $this->request->getVar();
        return view('async/question_bank/display_formatted_question_details', $data);
    }
    /*******************************************************/

    /**
     * Chapter Questions
     *
     * @param [type] $subject_id
     * @param [type] $chapter_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function chapter_questions($subject_id, $chapter_id)
    {
        $data['title'] = "Chapter Questions";
        $data['instituteID'] = session()->get('instituteID');
        $data['institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $data['subject_id'] = $subject_id;
        $data['decrypt_subject_id'] =  decrypt_cipher($subject_id);
        $data['chapter_id'] = $chapter_id;
        $SubjectsModel = new SubjectsModel();
        $data['subject_detail'] = $SubjectsModel->get_subject_detail(decrypt_cipher($subject_id));
        $ChaptersModel = new ChaptersModel();
        $data['chapters_data'] = $ChaptersModel->get_subject_chapters(decrypt_cipher($subject_id), decrypt_cipher($data['instituteID']));
        $data['chapter_detail'] = $ChaptersModel->get_chapter_details($chapter_id);
        return view('pages/question_bank/chapter_questions', $data);
    }
    /*******************************************************/



    /**
     *  Bulk PDF Parse
     *
     * @param [type] $subject_id
     * @param [type] $chapter_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function bulk_pdf_parse($subject_id, $chapter_id)
    {
        $data['title'] = "Bulk PDF Parse";
        $data['redirect'] = "questionBank/add_bulk_questions";
        $data['staff_id'] = decrypt_cipher(session()->get('login_id'));
        $data['instituteID'] = session()->get('instituteID');
        $data['decrypted_institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $data['subject_id'] = $subject_id;
        $data['decrypted_subject_id'] =  decrypt_cipher($subject_id);
        $data['chapter_id'] = $chapter_id;
        $data['decrypted_chapter_id'] =  decrypt_cipher($chapter_id);
        $SubjectsModel = new SubjectsModel();
        $data['subject_detail'] = $SubjectsModel->get_subject_detail(decrypt_cipher($subject_id));
        $ChaptersModel = new ChaptersModel();
        $data['chapters_data'] = $ChaptersModel->get_subject_chapters(decrypt_cipher($subject_id), decrypt_cipher($data['instituteID']));
        $data['chapter_detail'] = $ChaptersModel->get_chapter_details(decrypt_cipher($chapter_id));
        return view('pages/question_bank/bulk_pdf_parse', $data);
    }
    /*******************************************************/

    /**
     * Add Bulk Questions
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_bulk_questions($subject_id, $chapter_id)
    {
        $data['title'] = "Add Bulk Questions";
        $data['redirect'] = "questionBank/add_bulk_questions";
        $data['instituteID'] = session()->get('instituteID');
        $data['decrypted_institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $data['subject_id'] = $subject_id;
        $data['decrypted_subject_id'] =  decrypt_cipher($subject_id);
        $data['chapter_id'] = $chapter_id;
        $data['decrypted_chapter_id'] =  decrypt_cipher($chapter_id);
        $SubjectsModel = new SubjectsModel();
        $data['subject_detail'] = $SubjectsModel->get_subject_detail(decrypt_cipher($subject_id));
        $ChaptersModel = new ChaptersModel();
        $data['chapter_detail'] = $ChaptersModel->get_chapter_details(decrypt_cipher($chapter_id));
        return view('pages/question_bank/add_bulk_questions', $data);
    }
    /*******************************************************/


    /**
     * Get Multiple Question Bank File Uploads Divs - Async Call
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_question_bank_uploads()
    {
        $data['noQues']  = sanitize_input($this->request->getVar('noQues'));
        $data['optionsType']  = sanitize_input($this->request->getVar('optionsType'));
        $data['quetionType']  = sanitize_input($this->request->getVar('quetionType'));
        $data['difficulty_level']  = sanitize_input($this->request->getVar('difficulty_level'));
        $data['redirect'] = "questionBank/add_bulk_questions";
        echo view('async/question_bank/get_question_bank_uploads', $data);
    }
    /*******************************************************/



    /**
     * Update Question Modal
     *
     * @param [type] $question_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_question_modal($question_id, $redirect = 'questionBank/chapter_questions')
    {
        $data['title'] = "Update Question";
        $TestsModel = new TestsModel();
        $data['question_detail'] = $TestsModel->question_detail($question_id);
        $data['redirect'] = $redirect . "/" . encrypt_string($data['question_detail']['subject_id']) . "/" . $data['question_detail']['chapter'];
        $data['question_id'] = $question_id;
        $data['updater'] =  decrypt_cipher(session()->get('login_id'));
        echo view('modals/questions/update', $data);
    }
    /*******************************************************/

    /**
     * Delete Question Modal
     *
     * @param [type] $question_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_question_modal($question_id, $redirect = 'questionBank/chapter_questions')
    {
        $data['title'] = "Delete Question";
        $TestsModel = new TestsModel();
        $data['question_detail'] = $TestsModel->question_detail($question_id);
        $data['redirect'] = $redirect . "/" . encrypt_string($data['question_detail']['subject_id']) . "/" . $data['question_detail']['chapter'];
        $data['question_id'] = $question_id;
        echo view('modals/questions/delete', $data);
    }
    /*******************************************************/



    /**
     * Update Question Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_question_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'question_id' => ['label' => 'Question ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect));
        } else {
            $data = $this->request->getVar();
            $QuestionModel = new QuestionModel();
            if ($QuestionModel->update_question($data)) {
                session()->setFlashdata('toastr_success', 'Question updated successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/


    /**
     * Delete Question Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_question_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'question_id' => ['label' => 'Question ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect));
        } else {
            $data = $this->request->getVar();
            $QuestionModel = new QuestionModel();
            if ($QuestionModel->delete_question($data)) {
                session()->setFlashdata('toastr_success', 'Question deleted successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/

    /**
     * Delete Question file
     *
     * @param [type] $img_to_delete
     * @param [type] $question_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_question_img_urls($img_to_delete, $question_id)
    {
        $session = session();
        $redirect = 'questionBank/update_question/' . $question_id;
        $TestsModel = new TestsModel();
        $data['question_detail'] = $TestsModel->question_detail($question_id);
        $file_to_delete = $data['question_detail'][$img_to_delete];
        if (file_exists($file_to_delete)) {
            if (unlink($file_to_delete)) {
                $db = \Config\Database::connect();
                $update_array = array(
                    $img_to_delete => ""
                );
                $db->table('test_questions')->update($update_array, ['id' => $question_id]);
                session()->setFlashdata('toastr_success', 'file deleted successfully.');
            } else {
                echo 'errors occured';
                $session->setFlashdata('toastr_error', 'Error while delecting file');
            }
        } else {
            $session->setFlashdata('toastr_error', 'file not found');
        }
        return redirect()->to(base_url($redirect));
    }
    /*******************************************************/
}
