<?php

namespace App\Controllers;

use App\Models\ChaptersModel;
use \App\Models\ClassroomModel;
use \App\Models\UserActivityModel;
use \App\Models\SubjectsModel;
use \App\Models\SyllabusModel;

class Syllabus extends BaseController
{
	public function index()
	{
		// Log Activity 
		$this->activity->page_access_activity('Syllabus', '/syllabus');
		$data['title'] = "Syllabus";
		$data['instituteID'] = session()->get('instituteID');
		$instituteID = decrypt_cipher(session()->get('instituteID'));

		return view('pages/syllabus/overview', $data);
	}
	/*******************************************************/



	/**
	 * Load Classrooms 
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function load_classrooms()
	{
		// POST data
		$postData = object_to_array($this->request->getVar());
		// Get data
		$SyllabusModel = new SyllabusModel();
		$postData['institute_id'] = decrypt_cipher(session()->get('instituteID'));
		$SyllabusModel = $SyllabusModel->fetch_all_syllabus_data($postData);
		echo json_encode($SyllabusModel);
	}
	/*******************************************************/



	public function optimized_classrooms_list(){
		$post_data['search'] = $this->request->getVar('search');
		$post_data['institute_id'] = decrypt_cipher(session()->get('instituteID'));
		$ClassroomModel = new ClassroomModel();
		$result = $ClassroomModel->optimized_classrooms_list($post_data);
		print_r($result);
	}

	/**
	 * View Classroom Students
	 *
	 * @param [type] $classroom_id
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function classroom_students($classroom_id)
	{
		$data['title'] = "Classroom Students";
		$data['package_id'] = $classroom_id;
		$data['instituteID'] = session()->get('instituteID');
		$instituteID = decrypt_cipher(session()->get('instituteID'));
		$ClassroomModel = new ClassroomModel();
		$data['classroom_details'] = $ClassroomModel->get_classroom_details(decrypt_cipher($classroom_id));
		$data['classroom_students'] = $ClassroomModel->classroom_students(decrypt_cipher($classroom_id), $instituteID);
		$data['blocked_classroom_students'] = $ClassroomModel->blocked_classroom_students(decrypt_cipher($classroom_id), $instituteID);

		// Activity Log
		$log_info =  [
			'username' =>  session()->get('username'),
			'uri' => strtoupper("Classroom Students"),
			'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
			'admin_id' =>  decrypt_cipher(session()->get('login_id'))
		];
		$UserActivityModel = new UserActivityModel();
		$UserActivityModel->log('page_access', $log_info);

		return view('pages/classrooms/classroom_students', $data);
	}
	/*******************************************************/





	/**
	 * Modals 
	 */

	/**
	 * Add Classroom Modal
	 *
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function add_syllabus_modal($redirect = 'syllabus')
	{
		$data['title'] = "Add New syllabus";
		$data['instituteID'] = session()->get('instituteID');
		$data['redirect'] = $redirect; 
		$instituteID = decrypt_cipher(session()->get('instituteID'));
		$SubjectsModel=new SubjectsModel();
		$data['subjectlist'] =$SubjectsModel->get_subjects($instituteID); 

		$ClassroomModel = new ClassroomModel();
		$data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID);

		$data['validation'] =  \Config\Services::validation();
		echo view('modals/syllabus/add', $data);
	}
	/*******************************************************/

	/**
	 * Update Classroom Modal
	 *
	 * @param [Integer] $classroom_id
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function update_syllabus_modal($syllabus_id, $redirect = 'syllabus')
	{
		$data['title'] = "Update Syllabus Details";
		$data['classroom_id'] = $syllabus_id;
		$data['redirect'] = $redirect;
		$data['validation'] =  \Config\Services::validation();
	
	
		$instituteID = decrypt_cipher(session()->get('instituteID'));
		$SyllabusModel=new SyllabusModel();
		$SubjectsModel=new SubjectsModel();
		$data['subjectlist'] =$SubjectsModel->get_subjects($instituteID);   
		$ClassroomModel = new ClassroomModel();
		$data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID); 
		$data['syllabus_details'] = $SyllabusModel->get_syllabus_details(decrypt_cipher($syllabus_id));
		$data['syllabus_classroom'] = $SyllabusModel->get_syllabus_classroom_details(decrypt_cipher($syllabus_id));
		 
		echo view('modals/syllabus/edit', $data);
	}
	/*******************************************************/


