<?php

namespace App\Controllers;

use \App\Models\TestsModel;
use \App\Models\TestTemplatesModel;
use \App\Models\TestTemplateConfigModel;
use \App\Models\ClassroomModel;
use \App\Models\SubjectsModel;
use \App\Models\StudentModel;
use \App\Models\InstituteModel;
use \App\Models\OmrTemplatesModel;
use stdClass;
use RuntimeException;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;



class Tests extends BaseController
{

    public function index()
    {
        if (session()->get('exam_feature') == 0) {
            $session = session();
            $session->setFlashdata('toastr_error', 'UnAuthorized access.');
            return redirect()->to(base_url('/home'));
        }
        // Check Authorized User
		if (!isAuthorized("view_tests")) {
            $session = session();
            $session->setFlashdata('toastr_error', 'UnAuthorized access.');
			return redirect()->to(base_url('/home'));
		}

        // Log Activity 
        $this->activity->page_access_activity('Tests', '/tests');
        $data['title'] = "Manage Tests";
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $data['instituteID'] = session()->get('instituteID');
        $ClassroomModel = new ClassroomModel();
        $data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID);
        $TestsModel = new TestsModel();
        $data['tests_active_students'] = $TestsModel->tests_active_students($instituteID);
        $data['unsubmitted_tests_students'] = $TestsModel->unsubmitted_tests_students($instituteID);
        $data['deleted_tests_count'] = $TestsModel->deleted_tests_count($instituteID);
        $TestTemplatesModel = new TestTemplatesModel();
        $data['test_templates_count'] = $TestTemplatesModel->institute_templates_count($instituteID);
        $data['total_test_cnt'] = $TestsModel->test_count($instituteID);
        return view('pages/tests/overview', $data);
    }
    /*******************************************************/



    /**
     * Load Tests
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function load_tests()
    {
        // POST data
        $postData = object_to_array($this->request->getVar());
        // Get data
        $TestsModel = new TestsModel();
        $data['exams_data'] = $TestsModel->fetch_filtered_tests($postData);
        $data['perms'] = isset($_SESSION['perms']) ? $_SESSION['perms'] : array();
        return view('async/tests/load_tests', $data);
    }
    /*******************************************************/


    /**
     * Add Test Question Options
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_questions_options($test_id)
    {
        $data['title'] = "Add Questions in Test";
        $data['instituteID'] = session()->get('instituteID');
        $data['test_id'] = $test_id;
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        return view('pages/tests/add_questions_options', $data);
    }
    /*******************************************************/




    /**
     * Update Test Properties
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_test_question_properties()
    {
        // POST data
        $postData = $this->request->getVar();
        // Get data
        $TestsModel = new TestsModel();
        $result = $TestsModel->update_test_question_properties($postData);
        echo $result;
    }
    /*******************************************************/


    /**
     * Update Test Properties
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_test_properties()
    {
        // POST data
        $postData = $this->request->getVar();
        // Get data
        $TestsModel = new TestsModel();
        $result = $TestsModel->update_test_properties($postData);
        echo $result;
    }
    /*******************************************************/


    /**
     * Send Exam Notification
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function send_exam_notification($test_id)
    {

        $data1 = array(
            "id" => $test_id
        );
        $data = array(
            "test" => $data1,
            "requestType" => "NewExam"
        );
        $data_string = json_encode($data);
        // echo $data_string;

        // Initiate curl
        $ch = curl_init();
        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // POST ROW data
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            'AuthToken: ' . decrypt_cipher($_SESSION['admin_token'])
        ));
        // Set the url
        // Include Service URLs Parameters File
        include_once(APPPATH . "Views/service_urls.php");
        curl_setopt($ch, CURLOPT_URL, $sendNotificationUrl);
        // Execute
        $objTestString = curl_exec($ch);
        // Closing
        curl_close($ch);
        // echo $objTestString;
        $objTest = json_decode($objTestString, true);
        return $objTest;
    }


    /**
     * Import Offline Results
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function import_offline_results($test_id)
    {

        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['title'] = 'IMPORT OFFLINE RESULT: ' . $data['test_details']['test_name'];
        $data['test_id'] = $test_id;
        $data['instituteID'] = session()->get('instituteID');
        $data['redirect'] = 'tests/show_test_result/1/' . $test_id;
        $data['decrypt_test_id'] = decrypt_cipher($test_id);
        $data['decrypt_institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $data['validation'] =  \Config\Services::validation();
        $TestsModel = new TestsModel();
        $data['test_offset'] = $TestsModel->test_offset(decrypt_cipher($test_id));
        echo view('pages/tests/import_offline_results', $data);
    }
    /*******************************************************/



    /**
     * Fire Bulk SMS
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fire_bulk_sms($test_id)
    {


        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['title'] = 'RESULT: ' . $data['test_details']['test_name'];

        $data['test_id'] = $test_id;
        $data['redirect'] = 'tests';
        $data['instituteID'] = session()->get('instituteID');

        $data['decrypt_test_id'] = decrypt_cipher($test_id);
        $data['decrypt_institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $instituteID = decrypt_cipher(session()->get('instituteID'));

        $data['validation'] =  \Config\Services::validation();
        $TestsModel = new TestsModel();
        $data['revaluated_result'] = $TestsModel->revaluated_result(decrypt_cipher($test_id), $instituteID);
        $data['revaluated_result1'] = $TestsModel->revaluated_result(decrypt_cipher($test_id), $instituteID, 'Biology');
        $data['revaluated_result2'] = $TestsModel->revaluated_result(decrypt_cipher($test_id), $instituteID, 'Other');
        echo view('pages/tests/fire_bulk_sms', $data);
    }


    public function download_offline_test_result_template($test_id)
    {


        $TestsModel = new TestsModel();
        $result_fetch_test_questions = $TestsModel->fetch_test_questions(decrypt_cipher($test_id));
        $result_test_absent_students = $TestsModel->fetch_test_absent_students(decrypt_cipher($test_id));

        $question_numbers = array();
        $question_types = array();
        // Looping through the result of questions to send for excel export
        // This is to send array in form post to process
        if (!empty($result_fetch_test_questions)) {
            foreach ($result_fetch_test_questions as $row_fetch_test_questions) :
                array_push($question_numbers, $row_fetch_test_questions['question_number']);
                array_push($question_types, $row_fetch_test_questions['question_type']);
            endforeach;
        }


        $students_usernames = array();
        $students_names = array();
        $students_rollnos = array();
        if (!empty($result_test_absent_students)) {
            foreach ($result_test_absent_students as $row_test_absent_students) :
                array_push($students_usernames, $row_test_absent_students['username']);
                array_push($students_names, $row_test_absent_students['name']);
                array_push($students_rollnos, $row_test_absent_students['roll_no']);
            endforeach;
        }


        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return;
        }

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Adding first header row
        $sheet->setCellValueByColumnAndRow('1', '1', 'STUDENT USERNAME');
        $sheet->setCellValueByColumnAndRow('2', '1', 'STUDENT NAME');
        $sheet->setCellValueByColumnAndRow('3', '1', 'STUDENT ROLL NO');

        // For autosizing (cell automatic expansion) 
        // https://stackoverflow.com/questions/52070754/center-all-text-in-phpspreadsheet-and-make-the-cells-expand-to-fill-up-with-cont
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);


        //Styling the first row
        $styleArray = [
            'font' => [
                'bold' => true,
                'color' => [
                    'argb' => 'FFFFFFFF',
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFF0000',
                ]
            ]
        ];
        $sheet->getStyle('1')->applyFromArray($styleArray);

        // Looping through the question numbers and generating all columns based on it
        for ($i = 0; $i < count($question_numbers); $i++) {

            //Adding FORMAT OF CORRECT ANSWER in the 4th column of the excel
            $answer_format = "";
            if ($question_types[$i] == "SINGLE") {
                $answer_format = "A";
            }
            if ($question_types[$i] == "PASSAGE_MULTIPLE") {
                $answer_format = "A,B,C";
            }
            if ($question_types[$i] == "MATCH") {
                $answer_format = "A-P,B-R,S-T";
            }
            if ($question_types[$i] == "MULTIPLE") {
                $answer_format = "A,C";
            }
            if ($question_types[$i] == "NUMBER") {
                $answer_format = "723";
            }

            $sheet->setCellValueByColumnAndRow($i + 4, '1', "Q.No. " . $question_numbers[$i] . " ($question_types[$i]) Format: $answer_format");
        }

        // Looping through the Students data and generating all columns based on it
        for ($i = 0; $i < count($students_names); $i++) {
            //Adding Student Username in the 1st column of the excel
            $sheet->setCellValueByColumnAndRow('1', $i + 2, $students_usernames[$i]);
            //Adding Student Name in the 2nd column of the excel
            $sheet->setCellValueByColumnAndRow('2', $i + 2, $students_names[$i]);
            //Adding Student Roll No in the 3rd column of the excel
            $sheet->setCellValueByColumnAndRow('3', $i + 2, $students_rollnos[$i]);
        }

        $lastColumn = $sheet->getHighestColumn();
        $sheet->getColumnDimension($lastColumn)->setAutoSize(true);


        ob_clean(); // To remove issue with extra headers added automatically
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        date_default_timezone_set('Asia/Kolkata'); // IST
        header('Content-Disposition: attachment; filename="import offline result template - ' . date("dmYHis") . '.xlsx"');
        $writer->save('php://output');

        exit;
    }



    /**
     * Add Test Image Questions
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_test_img_questions($test_id)
    {
        $data['title'] = "Add Questions in Test from images";
        $data['test_id'] = $test_id;
        $data['instituteID'] = session()->get('instituteID');

        $data['decrypt_test_id'] = decrypt_cipher($test_id);
        $data['decrypt_institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $instituteID = decrypt_cipher(session()->get('instituteID'));

        $data['validation'] =  \Config\Services::validation();
        $TestsModel = new TestsModel();
        $data['test_offset'] = $TestsModel->test_offset(decrypt_cipher($test_id));
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));

        $SubjectsModel = new SubjectsModel();
        $data['subject_details'] = $SubjectsModel->get_subjects($instituteID);
        echo view('pages/tests/questions/add_img_questions', $data);
    }
    /*******************************************************/


    /**
     * Get Multiple Image Upload Div - Async Call
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_multiple_questions_div()
    {
        $data['redirect'] = 'tests';
        $data['noQues']  = sanitize_input($this->request->getVar('noQues'));
        $data['instituteID']  = sanitize_input($this->request->getVar('institute_id'));
        $data['test_id']  = sanitize_input($this->request->getVar('test_id'));
        $data['section']  = sanitize_input($this->request->getVar('section'));
        $data['subjectid']  = sanitize_input($this->request->getVar('subject_id'));
        $data['weightage']  = sanitize_input($this->request->getVar('weightage'));
        $data['negative_mark']  = sanitize_input($this->request->getVar('negative_mark'));
        $data['offset']  = sanitize_input($this->request->getVar('offset'));
        $data['optionsType']  = sanitize_input($this->request->getVar('optionsType'));
        $data['quetionType']  = sanitize_input($this->request->getVar('quetionType'));
        $data['questionPartialMarking']  = sanitize_input($this->request->getVar('questionPartialMarking'));
        $data['template_id']  = sanitize_input($this->request->getVar('template_id'));
        echo view('async/tests/multiple_questions_div', $data);
    }
    /*******************************************************/



    /**
     * OMR Test Papers Evalutions
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function omr_test_evalution($encrypt_test_id)
    {
        // Log Activity 
        $this->activity->page_access_activity('Tests', '/tests');
        $data['title'] = "OMR Test Evalution";
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $data['instituteId'] = $instituteID;
        $data['test_id'] = decrypt_cipher($encrypt_test_id);
        $data['encrypted_test_id'] = $encrypt_test_id;
        $TestsModel = new TestsModel();
        $test_details = $TestsModel->get_test_details($data['test_id']);
        $data['test_details'] = $test_details;

        if(isset($test_details['omr_template'])) {
            $OmrTemplatesModel = new OmrTemplatesModel();
            $data['omr_template'] = $OmrTemplatesModel->get_omr_template($test_details['omr_template']);    
        }
        
        return view('pages/tests/omr_test_evalution', $data);
    }
    /*******************************************************/

    /**
     * Add Bulk Test Question Images
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_test_bulk_images_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'test_id' => ['label' => 'Test ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            // Include Service URLs Parameters File
            include_once(APPPATH . "Views/service_urls.php");
            // Move Uploaded Questions Files
            $question_img_urls = array();
            $upload_folder_location = "/$quee_id/";

            $imagefile = $this->request->getFiles();
            foreach ($data['Que_subject_id'] as $key => $question_id) :

                $file = $imagefile['test_questions_files'][$key];
                $target_dir = $upload_folder_location . $question_id;
                $new_file_name = $file->getRandomName();
                $target_file = $target_dir . '/' . $new_file_name;
                if ($file->isValid()) :
                    $file->move($target_dir, $new_file_name);
                    array_push($question_img_urls, $target_file);
                else :
                    array_push($question_img_urls, "");
                endif;
            endforeach;


            // Move uploaded question images urls to data
            $data['question_img_urls'] = $question_img_urls;

            $TestsModel = new TestsModel();
            if ($TestsModel->add_test_questions_images($data)) {
                $session->setFlashdata('toastr_success', 'Test Questions images added successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/


    /**
     * Check/Update Test Questions
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_test_questions($test_id)
    {
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['test_sess_name'] = $data['test_details']['test_name'];
        $data['title'] = "Check-Update Questions in Test: " . $data['test_sess_name'];
        $data['test_id'] = $test_id;
        $data['redirect'] = 'tests/update_test_questions/' . $test_id;
        $data['test_sess_id'] =  decrypt_cipher($test_id);
        $data['instituteID'] = session()->get('instituteID');
        $data['decrypted_instituteID'] = decrypt_cipher(session()->get('instituteID'));
        echo view('pages/tests/questions/update_test_questions', $data);
    }
    /*******************************************************/





    /**
     * Realtime Overview
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function realtime_overview($test_id)
    {
        $TestsModel = new TestsModel();
        $data['test_id'] = $test_id;
        $data['decrypt_test_id'] = decrypt_cipher($test_id);
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['graph_script'] = true;
        $data['title'] = "Realtime Test Overview";
        $data['instituteID'] = session()->get('instituteID');
        $data['students_list'] = $TestsModel->fetch_test_students(decrypt_cipher($test_id));
        $data['test_avg_time_left'] = $TestsModel->test_avg_time_left(decrypt_cipher($test_id));
        $current_date_time = date('Y-m-d H:i:s');
        $data['is_test_past'] = false;
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        if ($data['test_details']['end_date'] < $current_date_time) {
            $data['is_test_past'] = true;
        }
        $data['test_avg_visited_count'] = $TestsModel->avg_visited_solved_count(decrypt_cipher($test_id), "visited");
        $data['test_avg_solved_count'] = $TestsModel->avg_visited_solved_count(decrypt_cipher($test_id), "solved");
        $data['test_active_students'] = $TestsModel->tests_active_students(decrypt_cipher($data['instituteID']), decrypt_cipher($test_id));
        $data['test_started_by'] = $TestsModel->test_students_cnt(decrypt_cipher($test_id), 'STARTED');
        $data['test_completed_by'] = $TestsModel->test_students_cnt(decrypt_cipher($test_id), 'COMPLETED');
        echo view('pages/tests/realtime_overview', $data);
    }
    /*******************************************************/


    /**
     * Real Time Location View 
     */
    public function realtime_location_view(string $test_id)
    {
        $data['title'] = "Realtime Location Overview";
        $data['test_id'] = decrypt_cipher($test_id);
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details($data['test_id']);
        echo view('pages/tests/realtime_location_view', $data);
    }
    /*******************************************************/



    /**
     * Realtime Student Overview
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function realtime_student_overview()
    {
        $TestsModel = new TestsModel();
        // POST DATA
        $data['student_id'] = sanitize_input($this->request->getVar('student_id'));
        $data['test_id'] = decrypt_cipher(sanitize_input($this->request->getVar('test_id')));
        $data['encrypted_test_id'] = sanitize_input($this->request->getVar('test_id'));
        $data['encrypted_student_id'] = encrypt_string($data['student_id']);
        $data['realtime_student_details'] = $TestsModel->realtime_student_overview($data['test_id'], $data['student_id']);
        echo view('async/tests/realtime_overview_search_student', $data);
    }
    /*******************************************************/

    /**
     * Fetch Test Device Information
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_device_distribution()
    {
        $test_id = $this->request->getVar('test_id');
        $TestsModel = new TestsModel();
        $result = $TestsModel->device_information(decrypt_cipher($test_id));
        echo json_encode($result);
    }
    /*******************************************************/

    /**
     * Print Test Paper with Solution and Without Solutions
     *
     * @param [type] $test_id
     * @param integer $solution_check
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function print_test_paper($test_id, $solution_check = 1)
    {
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['title'] = "Print Paper : " . strtoupper($data['test_details']['test_name']);
        $data['test_id'] = $test_id;
        $data['show_solutions'] = $solution_check;
        $data['instituteID'] = session()->get('instituteID');
        $data['instituteName'] = session()->get('instituteName');
        $data['students_list'] = $TestsModel->fetch_test_students(decrypt_cipher($test_id));
        echo view('pages/tests/print_test_paper', $data);
    }
    /*******************************************************/


    /**
     * Fetch Test Paper Data - Async
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_test_paper_data()
    {
        // POST data
        $data['test_id'] = sanitize_input(decrypt_cipher($this->request->getVar('test_id')));
        $data['instituteID'] = sanitize_input(decrypt_cipher($this->request->getVar('institute_id')));
        $data['instituteName'] = sanitize_input($this->request->getVar('instituteName'));
        $data['columns'] = sanitize_input($this->request->getVar('columns'));
        $data['show_solutions'] = sanitize_input($this->request->getVar('show_solutions'));
        $data['show_options'] = sanitize_input($this->request->getVar('show_options'));
        $data['no_of_que_per_page'] = sanitize_input($this->request->getVar('no_of_que_per_page'));

        // Saving values in session for saving state of dropdowns
        session()->set('columns_in_print', $data['columns']);
        session()->set('show_options_in_paper', $data['show_options']);


        echo view('async/tests/test_paper_data', $data);
    }
    /*******************************************************/


    /**
     * Auto Generate Test Chapter Wise
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function generate_chapter_wise_test($test_id)
    {
        $data['title'] = "Chapter wise auto-create exam";
        $data['test_id'] = $test_id;
        $data['decrypt_test_id'] =  decrypt_cipher($test_id);
        $data['instituteID'] = session()->get('instituteID');
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['number_of_questions'] = $TestsModel->fetch_number_of_questions(decrypt_cipher($test_id));
        $data['chapterwise_questions'] = $TestsModel->fetch_chapterwise_questions_count(decrypt_cipher($test_id));
        $data['type_of_questions'] = $TestsModel->fetch_type_of_questions(decrypt_cipher($test_id));
        $SubjectsModel = new SubjectsModel();
        $data['test_subjects'] = $SubjectsModel->get_subjects(decrypt_cipher($data['instituteID']));
        echo view('pages/tests/generate_chapter_wise_test', $data);
    }
    /*******************************************************/



    /**
     * Parse PDF for test Questions
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function parse_pdf($test_id)
    {

        if (session()->get('exam_feature') == 0) {
            $session = session();
            $session->setFlashdata('toastr_error', 'UnAuthorized access.');
            return redirect()->to(base_url('/home'));
        }

        // Log Activity 
        $this->activity->page_access_activity('Test Parse Pdf', '/tests/parse_pdf');

        $data['test_id'] = $test_id;
        $data['staff_id'] = decrypt_cipher(session()->get('login_id'));
        $data['decrypt_test_id'] =  decrypt_cipher($test_id);
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['title'] = "Parse PDF For " . $data['test_details']['test_name'];
        $data['instituteID'] = session()->get('instituteID');
        $data['decrypted_institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $instituteID = decrypt_cipher($data['instituteID']);
        $SubjectsModel = new SubjectsModel();
        $data['subject_details'] = $SubjectsModel->get_subjects($instituteID);
        echo view('pages/tests/questions/parse_pdf', $data);
    }
    /*******************************************************/


    /**
     * CROP Test Images
     *
     * @param integer $test_id
     * @param integer $question_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function cropper()
    {
        $test_id = $this->request->getVar('test_id');
        $question_id = $this->request->getVar('question_id');
        $url = $this->request->getVar('url');
        $data['testId'] = $test_id;
        $data['questionId'] = $question_id;
        $data['url'] = $url;
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details($test_id);
        $data['title'] = "CROP Image " . $data['test_details']['test_name'];
        echo view('pages/tests/questions/crop_image', $data);
    }
    /*******************************************************/



    /**
     * Add Test Subject Wise Question 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_test_question($subject_id, $subject_name, $test_id)
    {
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['title'] = 'Add Test Questions';
        $data['test_sess_id'] = $test_id;
        $data['subj_sess_id'] = $subject_id;
        $data['instituteID'] = session()->get('instituteID');
        $data['redirect'] = 'tests/update_test_questions/' . $test_id;
        $data['test_sess_name'] = $data['test_details']['test_name'];
        $data['subj_sess_name'] = $subject_name;
        echo view('pages/tests/questions/add_test_question', $data);
    }
    /*******************************************************/

    /**
     * Add Test Questions from Test Questions
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function question_bank($test_id)
    {
        $data['test_id'] = $test_id;
        $data['decrypt_test_id'] = decrypt_cipher($test_id);
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['title'] = "PICK Questions From Question Bank " . $data['test_details']['test_name'];
        $SubjectsModel = new SubjectsModel();
        $data['subjects_list'] = $SubjectsModel->get_subjects($data['instituteID']);
        echo view('pages/tests/questions/question_bank', $data);
    }
    /*******************************************************/


    /**
     * Add Test Anwser Key
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_answer_key($test_id)
    {
        $data['test_id'] = $test_id;
        $data['instituteID'] = session()->get('instituteID');
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['title'] = "Add Answer Key (" . $data['test_details']['test_name'] . ")";
        $data['students_list'] = $TestsModel->fetch_test_students(decrypt_cipher($test_id));
        $data['questions_list'] = $TestsModel->fetch_test_questions(decrypt_cipher($test_id));
        echo view('pages/tests/add_answer_key', $data);
    }
    /*******************************************************/


    /**
     * Print Test Answer Key
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function print_answer_key($test_id)
    {
        $data['title'] = "Print Answer Key";
        $data['test_id'] = $test_id;
        $data['decrypt_test_id'] = decrypt_cipher($test_id);
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        echo view('pages/tests/print_answer_key', $data);
    }
    /*******************************************************/



    /**
     * Add Test Solutions 
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_test_solutions($test_id)
    {
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['title'] = strtoupper("Add Solutions for test:" . $data['test_details']['test_name']);
        $data['test_id'] = $test_id;
        $data['instituteID'] = session()->get('instituteID');
        $data['solution_pdf_data'] = $TestsModel->get_solution_pdf_data(decrypt_cipher($test_id));
        $data['solution_video_data'] = $TestsModel->get_solution_video_data(decrypt_cipher($test_id), decrypt_cipher(session()->get('instituteID')));
        echo view('pages/tests/add_solutions', $data);
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * Add Solutions Images
     * @return View
     * @author PrachiP
     * @since 2021-10-21
     */
    public function add_solutions_images($test_id)
    {
        $data['title'] = "Add Solutions Images";
        $data['test_id'] =  decrypt_cipher($test_id);
        $data['institute_id'] = session()->get('instituteID');
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details($data['test_id']);
        $row_no_of_questions = $TestsModel->get_no_of_questions($data['test_id']);
        $data['number_of_questions'] = $row_no_of_questions['no_of_questions'];
        echo view('pages/tests/add_solutions_images', $data);
    }
    /*******************************************************/

    /**
     * Evaluate Subjective Answers
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function evaluate_subjective_answers($test_id)
    {
        $data['title'] = "Evaluate Subjective Answers";
        $data['test_id'] = $test_id;
        $data['instituteID'] = session()->get('instituteID');
        $TestsModel = new TestsModel();
        $data['testDetails'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['examAnswersResult'] = $TestsModel->examAnswersResult(decrypt_cipher($test_id));
        echo view('pages/tests/evaluate_subjective_answers', $data);
    }
    /*******************************************************/

    /**
     * Evaluate Student Subjective Answers 
     *
     * @param [type] $test_id
     * @param [type] $student_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function evaluate_students_subjective_answers($test_id, $student_id = "")
    {
        $data['title'] = "Evaluate Students Subjective Answers";
        $data['test_id'] = $test_id;
        $data['encrypted_test_id'] = encrypt_string($test_id);
        $data['student_id'] = $student_id;
        $data['instituteID'] = session()->get('instituteID');
        $data['adminId'] = decrypt_cipher(session()->get('login_id'));
        if ($student_id != "") {
            $StudentModel = new StudentModel();
            $data['student_details'] = $StudentModel->get_student_details($student_id, decrypt_cipher($data['instituteID']));
        }
        echo view('pages/tests/evaluate_students_subjective_answers', $data);
    }
    /*******************************************************/


    /**
     * Subjectwise Correctness Percentage
     *
     * @param [type] $test_id
     * @param [type] $student_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function subjectwise_correctness_percentage($test_id, $student_id)
    {

        $TestsModel = new TestsModel();
        $resultForSubjectWiseCorrection = $TestsModel->subjectwise_correctness_percentage($test_id, $student_id);

        /**Reference: https://www.w3schools.com/angular/angular_sql.asp */
        $outp = "";
        if (!empty($resultForSubjectWiseCorrection)) {
            foreach ($resultForSubjectWiseCorrection as $row) {
                if ($outp != "") {
                    $outp .= ",";
                }
                $outp .= '{"subject_id":"' . $row["subject_id"] . '",';
                $outp .= '"subject":"' . $row["subject"] . '",';
                $outp .= '"correctness":"' . $row["correctness"] . '",';
                $outp .= '"correctCount":"' . $row["correctCount"] . '",';
                $outp .= '"wrongCount":"' . $row["wrongCount"] . '",';
                $outp .= '"totalQuestions":"' . $row["totalQuestions"] . '"}';
            }
        }

        $outp = '{"records":[' . $outp . ']}';

        echo ($outp);
    }
    /*******************************************************/



    /**
     * Subjectwise Time taken tests
     *
     * @param [type] $test_id
     * @param [type] $student_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function subjectwise_time_taken_tests($test_id, $student_id)
    {

        $TestsModel = new TestsModel();
        $resultForTimeTaken = $TestsModel->subjectwise_time_taken_tests($test_id, $student_id);
        /** Reference: https://www.w3schools.com/angular/angular_sql.asp */
        $outp = "";
        foreach ($resultForTimeTaken as $row) {
            if ($outp != "") {
                $outp .= ",";
            }
            $outp .= '{"subject_id":"'  . $row["subject_id"] . '",';
            $outp .= '"subject":"'   . $row["subject"]        . '",';
            $outp .= '"time_taken":"' . $row["time_taken"]     . '"}';
        }

        $outp = '{"records":[' . $outp . ']}';
    }
    /*******************************************************/



    /**
     * Show Test Result
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function show_test_result($encrypt_flag = 1, $test_id)
    {
        $data['graph_script'] = true;

        if ($encrypt_flag == 1) {
            $data['test_id'] = decrypt_cipher($test_id);
        } else {
            $data['test_id'] = $test_id;
        }

        $data['encrypted_test_id'] = $test_id;
        $data['instituteID'] = session()->get('instituteID');
        $TestsModel = new TestsModel();
        // $data['students_list'] = $TestsModel->fetch_test_students($data['test_id']);
        $data['test_details'] = $TestsModel->get_test_details($data['test_id']);
        $InstituteModel = new InstituteModel();
        $data['institute_details'] = $InstituteModel->get_institute_details(decrypt_cipher($data['instituteID']));
        $data['title'] = $data['test_details']['test_name'];
        echo view('pages/tests/show_test_result', $data);
    }
    /*******************************************************/


    /**
     * Proctoring Analysis
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function proctoring_analysis($test_id)
    {
        $data['title'] = "Proctoring Analysis";
        $data['test_id'] = decrypt_cipher($test_id);
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        echo view('pages/tests/proctoring_analysis', $data);
    }
    /*******************************************************/


    public function ajax_fetch_proctoring_analysis()
    {
        // POST data
        $postData = $this->request->getVar();
        $postData['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        // Get data
        $StudentModel = new StudentModel();
        $students_data = $StudentModel->fetch_proctoring_students_data($postData);
        echo json_encode($students_data);
    }


    public function student_proctor_activity($test_id, $student_id)
    {
        $data['test_id'] = $test_id;
        $data['student_id'] = $student_id;
        $data['instituteID'] = session()->get('instituteID');
        $StudentModel = new StudentModel();
        $data['student_details'] = $StudentModel->get_student_details(decrypt_cipher($student_id), decrypt_cipher($data['instituteID']));
        $data['title'] = "REMOTE PROCTORING ANALYSIS OF " . $data['student_details']['name'];
        $TestsModel = new TestsModel();
        $data['student_proctor_avg_score'] = $TestsModel->student_proctor_avg_score(decrypt_cipher($test_id), decrypt_cipher($student_id));
        $data['proctor_images'] = $TestsModel->student_proctor_images(decrypt_cipher($test_id), decrypt_cipher($student_id));
        echo view('pages/tests/student_proctor_activity', $data);
    }

    public function proctoring_sessions($test_id)
    {
        $data['title'] = "Proctoring Sessions";
        $data['test_id'] = $test_id;
        $data['instituteID'] = session()->get('instituteID');
        $TestsModel = new TestsModel();
        $data['proctoring_sessions'] = $TestsModel->proctoring_sessions($test_id);
        echo view('pages/tests/proctoring_sessions', $data);
    }

    /**
     * Student Test Activity
     *
     * @param [type] $test_id
     * @param [type] $student_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_test_activity($test_id, $student_id)
    {
        $data['instituteID'] = session()->get('instituteID');
        $instituteID = decrypt_cipher($data['instituteID']);
        // Get data
        $StudentModel = new StudentModel();
        $decrypted_student_id = decrypt_cipher($student_id);
        $data['student_details'] = $StudentModel->get_student_details($decrypted_student_id, $instituteID);
        $data['title'] = "TEST ACTIVITY ANALYSIS OF " . $data['student_details']['name'];
        $decrypt_test_id = decrypt_cipher($test_id);
        $decrypt_student_id = decrypt_cipher($student_id);
        $data['instituteID'] = session()->get('instituteID');
        $TestsModel = new TestsModel();
        $data['student_test_activity_details'] = $TestsModel->student_test_activity($decrypt_test_id, $decrypt_student_id);
        echo view('pages/tests/student_test_activity', $data);
    }
    /*******************************************************/

    /**
     * Unsubmitted Tests Students
     *
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function unsubmitted_tests_students()
    {
        $data['title'] = "UNSUBMITTED STUDENTS REPORT";
        $data['instituteID'] = session()->get('instituteID');
        $data['decrypt_instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $TestsModel = new TestsModel();
        $data['unsubmitted_tests_students'] = $TestsModel->unsubmitted_tests_students(decrypt_cipher($data['instituteID']));
        $data['testwise_unsubmitted_counts'] = $TestsModel->testwise_unsubmitted_students(decrypt_cipher($data['instituteID']));
        echo view('pages/tests/unsubmitted_tests_students', $data);
    }
    /*******************************************************/


    /**
     * Add New Test
     *
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_new_test()
    {
        if (session()->get('exam_feature') == 0) {
            $session = session();
            $session->setFlashdata('toastr_error', 'UnAuthorized access.');
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Add Test";
        // Log Activity 
        $this->activity->page_access_activity('Add Test', '/tests/add_new_test');
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $TestTemplatesModel = new TestTemplatesModel();
        $data['test_templates'] = $TestTemplatesModel->fetch_test_templates($instituteID);
        $data['instituteID'] = session()->get('instituteID');
        $data['redirect'] = 'tests/add_new_test';
        $data['validation'] =  \Config\Services::validation();
        $ClassroomModel = new ClassroomModel();
        $data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID);
        echo view('pages/tests/add_new_test', $data);
    }
    /*******************************************************/


    /**
     * Update Test Details
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_test_details($test_id)
    {
        $data['title'] = "Update Test";
        $data['test_id'] = $test_id;
        $data['redirect'] = "tests";
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $TestTemplatesModel = new TestTemplatesModel();
        $data['test_templates'] = $TestTemplatesModel->fetch_test_templates($instituteID);
        $data['instituteID'] = session()->get('instituteID');
        $data['validation'] =  \Config\Services::validation();
        $ClassroomModel = new ClassroomModel();
        $data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID);
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['classrooms_array'] = $TestsModel->get_test_mapped_classrooms(decrypt_cipher($test_id));
        $OmrTemplatesModel = new OmrTemplatesModel();
        $data['omrTemplates'] = $OmrTemplatesModel->get_omr_templates($instituteID);
        echo view('pages/tests/update_test_details', $data);
    }
    /*******************************************************/


    /**
     * Tests Modals 
     */


    /**
     * Update Student Test Status Modal
     *
     * @param [type] $test_id
     * @param [type] $student_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function edit_student_test_status($test_id, $student_id, $redirect = 'tests/realtime_overview/')
    {
        $data['title'] = "Edit Test Status of this student";
        $data['test_id'] = $test_id;
        $data['student_id'] = $student_id;
        $data['redirect'] = $redirect . encrypt_string($test_id);
        $data['validation'] =  \Config\Services::validation();
        $TestsModel = new TestsModel();
        $data['realtime_student_details'] = $TestsModel->realtime_student_overview($data['test_id'], $data['student_id']);
        echo view('modals/tests/edit_student_test_status', $data);
    }
    /*******************************************************/



    /**
     * Clone Test to another classroom
     *
     * @param [type] $test_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function clone_test_modal($test_id, $redirect = 'tests')
    {
        $data['title'] = "Clone/ Copy Test";
        $data['test_id'] = $test_id;
        $data['redirect'] = $redirect;
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $data['instituteID'] = session()->get('instituteID');
        $data['validation'] =  \Config\Services::validation();
        $ClassroomModel = new ClassroomModel();
        $data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID);
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        echo view('modals/tests/clone', $data);
    }
    /*******************************************************/


    public function super_admin_clone_test($test_id, $institute_id, $package_id)
    {
        // Check permission to view Super Admin
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }
        $session = session();
        $data['original_test_id'] = $test_id;
        $data['institute_id'] = $institute_id;
        $data['package_id'] = $package_id;
        $TestsModel = new TestsModel();
        if ($TestsModel->clone_test_super_admin($data)) {
            echo 'Test cloned successfully.';
        } else {
            echo 'Error in processing.';
        }
    }


    /**
     * Add Solution PDF Modal
     *
     * @param [type] $test_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_solution_pdf_modal($test_id, $redirect = 'tests')
    {
        $data['instituteID'] = session()->get('instituteID');
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['title'] = strtoupper("Add Solutions PDF for: " . $data['test_details']['test_name']);
        $data['test_id'] = decrypt_cipher($test_id);
        $data['redirect'] = $redirect;
        echo view('modals/tests/add_solution_pdf', $data);
    }
    /*******************************************************/



    /**
     * Add Solution Video Modal
     *
     * @param [type] $test_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_solution_video_modal($test_id, $redirect = 'tests')
    {
        $data['instituteID'] = session()->get('instituteID');
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['title'] = strtoupper("Add Solutions Video URL for: " . $data['test_details']['test_name']);
        $data['test_id'] = $test_id;
        $data['redirect'] = $redirect . "/add_test_solutions/" . $test_id;
        echo view('modals/tests/add_solution_video', $data);
    }
    /*******************************************************/


    /**
     * Delete Solution Video Modal
     *
     * @param [type] $video_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_solution_video_modal($video_id, $test_id, $redirect = 'tests')
    {
        $data['instituteID'] = session()->get('instituteID');
        $data['video_id'] = $video_id;
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['title'] = strtoupper("Delete Test Video Solutions For: " . $data['test_details']['test_name']);
        $data['test_id'] = $test_id;
        $data['redirect'] = $redirect . "/add_test_solutions/" . $test_id;
        echo view('modals/tests/delete_solution_video', $data);
    }
    /*******************************************************/

    /**
     * Delete Test Modal
     *
     * @param [Integer] $test_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_test_modal($test_id, $redirect = 'tests')
    {
        $data['title'] = "Delete Test";
        $data['test_id'] = $test_id;
        $data['redirect'] = $redirect;
        $data['validation'] =  \Config\Services::validation();
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        echo view('modals/tests/delete', $data);
    }
    /*******************************************************/


    /**
     * End of Classroom Modal
     */


    /*******************************************************/
    /**
     * Deleted Tests
     * @author PrachiP
     * @version 2021/09/03
     * @return mixed view
     */
    public function deleted_tests()
    {
        $data['title'] = "Deleted Tests";
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $TestsModel = new TestsModel();
        $data['deleted_tests_data'] = $TestsModel->fetch_all_deleted_tests($instituteID);
        echo view('pages/tests/deleted_tests', $data);
    }
    /*******************************************************/



    /**
     * Submit Methods (Add, Edit, Delete)
     */

    /**
     * Add New Test Submit 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_new_test_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'add_test_name' => ['label' => 'Test Name', 'rules' => 'required|string|min_length[1]|max_length[120]'],
            'add_test_ui' => ['label' => 'Test UI', 'rules' => 'required|string|min_length[1]|max_length[120]'],
            'add_test_package' => ['label' => 'Classrooms', 'rules' => 'required|string|min_length[1]|max_length[120]'],
            'add_test_no_questions' => ['label' => 'No of Questions', 'rules' => 'required|string|min_length[1]|max_length[120]'],
            'add_test_total_marks' => ['label' => 'Total Marks', 'rules' => 'required|string|min_length[1]|max_length[120]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            if ($data['end_date'] <= $data['start_date']) {
                $session->setFlashdata('toastr_error', 'End date should be greater than Start date.');
                return redirect()->to(base_url($redirect));
            }

            if (($data['add_test_duration_hours'] == '0') && ($data['add_test_duration_minutes'] == '0')) {
                $session->setFlashdata('toastr_error', 'Test duration should be greater than 0.');
                return redirect()->to(base_url($redirect));
            }

            $TestsModel = new TestsModel();
            $test_id = $TestsModel->add_new_test($data);
            if ($test_id) {
                $this->send_exam_notification($test_id);
                $session->setFlashdata('toastr_success', 'Added New Test successfully.');
                return redirect()->to(base_url('Tests/add_questions_options/' . encrypt_string($test_id)));
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
                return redirect()->to(base_url($redirect));
            }
        }
    }
    /*******************************************************/


    /**
     * Delete Test Info Submit 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_test_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'test_id' => ['label' => 'Test ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $TestsModel = new TestsModel();
            if ($TestsModel->archive_test_info($data)) {
                $session->setFlashdata('toastr_success', 'Test information deleted successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/




    /**
     * Update Test Info Submit 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_test_info_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'update_test_name' => ['label' => 'Test Name', 'rules' => 'required|string|min_length[1]|max_length[120]'],
            'update_test_ui' => ['label' => 'Test UI', 'rules' => 'required|string|min_length[1]|max_length[120]'],
            'update_test_package' => ['label' => 'Classrooms', 'rules' => 'required|string|min_length[1]|max_length[120]'],
            'update_no_questions' => ['label' => 'No of Questions', 'rules' => 'required|string|min_length[1]|max_length[120]'],
            'update_total_marks' => ['label' => 'Total Marks', 'rules' => 'required|string|min_length[1]|max_length[120]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            if ($data['update_end_date'] <= $data['update_start_date']) {
                $session->setFlashdata('toastr_error', 'End date should be greater than Start date.');
                return redirect()->to(base_url($redirect));
            }

            if (($data['update_duration_hours'] == '0') && ($data['update_duration_minutes'] == '0')) {
                $session->setFlashdata('toastr_error', 'Test duration should be greater than 0.');
                return redirect()->to(base_url($redirect));
            }
            $TestsModel = new TestsModel();
            $test_id = decrypt_cipher($this->request->getVar('update_test_id'));
            $this->send_exam_notification($test_id);
            if ($TestsModel->update_test_info($data)) {
                $session->setFlashdata('toastr_success', 'Updated Test information successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/




    /**
     * Clone Test Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function clone_test_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'original_test_id' => ['label' => 'Test ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $TestsModel = new TestsModel();
            if ($TestsModel->clone_test($data)) {
                $session->setFlashdata('toastr_success', 'Test cloned successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/



    /**
     * Add Bulk Solution Images
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_bulk_solutions_images()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'test_id' => ['label' => 'Test ID', 'rules' => 'required|string|min_length[1]']
        ]);
        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $TestsModel = new TestsModel();
            if ($TestsModel->add_bulk_solutions_images($data)) {
                $session->setFlashdata('toastr_success', 'Bulk solutions images added successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/




    /**
     * Unable Deleted Test
     *
     * @param [type] $test_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function unable_deleted_test($test_id, $redirect = '/tests/deleted_tests')
    {
        $data['test_id'] = decrypt_cipher($test_id);
        $TestsModel = new TestsModel();
        $session = session();
        if ($TestsModel->unable_deleted_test($data)) {
            $session->setFlashdata('toastr_success', 'Undo Deleted Test successfully.');
        } else {
            $session->setFlashdata('toastr_error', 'Error in processing.');
        }
        return redirect()->to(base_url($redirect));
    }
    /*******************************************************/



    /**
     * Add Solution Video Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_solution_video_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'test_id' => ['label' => 'Test ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $TestsModel = new TestsModel();
            $data['test_id'] = decrypt_cipher($this->request->getVar('test_id'));
            $data['institute_id'] = decrypt_cipher($this->request->getVar('institute_id'));

            if ($TestsModel->add_test_video_solutions($data)) {
                $session->setFlashdata('toastr_success', 'Test video solutions added successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/


    /**
     * Delete Solution Video Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_solution_video_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'video_id' => ['label' => 'Video ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $TestsModel = new TestsModel();
            if ($TestsModel->delete_test_video_solutions($data)) {
                $session->setFlashdata('toastr_success', 'Test video solutions deleted successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/


    /*******************************************************/
    /**
     * Add bulk answer key Submit
     *
     * @return Void
     * @since 2021/10/13
     * @author PrachiP
     */
    public function add_bulk_answer_key_submit()
    {
        $session = session();
        $result = $this->validate([
            'question_id' => ['label' => 'Question ID', 'rules' => 'required']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url('tests/add_answer_key/' . $this->request->getVar('test_id')))->withInput();
        } else {
            $data = $this->request->getVar();
            $TestsModel = new TestsModel();
            $TestsModel->add_bulk_answer_key($data);
            $session->setFlashdata('toastr_success', 'Answer key added!!');
            return redirect()->to(base_url('tests/add_answer_key/' . $this->request->getVar('test_id')));
        }
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * import excel answer key
     * @return Void
     * @since 2021/10/14
     * @author PrachiP
     */
    public function import_excel_answer_key()
    {
        $session = session();
        $result = $this->validate([
            'excel_file' => ['label' => 'Excel File', 'rules' => 'uploaded[excel_file]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url('tests/add_answer_key/' . $this->request->getVar('import_test_id')))->withInput();
        } else {
            $data = $this->request->getVar();
            $new_file_url = "";
            $file = $this->request->getFile('excel_file');
            if (!$file->isValid()) {
                throw new RuntimeException($file->getErrorString() . '(' . $file->getError() . ')');
            }
            $upload_folder_location =  WRITEPATH . 'uploads/tests/excel/';
            $newName = $file->getRandomName();
            if ($file->move($upload_folder_location, $newName)) :
                $new_file_url =  $upload_folder_location . $newName;
            endif;
            $data['file_url'] = $new_file_url;
            $TestsModel = new TestsModel();
            $TestsModel->import_excel_answer_key($data);
            $session->setFlashdata('toastr_success', 'Answer key added!!');
            return redirect()->to(base_url('tests/add_answer_key/' . $this->request->getVar('import_test_id')));
        }
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * ajax get answer key print
     * @return Void
     * @since 2021/10/14
     * @author PrachiP
     */
    public function ajax_get_answer_key_print()
    {
        $data['columns'] = $this->request->getVar('columns');
        $test_id = $this->request->getVar('test_id');
        $TestsModel = new TestsModel();
        $data['questions_list'] = $TestsModel->fetch_test_questions(decrypt_cipher($test_id));
        echo view('async/tests/answer_key_print', $data);
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * Ajax get answer key print
     * @return Void
     * @since 2021/10/14
     * @author PrachiP
     */
    public function append_test_chapters($subject_id)
    {
        $TestsModel = new TestsModel();
        $institute_id = decrypt_cipher(session()->get('instituteID'));
        $test_chapters = $TestsModel->fetch_test_chapters($subject_id, $institute_id);
        if (!empty($test_chapters)) :
            foreach ($test_chapters as $data) :
                $chap_id = $data['id'];
                $chap_name = $data['chapter_name'];
                echo "<option value='$chap_id'> $chap_name </option>";
            endforeach;
        endif;
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * show instructions modal
     * @return View
     * @since 2021/10/18
     * @author PrachiP
     */
    public function show_instructions_modal()
    {
        $data['title'] = "Instructions to prepare the PDF";
        echo view('modals/tests/show_instructions_modal', $data);
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * Fetch test absent students
     * @return Void
     * @since 2021/10/18
     * @author PrachiP
     */
    public function ajax_fetch_test_absent_students()
    {
        $test_id = $this->request->getVar('test_id');
        $TestsModel = new TestsModel();
        $data['test_absent_students'] = $TestsModel->fetch_test_absent_students(decrypt_cipher($test_id));
        echo view('async/tests/test_absent_students', $data);
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * Fetch test absent students
     * @return Void
     * @since 2021/10/19
     * @author PrachiP
     */
    public function import_bulk_questions_submit()
    {
        $data = $this->request->getVar();
        $TestsModel = new TestsModel();
        echo $TestsModel->import_bulk_questions_submit($data);
    }
    /*******************************************************/


    /**
     * Submit Ongoing Tests
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function submit_ongoing_tests()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'test_id' => ['label' => 'Test ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $result = new stdClass();
            $data = $this->request->getVar();
            $TestsModel = new TestsModel();
            if ($TestsModel->submit_ongoing_tests($data)) {
                $result->responseText = " STARTED tests submitted successfully.";
                $result->statusCode = 200;
                $session->setFlashdata('toastr_success', 'STARTED tests submitted successfully.');
            } else {
                $result->responseText = "Database Error.";
                $result->statusCode = -111;
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return json_encode($result);
        }
    }
    /*******************************************************/


    /*******************************************************/
    /**
     * Download answer key template
     * @return View
     * @since 2021-10-21
     * @author PrachiP
     */
    public function ajax_download_answer_key_template()
    {
        $data['question_numbers'] = $this->request->getVar('question_numbers');
        $data['correct_answers'] = $this->request->getVar('correct_answers');
        $data['question_type'] = $this->request->getVar('question_type');
        $data['correct_anwser_format'] = $this->request->getVar('correct_anwser_format');
        echo view('async/tests/answer_key_template', $data);
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * Add bulk solutions images
     * @return Void
     * @since 2021-10-21
     * @author PrachiP
     */
    public function add_bulk_solutions_images_submit()
    {
        $session = session();
        $result = $this->validate([
            'solution_images_files' => ['label' => 'Solution Images Files', 'rules' => 'uploaded[solution_images_files]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url('tests'))->withInput();
        } else {
            $data = $this->request->getVar();
            $upload_folder_location = WRITEPATH . 'uploads/questions/';
            $files = $this->request->getFileMultiple('solution_images_files');
            $data['filePreviewName'] = [];
            foreach ($files as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    if ($file->move($upload_folder_location, $newName)) {
                        $file_url =  $upload_folder_location . $newName;
                    }
                    array_push($data['filePreviewName'], $file_url);
                }
            }
            $TestsModel = new TestsModel();
            if ($TestsModel->add_bulk_solutions_images_submit($data)) {
                $session->setFlashdata('toastr_success', 'Solutions were added successfully.');
                return redirect()->to(base_url('tests'));
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
                return redirect()->to(base_url('tests'));
            }
        }
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * Add one by one solutions images
     * @return Void
     * @since 2021-10-21
     * @author PrachiP
     */
    public function add_onebyone_solutions_images_submit()
    {
        $session = session();
        $result = $this->validate([
            'solution_img_file' => ['label' => 'Solution Image File', 'rules' => 'uploaded[solution_img_file]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url('tests'))->withInput();
        } else {
            $data = $this->request->getVar();
            $upload_folder_location = WRITEPATH . 'uploads/questions/';
            $files = $this->request->getFileMultiple('solution_img_file');
            $data['filePreviewName'] = [];
            foreach ($files as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    if ($file->move($upload_folder_location, $newName)) {
                        $file_url =  $upload_folder_location . $newName;
                    }
                    array_push($data['filePreviewName'], $file_url);
                }
            }
            $TestsModel = new TestsModel();
            if ($TestsModel->add_onebyone_solutions_images_submit($data)) {
                $session->setFlashdata('toastr_success', 'Solutions were added successfully.');
                return redirect()->to(base_url('tests'));
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
                return redirect()->to(base_url('tests'));
            }
        }
    }
    /*******************************************************/



    /**
     * Student Test Status - Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_test_status_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'test_id' => ['label' => 'Test ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url('tests'))->withInput();
        } else {
            $data = $this->request->getVar();
            $TestsModel = new TestsModel();
            if ($TestsModel->update_student_test_status($data)) {
                $session->setFlashdata('toastr_success', 'Student test status updated successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/


    /**
     * Revaluate Test Result
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function revaluate_result($test_id)
    {
        $redirect = "/tests/show_test_result/1/" . $test_id;
        $TestsModel = new TestsModel();
        $data['instituteID'] = session()->get('instituteID');
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['test_status_data'] = $TestsModel->get_evalution_date(decrypt_cipher($test_id));
        $data['title'] = strtoupper("Generating Result For " . $data['test_details']['test_name']);
        $data['test_id'] = decrypt_cipher($test_id);
        $data['redirect'] = $redirect;
        echo view('pages/tests/reevaluate_test_result', $data);
    }
    /*******************************************************/


    public function get_tests_revaluated_students()
    {
        $post_data = $this->request->getVar();
        $TestsModel = new TestsModel();
        $result =  $TestsModel->get_tests_revaluated_students($post_data);
        echo $result;
    }

    // public function revaluate_result1($test_id)
    // {
    //     $session = session();
    //     $redirect = "/tests/show_test_result/1/" . encrypt_string($test_id);
    //     $TestsModel = new TestsModel();
    //     if ($TestsModel->revaluate_test_result($test_id)) {
    //         $session->setFlashdata('toastr_success', 'Revaluates test result successfully.');
    //     } else {
    //         $session->setFlashdata('toastr_error', 'Error in processing.');
    //     }
    //     return redirect()->to(base_url($redirect));
    // }

    /**
     * Import Offline Results Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function import_offline_results_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'test_id' => ['label' => 'Test ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url('tests'))->withInput();
        } else {
            $data = $this->request->getVar();
            $TestsModel = new TestsModel();
            if ($TestsModel->import_offline_results($data)) {
                $session->setFlashdata('toastr_success', 'Offline results imported successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/




    /**
     * Add Test Instruction Submit
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_instruction_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'test_id' => ['label' => 'Test ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url('tests'))->withInput();
        } else {
            $data = $this->request->getVar();
            $TestsModel = new TestsModel();
            if ($TestsModel->test_instruction_submit($data)) {
                $session->setFlashdata('toastr_success', 'Test instructions added successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/




    public function offline_test_result_submit()
    {

        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'test_id' => ['label' => 'Test ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url('tests'))->withInput();
        } else {

            $import_test_id = sanitize_input($this->request->getVar('test_id'));
            $import_institute_id = sanitize_input($this->request->getVar('institute_id'));


            $db = \Config\Database::connect();
            $query = $db->query("SELECT question_id,question_number 
            FROM test_questions_map 
            WHERE test_id = '$import_test_id' 
            AND question_disabled = 0 
            ORDER BY question_number asc");

            $result_get_question_ids = $query->getResultArray();


            $result_get_question_arr = array();
            foreach ($result_get_question_ids as $row_get_question_id) :
                array_push($result_get_question_arr, $row_get_question_id);
            endforeach;

            $helper = new Sample();
            $file = $this->request->getFile('excel_file');
            if (!$file->isValid()) {
                throw new RuntimeException($file->getErrorString() . '(' . $file->getError() . ')');
            }

            $upload_folder_location =  WRITEPATH . 'uploads/import_excels/';
            $newName = $file->getRandomName();
            //Getting the uploaded file
            if ($file->move($upload_folder_location, $newName)) {

                $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $helper = new Sample();
                if ($helper->isCli()) {
                    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

                    return;
                }
                // var_dump($_FILES["excel_file"]);
                $Reader->setReadDataOnly(true);
                $spreadSheet = $Reader->load($upload_folder_location . $newName);
                $excelSheet = $spreadSheet->getActiveSheet();
                $spreadSheetAry = $excelSheet->toArray();



                $rowsCount = count($spreadSheetAry);


                // Initializing question number and question ID array
                $questions_found_array = array();
                // Initializing student username and student ID array
                $students_found_array = array();

                // Initializing not found arrays
                $question_not_found_array = array();
                $students_not_found_array = array();

                $data_insert_error_array = array();

                $successful_inserts_student_counts = array();


                //Looping through the file
                for ($i = 1; $i <= $rowsCount; $i++) {
                    $student_username = "";
                    if (isset($spreadSheetAry[$i][0])) {
                        $student_username = sanitize_input($spreadSheetAry[$i][0]);
                    }

                    if (!empty($student_username)) {
                        // The student username has some value and not empty
                        $db_student_id = "";

                        // Get the student ID based on student USERNAME

                        $query_get_student_id = $db->query("SELECT student_id FROM student_login WHERE username = '$student_username' AND institute_id = '$import_institute_id'");

                        $result_get_student_id = $query_get_student_id->getRowArray();


                        if (empty($result_get_student_id)) {
                            // Student with the username not found
                            array_push($students_not_found_array, $student_username);
                            $students_found_array["$student_username"] = "NOTFOUND";
                        } else {
                            // Student found
                            // Get the stuent ID and insert in the results table
                            $row_get_student_id = $result_get_student_id;

                            // Inserting into the associative array
                            $students_found_array["$student_username"] = $row_get_student_id['student_id'];

                            $db_student_id = $row_get_student_id['student_id'];



                            $current_date = date('Y-m-d H:i:s');



                            $query_for_test_status =  $db->query("SELECT * FROM test_status WHERE test_id = '$import_test_id' AND student_id = '$db_student_id'");

                            $result_for_test_status = $query_for_test_status->getRowArray();
                            if (!empty($result_for_test_status)) {
                                // Inserting in the Test Status table
                                $db->query("INSERT INTO test_status (test_id, student_id, status, submission_type, admin_submission_date) VALUES ('$import_test_id', '$db_student_id', 'COMPLETED', 'admin', '$current_date')");
                            } else {
                                // Updating the Test Status table
                                $db->query("UPDATE test_status SET status = 'COMPLETED', submission_type = 'admin', admin_submission_date = '$current_date' WHERE  test_id = '$import_test_id' AND student_id = '$db_student_id' ");
                            }
                        }



                        if (!empty($db_student_id) && $db_student_id != "") {

                            if (!isset($successful_inserts_student_counts["$student_username"])) {
                                $successful_inserts_student_counts["$student_username"] = 0;
                            }


                            $j = 2;
                            if (!empty($result_get_question_arr)) {

                                foreach ($result_get_question_arr as $row_get_question_id) {

                                    $j++;

                                    // Inserting into the associative array
                                    $question_number  = $row_get_question_id['question_number'];
                                    $questions_found_array[$question_number] = $row_get_question_id['question_number'];

                                    $db_question_id = $row_get_question_id['question_id'];

                                    $selected_option = sanitize_input($spreadSheetAry[$i][$j]);

                                    // Process the selected answer column value
                                    $option_selected_formatted = "";

                                    // Checking if selected answer has a single character
                                    // This is for single type of questions
                                    if (strlen($selected_option) == 1) {
                                        switch ($selected_option) {
                                            case "A":
                                                $option_selected_formatted = "option1";
                                                break;
                                            case "B":
                                                $option_selected_formatted = "option2";
                                                break;
                                            case "C":
                                                $option_selected_formatted = "option3";
                                                break;
                                            case "D":
                                                $option_selected_formatted = "option4";
                                                break;
                                            default:
                                                $option_selected_formatted = $selected_option;
                                        }
                                    } else {
                                        // Else the answer may be multiple, number or match type
                                        $option_selected_formatted = $selected_option;
                                        $option_selected_formatted = str_replace(" ", "", $selected_option);

                                        if (strpos($selected_option, '-') === false) {
                                            // Selected Answer string DOES NOT have hyphen so it is NOT Match type
                                            $option_selected_formatted = str_replace("A", "option1", $selected_option);
                                            $option_selected_formatted = str_replace("B", "option2", $selected_option);
                                            $option_selected_formatted = str_replace("C", "option3", $selected_option);
                                            $option_selected_formatted = str_replace("D", "option4", $selected_option);
                                        }
                                    }


                                    // Check already test result entry available  for particular student


                                    $query_check_test_result =   $db->query("SELECT option_selected FROM test_result WHERE test_id = '$import_test_id' AND student_id = '$db_student_id' AND question_id = '$db_question_id' ");

                                    $result_check_test_result = $query_check_test_result->getRowArray();
                                    if (!empty($result_check_test_result)) {

                                        // Test Result Update
                                        if ($db->query("UPDATE test_result SET option_selected = '$option_selected_formatted' WHERE test_id = '$import_test_id' AND student_id = '$db_student_id' AND question_id = '$db_question_id'")) {

                                            $successful_inserts_student_counts["$student_username"] = $successful_inserts_student_counts["$student_username"] + 1;
                                        } else {
                                            array_push($data_insert_error_array, "Test Result Update failed for student=$student_username for question=$question_number");
                                        }
                                    } else {
                                        // Inserting in the database
                                        // Test Result Insert
                                        if (
                                            $db->query("INSERT INTO test_result (test_id, student_id, question_id, option_selected, flagged) VALUES ('$import_test_id', '$db_student_id', '$db_question_id', '$option_selected_formatted','0')")

                                        ) {
                                            $successful_inserts_student_counts["$student_username"] = $successful_inserts_student_counts["$student_username"] + 1;
                                        } else {
                                            array_push($data_insert_error_array, "Test Result Insert failed for student=$student_username for question=$question_number");
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $data['successful_inserts_student_counts'] = $successful_inserts_student_counts;
                $data['question_not_found_array'] = $question_not_found_array;
                $data['students_not_found_array'] = $students_not_found_array;
                $data['test_id'] = encrypt_string($import_test_id);
                $data['title'] = 'Import Test Results';

                $session->setFlashdata('toastr_success', 'Imported Offline Results Successfully.');

                echo view('pages/tests/import_offline_output', $data);
            }
        }
    }

    /**
     * Delete Question file
     *
     * @param [type] $img_to_delete
     * @param [type] $question_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_question_img_urls($img_to_delete, $question_id, $test_id)
    {
        $session = session();
        $redirect = 'tests/update_question/' . $question_id . '/' . $test_id;
        $TestsModel = new TestsModel();
        $data['question_detail'] = $TestsModel->question_detail($question_id);
        $file_to_delete = $data['question_detail'][$img_to_delete];
        if (file_exists($file_to_delete)) {
            if (unlink($file_to_delete)) {
                $db = \Config\Database::connect();
                $update_array = array(
                    $img_to_delete => ""
                );
                $db->table('test_questions')->update($update_array, ['id' => $question_id]);
                session()->setFlashdata('toastr_success', 'file deleted successfully.');
            } else {
                echo 'errors occured';
                $session->setFlashdata('toastr_error', 'Error while delecting file');
            }
        } else {
            $session->setFlashdata('toastr_error', 'file not found');
        }
        return redirect()->to(base_url($redirect));
    }


    /**
     * Average marks Reports
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function avg_marks_report()
    {
        // Log Activity 
        $this->activity->page_access_activity('Average Marks Report');
        $data['title'] = "Average Marks Report";
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $data['instituteID'] = session()->get('instituteID');
        $ClassroomModel = new ClassroomModel();
        $data['classroom_list'] = $ClassroomModel->fetch_all_classrooms($instituteID);
        echo view('pages/tests/avg_marks_report', $data);
    }
    /*******************************************************/

    /**
     * Load Classrom Tests
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function load_classroom_tests()
    {
        // POST data
        $postData = object_to_array($this->request->getVar());
        $postData['institute_id'] =  decrypt_cipher($postData['instituteID']);
        // Get data
        $TestsModel = new TestsModel();
        $data['exams_data'] = $TestsModel->load_classrooms_tests($postData);
        return view('async/tests/load_classroom_tests', $data);
    }
    /*******************************************************/

    /**
     * Delete Multiple Test Questions
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function bulk_delete_question_ids()
    {
        $session = session();
        $data = $this->request->getVar();
        $data['test_id'] = decrypt_cipher($data['test_id']);
        $redirect =  $data['redirect'];
        $TestsModel = new TestsModel();
        if ($TestsModel->delete_multiple_questions($data)) {
            // Reset question number sequence
            if (isset($data['reset_question_sequence']) && $data['reset_question_sequence'] == '1') {
                if (!$TestsModel->reset_test_question_numbers($data['test_id'])) {
                    $session->setFlashdata('toastr_error', 'Error in reseting question sequence');
                    return redirect()->to(base_url($redirect));
                }
            }
            session()->setFlashdata('toastr_success', 'Selected questions deleted successfully.');
        } else {
            $session->setFlashdata('toastr_error', 'Error in processing');
        }
        return redirect()->to(base_url($redirect));
    }
    /*******************************************************/



    /**
     * Reset Question Numbers
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function reset_question_numbers($test_id)
    {
        $session = session();
        $decrypt_test_id = decrypt_cipher($test_id);
        $TestsModel = new TestsModel();
        $redirect = '/tests/update_test_questions/' . $test_id;
        if ($TestsModel->reset_test_question_numbers($decrypt_test_id)) {
            session()->setFlashdata('toastr_success', 'Selected questions deleted successfully.');
        } else {
            $session->setFlashdata('toastr_error', 'Error in processing');
        }
        return redirect()->to(base_url($redirect));
    }
    /*******************************************************/


    /**
     * Load Tests - Institute wise - SuperAdmin Panel 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function load_institutes_wise_tests_count()
    {
        // POST data
        $postData = object_to_array($this->request->getVar());
        $postData['super_admin_role'] = session()->get('super_admin_role');
        $postData['login_id'] = decrypt_cipher(session()->get('login_id'));
        // Get data
        $TestsModel = new TestsModel();
        $institute_tests_data = $TestsModel->institutes_wise_tests_count($postData);
        echo json_encode($institute_tests_data);
    }
    /*******************************************************/


    /**
     * Reset Student Exam Session
     *
     * @param int $test_id
     * @param int $student_id
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function reset_student_exam_session($test_id, $student_id, $redirect = 'tests/realtime_overview/')
    {
        $data['title'] = "Reset student exam session";
        $data['test_id'] = $test_id;
        $data['student_id'] = $student_id;
        $data['redirect'] = $redirect . encrypt_string($test_id);
        $data['validation'] =  \Config\Services::validation();
        $TestsModel = new TestsModel();
        $data['realtime_student_details'] = $TestsModel->realtime_student_overview($data['test_id'], $data['student_id']);
        echo view('modals/tests/reset_student_exam_session', $data);
    }
    /*******************************************************/

    /**
     * Reset Student Exam Session - Submit Method
     */
    public function reset_student_exam_session_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'test_id' => ['label' => 'Test ID', 'rules' => 'required|string|min_length[1]'],
            'student_id' => ['label' => 'Student ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url('tests'))->withInput();
        } else {
            $data = $this->request->getVar();
            $TestsModel = new TestsModel();
            if ($TestsModel->reset_student_exam_session($data)) {
                $session->setFlashdata('toastr_success', 'Student exam session reset successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/

    /**
     * Student Option Selected
     */
    public function student_test_options($test_id, $username)
    {

        // Log Activity 
        $this->activity->page_access_activity('Tests', '/tests/student_test_options');
        // Check Authorized User
        if (!isAuthorized("manage_tests")) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Students Test Options";
        $data['test_id'] = $test_id;
        $data['username'] = $username;
        $StudentModel = new StudentModel();
        $data['student_details'] = $StudentModel->student_username_details($username);
        $TestsModel = new TestsModel();
        $data['student_test_options'] = $TestsModel->student_test_options_selected($test_id, $username);
        $data['student_test_status'] = $TestsModel->realtime_student_overview($test_id, $data['student_details']['id']);
        $data['test_details'] = $TestsModel->get_test_details($test_id);
        echo view('pages/tests/student_test_options', $data);
    }
    /*******************************************************/


    /**
     * Update Student Option Selected -OMR Test Only
     */
    public function update_student_test_option()
    {
        // Check Authorized User
        if (!isAuthorized("manage_tests")) {
            echo 'UnAuthorized access';
            exit;
        }

        // POST data
        $postData = $this->request->getVar();
        // Get data
        $TestsModel = new TestsModel();
        $result = $TestsModel->update_student_test_option($postData);
        echo $result;
    }
    /*******************************************************/


    /**
     * Video Proctoring Section
     */
    public function video_proctoring_section(string $encrypted_test_id)
    {
        // Log Activity 
        $this->activity->page_access_activity('Video Proctoring', '/tests/video_proctoring_section');
        // Check Authorized User
        if (!isAuthorized("manage_tests")) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Video Proctoring Section";
        $data['encrypted_test_id'] = $encrypted_test_id;
        $data['test_id'] = decrypt_cipher($encrypted_test_id);
        $data['institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $TestsModel = new TestsModel();
        $data['video_proctoring_data'] = $TestsModel->video_proctoring_data($data['test_id'], $data['institute_id']);
        $data['test_details'] = $TestsModel->get_test_details($data['test_id']);
        echo view('pages/tests/video_proctoring_section', $data);
    }
    /*******************************************************/

    /**
     * Selected Tets Names
     */
    public function get_test_names()
    {
        // Check Authorized User
        if (!isAuthorized("manage_tests")) {
            echo 'UnAuthorized access';
            exit;
        }
        // POST data
        $test_ids = $this->request->getVar('test_ids');
        // Get data
        $TestsModel = new TestsModel();
        $result = $TestsModel->get_test_names($test_ids);
        echo $result;
    }
    /*******************************************************/

    /**
     * Video Proctoring Session
     */
    public function video_proctoring_session(string $meeting_id, string $meeting_password, string $test_id)
    {
        // Log Activity 
        $this->activity->page_access_activity('Video Proctoring Session', '/tests/video_proctoring_session');
        // Check Authorized User
        if (!isAuthorized("manage_tests")) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Video Proctoring Session";
        $data['meeting_id'] = decrypt_cipher($meeting_id);
        $data['meeting_password'] = decrypt_cipher($meeting_password);
        $data['test_id'] = decrypt_cipher($test_id);
        $data['institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details($data['test_id']);
        echo view('pages/tests/video_proctoring_session', $data);
    }
    /*******************************************************/


    /**
     * Video Proctoring Recording
     */
    public function video_proctoring_recording(string $video_session_id)
    {
        // Log Activity 
        $this->activity->page_access_activity('Video Proctoring Recording', '/tests/video_proctoring_recording');
        // Check Authorized User
        if (!isAuthorized("manage_tests")) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Video Proctoring Recording";
        $data['video_session_id'] = decrypt_cipher($video_session_id);
        $data['institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $TestsModel = new TestsModel();
        $data['video_session_data'] = $TestsModel->video_session_data($data['video_session_id']);
        echo view('pages/tests/video_proctoring_recording', $data);
    }
    /*******************************************************/



    public function import_offline_results_async()
    {
        $upload_folder_location =  WRITEPATH . 'uploads/import_excels/';
        $file = $this->request->getFile('file');
        if (!$file->isValid()) {
            throw new RuntimeException($file->getErrorString() . '(' . $file->getError() . ')');
        }
        $newName = $file->getRandomName();
        if ($file->move($upload_folder_location, $newName)) {
            $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $Reader->setReadDataOnly(true);
            $spreadSheet = $Reader->load($upload_folder_location . $newName);
            $excelSheet = $spreadSheet->getActiveSheet();
            $spreadSheetAry = $excelSheet->toArray();
            print_r(json_encode($spreadSheetAry));
        }
    }

    public function process_excel_data()
    {
        $post_data['row_data'] = $this->request->getVar('row_data');
        $post_data['test_id'] = $this->request->getVar('test_id');
        $post_data['omr_check'] = $this->request->getVar('omr_check');
        $post_data['institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $TestsModel = new TestsModel();
        $result = $TestsModel->add_offline_result($post_data);
        print_r(json_encode($result));
    }

    // Get OMR Templates Based on Institute ID
    public function get_omr_templates()
    {
        $institute_id = decrypt_cipher(session()->get('instituteID'));
        $OmrTemplatesModel = new OmrTemplatesModel();
        $result = $OmrTemplatesModel->get_omr_templates($institute_id);
        print_r(json_encode($result));
    }


    /**
     * Upload Offlne Exam - Pdf Paper
     *
     * @param string $test_id
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function upload_exam_pdf_paper(string $test_id)
    {
        if (session()->get('exam_feature') == 0) {
            $session = session();
            $session->setFlashdata('toastr_error', 'UnAuthorized access.');
            return redirect()->to(base_url('/home'));
        }

        // Log Activity 
        $this->activity->page_access_activity('Test Upload Pdf Exam Paper', '/tests/upload_exam_pdf_paper');

        $data['title'] = "Upload PDF Paper";
        $data['instituteID'] = session()->get('instituteID');
        $data['decrypted_institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $data['test_id'] = $test_id;
        $data['staff_id'] = decrypt_cipher(session()->get('login_id'));
        $data['decrypt_test_id'] =  decrypt_cipher($test_id);

        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['test_template_data'] = array();
        if(!empty($data['test_details']['template_id'])){
            $TestTemplateConfigModel = new TestTemplateConfigModel();
            $data['test_template_data'] = $TestTemplateConfigModel->fetch_template_section_rules($data['test_details']['template_id']);  
        }
        $SubjectsModel = new SubjectsModel();
        $data['subject_details'] = $SubjectsModel->get_subjects($data['decrypted_institute_id']);
        echo view('pages/tests/upload_exam_pdf_paper', $data);
    }

    /**
     * Update Offlne Exam - Pdf Paper
     *
     * @param string $test_id
     * @return void
     * @author Ajinkya K
     */
    public function update_exam_pdf_paper(string $test_id)
    {
        if (session()->get('exam_feature') == 0) {
            $session = session();
            $session->setFlashdata('toastr_error', 'UnAuthorized access.');
            return redirect()->to(base_url('/home'));
        }

        // Log Activity 
        $this->activity->page_access_activity('Test Update Uploaded Pdf Exam Paper', '/tests/upload_exam_pdf_paper');

        $data['title'] = "Update PDF Paper";
        $data['instituteID'] = session()->get('instituteID');
        $data['decrypted_institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $data['test_id'] = $test_id;
        $data['staff_id'] = decrypt_cipher(session()->get('login_id'));
        $data['decrypt_test_id'] =  decrypt_cipher($test_id);

        $TestsModel = new TestsModel();
        $data['test_details'] = $TestsModel->get_test_details(decrypt_cipher($test_id));
        $data['test_template_data'] = array();

        //Fetch already added sections
        $data['uploaded_questions'] = $TestsModel->section_wise_question_count(decrypt_cipher($test_id));
        if(!isset($data['uploaded_questions']) || empty($data['uploaded_questions'])) {
            if(!empty($data['test_details']['template_id'])){
                $TestTemplateConfigModel = new TestTemplateConfigModel();
                $data['test_template_data'] = $TestTemplateConfigModel->fetch_template_section_rules($data['test_details']['template_id']);  
            }
        }
        $SubjectsModel = new SubjectsModel();
        $data['subject_details'] = $SubjectsModel->get_subjects($data['decrypted_institute_id']);
        echo view('pages/tests/upload_exam_pdf_paper', $data);
    }
}
