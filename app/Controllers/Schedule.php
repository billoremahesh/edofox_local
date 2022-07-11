<?php

namespace App\Controllers;

use \App\Models\InstituteScheduleModel;
use \App\Models\ClassroomModel;
use \App\Models\SubjectsModel;


class Schedule extends BaseController
{
	public function index()
	{
		$data['title'] = "Schedule Management";
		$data['module'] = "Schedule";  
		return view('pages/schedule/overview', $data);
	}
	/*******************************************************/



	// Fetch Schedule Events for Displaying on the calender
	public function fetch_schedule_events()
	{
		$post_data = $this->request->getVar();
		$InstituteScheduleModel = new InstituteScheduleModel();
		$post_data['institute_id'] = decrypt_cipher(session()->get('instituteID'));

		// $dates = explode(" to ", $post_data['schedule_date']);
		// $post_data['start'] = $dates[0];
		// if (isset($dates[1])) {
		// 	$post_data['end'] = $dates[1];
		// } else {
		// 	$post_data['end'] = $dates[0];
		// }


		$post_data['start'] = $post_data['schedule_start_date'];
		$post_data['end'] = $post_data['schedule_end_date'];
		$date_range = getDatesFromRange($post_data['start'], $post_data['end']);
		$data=[];
		foreach ($date_range as $date) {
			$post_data['day'] = date('N', strtotime($date));
			$post_data['date'] = $date; 
			
		
			$holiday_all =  $InstituteScheduleModel->fetch_holiday_all_classes($post_data); 
			$holiday_data = $InstituteScheduleModel->fetch_holiday_events($post_data); 
			$event_data = $InstituteScheduleModel->fetch_schedule_events($post_data);


			if (!empty($holiday_all)) {
				foreach ($holiday_all as $event) {  
					$data[] = array(
						'id' => encrypt_string($event['id']),
						'title' => $event['title'],
						'package_name' => '',
						'subject_name' =>'',
						'date' => $date,
						'starts_at' => $event['starts_at'],
						'ends_at' =>  $event['ends_at'],
						'duration' => $event['duration'],
						'total_students' => '',
						'present_students' => '',
						'frequency'=>$event['frequency']
					);
				}
			}else if (!empty($holiday_data)) {
				foreach ($holiday_data as $event) { 
					$data[] = array(
						'id' => encrypt_string($event['id']),
						'title' => $event['title'],
						'package_name' => '',
						'subject_name' =>'',
						'date' => $date,
						'starts_at' => $event['starts_at'],
						'ends_at' =>  $event['ends_at'],
						'duration' => $event['duration'],
						'total_students' => '',
						'present_students' => '',
						'frequency'=>$event['frequency']
					);
				}
			}else if (!empty($event_data)) {
				foreach ($event_data as $event) {
					$data[] = array(
						'id' => encrypt_string($event['id']),
						'title' => $event['title'],
						'package_name' => $event['package_name'],
						'subject_name' => $event['subject'],
						'date' => $date,
						'starts_at' => $event['starts_at'],
						'ends_at' =>  $event['ends_at'],
						'duration' => $event['duration'],
						'total_students' => $event['total_students'],
						'present_students' => $event['present_students'],
						'frequency'=>$event['frequency']
					);
				}
			} else {
				$data[] = array(
					'date' => $date
				);
			}
		}
		if (!isset($data)) {
			$data = array();
		}
		echo json_encode($data);
	}
	/*******************************************************/


	/**
	 * Modals 
	 */

	/**
	 * Add Schedule Modal
	 *
	 * @param string $redirect
	 *
	 * @return void
	 * @author Hemant K
	 */
	public function add_schedule_modal($classroom, $schedule_date,$redirect = 'schedule')
	{
		$data['title'] = "Add New Schedule";
		$data['instituteID'] = session()->get('instituteID');
		$data['classroom_id'] = $classroom;
		$data['schedule_date'] = $schedule_date;
		$data['day'] = date('N', strtotime($schedule_date));
		$instituteID = decrypt_cipher($data['instituteID']);
		$ClassroomModel = new ClassroomModel();
		$data['classroom_details'] = $ClassroomModel->get_classroom_details($classroom);
		$SubjectsModel = new SubjectsModel();
		$data['subjects_list'] = $SubjectsModel->get_subjects($instituteID);
		$data['redirect'] = $redirect;
		echo view('modals/schedule/add_schedule', $data);
	}
	/*******************************************************/
    /*************************** add holiday start  */
	public function add_holiday_modal($redirect = 'schedule')
	{
	 
		$data['title'] = "Add New Holiday";
		$data['instituteID'] = session()->get('instituteID');

		$instituteID = decrypt_cipher($data['instituteID']);

		$ClassroomModel = new ClassroomModel();
		$data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID);

