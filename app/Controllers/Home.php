<?php

namespace App\Controllers;

use \App\Models\InstituteModel;
use \App\Models\StudentModel;
use \App\Models\TestsModel;
use \App\Models\ClassroomModel;
use \App\Models\DlpModel;
use \App\Models\InvoiceModel;

use QRCode;

class Home extends BaseController
{
	public function index()
	{
		$data['graph_script'] = true;
		$data['title'] = "Dashboard";
		$userType = session()->get('user_type');
		if ($userType == "super_admin") {
			$InstituteModel = new InstituteModel();
			$data['institute_cnt'] = $InstituteModel->get_institutes_count();
			$StudentModel = new StudentModel();
			$data['total_student_cnt'] = $StudentModel->get_all_students_count();
			$TestsModel = new TestsModel();
			$data['total_test_cnt'] = $TestsModel->test_count();
			$data['todays_test_cnt'] = $TestsModel->todays_test_count();
			$data['todays_test_stu_cnt'] = $TestsModel->todays_test_total_stu_count();
			$data['tomorrows_cnt'] = $TestsModel->tomorrows_test_count();
			$data['tomorrows_test_stu_cnt'] = $TestsModel->tomorrows_exam_stu_cnt();
			$data['total_planned_test_cnt'] = $TestsModel->total_planned_test();
			$data['total_submission_cnt'] = $TestsModel->submission_count();
			$data['total_stu_planned_test_cnt'] = $TestsModel->total_stu_planned_test();
			$data['test_attempts_date_wise_cnt'] = $TestsModel->test_attempts_date_wise_count();
			$data['test_ongoing_stu_cnt'] = $TestsModel->total_stu_ongoing_test();
			return view('super_admin/dashboard', $data);
		} else {
			$instituteID = decrypt_cipher(session()->get('instituteID'));
			$InstituteModel = new InstituteModel();
			$StudentModel = new StudentModel();
			$TestsModel = new TestsModel();
			$ClassroomModel = new ClassroomModel();
			$DlpModel = new DlpModel();
			$InvoiceModel = new InvoiceModel();
			$postData['institute_id'] = $instituteID;
			$data['total_pending_invoices'] = $InvoiceModel->total_pending_invoices($postData);
			$data['institute_details'] = $InstituteModel->get_institute_details($instituteID);
			$data['registeredCount'] = $InstituteModel->registeredCount($instituteID);
			$data['student_cnt'] = $StudentModel->get_students_count($instituteID);
			$data['todays_test_cnt'] = $TestsModel->todays_test_count($instituteID);
			$data['total_test_cnt'] = $TestsModel->test_count($instituteID);
			$data['total_classrooms'] = $ClassroomModel->classroom_count($instituteID);
			$data['dlp_video_count'] = $DlpModel->dlp_content_count($instituteID, 'DLPVIDEO');
			$data['dlp_doc_count'] = $DlpModel->dlp_content_count($instituteID, 'DOC');
			return view('dashboard', $data);
		}
	}
	/*******************************************************/


	/**
	 * QR Code 
	 */
	public function qr_code()
	{
		$data['title'] = "QR Code";
		$instituteID = decrypt_cipher(session()->get('instituteID'));
		$InstituteModel = new InstituteModel();
		$data['institute_details'] = $InstituteModel->get_institute_details($instituteID);
		$refer_link = base_url("/registration/signup/" . $instituteID);
		$instituteName = session()->get('instituteName');
		/**
		 * If you have PHP 5.4 or higher, you can instantiate the object like this:
		 * (new QRCode)->fullName('...')->... // Create vCard Object
		 */
		$oQRC = new QRCode; // Create vCard Object
		$oQRC->fullName($instituteName) // Add Full Name
			->nickName($instituteName) // Add Nickname
			->url($refer_link) // Add URL Website
			->note('Introducing REFER Link') // Add Note
			->categories('refer') // Add Categories
			->photo('http://files.phpclasses.org/picture/user/1122955.jpg') // Add Avatar
			->lang('en-US') // Add Language
			->finish(); // End vCard
		// echo '<p><img src="' . $oQRC->get(300) . '" alt="QR Code" /></p>'; // Generate and display the QR Code
		// $oQRC->display(); // Display
		$data['oQRC'] = $oQRC;
		return view('/layouts/qr_code', $data);
	}
	/*******************************************************/


	/**
	 * Release Updates 
	 */
	public function release_updates()
	{
		$data['title'] = "What's new";
		return view('/layouts/release_updates', $data);
	}
	/*******************************************************/



	public function feature_blocked()
	{
		$data['title'] = "Feature Blocked";
		return view('layouts/feature_disabled', $data);
	}
}
