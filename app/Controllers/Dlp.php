<?php

namespace App\Controllers;

use \App\Models\ClassroomModel;
use \App\Models\SubjectsModel;
use \App\Models\ChaptersModel;
use \App\Models\DlpModel;
use \App\Models\TestsModel;

class Dlp extends BaseController
{

	public function index()
	{

		// Check Authorized User
		if (!isAuthorized("view_dlp")) {
			return redirect()->to(base_url('/home'));
		}
		if (session()->get('dlp_count') != 1) {
			$session = session();
			$session->setFlashdata('toastr_error', 'UnAuthorized access.');
			return redirect()->to(base_url('/home'));
		}

		// Log Activity 
		$this->activity->page_access_activity('DLP', '/dlp');
		$data['title'] = "Manage Distance Learning";
		$data['instituteID'] = session()->get('instituteID');
		$instituteID = decrypt_cipher(session()->get('instituteID'));
		$ClassroomModel = new ClassroomModel();
		$DlpModel = new DlpModel();
		$data['classrooms_data'] = $ClassroomModel->fetch_all_classrooms($instituteID, 'DLP');
		$data['dlp_classroom_count'] = $ClassroomModel->classroom_count($instituteID, 'DLP');
		$data['dlp_video_count'] = $DlpModel->dlp_content_count($instituteID, 'DLPVIDEO');
		$data['dlp_doc_count'] = $DlpModel->dlp_content_count($instituteID, 'DOC');
		$data['dlp_assignments_count'] = $DlpModel->dlp_assignments_count($instituteID);
		return view('pages/dlp/overview', $data);
	}
	/*******************************************************/




	/**
	 * Chapter Content
	 *
	 * @param string $classroom_id
	 * @param string $subject_id
	 * @param string $chapter_id
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function chapter_content(string $classroom_id, string $subject_id, string $chapter_id)
	{
		$data['title'] = "Manage DLP Content";
		$data['redirect'] = "/dlp/chapter_content/" . $classroom_id . "/" . $subject_id . "/" . $chapter_id;
		$data['classroom_id'] = decrypt_cipher($classroom_id);
		$data['chapter_id'] = decrypt_cipher($chapter_id);
		$data['subject_id'] = decrypt_cipher($subject_id);
		$data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
		$ChaptersModel = new ChaptersModel();
		$SubjectsModel = new SubjectsModel();
		$data['chapters_data'] = $ChaptersModel->get_dlp_chapters(decrypt_cipher($classroom_id), decrypt_cipher($subject_id));
		$data['subject_details'] = $SubjectsModel->get_subject_detail(decrypt_cipher($subject_id));
		return view('pages/dlp/chapter_content', $data);
	}
	/*******************************************************/


	/**
	 * Load DLP Subject Chapters
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function load_dlp_subject_chapters($classroom_id)
	{
		$ClassroomModel = new ClassroomModel();
		$data['classrooms_details'] = $ClassroomModel->get_classroom_details($classroom_id);
		$data['classroom_id'] = $classroom_id;
		$SubjectsModel = new SubjectsModel();
		$data['active_subjects_list'] = $SubjectsModel->get_dlp_classroom_subjects($classroom_id, 0);
		$data['disabled_subjects_list'] = $SubjectsModel->get_dlp_classroom_subjects($classroom_id, 1);
		echo view('async/dlp/load_dlp_subject_chapters', $data);
	}
	/*******************************************************/