		$SubjectsModel = new SubjectsModel();
		$data['subjects_list'] = $SubjectsModel->get_subjects($instituteID);

		$data['redirect'] = $redirect;
		echo view('modals/schedule/add_holiday_modal', $data);
	}
    /*************************** add holiday end */

	/** holiday_list start  */
	public function holiday_list($redirect = 'schedule')
	{
	 
		$data['title'] = "Holiday List";
		$data['instituteID'] = session()->get('instituteID');

		$instituteID = decrypt_cipher($data['instituteID']);

		$ClassroomModel = new ClassroomModel();
		$data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID);

		$InstituteScheduleModel = new InstituteScheduleModel();  
		$post_data['institute_id'] = $instituteID;
		$post_data['type'] = 'Holiday'; 
		$holiday_list = $InstituteScheduleModel->fetch_holiday_list($post_data); 
		$data['holiday_list'] = $holiday_list;
		$SubjectsModel = new SubjectsModel();
		$data['subjects_list'] = $SubjectsModel->get_subjects($instituteID);

		$data['redirect'] = $redirect; 
		echo view('modals/schedule/holiday_list', $data);
	}
	/** holiday_list end  */

	/* holiday calender start */
	public function holiday_calender($redirect = 'schedule'){
		$data['title'] = "Holiday Calender";
		$data['instituteID'] = session()->get('instituteID');

		$instituteID = decrypt_cipher($data['instituteID']);

		$ClassroomModel = new ClassroomModel();
		$data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID);

		$InstituteScheduleModel = new InstituteScheduleModel();  
		$post_data['institute_id'] = $instituteID;
		$post_data['type'] = 'Holiday'; 
		$holiday_list = $InstituteScheduleModel->fetch_holiday_list($post_data); 
		$data['holiday_list'] = $holiday_list;
		$SubjectsModel = new SubjectsModel();
		$data['subjects_list'] = $SubjectsModel->get_subjects($instituteID);

		$data['redirect'] = $redirect; 
		echo view('modals/schedule/holiday_calender', $data); 
	}
	/* holiday calender end */

	/**
	 * Add Bulk Schedules
	 */
	public function add_bulk_schedules_modal($redirect = 'schedule')
	{
		$data['title'] = "Add New Schedule";
		$data['instituteID'] = session()->get('instituteID');

		$instituteID = decrypt_cipher($data['instituteID']);

		$ClassroomModel = new ClassroomModel();
		$data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID);

		$SubjectsModel = new SubjectsModel();
		$data['subjects_list'] = $SubjectsModel->get_subjects($instituteID);

		$data['redirect'] = $redirect;
		echo view('modals/schedule/bulk_schedules', $data);
	}
	/*******************************************************/


	/**
	 * Update Schedule Modal
	 *
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushikesh B
	 */
	public function update_schedule_modal(string $schedule_id, $redirect = 'schedule')
	{
		$data['title'] = "Update Schedule";

		$data['instituteID'] = session()->get('instituteID');
		$data['schedule_id'] = $schedule_id;

		$instituteID = decrypt_cipher($data['instituteID']);

		$ClassroomModel = new ClassroomModel();
		$data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID);

		$SubjectsModel = new SubjectsModel();
		$data['subjects_list'] = $SubjectsModel->get_subjects($instituteID);

		$InstituteScheduleModel = new InstituteScheduleModel();
		$data['schedule_details'] = $InstituteScheduleModel->fetch_institute_schedule_data(decrypt_cipher($data['schedule_id']));

		$data['redirect'] = $redirect;
		echo view('modals/schedule/update_schedule', $data);
	}
	/*******************************************************/


	/**
	 * Delete Schedule Modal
	 *
	 * @param string $redirect
	 *
	 * @return void
	 * @author Hemant K
	 */
	public function delete_schedule_modal(string $schedule_id, $redirect = 'schedule')
	{
		$data['title'] = "Delete Schedule";

		$data['schedule_id'] = $schedule_id;

		$InstituteScheduleModel = new InstituteScheduleModel();
		$data['schedule_details'] = $InstituteScheduleModel->fetch_institute_schedule_data(decrypt_cipher($data['schedule_id']));

		$data['redirect'] = $redirect;
		echo view('modals/schedule/delete_schedule', $data);
	}
	/*******************************************************/ 

	/**
	 * Submit Methods (Add, Edit, Delete)
	 */

	/**
	 * Add Schedule Submit 
	 *
	 * @return void
	 * @author Hemant K
	 */
	public function add_schedule_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'session_title' => ['label' => 'Class Session Title', 'rules' => 'required|string|min_length[1]|max_length[240]'],
			'session_classroom' => ['label' => 'Class Session Classroom', 'rules' => 'required'],
			'session_subject' => ['label' => 'Class Session Subject', 'rules' => 'required']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$InstituteScheduleModel = new InstituteScheduleModel();
			$data['institute_id'] = decrypt_cipher($data['institute_id']);
		    $if_exit= $InstituteScheduleModel->checkSchedule($data);
			 if($if_exit==1){
			if ($InstituteScheduleModel->add_new_schedule($data)) {
				$session->setFlashdata('toastr_success', 'Added New Session Schedule successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
			}else{
				$session->setFlashdata('toastr_error', 'Error in processing.');
				return redirect()->to(base_url($redirect));
			}
			
		}
	}
	/*******************************************************/


	/** add holiday record start */
	public function add_holiday_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect'); 
		$result = $this->validate([
			'session_title' => ['label' => 'Class Session Title', 'rules' => 'required|string|min_length[1]|max_length[240]'],
			'session_classroom' => ['label' => 'Class Session Classroom', 'rules' => 'required']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$InstituteScheduleModel = new InstituteScheduleModel();
			$data['institute_id'] = decrypt_cipher($data['institute_id']);

			$ClassroomModel = new ClassroomModel();
	    	$data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($data['institute_id']); 
			
			if ($InstituteScheduleModel->add_new_holiday($data)) {
				$session->setFlashdata('toastr_success', 'Added New Session Holiday successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
    /** add holiday record end */

	/**
	 * Update Schedule Submit 
	 *
	 * @return void
	 * @author Hemant K
	 */
	public function update_schedule_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'session_title' => ['label' => 'Class Session Title', 'rules' => 'required|string|min_length[1]|max_length[240]'],
			'session_classroom' => ['label' => 'Class Session Classroom', 'rules' => 'required'],
			'session_subject' => ['label' => 'Class Session Subject', 'rules' => 'required'],
			'session_week_day' => ['label' => 'Class Session Day', 'rules' => 'required'],
			'session_start_time' => ['label' => 'Class Session Start Time', 'rules' => 'required'],
			'session_end_time' => ['label' => 'Class Session End Time', 'rules' => 'required']
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$InstituteScheduleModel = new InstituteScheduleModel();
			$data['schedule_id'] = decrypt_cipher($data['schedule_id']);
			if ($InstituteScheduleModel->update_schedule($data)) {
				$session->setFlashdata('toastr_success', 'Schedule updated successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/


	/**
	 * Delete Schedule Submit 
	 *
	 * @return void
	 * @author Rushikesh B
	 */
	public function delete_schedule_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'schedule_id' => ['label' => 'Schedule id', 'rules' => 'required'],
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar();
			$InstituteScheduleModel = new InstituteScheduleModel();
			$data['schedule_id'] = decrypt_cipher($data['schedule_id']);
			if ($InstituteScheduleModel->update_schedule($data)) {
				$session->setFlashdata('toastr_success', 'Schedule deleted successfully.');
			} else {
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	/*******************************************************/

	
	/** holiday delete start */ 
	public function delete_holiday_modal(string $holiday_id, $redirect = 'schedule')
    {
		$data['title'] = "Delete Holiday";

		$data['holiday_id'] = $holiday_id;

		$InstituteScheduleModel = new InstituteScheduleModel();
		$data['holiday_details'] = $InstituteScheduleModel->fetch_institute_holiday_data($data['holiday_id']);

		$data['redirect'] = $redirect; 
		echo view('modals/schedule/delete_holiday', $data);
 
    }
	/** holiday delete start */

    /* delete holiday submit start */
	public function delete_holiday_submit(){
		$session = session();
		$redirect = $this->request->getVar('redirect'); 
		$result = $this->validate([
			'schedule_id' => ['label' => 'holiday id', 'rules' => 'required'],
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {
			$data = $this->request->getVar(); 
			$InstituteScheduleModel = new InstituteScheduleModel();
			$data['schedule_id'] = $data['schedule_id']; 
			if ($InstituteScheduleModel->update_schedule($data)) {
				$session->setFlashdata('toastr_success', 'Holiday deleted successfully.');
			} else { 
				$session->setFlashdata('toastr_error', 'Error in processing.');
			}
			return redirect()->to(base_url($redirect));
		}
	}
	 /* delete holiday submit end */

}