	/**
	 * Delete Classroom Modal
	 *
	 * @param [Integer] $classroom_id
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function delete_syllabus_modal($syllabus_id, $redirect = 'syllabus')
	{
		$data['title'] = "Delete Syllabus";
		$data['classroom_id'] = $syllabus_id;
		$data['redirect'] = $redirect;
		$data['validation'] =  \Config\Services::validation();
		$SyllabusModel = new SyllabusModel();
		$data['classroom_details'] = $SyllabusModel->get_syllabus_details(decrypt_cipher($syllabus_id));
		echo view('modals/syllabus/delete', $data);
	}
	/*******************************************************/


	/**
	 * Delete Classroom Students
	 *
	 * @param [type] $classroom_id
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function delete_bulk_classroom_students_modal($classroom_id, $redirect = 'classrooms/classroom_students/')
	{
		$data['title'] = "Delete Classroom Students";
		$data['classroom_id'] = $classroom_id;
		$data['redirect'] = $redirect . $classroom_id;
		$data['validation'] =  \Config\Services::validation();
		$ClassroomModel = new ClassroomModel();
		$data['classroom_details'] = $ClassroomModel->get_classroom_details(decrypt_cipher($classroom_id));
		echo view('modals/classrooms/delete_bulk_classroom_students', $data);
	}
	/*******************************************************/


	/**
	 * Migrate Classroom Students
	 *
	 * @param [type] $classroom_id
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function migrate_bulk_classroom_students_modal($classroom_id, $redirect = 'classrooms/classroom_students/')
	{
		$data['title'] = "Migrate Classroom Students";
		$data['classroom_id'] = $classroom_id;
		$data['institute_id'] = session()->get('instituteID');
		$data['redirect'] = $redirect . $classroom_id;
		$data['validation'] =  \Config\Services::validation();
		$ClassroomModel = new ClassroomModel();
		$data['classroom_details'] = $ClassroomModel->get_classroom_details(decrypt_cipher($classroom_id));
		$instituteID = decrypt_cipher(session()->get('instituteID'));
		$data['classroom_list'] = $ClassroomModel->classroom_list($instituteID);
		echo view('modals/classrooms/migrate_bulk_classroom_students', $data);
	}
	/*******************************************************/



	/**
	 * Enable Classroom Modal
	 * @author Pratik <pratik.kulkarni54@gmail.com>
	 * @param [type] $classroom_id
	 * @return void
	 */
	public function enable_classroom_modal($classroom_id)
	{
		$data['title'] = "Enable Classroom";
		$data['classroom_id'] = $classroom_id;
		$data['institute_id'] = session()->get('instituteID');
		$data['validation'] =  \Config\Services::validation();
		$ClassroomModel = new ClassroomModel();
		$data['classroom_details'] = $ClassroomModel->get_classroom_details($classroom_id);
		echo view('modals/classrooms/enable_classroom', $data);
	}
	/*******************************************************/



	/**
	 * End of Classroom Modal
	 */




	/**
	 * Submit Methods (Add, Edit, Delete)
	 */

