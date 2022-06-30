<?php

namespace App\Controllers\API;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use \App\Models\StudentModel;

class Student extends ResourceController
{
    use ResponseTrait;


    function index()
    {
        // Forbidden action
        return $this->failForbidden("Access denied");
    }

    public function get_student_data()
    {
        session();
        // This helper required for validation
        helper(['form', 'url']);

        $result = $this->validate([
            'token' => 'required|string|min_length[1]|max_length[80]'
        ]);

        if (!$result) {
            // Generic failure response
            return $this->fail("Validation failed", 400);
        } else {
            $token = $this->request->getVar('token');
            $token = sanitize_input($token);
            $StudentModel = new StudentModel();
            $student_data = $StudentModel->fetch_student_data($token);
            if (!empty($student_data)) {
                // Generic response method
                return $this->respond($student_data, 200);
            } else {
                // Generic failure response
                return $this->fail("Error in processing", 400);
            }
        }
    }
}
