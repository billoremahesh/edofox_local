<?php

namespace App\Controllers;

use \App\Models\ClassroomModel;
use \App\Models\LiveSessionsModel;


class LiveClassrooms extends BaseController
{


    public function index()
    {
        // Log Activity 
        $this->activity->page_access_activity('Live Classrooms', '/LiveClassrooms');
        // Check Authorized User
        if (!isAuthorized("view_classrooms")) {
            return redirect()->to(base_url('/home'));
        }
        if (session()->get('live_count') != 1) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Live Classrooms";
        return view('pages/live_classrooms/overview', $data);
    }




    public function load_live_lectures()
    {
        $postData = $this->request->getVar();
        $postData['admin_id'] = decrypt_cipher(session()->get('login_id'));
        $postData['institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $postData['perms'] = session()->get('perms');
        $LiveSessionsModel = new LiveSessionsModel();
        $live_classrooms = $LiveSessionsModel->live_classrooms($postData);
        echo json_encode($live_classrooms);
    }




    public function start_new_live_lecture()
    {
        $data['title'] = "Start New Lecture";
        $data['admin_id'] = decrypt_cipher(session()->get('login_id'));
        $data['institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $ClassroomModel = new ClassroomModel();
        $data['classroom_list'] = $ClassroomModel->classroom_list($data['institute_id']);
        echo view('modals/live_classrooms/start_new_lecture', $data);
    }



    public function join($schedule_id,$stream_id,$lecture_saved ="")
    {
        $data['title'] = "Live LECTURE";
        $data['session_id'] = $schedule_id;
        $data['stream_id'] = $stream_id;
        $data['lecture_saved'] = $lecture_saved;
        $data['admin_id'] = decrypt_cipher(session()->get('login_id'));
        $data['institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $data['admin_id'] = decrypt_cipher(session()->get('login_id'));
        $data['username'] = session()->get('username');
        $LiveSessionsModel = new LiveSessionsModel();
        $data['live_lecture_details'] = $LiveSessionsModel->get_live_lecture_details($schedule_id,$stream_id,$data['admin_id']);
        return view('pages/live_classrooms/join', $data);
    }
    /*******************************************************/
}