	/**
	 * Add Classroom Submit 
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function add_syllabus_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'subject_name' => ['label' => 'Subject Name', 'rules' => 'required'],
			'syllabus_name' => ['label' => 'Syllabus Name', 'rules' => 'required|max_length[50]'],
			'session_classroom' => ['label' => 'Classroom', 'rules' => 'required'],
			'description' => ['label' => 'description', 'rules' => 'required']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar(); 
			$SyllabusModel = new SyllabusModel();
			if ($SyllabusModel->add_syllabus($data)) {
				$session->setFlashdata('toastr_success', 'Added New Syllabus successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/



	/**
	 * Update Classroom Submit 
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function update_syllabus_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'subject_name' => ['label' => 'Subject Name', 'rules' => 'required'],
			'syllabus_name' => ['label' => 'Syllabus Name', 'rules' => 'required|max_length[50]'],
			'session_classroom' => ['label' => 'Classroom', 'rules' => 'required'],
			'description' => ['label' => 'description', 'rules' => 'required']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$SyllabusModel = new SyllabusModel();
			if ($SyllabusModel->update_syllabus($data)) {
				$session->setFlashdata('toastr_success', 'Syllabus updated successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/



	/**
	 * Delete Classroom Submit 
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function delete_syllabus_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'syllabus_id' => ['label' => 'Syllabus ID', 'rules' => 'required|string|min_length[1]']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
		 
			$SyllabusModel = new SyllabusModel();
			if ($SyllabusModel->delete_syllabus($data)) {
				$session->setFlashdata('toastr_success', 'Syllabus deleted successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/


	/**
	 * Delete Bulk Classroom Students
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function delete_classroom_students_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'classroom_id' => ['label' => 'Classroom ID', 'rules' => 'required|string|min_length[1]']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$ClassroomModel = new ClassroomModel();
			if ($ClassroomModel->delete_classroom_students($data)) {
				$session->setFlashdata('toastr_success', 'Classroom students deleted successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/



	/**
	 * Migrate Classroom Students
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function migrate_classroom_students_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'classroom_id' => ['label' => 'Classroom ID', 'rules' => 'required|string|min_length[1]']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$ClassroomModel = new ClassroomModel();
			if ($ClassroomModel->migrate_classroom_students($data)) {
				$session->setFlashdata('toastr_success', 'Classroom students migrated successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/


	/*******************************************************/

