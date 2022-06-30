<?php

namespace App\Controllers;

use \App\Models\RoutineResultsModel;

class RoutineResults extends BaseController
{
    public function index()
    {
        // Log Activity 
        $this->activity->page_access_activity('RoutineResults', '/RoutineResults');
    }


    public function fetch_results()
    {
        $unique_identifier = $this->request->getVar('unique_identifier');
        $RoutineResultsModel = new RoutineResultsModel();
        $students_data = $RoutineResultsModel->fetch_results($unique_identifier);
        print_r(json_encode($students_data));
    }

    public function fetch_failed_results(){
        $unique_identifier = $this->request->getVar('unique_identifier');
        $RoutineResultsModel = new RoutineResultsModel();
        $students_data = $RoutineResultsModel->fetch_failed_results($unique_identifier);
        print_r(json_encode($students_data));
    }
}