	/**
	 * Load DLP Chapter Content
	 *
	 * @param int $chapter_id
	 * @param int $classroom_id
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function load_dlp_chapter_content($chapter_id, $classroom_id)
	{
		$data['instituteID'] = session()->get('instituteID');
		$instituteID = decrypt_cipher(session()->get('instituteID'));
		$data['chapter_id'] = $chapter_id;
		$data['classroom_id'] = $classroom_id;
		$ChaptersModel = new ChaptersModel();
		$data['chapter_details'] = $ChaptersModel->get_chapter_details($chapter_id);
		$data['subject_id'] = $data['chapter_details']['subject'];
		$DlpModel = new DlpModel();
		$data['chapter_video_content'] = $DlpModel->dlp_chapter_content($chapter_id, $classroom_id, $instituteID, 'DLPVIDEO');
		$data['chapter_disabled_video_content'] = $DlpModel->dlp_chapter_content($chapter_id, $classroom_id, $instituteID, 'DLPVIDEO', 1);
		$data['chapter_doc_content'] = $DlpModel->dlp_chapter_content($chapter_id, $classroom_id, $instituteID, 'DOC');
		$data['chapter_disabled_doc_content'] = $DlpModel->dlp_chapter_content($chapter_id, $classroom_id, $instituteID, 'DOC', 1);
		$data['chapter_test_content'] = $DlpModel->dlp_chapter_content($chapter_id, $classroom_id, $instituteID, 'Test');
		echo view('async/dlp/load_dlp_chapter_content', $data);
	}
	/*******************************************************/

	public function fetch_course_tests($course_id)
	{
		$TestsModel = new TestsModel();
		$data['course_tests'] = $TestsModel->fetch_course_tests($course_id);
		$data['course_id'] = $course_id;
		echo view('async/load_course_tests', $data);
	}

	/**
	 * Deleted DLP Content
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function deleted_dlp_content()
	{
		$data['title'] = 'Deleted DLP Content';
		$data['instituteID'] = session()->get('instituteID');
		// Activity Log
		$log_info =  [
			'username' =>  session()->get('username'),
			'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
			'uri' => "DELETED DLP CONTENT",
			'admin_id' =>  decrypt_cipher(session()->get('login_id'))
		];
		$this->userActivity->log('page_access', $log_info);

		echo view('pages/dlp/deleted_dlp_content', $data);
	}
	/*******************************************************/


	/**
	 * Load Asynchronously DLP deleted content
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function load_deleted_dlp_content()
	{
		// POST data
		$postData = $this->request->getVar();
		$postData['instituteID'] = decrypt_cipher(session()->get('instituteID'));
		// Get data
		$DlpModel = new DlpModel();
		$dlp_data = $DlpModel->deleted_dlp_content($postData);
		echo json_encode($dlp_data);
	}
	/*******************************************************/


	/**
	 * Modals 
	 */

	/**
	 * Add DLP Content Modal
	 *
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function add_dlp_content_modal($redirect = 'dlp')
	{
		$data['title'] = "Add New DLP Resource";
		$data['instituteID'] = session()->get('instituteID');
		$data['redirect'] = $redirect;
		$data['validation'] =  \Config\Services::validation();
		$instituteID = decrypt_cipher(session()->get('instituteID'));
		$ClassroomModel = new ClassroomModel();
		$data['classrooms_list'] = $ClassroomModel->fetch_all_classrooms($instituteID, 'DLP');
		echo view('modals/dlp/add', $data);
	}
	/*******************************************************/

	/**
	 * Add DLP Chapter Content
	 *
	 * @param string $classroom_id
	 * @param string $subject_id
	 * @param string $chapter_id
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function add_dlp_chapter_content_modal(string $classroom_id, string $subject_id, string $chapter_id, $redirect = 'dlp/chapter_content/')
	{
		$data['title'] = "Add New DLP Resource";
		$data['instituteID'] = session()->get('instituteID');
		$data['classroom_id'] = $classroom_id;
		$data['chapter_id'] = $chapter_id;
		$data['subject_id'] = $subject_id;
		$data['redirect'] = $redirect . encrypt_string($classroom_id) . '/' . encrypt_string($subject_id) . '/' . encrypt_string($chapter_id);
		echo view('modals/dlp/add_chapter_content', $data);
	}
	/*******************************************************/

