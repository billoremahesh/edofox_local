<?php

namespace App\Controllers\API;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Libraries\Activity;
use App\Libraries\ZoomApi;

class Authenticate extends ResourceController
{

	use ResponseTrait;


	function index()
	{
		// Forbidden action
		return $this->failForbidden("Access denied");
	}


	function admin_token()
	{
		session();
		if (session()->has('EdofoxAdminLoggedIn')) {
			$data = array();
			$data['admin_token'] = decrypt_cipher(session()->get('admin_token'));
			$response = [
				'data' => $data,
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Request Successful'
				]
			];
			print_r(json_encode($response));
			exit;
		} else {
			// Invalid
			$response = [
				'status'   => 401,
				'error'    => null,
				'messages' => [
					'success' => 'Unauthorized'
				]
			];
			print_r(json_encode($response));
			exit;
		}
	}


	/**
	 * It is added for live session for admin side
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	function student_token()
	{
		session();
		if (session()->has('EdofoxAdminLoggedIn')) {
			$data = array();
			$data['student_token'] = decrypt_cipher(session()->get('admin_token'));
			$response = [
				'data' => $data,
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Request Successful'
				]
			];
			print_r(json_encode($response));
			exit;
		} else {
			// Invalid
			$response = [
				'status'   => 401,
				'error'    => null,
				'messages' => [
					'success' => 'Unauthorized'
				]
			];
			print_r(json_encode($response));
			exit;
		}
	}

	/**
	 * Generate Signature function for Zoom call
	 *
	 * @return void
	 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
	 */
	function generate_signature()
	{
		// Preload Activity Libraries
		// $this->zoom_api = new ZoomApi(service('request'));
		$api_key = 'rxQth6vBQxe-v6DMtW4ZIg';
		$api_secret = 'sX3ZudTEqSgHzy4LFpxsvYL2uUXjYPdQaFnu';
		$meeting_number = $this->request->getVar('meetingNumber');
		$role = $this->request->getVar('role');
		//Set the timezone to UTC
		date_default_timezone_set("UTC");
		$time = time() * 1000 - 30000; //time in milliseconds (or close enough)
		$data = base64_encode($api_key . $meeting_number . $time . $role);
		$hash = hash_hmac('sha256', $data, $api_secret, true);
		$_sig = $api_key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);
		//return signature, url safe base64 encoded
		$signature = rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');

		$data = array();
		$data['signature'] = $signature;
		$response = [
			'data' => $data,
			'status'   => 200,
			'error'    => null,
			'messages' => [
				'success' => 'Request Successful'
			]
		];
		print_r(json_encode($response));
		exit;
	}


	public function encrypt_str(string $str)
	{
		$encrypter = \Config\Services::encrypter();
		$encrypted_str =  bin2hex($encrypter->encrypt($str));
		$response = [
			'data' => $encrypted_str,
			'status'   => 200,
			'error'    => null,
			'messages' => [
				'success' => 'Request Successful'
			]
		];
		print_r(json_encode($response));
		exit;
	}


	public function decrypt_str(string $str)
	{
		$encrypter = \Config\Services::encrypter();
		try {
			$decrypted_str =  $encrypter->decrypt(hex2bin($str));
			$response = [
				'data' => $decrypted_str,
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => 'Request Successful'
				]
			];
			print_r(json_encode($response));
			exit;
		} catch (\Exception $e) {
			// Invalid
			$response = [
				'status'   => 401,
				'error'    => "There was some error in security. Please try again.",
				'messages' => [
					'success' => 'Unauthorized'
				]
			];
			print_r(json_encode($response));
			exit;
		}
	}
}
