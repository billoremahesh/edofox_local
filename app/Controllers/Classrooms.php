<?php

namespace App\Controllers;

use \App\Models\ClassroomModel;
use \App\Models\UserActivityModel;

class Classrooms extends BaseController
{
	public function index()
	{
		// Log Activity 
		$this->activity->page_access_activity('Classrooms', '/classrooms');
		$data['title'] = "Classrooms";
		$data['instituteID'] = session()->get('instituteID');
		$instituteID = decrypt_cipher(session()->get('instituteID'));
		return view('pages/classrooms/overview', $data);
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
		$ClassroomModel = new ClassroomModel();
		$postData['institute_id'] = decrypt_cipher(session()->get('instituteID'));
		$classrooms_data = $ClassroomModel->fetch_all_classrooms_data($postData);
		echo json_encode($classrooms_data);
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
	public function add_classroom_modal($redirect = 'classrooms')
	{
		$data['title'] = "Add New Classroom";
		$data['instituteID'] = session()->get('instituteID');
		$data['redirect'] = $redirect;
		$data['validation'] =  \Config\Services::validation();
		echo view('modals/classrooms/add', $data);
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
	public function update_classroom_modal($classroom_id, $redirect = 'classrooms')
	{
		$data['title'] = "Update Classroom Details";
		$data['classroom_id'] = $classroom_id;
		$data['redirect'] = $redirect;
		$data['validation'] =  \Config\Services::validation();
		$ClassroomModel = new ClassroomModel();
		$data['classroom_details'] = $ClassroomModel->get_classroom_details(decrypt_cipher($classroom_id));
		echo view('modals/classrooms/edit', $data);
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
	public function delete_classroom_modal($classroom_id, $redirect = 'classrooms')
	{
		$data['title'] = "Delete Classroom";
		$data['classroom_id'] = $classroom_id;
		$data['redirect'] = $redirect;
		$data['validation'] =  \Config\Services::validation();
		$ClassroomModel = new ClassroomModel();
		$data['classroom_details'] = $ClassroomModel->get_classroom_details(decrypt_cipher($classroom_id));
		echo view('modals/classrooms/delete', $data);
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
	public function add_classroom_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'package_name' => ['label' => 'Classroom Name', 'rules' => 'required|string|min_length[1]|max_length[120]'],
			'package_price' => ['label' => 'Price', 'rules' => 'required|string|min_length[1]|max_length[120]'],
			'package_offline_price' => ['label' => 'Offline Price', 'rules' => 'required|string|min_length[1]|max_length[120]'],
			'package_type' => ['label' => 'Type', 'rules' => 'permit_empty|string|min_length[1]|max_length[120]'],
			'is_public_check' => ['label' => 'Is Public', 'rules' => 'permit_empty|string|min_length[1]|max_length[120]']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$ClassroomModel = new ClassroomModel();
			if ($ClassroomModel->add_classroom($data)) {
				$session->setFlashdata('toastr_success', 'Added New Classroom successfully.');
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
	public function update_classroom_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'package_name' => ['label' => 'Classroom Name', 'rules' => 'required|string|min_length[1]|max_length[120]'],
			'package_price' => ['label' => 'Price', 'rules' => 'required|string|min_length[1]|max_length[120]'],
			'package_offline_price' => ['label' => 'Offline Price', 'rules' => 'required|string|min_length[1]|max_length[120]'],
			'package_type' => ['label' => 'Type', 'rules' => 'permit_empty|string|min_length[1]|max_length[120]'],
			'is_public_check' => ['label' => 'Is Public', 'rules' => 'permit_empty|string|min_length[1]|max_length[120]']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$ClassroomModel = new ClassroomModel();
			if ($ClassroomModel->update_classroom($data)) {
				$session->setFlashdata('toastr_success', 'Classroom updated successfully.');
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
	public function delete_classroom_submit()
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
			if ($ClassroomModel->delete_classroom($data)) {
				$session->setFlashdata('toastr_success', 'Classroom deleted successfully.');
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
}