	/**
	 * Display Course Video
	 *
	 * @param [type] $subtopic
	 * @param [type] $videoUrl
	 * @param [type] $testId
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function display_course_videos()
	{
		$data['instituteID'] = session()->get('instituteID');
		$data['subtopic'] = $this->request->getVar('subtopic');
		$data['videoUrl'] = $this->request->getVar('videoUrl');
		$data['testId'] = $this->request->getVar('testId');
		$data['status'] = $this->request->getVar('status');
		$data['progress'] = $this->request->getVar('progress');
		$data['testName'] = "";
		if ($data['testId'] != "" && $data['testId'] != 0) {
			$TestsModel = new TestsModel();
			$data['test_details'] = $TestsModel->get_test_details($data['testId']);
			$data['testName'] = $data['test_details']['test_name'];
		}
		$data['title'] = strtoupper($data['subtopic']);
		echo view('async/dlp/display_course_videos', $data);
	}
	/*******************************************************/


	/**
	 * Manage Chapter entitites - Async Call
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function manage_chapter_entities()
	{
		// POST data
		$postData = $this->request->getVar();
		$DlpModel = new DlpModel();
		$result = $DlpModel->update_chapter_entities($postData);
		echo $result;
	}
	/*******************************************************/


	/**
	 * Manage DLP Subject Entitites
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function manage_subject_entities()
	{
		// POST data
		$postData = $this->request->getVar();
		$DlpModel = new DlpModel();
		$result = $DlpModel->update_subject_entities($postData);
		echo $result;
	}
	/*******************************************************/


	/**
	 * Add DLP Subject Modal
	 *
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function add_dlp_subject_modal($classroom_id, $redirect = 'dlp')
	{
		$data['title'] = "Add Subjects in this classroom";
		$data['instituteID'] = session()->get('instituteID');
		$data['redirect'] = $redirect;
		$data['classroom_id'] = $classroom_id;
		$data['validation'] =  \Config\Services::validation();
		$instituteID = decrypt_cipher(session()->get('instituteID'));
		$SubjectsModel = new SubjectsModel();
		$data['subjects_list'] = $SubjectsModel->get_subjects($instituteID);
		$data['dlp_not_mapped_subjects'] = $SubjectsModel->get_subjects($instituteID);
		echo view('modals/dlp/add_subject', $data);
	}
	/*******************************************************/


	/**
	 * Add DLP Chapter Modal
	 *
	 * @param [type] $classroom_id
	 * @param [type] $subject_id
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function add_dlp_chapter_modal(int $classroom_id, int $subject_id, $redirect = 'dlp')
	{
		$data['title'] = "Add Chapter in this classroom";
		$data['instituteID'] = session()->get('instituteID');
		$data['redirect'] = $redirect;
		$data['classroom_id'] = $classroom_id;
		$data['subject_id'] = $subject_id;
		$data['validation'] =  \Config\Services::validation();
		$instituteID = decrypt_cipher(session()->get('instituteID'));
		$ChaptersModel = new ChaptersModel();
		$data['dlp_chapters_list'] = $ChaptersModel->get_dlp_not_mapped_chapters($classroom_id, $subject_id);
		$SubjectsModel = new SubjectsModel();
		$data['dlp_subjects_list'] = $SubjectsModel->get_subjects($instituteID);
		echo view('modals/dlp/add_chapter', $data);
	}
	/*******************************************************/


