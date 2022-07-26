<?php

namespace App\Controllers;

use \App\Models\DoubtsModel;
use \App\Models\SubjectsModel;

class Doubts extends BaseController
{
	public function index()
	{
		// Check Authorized User
		if (!isAuthorized("view_doubts")) {
            $session = session();
            $session->setFlashdata('toastr_error', 'UnAuthorized access.');
			return redirect()->to(base_url('/home'));
		}
		// Log Activity 
		$this->activity->page_access_activity('Doubts', '/doubts');
		$data['title'] = "Doubts";
		$data['sessionType'] = session()->get('sessionType');
		$data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
		$SubjectsModel = new SubjectsModel();
		$data['resultForSubjects'] = $SubjectsModel->get_subjects($data['instituteID']);
		$data['superadminFlag'] = false;
		if (in_array("all_perms", session()->get('perms'))) {
			$data['superadminFlag'] = true;
		}
		return view('pages/doubts/overview', $data);
	}


	/**
	 * Load Filtered Doubts - Async Call
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function fetch_filtered_doubts()
	{
		$data['post_data'] =  $this->request->getVar();
		return view('async/doubts/show_filtered_doubts', $data);
	}
	/*******************************************************/



	/**
	 *  Doubt Details
	 *
	 * @param [type] $doubt_id
	 * @param string $type
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function doubt_details($doubt_id, $type = "", $redirect = 'doubts/doubt_details/')
	{
		$data['title'] = "Doubt Details";
		$data['instituteID'] = session()->get('instituteID');
		$data['doubt_id'] = $doubt_id;
		$data['doubtType'] = $type;
		$data['redirect'] = $redirect . $doubt_id;
		return view('pages/doubts/doubt_details', $data);
	}
	/*******************************************************/



	/**
	 * Classroom Modals 
	 */


	/**
	 * Resolve Doubts Modal
	 *
	 * @param [Integer] $doubt_id
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function resolve_doubts_modal($doubt_question_id, $doubt_question_type = "", $feedback_id = "")
	{
		$data['title'] = "Resolve Doubt";
		$data['doubt_question_id'] = $doubt_question_id;
		$data['doubt_question_type'] = $doubt_question_type;
		$data['feedback_id'] = $feedback_id;
		$data['instituteId'] = decrypt_cipher(session()->get('instituteID'));
		echo view('modals/doubts/resolve', $data);
	}
	/*******************************************************/

	/**
	 * Move Doubt to Pending - Modal
	 *
	 * @param [type] $doubt_id
	 * @param string $type
	 * @param string $redirect
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function move_to_pending_doubt_modal($doubt_id, $type = "", $redirect = 'doubts')
	{
		$data['title'] = "Move Doubt to Pending";
		$data['instituteID'] = session()->get('instituteID');
		$data['doubt_id'] = $doubt_id;
		$data['type'] = $type;
		$data['redirect'] = $redirect;
		echo view('modals/doubts/move_to_pending', $data);
	}
	/*******************************************************/

	/**
	 * End of Classroom Modal
	 */




	/**
	 * Submit Methods (Add, Edit, Delete)
	 */

	/**
	 * Doubt - Move to Pending 
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	public function move_doubt_to_pending_submit()
	{
		$session = session();
		$redirect = $this->request->getVar('redirect');
		$result = $this->validate([
			'doubt_question_id' => 'required'
		]);

		if (!$result) {
			$session->setFlashdata('toastr_error', 'Validation failed.');
			return redirect()->to(base_url($redirect))->withInput();
		} else {

			$data = $this->request->getVar();
			$DoubtsModel = new DoubtsModel();

			if ($DoubtsModel->move_doubt_to_pending($data)) :
				$session->setFlashdata('toastr_success', 'Question Doubt moved back to Pending');
				return redirect()->to(base_url($redirect));
			else :
				$session->setFlashdata('toastr_error', 'Error in processing.');
				return redirect()->to(base_url($redirect));
			endif;
		}
	}
	/*******************************************************/
}