	/**
	 * Enable Classroom submit
	 * @author Pratik <pratik.kulkarni54@gmail.com>
	 * @return void
	 */
	public function enable_classroom_submit()
	{
		$session = session();
		$result = $this->validate([
			'classroom_id' => ['label' => 'Classroom ID', 'rules' => 'required|string|min_length[1]']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url('classrooms'))->withInput();
		} else {
			$data = $this->request->getVar();
			$ClassroomModel = new ClassroomModel();
			if ($ClassroomModel->enable_classroom_submit($data)) {
				$session->setFlashdata('toastr_success', 'Classroom Enabled successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url('classrooms'));
		}
	}

	public function block_students_submit()
	{
		$session = session();
		$result = $this->validate([
			'block_accounts_package_id' => ['label' => 'Package Id', 'rules' => 'required'],
			'block_accounts_student_ids' => ['label' => 'Student Id', 'rules' => 'required']
		]);

		$block_accounts_package_id = $this->request->getVar('block_accounts_package_id');

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url('classrooms/classroom_students/' . $block_accounts_package_id))->withInput();
		} else {
			$data = $this->request->getVar();

			$ClassroomModel = new ClassroomModel();
			if ($ClassroomModel->block_students_submit($data)) {
				$session->setFlashdata('toastr_success', 'Selected Students Blocked successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url('classrooms/classroom_students/' . $block_accounts_package_id));
		}
	}




	public function unblock_students_submit()
	{
		$session = session();
		$result = $this->validate([
			'unblock_accounts_package_id' => ['label' => 'Package Id', 'rules' => 'required'],
			'unblock_accounts_student_ids' => ['label' => 'Student Id', 'rules' => 'required']
		]);

		$unblock_accounts_package_id = $this->request->getVar('unblock_accounts_package_id');


		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url('classrooms/classroom_students/' . $unblock_accounts_package_id))->withInput();
		} else {
			$data = $this->request->getVar();

			$ClassroomModel = new ClassroomModel();
			if ($ClassroomModel->unblock_students_submit($data)) {
				$session->setFlashdata('toastr_success', 'Selected Students Unblocked successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url('classrooms/classroom_students/' . $unblock_accounts_package_id));
		}
	}



	/*******************************************************/


	/**
	 * End of Submit Methods
	 */


	/**
	 * Fetch Course Subjects
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function fetch_course_subjects()
	{
		// POST data
		$course_id = $this->request->getVar('classrooms');
		$ClassroomModel = new ClassroomModel();
		$data['course_subjects'] = $ClassroomModel->classroom_subjects($course_id);
		$data['course_id'] = $course_id;
		echo view('async/subjects/course_subjects', $data);
	}
	/*******************************************************/


	/**
	 * Fetch Course Chapters
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function fetch_course_chapters()
	{
		// POST data
		$course_id = $this->request->getVar('classrooms');
		$subject_id = $this->request->getVar('subject_id');
		$ClassroomModel = new ClassroomModel();
		$data['course_chapters'] = $ClassroomModel->classroom_chapters($course_id, $subject_id);
		echo view('async/chapters/course_chapters', $data);
	}
	/*******************************************************/

	public function syllabus()
	{
		// Log Activity 
		$this->activity->page_access_activity('Classrooms', '/syllabus');
		$data['title'] = "Syllabus";
		$data['instituteID'] = session()->get('instituteID');
		$instituteID = decrypt_cipher(session()->get('instituteID'));
	 
		return view('pages/syllabus/overview', $data);
	}

	/**
	 * Add Classroom Modal
	 *
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function add_syllabus_configuration_modal($syllabus_id)
	{  
		$data['title'] = "Syllabus Configuration";
		$data['instituteID'] = session()->get('instituteID'); 
		$data['redirect'] = 'syllabus/syllabus_configuration/';  
		$instituteID = decrypt_cipher(session()->get('instituteID')); 
		$SyllabusModel=new SyllabusModel();
		$syllabusDetails =$SyllabusModel->get_syllabus_details($syllabus_id);  
		$data['syllabusDetails']=$syllabusDetails; 
		$select_data=$SyllabusModel->selected_chapter($syllabus_id); 
		$data['selected_chapter']=$select_data['s_chapter_id'];
		$data['chapter_list'] = $SyllabusModel->get_subject_chapters($syllabusDetails['subject_id'],$instituteID); 
		$data['validation'] =  \Config\Services::validation(); 
		echo view('modals/syllabus/syllabus_configuration', $data);
	}
	/*******************************************************/

	
	public function syllabus_configuration($syllabus_id, $redirect = 'syllabus')
	{
		// Log Activity  

		$this->activity->page_access_activity('Syllabus', '/syllabus');
		$data['title'] = "Syllabus configuration";
		$data['instituteID'] = session()->get('instituteID');
		$instituteID = decrypt_cipher(session()->get('instituteID'));
        $SyllabusModel= new SyllabusModel(); 
		$data['redirect']=$redirect;
		$data['syllabus_details'] = $SyllabusModel->get_syllabus_record(decrypt_cipher($syllabus_id)); 
		return view('pages/syllabus/syllabus_configuration', $data);
	}
	/*******************************************************/

	public  function get_syllabus_details(){  
		$subject_id =  sanitize_input($this->request->getVar('subject_id'));
		$syllabus_id =sanitize_input($this->request->getVar('syllabus_id')); 
        $SyllabusModel = new SyllabusModel();
		$check_syllabus_chapter=$SyllabusModel->check_syllabus_chapter($syllabus_id); 
		$result=[];
		if($check_syllabus_chapter==null){
		$ChaptersModel = new ChaptersModel();		
        $result = $ChaptersModel->get_subject_chapters($subject_id, decrypt_cipher(session()->get('instituteID')));
		}else{
        $SyllabusModel = new SyllabusModel();		
        $result = $SyllabusModel->get_selected_chapters($syllabus_id);
		}

		echo json_encode($result);
	}

	public function update_syllabus_topic(){
		$data =$this->request->getVar();
		$SyllabusModel= new SyllabusModel(); 

		$chapter_id =  sanitize_input($this->request->getVar('chapter_id'));
        $ChaptersModel = new ChaptersModel();
        $data['chapter_details'] = $ChaptersModel->get_chapter_details($chapter_id);
      
		$result = $SyllabusModel->update_syllabus_topic($data);  
        echo  json_encode($result);

	}

	
	/**
	 * Submit Methods (Add, Edit, Delete)
	 */

	/**
	 * Add Classroom Submit 
	 *
	 * @return void
	 * @author sunil
	 */
	public function add_syllabus_chapter_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'chapter' => ['label' => 'Chapter Name', 'rules' => 'required'],
			'difficulty' => ['label' => 'difficulty', 'rules' => 'required'],
			'importance' => ['label' => 'importance', 'rules' => 'required']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();    

			$SyllabusModel = new SyllabusModel();
			if ($SyllabusModel->add_syllabus_chapter($data)) {
				$session->setFlashdata('toastr_success', 'Added New Chapter In Syllabuse successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			} 
			$redirect=$redirect.'/'.encrypt_string($data['syllabus_id']);
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/

	/**
	 * Add Classroom Modal
	 *
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function update_syllabus_configuration_modal($syllabus_id)
	{  
		$data['title'] = "Update Syllabus Configuration";
		$data['instituteID'] = session()->get('instituteID'); 
		$data['redirect'] = 'syllabus/syllabus_configuration/';  
		$instituteID = decrypt_cipher(session()->get('instituteID')); 
		$SyllabusModel=new SyllabusModel();
		$syllabusDetails =$SyllabusModel->get_syllabus_details($syllabus_id);  
		$data['syllabusDetails']=$syllabusDetails; 
		$result=$SyllabusModel->selected_chapter($syllabus_id); 
		$data['s_chapter_id']=$result['s_chapter_id'];
		$data['difficulty']=$result['difficulty'];
		$data['importance']=$result['importance']; 
		$data['chapter_list'] = $SyllabusModel->get_subject_chapters($syllabusDetails['subject_id'],$instituteID); 
		$data['validation'] =  \Config\Services::validation(); 
	 
		echo view('modals/syllabus/update_syllabus_configuration', $data);
	}
	/*******************************************************/


		/**
	 * Add Classroom Submit 
	 *
	 * @return void
	 * @author sunil
	 */
	public function update_syllabus_chapter_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'chapter' => ['label' => 'Chapter Name', 'rules' => 'required'],
			'difficulty' => ['label' => 'difficulty', 'rules' => 'required'],
			'importance' => ['label' => 'importance', 'rules' => 'required']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();    
		
			$SyllabusModel = new SyllabusModel();
			if ($SyllabusModel->update_syllabus_chapter($data)) {
				$session->setFlashdata('toastr_success', 'Chapter Syllabus Updated Successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			$redirect=$redirect.'/'.encrypt_string($data['syllabus_id']);
			return redirect()->to(base_url($redirect));
		}
	}

	

	/**
	 * Delete Classroom Modal
	 *
	 * @param [Integer] $classroom_id
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function delete_syllabus_configuration_modal($syllabus_id)
	{
		$data['title'] = "Delete Syllabus of topics";
		$data['syllabus_id'] = $syllabus_id;
		$data['redirect'] = 'syllabus/syllabus_configuration/';  
		$data['validation'] =  \Config\Services::validation();
		$SyllabusModel = new SyllabusModel();
		$data['syllabus_details'] = $SyllabusModel->get_syllabus_record($syllabus_id);  
		echo view('modals/syllabus/syllabus_configuration_delete', $data);
	}
	/*******************************************************/

   

	/**
	 * Delete Classroom Submit 
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function delete_syllabus_topic_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'syllabus_id' => ['label' => 'Syllabus ID', 'rules' => 'required|string|min_length[1]']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
		 
			$SyllabusModel = new SyllabusModel();
			if ($SyllabusModel->delete_syllabus_topics($data)) {
				$session->setFlashdata('toastr_success', 'Syllabus topics deleted successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			$redirect=$redirect.'/'.encrypt_string($data['syllabus_id']); 
			return redirect()->to(base_url($redirect)); 
		}
	}
	/*******************************************************/
}