	/**
	 * Update DLP Resource Modal
	 *
	 * @param integer $classroom_id
	 * @param integer $chapter_id
	 * @param integer $mapping_id
	 * @param integer $resource_id
	 * @param string $resource_type
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function update_dlp_resource_modal(int $chapter_id, int $mapping_id, int $resource_id, int $classroom_id, string $resource_type = "", $redirect = 'dlp/chapter_content')
	{
		$data['title'] = "Update DLP Resource";
		$data['instituteID'] = session()->get('instituteID');
		$data['edit_chapter_id'] = $chapter_id;
		$ChaptersModel = new ChaptersModel();
		$data['chapter_details'] = $ChaptersModel->get_chapter_details($chapter_id);
		$subject_id = $data['chapter_details']['subject'];
		$data['resource_mapping_id'] = $mapping_id;
		$data['resource_id'] = $resource_id;
		$data['resource_type'] = $resource_type;
		$data['redirect'] = $redirect . "/" . encrypt_string($classroom_id) . "/" . encrypt_string($subject_id) . "/" . encrypt_string($chapter_id);
		$data['validation'] =  \Config\Services::validation();
		$DlpModel = new DlpModel();
		$data['resource_details'] = $DlpModel->dlp_resource_details($resource_id);
		$data['dlp_resource_mapping_details'] = $DlpModel->dlp_resource_mapping_details($mapping_id);
		echo view('modals/dlp/update_dlp_resource', $data);
	}
	/*******************************************************/


	/**
	 * Download DLP Document
	 *
	 * @param string $docUrl
	 * @param string $doc_name
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function download_dlp_doc($docUrl = "", $doc_name = "", $redirect = 'dlp')
	{
		$data['title'] = $doc_name;
		$data['docUrl'] = $docUrl;
		$data['redirect'] = $redirect;
		echo view('modals/dlp/document', $data);
	}
	/*******************************************************/

	/**
	 * Add DLP Subject Submit
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function add_dlp_subject_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'classroom_id' => ['label' => 'Classroom Name', 'rules' => 'required']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$DlpModel = new DlpModel();
			if ($DlpModel->add_dlp_subject($data)) {
				$session->setFlashdata('toastr_success', 'Added New Subject successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/


	/**
	 * Add DLP content Submit
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function add_dlp_content_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'institute_id' => ['label' => 'Institute Name', 'rules' => 'required']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$DlpModel = new DlpModel();
			if ($DlpModel->add_dlp_content($data)) {
				$session->setFlashdata('toastr_success', 'Added DLP content successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/



	/**
	 * Add DLP Chapter Submit
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function add_dlp_chapter_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'classroom_id' => ['label' => 'Classroom Name', 'rules' => 'required']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$DlpModel = new DlpModel();
			if ($DlpModel->add_dlp_chapter($data)) {
				$session->setFlashdata('toastr_success', 'Added New Chapter successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/

	/*******************************************************/
	/**
	 * Add New subjects in bulk Submit
	 *
	 * @return void
	 * @author PrachiP
	 * @since 2021-11-16
	 */
	public function add_new_subjects_bulk_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'subject_names' => ['label' => 'Subject Names', 'rules' => 'required']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$SubjectsModel = new SubjectsModel();
			if ($SubjectsModel->add_new_subjects($data)) {
				$session->setFlashdata('toastr_success', 'Added New Subject successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/

	/*******************************************************/
	/**
	 * Add New chapters in subject Submit
	 *
	 * @return void
	 * @author PrachiP
	 * @since 2021-11-16
	 */
	public function add_new_chapters_in_subject_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'chapter_names' => ['label' => 'Chapter Names', 'rules' => 'required']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$ChaptersModel = new ChaptersModel();
			if ($ChaptersModel->add_new_chapters($data)) {
				$session->setFlashdata('toastr_success', 'Added New Chapter successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/


	/**
	 * Update DLP Resource 
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function update_dlp_resource_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'resource_id' => ['label' => 'Resource ID', 'rules' => 'required']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$DlpModel = new DlpModel();
			if ($DlpModel->update_dlp_resource($data)) {
				$session->setFlashdata('toastr_success', 'Updated Resource successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/


	/**
	 * Clone DLP Chapter Content 
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function clone_dlp_chapter_content_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'package_id' => ['label' => 'Package ID', 'rules' => 'required']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$DlpModel = new DlpModel();
			if ($DlpModel->clone_dlp_chapter_content($data)) {
				$session->setFlashdata('toastr_success', 'DLP chapter content cloned successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/
}
