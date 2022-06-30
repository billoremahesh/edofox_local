<?php

namespace App\Controllers;

use \App\Models\InstituteScheduleModel;
use \App\Models\ClassroomModel;

class Attendance extends BaseController
{
	public function index()
	{
		$data['title'] = "Attendance Management";
		$data['module'] = "Attendance";
		return view('pages/schedule/overview', $data);
	}
	/*******************************************************/

	/**
	 * Take Attendance 
	 *
	 * @return void
	 * @author RushikeshB
	 */
	public function take_attendance(string $session_id, string $attendance_date)
	{

		$date_now = date("Y-m-d");

		if ($attendance_date > $date_now) {
			$session = session();
			$session->setFlashdata('toastr_error', 'Attendance date greater than today.');
			return redirect()->to(base_url('/home'));
		}

		// Log Activity 
		$this->activity->page_access_activity('Attendance', '/attendance');
		$data['title'] = "Take Attendance";
		$data['institute_id'] = decrypt_cipher(session()->get('instituteID'));
		$data['session_id'] = decrypt_cipher($session_id);
		$data['encrypt_session_id'] = $session_id;
		$data['attendance_date'] = date_format_custom($attendance_date, "Y-m-d");
		$InstituteScheduleModel = new InstituteScheduleModel();
		$data['schedule_details'] = $InstituteScheduleModel->fetch_institute_schedule_data($data['session_id']);
		return view('pages/attendance/take_attendance', $data);
	}
	/*******************************************************/


	// After taking attendance - shown view to display present and absent students
	public function overview(string $session_id, string $attendance_date)
	{

		$data['title'] = "Attendance Overview";
		$data['institute_id'] = decrypt_cipher(session()->get('instituteID'));
		$data['session_id'] = decrypt_cipher($session_id);
		$data['encrypt_session_id'] = $session_id;
		$data['attendance_date'] = date_format_custom($attendance_date, "Y-m-d");
		$InstituteScheduleModel = new InstituteScheduleModel();
		$data['schedule_details'] = $InstituteScheduleModel->fetch_institute_schedule_data($data['session_id']);
		return view('pages/attendance/overview', $data);	
	}
	/*******************************************************/
}
