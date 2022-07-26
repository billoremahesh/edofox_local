<?php

namespace App\Models;

use App\Models\TestQuestionsMapModel;


use CodeIgniter\Model;


use PhpOffice\PhpSpreadsheet\Helper\Sample;

class TestsModel extends Model
{


    /**
     * Check Mapped Classrooms to staff in case of not given global permissions
     *
     * @return string
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function apply_classroom_filter()
    {

        $classroom_mapped_ids = session()->get('classroom_mapped_arr');
        $append_string = "";
        if (!empty($classroom_mapped_ids)) {
            $append_string = "  JOIN test_package_map 
                            ON test.test_id = test_package_map.test_id
                            JOIN packages 
                            ON packages.id = test_package_map.package_id 
                            AND packages.id IN ($classroom_mapped_ids) ";
        }
        return $append_string;
    }
    /*******************************************************/


    /*****************************************************************
     *############### START OF FUNCTIONS - RETURNS COUNT  ############*
     *****************************************************************/

    /**
     * Total Test Count 
     *
     * @return Integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function test_count($institute_id = "")
    {
        $db = \Config\Database::connect();
        $append_string = $this->apply_classroom_filter();
        $institute_check_condn = "";
        if ($institute_id != "") {
            $institute_check_condn = " AND test.institute_id = :institute_id: ";
        }

        $sql_fetch_total_exam = "SELECT count(*) as test_cnt
        FROM test 
        $append_string
        WHERE test.status != 'ARCHIVED'
        $institute_check_condn ";

        $query = $db->query($sql_fetch_total_exam, [
            'institute_id' => sanitize_input($institute_id)
        ]);
        $result = $query->getRowArray();

        if (!empty($result)) {
            return $result['test_cnt'];
        }
        return 0;
    }
    /*******************************************************/





    /**
     * Todays Test Count 
     *
     * @return Integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function todays_test_count($institute_id = "")
    {
        $db = \Config\Database::connect();
        $append_string = $this->apply_classroom_filter();
        $institute_check_condn = "";
        if ($institute_id != "") {
            $institute_check_condn = " AND test.institute_id = :institute_id: ";
        }
        $sql_fetch_todays_exam = "SELECT COUNT(*) as todays_test_cnt 
        FROM test
        $append_string
        WHERE DATE(test.start_date) = CURDATE() 
        $institute_check_condn ";

        $query = $db->query($sql_fetch_todays_exam, [
            'institute_id' => sanitize_input($institute_id)
        ]);
        $result = $query->getRowArray();
        if (!empty($result)) {
            return $result['todays_test_cnt'];
        }
        return 0;
    }
    /*******************************************************/



    /**
     * Institutes Wise Todays Test Count
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function institutes_wise_todays_test()
    {
        $db = \Config\Database::connect();

        $sql_fetch_todays_exam = "SELECT institute.id as instituteid, institute.institute_name,COUNT(test.test_id) as todays_test_cnt 
        FROM test
        join institute on institute.id = test.institute_id
        WHERE DATE(test.start_date) = CURDATE() 
        group by test.institute_id ";

        $query = $db->query($sql_fetch_todays_exam);
        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/




    /**
     * Institutes Wise Test Count
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function institutes_wise_tests_count(array $requestData)
    {
        // Server Side Processing - ORDER BY and ASC,DESC
        $order = array();
        if (isset($requestData['order'])) {
            $order = $requestData['order'];
        }


        // Check Role
        $role_condition = "";
        if (isset($requestData['super_admin_role']) && isset($requestData['login_id'])) {
            $role = sanitize_input($requestData['super_admin_role']);
            $login_id = sanitize_input($requestData['login_id']);
            if ($role != 'Super Admin') {
                $role_condition = " AND institute.account_manager = '$login_id' ";
            }
        }

        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }

        $columns_valid = array();

        if (!isset($columns_valid[$col])) {
            $orderby = "institute.institute_name";
        } else {
            $orderby = $columns_valid[$col];
        }


        $filter_condn = "";
        if (isset($requestData['test_filter']) && !empty($requestData['test_filter'])) {
            $test_filter = sanitize_input($requestData['test_filter']);

            if ($test_filter == "today") {
                $filter_condn = " AND DATE(test.start_date) = CURDATE() ";
            }


            if ($test_filter == "tommrow") {
                $filter_condn = " AND (DATE(test.start_date) = (CURDATE() + 1) )";
            }


            if ($test_filter == "yesterday") {
                $filter_condn = " AND (DATE(test.start_date) = (CURDATE() - 1) )";
            }

            if ($test_filter == "planned") {
                $filter_condn = " AND test.start_date > (CURDATE() + 7) ";
            }
        }

        $institute_check_condn = "";
        if (isset($requestData['institute_id']) && !empty($requestData['institute_id'])) {
            $instituteID = sanitize_input($requestData['institute_id']);
            $institute_check_condn = " AND institute_id = '$instituteID' ";
        }



        $searchQuery = "";

        if (!empty($requestData['search']['value'])) {
            $searched_term = sanitize_input($requestData['search']['value']);
            $searchQuery .= " AND ( 
       institute.institute_name LIKE '%" . $searched_term . "%'
       )";
        }


        $sql = "SELECT institute.id as instituteid, institute.institute_name,COUNT(DISTINCT test.test_id) as test_cnt 
        FROM test
        JOIN institute 
        ON institute.id = test.institute_id
        WHERE 1=1 $filter_condn $role_condition 
        GROUP BY test.institute_id ";

        $totalquery = $this->db->query($sql);
        $resultTotalData = $totalquery->getResultArray();
        $totalData = count($resultTotalData);
        $sql .= " $searchQuery  $institute_check_condn ";
        $total_filtered_query = $this->db->query($sql);
        $resultTotalDataFiltered = $total_filtered_query->getResultArray();
        $totalDataFiltered = count($resultTotalDataFiltered);

        // Result with filtered data with limit
        $sql .= " ORDER BY $orderby $dir LIMIT " . $requestData['start'] . " ," . $requestData['length'];

        $total_filtered_limit_query = $this->db->query($sql);
        $filter_result = $total_filtered_limit_query->getResultArray();

        $data = array();

        $i =  $requestData['start'] + 1;
        foreach ($filter_result as $row) {

            $nestedData = array();

            $instituteid = encrypt_string($row['instituteid']);

            $nestedData[] = $i;
            $nestedData[] = $row["institute_name"];
            $nestedData[] = $row["test_cnt"];
            //  External link
            $external_link_btn = htmlspecialchars("<a class='material_icon_custom_div' data-bs-toggle='tooltip' title='Go to New Institute Pannel' onclick=" . "show_edit_modal('modal_div','external_link','/institutes/new_admin_indirect_login/" . $instituteid . "');" . "><i class='material_button_edit_icon material-icons'>call_missed_outgoing</i></a>");

            $nestedData[] = htmlspecialchars_decode("$external_link_btn");
            $data[] = $nestedData;
            $i++;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalDataFiltered),
            "data"            => $data
        );

        return $json_data;
    }


    /**
     * Test active students count in last 5 minutes
     *
     * @param Integer $institute_id
     * @param Integer $test_id
     *
     * @return Integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function tests_active_students(int $institute_id, int $test_id = 0)
    {
        $db = \Config\Database::connect();
        $append_string = $this->apply_classroom_filter();

        if ($test_id == 0) {
            $sql_all_active_students_count = "SELECT COUNT(test_status.id) AS active_students_count 
            FROM test_status 
            INNER JOIN test
            ON test_status.test_id = test.test_id
            $append_string
            WHERE test.institute_id = :institute_id:  
            AND test_status.last_answered > (DATE_SUB(NOW(),INTERVAL 5 MINUTE)) 
            GROUP BY test_status.student_id ";
        } else {
            $sql_all_active_students_count = "SELECT COUNT(test_status.id) AS active_students_count 
            FROM test_status 
            INNER JOIN test
            ON test_status.test_id = test.test_id 
            AND test.test_id = :test_id:
            $append_string
            WHERE test.institute_id = :institute_id: 
            AND test_status.last_answered > (DATE_SUB(NOW(),INTERVAL 5 MINUTE)) 
            GROUP BY test_status.student_id";
        }

        $query = $db->query($sql_all_active_students_count, [
            'institute_id' => sanitize_input($institute_id),
            'test_id' => sanitize_input($test_id)
        ]);

        $result = $query->getRowArray();

        if (!empty($result)) {
            return $result['active_students_count'];
        } else {
            return 0;
        }
    }
    /*******************************************************/


    /**
     * Tests unsubmitted students sounts
     *
     * @param integer $institute_id
     *
     * @return Integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function unsubmitted_tests_students(int $institute_id)
    {
        $db = \Config\Database::connect();
        $append_string = $this->apply_classroom_filter();

        $sql_all_active_students_count = "SELECT COUNT(test_status.id) AS unsubmitted_students_count 
        FROM test_status 
        INNER JOIN test
        ON test_status.test_id = test.test_id
        $append_string
        WHERE test.institute_id = :institute_id:  
        AND test_status.status = 'STARTED'
        AND test.end_date < :current_date_time:  ";

        $query = $db->query($sql_all_active_students_count, [
            'institute_id' => sanitize_input($institute_id),
            'current_date_time' => date('Y-m-d H:i:s')
        ]);

        $result = $query->getRowArray();

        if (!empty($result)) {
            return $result['unsubmitted_students_count'];
        } else {
            return 0;
        }
    }
    /*******************************************************/

    /**
     * Section wise question count (required for edit upload PDF feature)
     *
     * @param integer $institute_id
     *
     * @return Integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function section_wise_question_count(int $test_id)
    {
        $db = \Config\Database::connect();
        
        $sql_all_active_students_count = "SELECT COUNT(test_questions_map.id) AS section_count, 
        test_questions_map.question_number, test_questions_map.section 
        FROM test_questions_map WHERE test_questions_map.test_id = :test_id:  
        AND test_questions_map.question_disabled = 0
        group by test_questions_map.section
        order by test_questions_map.question_number ASC ";

        $query = $db->query($sql_all_active_students_count, [
            'test_id' => sanitize_input($test_id)
        ]);

        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/





    /**
     * Video Proctoring Data
     *
     * @param integer $test_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function video_proctoring_data(int $test_id, int $institute_id)
    {
        $db = \Config\Database::connect();

        $sql = "SELECT live_session.id,session_name,package_name,schedule_id,meeting_password,pkgCount.cnt,live_session.start_date,live_session.end_date 
        FROM live_session 
        join packages 
        on live_session.classroom_id = packages.id
        left join (select count(*) as cnt,package_id 
        from student_institute 
        where institute_id = :institute_id: and is_disabled = 0 group by package_id)
        as pkgCount 
        on pkgCount.package_id = packages.id
        where test_id = :test_id: ";

        $query = $db->query($sql, [
            'test_id' => sanitize_input($test_id),
            'institute_id' =>  sanitize_input($institute_id)
        ]);

        $result = $query->getResultArray();

        return $result;
    }
    /*******************************************************/

    /**
     * Get Test Names
     *
     * @param string $test_ids
     *
     * @return string
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_test_names(string $test_ids)
    {
        $db = \Config\Database::connect();

        $test_ids = sanitize_input($test_ids);

        $sql = "select GROUP_CONCAT(test_name) as test_names
        from test
        where test_id IN ($test_ids)";

        $query = $db->query($sql);

        $result = $query->getRowArray();
        return $result['test_names'];
    }
    /*******************************************************/

    /**
     * Deleted Tests Count
     *
     * @param integer $institute_id
     *
     * @return integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function deleted_tests_count(int $institute_id)
    {
        $db = \Config\Database::connect();
        $append_string = $this->apply_classroom_filter();

        $sql = "SELECT count(test.test_id) as deleted_tests_count
        FROM test
        $append_string
        WHERE test.institute_id = :institute_id:
        AND test.status = 'ARCHIVED' ";

        $query = $db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        $result = $query->getRowArray();

        if (!empty($result)) {
            return $result['deleted_tests_count'];
        } else {
            return 0;
        }
    }
    /*******************************************************/


    /*****************************************************************
     *############### END OF FUNCTIONS - RETURNS COUNT  ############*
     *****************************************************************/






    /*****************************************************************
     *################# START OF FUNCTIONS - TEST ID  ###############*
     *****************************************************************/

    /**
     * Test Offset
     *
     * @param Integer $test_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function test_offset(int $test_id)
    {
        $db = \Config\Database::connect();
        $sql_test_offset = "SELECT count(question_number) as cnt,MAX(question_number) as qno
        FROM test_questions_map 
        WHERE test_id = :test_id: ";
        $query = $db->query($sql_test_offset, [
            'test_id' => sanitize_input($test_id)
        ]);
        $result = $query->getRowArray();
        return $result;
    }
    /********************************************************/



    /**
     * Get Test Solution PDF Data
     *
     * @param Integer $test_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_solution_pdf_data(int $test_id)
    {
        $db = \Config\Database::connect();
        $sql_test_offset = "SELECT solutions_pdf_url 
        FROM test
        WHERE test_id = :test_id: ";
        $query = $db->query($sql_test_offset, [
            'test_id' => sanitize_input($test_id)
        ]);
        $result = $query->getRowArray();
        return $result;
    }
    /********************************************************/



    /**
     * Get Test Solution Video Data
     *
     * @param Integer $test_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_solution_video_data(int $test_id, int $institute_id)
    {
        $db = \Config\Database::connect();
        $sql_test_offset = "SELECT id, video_name, video_url 
        FROM video_lectures
        WHERE test_id = :test_id:
        AND institute_id= :institute_id:
        AND is_disabled = 0
        AND type = 'SOLUTION' ";
        $query = $db->query($sql_test_offset, [
            'test_id' => sanitize_input($test_id),
            'institute_id' => sanitize_input($institute_id),
        ]);
        $result = $query->getResultArray();
        return $result;
    }
    /********************************************************/


    /**
     * Fetch Student List mapped to Test
     *
     * @param Integer $test_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_test_students(int $test_id)
    {
        $db = \Config\Database::connect();

        $sql_query = "SELECT name,roll_no,student.id 
        FROM test_status 
        JOIN student 
        ON test_status.student_id = student.id 
        WHERE test_id = :test_id: 
        ORDER BY student.name";
        $query = $db->query($sql_query, [
            'test_id' => sanitize_input($test_id)
        ]);
        $result = $query->getResultArray();
        return $result;
    }
    /********************************************************/


    /**
     * Fetch Evaluation Date 
     * @param int $test_id
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_evalution_date(int $test_id)
    {
        $db = \Config\Database::connect();
        $sql_query = "SELECT evaluation_date,count(*) student_cnt
        FROM test_status 
        WHERE test_id = :test_id:";
        $query = $db->query($sql_query, [
            'test_id' => sanitize_input($test_id)
        ]);
        $result = $query->getRowArray();
        if (!empty($result)) {
            if (empty($result['evaluation_date'])) {
                $result['evaluation_date'] = date('Y-m-d H:i:s');
            }
        }
        return $result;
    }
    /********************************************************/


    /**
     * Get Tests Revaluated Students
     * @param int $test_id
     * @return int
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_tests_revaluated_students($requestData)
    {

        $evaluation_date = $requestData['evaluation_date'];
        $test_id = $requestData['test_id'];
        $db = \Config\Database::connect();
        $sql_query = "SELECT COUNT(id) student_cnt
        FROM test_status 
        WHERE test_id = :test_id: AND evaluation_date > :evaluation_date: ";
        $query = $db->query($sql_query, [
            'test_id' => sanitize_input($test_id),
            'evaluation_date' => sanitize_input($evaluation_date)
        ]);
        $result = $query->getRowArray();
        if (!empty($result)) {
            $student_count = $result['student_cnt'];
        } else {
            $student_count = 0;
        }
        return $student_count;
    }
    /********************************************************/



    public function fetch_course_tests(int $course_id)
    {
        $db = \Config\Database::connect();
        $sql_test_offset = "SELECT test.test_id, test.test_name, packages.package_name FROM test 
        INNER JOIN packages ON test.package_id = packages.id 
        WHERE package_id= :course_id: 
        ORDER BY test.created_date DESC";
        $query = $db->query($sql_test_offset, [
            'course_id' => sanitize_input($course_id)
        ]);
        $result = $query->getResultArray();
        return $result;
    }

    /**
     * Test Average Time Left
     *
     * @param Integer $test_id
     *
     * @return String
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function test_avg_time_left(int $test_id)
    {
        $db = \Config\Database::connect();

        $sql_query = "SELECT avg(time_left) avg_time_left
        FROM test_status 
        WHERE test_id = :test_id:";
        $query = $db->query($sql_query, [
            'test_id' => sanitize_input($test_id)
        ]);
        $result = $query->getRowArray();
        if (!empty($result)) {
            $round_avg_time_left = $result['avg_time_left'] / 60;
            $final_avg_time_left =  number_format((float)$round_avg_time_left, 2, '.', '');
            return $final_avg_time_left;
        } else {
            return 0;
        }
    }
    /********************************************************/


    /**
     * Get Test Device Information
     *
     * @param Integer $test_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function device_information(int $test_id)
    {
        $db = \Config\Database::connect();
        $sql_count_devices = "SELECT count(*) as device_count, device 
        FROM test_status
        WHERE test_id = :test_id: 
        GROUP BY device";

        $query = $db->query($sql_count_devices, [
            'test_id' => sanitize_input($test_id)
        ]);
        $result = $query->getResultArray();
        return $result;
    }
    /********************************************************/

    /**
     * Get Test Details
     *
     * @param Integer $test_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_test_details(int $test_id)
    {
        $sql = "SELECT test.*,test_templates.template_name
        FROM test 
        LEFT JOIN test_templates
        ON test_templates.id = test.template_id
        WHERE test.test_id = :test_id: ";

        $query = $this->db->query($sql, [
            'test_id' => sanitize_input($test_id)
        ]);

        return $query->getRowArray();
    }
    /*******************************************************/


    /**
     * Video Proctoring Session Data
     *
     * @param integer $video_session_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function video_session_data(int $video_session_id)
    {
        $sql = "SELECT live_session.*
        FROM live_session 
        WHERE id = :video_session_id: ";

        $query = $this->db->query($sql, [
            'video_session_id' => sanitize_input($video_session_id)
        ]);

        return $query->getRowArray();
    }
    /*******************************************************/


    /**
     * Test Student Counts
     *
     * @param Integer $test_id
     * @param String $status
     *
     * @return Integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function test_students_cnt(int $test_id, string $status)
    {
        $db = \Config\Database::connect();
        $sql_test_query = "SELECT count(status) AS started_count 
        FROM test_status
        WHERE test_status.test_id = :test_id: 
        AND test_status.status = :status: ";

        $query = $db->query($sql_test_query, [
            'test_id' => sanitize_input($test_id),
            'status' => sanitize_input($status)
        ]);
        $result = $query->getRowArray();
        if (!empty($result)) {
            return $result['started_count'];
        } else {
            return 0;
        }
    }
    /*******************************************************/




    /**
     * Average Visited Count
     *
     * @param Integer $test_id
     * @param String $type
     *
     * @return Integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function avg_visited_solved_count(int $test_id, string $type)
    {

        $db = \Config\Database::connect();
        $type = sanitize_input($type);
        if ($type == "visited") {
            $sql_test_query = "select round(avg(cnt),2) as avg_cnt from ( SELECT count(id) as cnt,student_id FROM test_result where test_id = :test_id: GROUP by student_id) avg_cnt_table";
        } else {
            $sql_test_query = "select round(avg(cnt),2) as avg_cnt from ( SELECT count(id) as cnt,student_id FROM test_result where test_id = :test_id: AND trim(length(option_selected)) > 0 GROUP by student_id) avg_cnt_table";
        }
        $query = $db->query($sql_test_query, [
            'test_id' => sanitize_input($test_id)
        ]);
        $result = $query->getRowArray();
        if (!empty($result)) {
            return $result['avg_cnt'];
        } else {
            return 0;
        }
    }
    /*******************************************************/




    /**
     * Fetch Test Questions
     *
     * @param Integer $test_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_test_questions(int $test_id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT test_questions.id, test_questions.option1, test_questions.option2, test_questions.option3, test_questions.option4, test_questions.correct_answer, test_questions.alt_answer, test_questions.question_type, test_questions_map.question_number, test_subjects.subject 
        FROM test_questions_map
        JOIN test_questions
        ON test_questions.id = test_questions_map.question_id
        JOIN test_subjects
        ON test_questions.subject_id = test_subjects.subject_id
        WHERE test_questions_map.test_id = '$test_id' 
        AND test_questions_map.question_disabled != 1
        ORDER BY test_questions_map.question_number");
        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/




    /**
     * Get number of questions
     *
     * @param Integer $test_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_number_of_questions(int $test_id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * 
        FROM test 
        LEFT JOIN (SELECT COUNT(*) as questionsAddedCount, test_id FROM test_questions_map WHERE question_disabled=0 GROUP BY test_id) as questionsMap 
        ON questionsMap.test_id = test.test_id
        WHERE test.test_id='$test_id'");
        return $query->getRowArray();
    }
    /*******************************************************/




    /**
     * Get Test Mapped Classrooms
     *
     * @param Integer $test_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_test_mapped_classrooms(int $test_id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT group_concat(package_id) as packages
        FROM test_package_map 
        WHERE test_id = '$test_id' 
        AND is_disabled='0'");
        return $query->getRowArray();
    }
    /*******************************************************/




    /*****************************************************************
     *################## END OF FUNCTIONS - TEST ID  #############*
     *****************************************************************/



    /*****************************************************************
     *################ START OF FUNCTIONS - Filtered Data  ###########*
     *****************************************************************/

    /**
     * Fetch Filtered Tests 
     *
     * @param Array $data
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_filtered_tests(array $data)
    {
        $db = \Config\Database::connect();
        $instituteID = sanitize_input(decrypt_cipher($data['instituteID']));
        $testType = sanitize_input($data['requestType']);
        $durationType = sanitize_input($data['durationType']);
        $start = sanitize_input($data['start']);
        $end = sanitize_input($data['end']);
        $classroom = sanitize_input($data['classroom']);

        $dateQuery = "";
        $duration = "";
        $endQuery = "";
        $classroomfilterQuery = "";

        // To do stuff conditionally depending on the current date and time

        $current_date_time = date('Y-m-d H:i:s');
        $packageMapQuery = "";

        if ($testType === 'active') {
            $dateQuery = " AND date(test.start_date) <= '$current_date_time' AND test.end_date >= '$current_date_time'";
        } else if ($testType === 'upcoming') {
            $dateQuery = " AND date(test.start_date) > '$current_date_time'";
        } else {
            $dateQuery = " AND test.end_date < '$current_date_time'";
        }

        if ($durationType === 'exams') {
            $duration = " AND test_ui != 'DPP' ";
        } else if ($durationType === 'assignments') {
            $duration = " AND test_ui = 'DPP' ";
        } else {
            $duration = '';
        }

        if ($start !== "" && $end !== '') {
            $endQuery = " AND end_date BETWEEN '$start' AND '$end'";
        }

        // Filter for classroom
        if ($classroom != 'all') {
            $classroom = str_replace("all,", "", $classroom);
            $classroomfilterQuery = " AND test_package_map.package_id IN ($classroom) ";
        }



        // Check Mapped Classrooms to staff in case of not given global permissions
        $classroom_mapped_ids = session()->get('classroom_mapped_arr');
        $append_string = "";
        if (!empty($classroom_mapped_ids)) {
            $append_string = " AND packages.id IN ($classroom_mapped_ids) ";
        }

        $sql_for_test = $db->query("SELECT test.*, packages.*, questionsCount.questionsAdded , group_concat(packages.package_name) as package_list
        FROM test 
        LEFT JOIN test_package_map 
        ON test.test_id = test_package_map.test_id
        LEFT JOIN packages 
        ON packages.id = test_package_map.package_id
        LEFT JOIN (select count(*) as questionsAdded,test_id FROM test_questions_map WHERE question_disabled != 1 AND test_id in (select test_id from test where test.institute_id = '$instituteID' $dateQuery)  group by test_id) as questionsCount on questionsCount.test_id = test.test_id
        where test.institute_id = '$instituteID' AND test.status != 'ARCHIVED' AND test_package_map.is_disabled = 0 $dateQuery $duration $endQuery $classroomfilterQuery $packageMapQuery
        $append_string
        GROUP BY test_package_map.test_id
        ORDER BY test.created_date DESC");
        $result = $sql_for_test->getResultArray();
        return $result;
    }
    /********************************************************/




    /**
     * Fetch All Deleted Tests
     *
     * @param Integer $institute_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_all_deleted_tests(int $institute_id)
    {
        $db = \Config\Database::connect();
        $append_string = $this->apply_classroom_filter();

        $sql = "SELECT test.*,questionsMap.questionsAddedCount
        FROM test
        $append_string
        LEFT JOIN (SELECT COUNT(*) as questionsAddedCount, test_id 
        FROM test_questions_map 
        WHERE question_disabled= 0 GROUP BY test_id) as questionsMap 
        ON questionsMap.test_id = test.test_id
        WHERE test.institute_id = :institute_id:
        AND test.status = 'ARCHIVED' 
        ORDER BY test.created_date DESC";

        $query = $db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);


        $result = $query->getResultArray();
        return $result;
    }
    /********************************************************/


    /**
     * Testwise Unsubmitted Students Count
     *
     * @param Integer $institute_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function  testwise_unsubmitted_students(int $institute_id)
    {
        $current_date_time = date('Y-m-d H:i:s');
        $db = \Config\Database::connect();
        $sql = "SELECT count(test.test_name) AS unsubmitted_students_count, test.test_id, test.test_name, test.end_date FROM test
        INNER JOIN test_status
        ON test.test_id = test_status.test_id
        WHERE test_status.status = 'STARTED' 
        AND test.institute_id = :institute_id: 
        AND test.end_date < '$current_date_time' 
        GROUP BY test_status.test_id
        ORDER BY test.end_date DESC";

        $query = $db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/

    /*****************************************************************
     *################ END OF FUNCTIONS - Filtered Data  ###########*
     *****************************************************************/

    /**
     * fetch no. of questions
     * @return Array
     * @since 2021/10/14
     * @author PrachiP
     */
    public function fetch_chapterwise_questions_count($test_id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT test_questions.chapter, count(*) as chapter_count, chapters.chapter_name FROM test_questions_map
        LEFT JOIN test_questions 
        ON test_questions_map.question_id = test_questions.id 
        LEFT JOIN chapters
        ON test_questions.chapter = chapters.id
        WHERE test_questions_map.test_id='$test_id'
        AND test_questions_map.question_disabled=0
        GROUP BY test_questions.chapter");
        return $query->getResultArray();
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * fetch type of questions
     * @return Array
     * @since 2021/10/14
     * @author PrachiP
     */
    public function fetch_type_of_questions($test_id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT count(*) as typeCount, question_type 
        FROM test_questions_map 
        JOIN test_questions 
        ON test_questions.id = test_questions_map.question_id 
        WHERE test_id = '$test_id' 
        AND test_questions_map.question_disabled = 0 
        GROUP BY question_type");
        return $query->getResultArray();
    }
    /*******************************************************/


    /*******************************************************/
    /**
     * fetch test chapters
     * @return Array
     * @since 2021/10/14
     * @author PrachiP
     */
    public function fetch_test_chapters($subject_id, $institute_id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT chapters.*,questionsCount.que_count
        FROM chapters 
        LEFT JOIN (select count(*) as que_count,chapter from test_questions where status = 'A' and is_dummy = 0 group by chapter) as questionsCount 
        ON questionsCount.chapter = chapters.id 
        WHERE subject = '$subject_id' 
        AND (chapters.institute_id is null OR chapters.institute_id = '$institute_id' )");
        return $query->getResultArray();
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * fetch test absent students
     * @return Array
     * @since 2021/10/18
     * @author PrachiP
     */
    public function fetch_test_absent_students($test_id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT student.roll_no, student.name, student.caste_category, student.mobile_no, student.parent_mobile_no, test_package_map.package_id, packages.package_name,student_login.username 
        FROM student 
        INNER JOIN student_institute 
        ON student.id = student_institute.student_id  AND student_institute.is_disabled = 0
        INNER JOIN test_package_map 
        ON student_institute.package_id = test_package_map.package_id AND test_package_map.is_disabled = 0
        INNER JOIN packages
        on test_package_map.package_id = packages.id AND packages.is_disabled = 0
        INNER JOIN student_login 
        ON student.id = student_login.student_id 
        WHERE test_package_map.test_id = $test_id 
        AND student.id NOT IN (SELECT student_id FROM test_status WHERE test_id = $test_id)
        AND (student_login.student_access IS NULL OR student_login.student_access = '')
        ORDER BY student.roll_no");
        return $query->getResultArray();
    }
    /*******************************************************/


    /*******************************************************/
    /**
     * Test Questions Mapped - Used for creating test template config rules
     * @param int $test_id
     * @return array
     * @author Rushikesh B
     */
    public function test_questions_mapped(int $test_id)
    {
        $db = \Config\Database::connect();


        $query = $db->query("select  t.*,test_questions.question_type,test_questions.subject_id
        from test_questions_map t
        join (select section,min(question_number) as val_1,max(question_number) as val_2
        from test_questions_map
        where  test_id = '$test_id'
        group by section) v
        on t.section = v.section
        and (t.question_number = v.val_1 or t.question_number = v.val_2)
        join test_questions
        on test_questions.id = t.question_id
        where  test_id = '$test_id' ");
        return $query->getResultArray();
    }
    /*******************************************************/



    /*******************************************************/
    /**
     * Student Test Options Selected
     * @param int $test_id
     * @param string $username
     * @return array
     * @author Rushikesh B
     */
    public function student_test_options_selected(int $test_id, string $username)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT test_result.*, test_questions_map.question_number,
        test_questions.question_type,test_questions.correct_answer 
        FROM test_result
        join test_questions_map 
        on test_result.test_id = test_questions_map.test_id 
        and test_result.question_id = test_questions_map.question_id 
        join test_questions on test_questions.id = test_questions_map.question_id
        join student_login
        on student_login.student_id = test_result.student_id
        where test_result.test_id = '$test_id' 
        and student_login.username = '$username' 
        order by test_questions_map.question_number asc ");
        return $query->getResultArray();
    }
    /*******************************************************/


    /*******************************************************/
    /**
     * import bulk questions
     * @return Void
     * @since 2021/10/19
     * @author PrachiP
     */
    public function import_bulk_questions_submit($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        $no_of_questions_imported = 0;
        $test_id = decrypt_cipher($data['test_id']);
        if (isset($data['questionNumber']) && !empty($data['questionNumber'])) :
            $questionNumber = $data['questionNumber'];
        else :
            $query = $db->query("SELECT MAX( question_number ) AS max
            FROM test_questions_map 
            WHERE test_id = $test_id 
            AND question_disabled = 0");
            $result = $query->getRow();
            $questionNumber = $result->max;
        endif;
        $section = $data["section"];
        $weightage = $data["weightage"];
        $negativeMarks = $data["negativeMarks"];
        $chkboxQues_append = $data['chkboxQues_append'];
        foreach ($chkboxQues_append as $question_id) {
            $insert_array = array(
                'test_id' => $test_id,
                'question_id' => $question_id,
                'question_number' => $questionNumber,
                'section' => $section,
                'weightage' => $weightage,
                'negative_marks' => $negativeMarks
            );
            $db->table('test_questions_map')->insert($insert_array);
            $no_of_questions_imported++;
            $questionNumber++;
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username'),
                'test_id' => $test_id
            ];
            log_message('error', 'User {username} tried to import questions but failed for the test ID {test_id}', $log_info);
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Imported total $no_of_questions_imported questions in bulk",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/

    public function submit_ongoing_tests(array $data)
    {

        $db = \Config\Database::connect();
        $db->transStart();

        $current_date = date('Y-m-d H:i:s');

        $test_id = $data['test_id'];

        $update_array = array(
            'status' => 'COMPLETED',
            'submission_type' => 'admin',
            'admin_submission_date' => $current_date
        );

        $db->table('test_status')->update($update_array, ['test_id' => $test_id, 'status' => 'STARTED']);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Submitted Ongoing Tests",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /*******************************************************/
    /**
     * import bulk questions
     * @return Array
     * @since 2021/10/19
     * @author PrachiP
     */
    public function get_no_of_questions($test_id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT no_of_questions 
        FROM test 
        WHERE test_id = '$test_id'");
        return $query->getRowArray();
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * Add bulk solutions images
     * @return Void
     * @since 2021/10/21
     * @author PrachiP
     */
    public function add_bulk_solutions_images_submit($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $current_date = date('Y-m-d H:i:s');

        $from_question_no = $data['from_question_no'];
        $to_question_no = $data['to_question_no'];
        $filePreviewName = $data['filePreviewName'];

        $test_id = decrypt_cipher($data['test_id']);
        $instituteID = decrypt_cipher($data['institute_id']);

        for ($question_number = $from_question_no; $question_number <= $to_question_no; $question_number++) {
            $query_fetched_question = $db->query("SELECT test_questions.id, test_questions.correct_answer, test_questions.question_type, test_questions.solution_img_url
            FROM test_questions_map
            JOIN test_questions
            ON test_questions.id=test_questions_map.question_id
            INNER JOIN test
            ON test_questions_map.test_id = test.test_id AND test.institute_id = '$instituteID'
            WHERE test_questions_map.question_disabled = '0' 
            AND test_questions_map.test_id = '$test_id' 
            AND test_questions_map.question_number='$question_number'");
            $result_fetched_question = $query_fetched_question->getRowArray();
            $question_id = 0;
            if (!empty($result_fetched_question)) {
                $question_id = $result_fetched_question['id'];
            }
            $solution_img_url = $filePreviewName[$question_number - 1];

            $update_array = array(
                'solution_img_url' => $solution_img_url
            );

            $db->table('test_questions')->update($update_array, ['id' => $question_id]);
        }
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Added Bulk Question Images",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /*******************************************************/
    /**
     * Add one by one solutions images
     * @return Void
     * @since 2021/10/21
     * @author PrachiP
     */
    public function add_onebyone_solutions_images_submit($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        $question_ids = $data['question_id'];
        $solution_img_file = $data['filePreviewName'];

        if (!empty($question_ids)) {
            foreach ($question_ids as $key => $question_id) {
                $solution_img_file_url = $solution_img_file[$key];
                $update_array = array(
                    'solution_img_url' => $solution_img_file_url
                );

                $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Added one by one solution images",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/





    /**
     * Realtime Student Test Overview
     *
     * @param integer $test_id
     * @param integer $student_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function realtime_student_overview(int $test_id, int $student_id)
    {
        $db = \Config\Database::connect();
        $sql = "select student.id,student.name, student.roll_no, student.mobile_no, student.school_district, test_status.status,
        test_status.time_left, test_status.created_date, test_status.exam_started_count, attempted.attemptedCount,solved.solvedCount,
        flagged.flaggedCount, test_status.device, test_status.device_info, test_status.submission_type, test_status.proctoring_remarks,test_status.omr_answer_sheet
        from test_status
        JOIN student on test_status.student_id = student.id
        left join (SELECT count(*) as attemptedCount,student_id FROM test_result where test_id = :test_id: AND student_id = :student_id:) as attempted
        on attempted.student_id = student.id
        left join (SELECT count(*) as solvedCount,student_id FROM test_result where length(trim(option_selected)) > 0
        AND test_id = :test_id: AND student_id = :student_id: ) as solved on solved.student_id = student.id
        left join (SELECT count(*) as flaggedCount,student_id FROM test_result where flagged = 1 AND test_id = :test_id: AND student_id = :student_id:) as flagged on flagged.student_id = student.id
        where test_id = :test_id: AND student.id = :student_id: ";

        $query = $db->query($sql, [
            'test_id' => sanitize_input($test_id),
            'student_id' => sanitize_input($student_id)
        ]);

        $result = $query->getRowArray();
        return $result;
    }
    /*******************************************************/


    /**
     * Update Student Test Status
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_student_test_status($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();


        $student_id = sanitize_input($data['student_id']);
        $test_id = sanitize_input($data['test_id']);
        $test_status_value = sanitize_input($data['test_status_value']);

        $time_left_value = 'NULL';
        if (isset($data['time_left_value']) && !empty($data['time_left_value'])) {
            $time_left_value = sanitize_input($data['time_left_value']);
        }


        $exam_started_count = 'NULL';
        if (isset($data['exam_started_count']) && !empty($data['exam_started_count'])) {
            $exam_started_count = sanitize_input($data['exam_started_count']);
        }

        $submissionType = '';
        $admin_reset = 1;
        if ($test_status_value == 'COMPLETED') {
            $submissionType = 'admin';
            $admin_reset = 0;
        }

        $update_array = array(
            'status' => $test_status_value,
            'time_left' => $time_left_value,
            'exam_started_count' => $exam_started_count,
            'submission_type' => $submissionType,
            'admin_reset' => $admin_reset
        );

        $db->table('test_status')->update($update_array, ['test_id' => $test_id, 'student_id' => $student_id]);

        // Make entry into test activity for this student and test

        $current_date = date('Y-m-d H:i:s');

        $test_activity_data = array(
            'test_id' => $test_id,
            'student_id' => $student_id,
            'activity_type' => $test_status_value,
            'created_date' => $current_date,
            'device' => 'admin'
        );

        $db->table('test_activity')->insert($test_activity_data);


        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Updated Student Test Status",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Reset Student Exam Session
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function reset_student_exam_session($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();


        $student_id = sanitize_input($data['student_id']);
        $test_id = sanitize_input($data['test_id']);


        $update_array = array(
            'ws_session_id' => NULL
        );

        $db->table('test_status')->update($update_array, ['test_id' => $test_id, 'student_id' => $student_id]);

        // Make entry into test activity for this student and test

        $current_date = date('Y-m-d H:i:s');

        $test_activity_data = array(
            'test_id' => $test_id,
            'student_id' => $student_id,
            'activity_type' => 'UNBLOCKED',
            'created_date' => $current_date,
            'device' => 'admin'
        );

        $db->table('test_activity')->insert($test_activity_data);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Student Test session reset successfully",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Student Test Activity
     *
     * @param integer $test_id
     * @param integer $student_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_test_activity(int $test_id, int $student_id)
    {
        $db = \Config\Database::connect();
        $sql = "select *,test_activity.created_date as activity_time 
        FROM test_activity 
        LEFT JOIN test_questions_map 
        ON test_questions_map.question_id = test_activity.question_id 
        AND test_questions_map.test_id = test_activity.test_id 
        WHERE test_activity.test_id = :test_id: 
        AND test_activity.student_id = :student_id:
        ORDER BY test_activity.created_date DESC";

        $query = $db->query($sql, [
            'test_id' => sanitize_input($test_id),
            'student_id' => sanitize_input($student_id)
        ]);

        $result = $query->getResultArray();
        return $result;
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
    public function revaluate_test_result($test_id)
    {

        $db = \Config\Database::connect();
        $db->transStart();

        $testidFsms = sanitize_input($test_id);

        $sql = "SELECT round_marks FROM test where test_id = '$testidFsms'";
        $query = $db->query($sql);
        $resultRoundMarks = $query->getRowArray();
        $round_marks_check = $resultRoundMarks['round_marks'];


        $sqlForSubject = "SELECT test_subjects.subject
        FROM test_questions 
        INNER JOIN test_subjects ON test_subjects.subject_id = test_questions.subject_id 
        INNER JOIN test_questions_map tqm ON test_questions.id = tqm.question_id 
        AND tqm.test_id='$testidFsms' 
        GROUP BY test_questions.subject_id 
        ORDER BY test_subjects.subject";
        $resultForSubject = $db->query($sqlForSubject)->getResultArray();


        $subjectGroupArr = array();
        foreach ($resultForSubject as $rowFORSubject) {
            array_push($subjectGroupArr, $rowFORSubject['subject']);
        }

        $subjectGroupStrng = implode(",", $subjectGroupArr);

        if ($round_marks_check == "Y") {
            $sql = "SELECT s.id,s.name,s.roll_no,s.mobile_no,s.parent_mobile_no,ROUND(ts.score, 2) as student_score,
        tt.top_score,test_name,total_marks,ts.solved,ts.correct,test.no_of_questions,s.caste_category
        FROM student as s
        INNER JOIN test_status as ts
        ON ts.student_id = s.id 
        INNER JOIN test 
        ON test.test_id = ts.test_id
        INNER JOIN  (SELECT MAX(score) as top_score,test_id FROM test_status WHERE test_id='$testidFsms') tt
        ON tt.test_id = ts.test_id
        WHERE ts.test_id='$testidFsms' and ts.status='COMPLETED'
        ORDER BY student_score desc";

            $result = $db->query($sql)->getResultArray();
        } else {
            $sql = "SELECT s.id,s.name,s.roll_no,s.mobile_no,s.parent_mobile_no,ROUND(ts.score, 0) as student_score,
        tt.top_score,test_name,total_marks,ts.solved,ts.correct,test.no_of_questions,s.caste_category
        FROM student as s
        INNER JOIN test_status as ts
        ON ts.student_id = s.id 
        INNER JOIN test 
        ON test.test_id = ts.test_id
        INNER JOIN  (SELECT MAX(score) as top_score,test_id FROM test_status WHERE test_id='$testidFsms') tt
        ON tt.test_id = ts.test_id
        WHERE ts.test_id='$testidFsms' and ts.status='COMPLETED'
        ORDER BY student_score desc";

            $result = $db->query($sql)->getResultArray();
        }

        $index = 1;
        if (!empty($result)) {

            foreach ($result as $row) {

                $test_name = $row['test_name'];
                $total_marks = $row['total_marks'];
                $top_score = $row['top_score'];
                $stuid = $row['id'];
                $stu_name = $row['name'];
                $stu_roll_no = $row['roll_no'];
                $stu_mobile_no = $row['mobile_no'];
                $stu_parent_mobile_no = $row['parent_mobile_no'];
                $stu_score = $row['student_score'];
                $stu_score = $row['student_score'];
                $stu_score = $row['student_score'];
                $stu_que_total = $row['no_of_questions'];
                $stu_que_solved = $row['solved'];
                $stu_que_correct = $row['correct'];
                $stu_que_wrong =  $stu_que_solved - $stu_que_correct;
                $stu_que_not_attempted =  $stu_que_total - $stu_que_solved;
                $category = $row['caste_category'];

                if ($round_marks_check == "Y") {
                    $sqlForPCMB = "SELECT test_subjects.subject,if(SUM(test_result.marks) IS NULL, ' - ',ROUND(SUM(test_result.marks),2)) as subMarks
             FROM test_questions 
             INNER JOIN test_subjects ON test_subjects.subject_id = test_questions.subject_id 
             INNER JOIN test_questions_map tqm ON test_questions.id = tqm.question_id AND tqm.test_id='$testidFsms' 
             LEFT JOIN test_result ON test_result.question_id = test_questions.id AND test_result.test_id='$testidFsms' AND test_result.student_id= '$stuid'
             GROUP BY test_questions.subject_id 
             ORDER BY test_subjects.subject";
                } else {
                    $sqlForPCMB = "SELECT test_subjects.subject,if(SUM(test_result.marks) IS NULL, ' - ',ROUND(SUM(test_result.marks),0)) as subMarks
            FROM test_questions 
            INNER JOIN test_subjects ON test_subjects.subject_id = test_questions.subject_id 
            INNER JOIN test_questions_map tqm ON test_questions.id = tqm.question_id AND tqm.test_id='$testidFsms' 
            LEFT JOIN test_result ON test_result.question_id = test_questions.id AND test_result.test_id='$testidFsms' AND test_result.student_id= '$stuid'
            GROUP BY test_questions.subject_id 
            ORDER BY test_subjects.subject";
                }


                $resultForPCMB = $db->query($sqlForPCMB)->getResultArray();
                $subMarksContent = "";
                $subjectGroupMarkStrng = "";
                $subjectGroupMarkArr = array();

                foreach ($resultForPCMB as $rowFORPCMB) {
                    $subMarksContent = $subMarksContent . $rowFORPCMB['subject'] . ": " . $rowFORPCMB['subMarks'] . " ";
                }

                if ($round_marks_check == "Y") {
                    $sqlForSubjectMark = "SELECT test_subjects.subject,if(SUM(test_result.marks) IS NULL, ' - ',ROUND(SUM(test_result.marks),2)) as subMarks
             FROM test_questions 
             INNER JOIN test_subjects ON test_subjects.subject_id = test_questions.subject_id 
             INNER JOIN test_questions_map tqm ON test_questions.id = tqm.question_id AND tqm.test_id='$testidFsms' 
             LEFT JOIN test_result ON test_result.question_id = test_questions.id AND test_result.test_id='$testidFsms' AND test_result.student_id= '$stuid'
             GROUP BY test_questions.subject_id 
             ORDER BY test_subjects.subject";
                } else {
                    $sqlForSubjectMark = "SELECT test_subjects.subject,if(SUM(test_result.marks) IS NULL, ' - ',ROUND(SUM(test_result.marks),0)) as subMarks
            FROM test_questions 
            INNER JOIN test_subjects ON test_subjects.subject_id = test_questions.subject_id 
            INNER JOIN test_questions_map tqm ON test_questions.id = tqm.question_id AND tqm.test_id='$testidFsms' 
            LEFT JOIN test_result ON test_result.question_id = test_questions.id AND test_result.test_id='$testidFsms' AND test_result.student_id= '$stuid'
            GROUP BY test_questions.subject_id 
            ORDER BY test_subjects.subject";
                }


                $resultForSubjectMark = $db->query($sqlForSubjectMark)->getResultArray();

                foreach ($resultForSubjectMark as $rowFORSubjectMark) {
                    array_push($subjectGroupMarkArr, $rowFORSubjectMark['subMarks']);
                }

                $subjectGroupMarkStrng = str_replace(" - ", "'-'", implode(",", $subjectGroupMarkArr));

                if ($round_marks_check == "Y") {
                    $message = "Hi " . $row['name'] . " you scored $subMarksContent, Total:" . $row['student_score'] . " out of " . $row['total_marks'] . " in " . $row['test_name'] . " your Rank is " . $index . " and top score is " . round($row['top_score'], 2);
                } else {
                    $message = "Hi " . $row['name'] . " you scored $subMarksContent, Total:" . $row['student_score'] . " out of " . $row['total_marks'] . " in " . $row['test_name'] . " your Rank is " . $index . " and top score is " . round($row['top_score'], 0);
                }

                //     $sqlInsert = "INSERT INTO revaluate_result(test_id, test_name, total_marks, top_score, stu_id, stu_name, stu_roll_no, stu_mobile_no, parent_mobile_no, stu_rank, subjects_group, $subjectGroupStrng, score, message,correct_ans,wrong_ans,not_attempted,category) VALUES 
                // ('$testidFsms','$test_name','$total_marks','$top_score','$stuid','$stu_name','$stu_roll_no','$stu_mobile_no','$stu_parent_mobile_no','$index','$subjectGroupStrng',$subjectGroupMarkStrng,$stu_score,'$message','$stu_que_correct','$stu_que_wrong','$stu_que_not_attempted','$category')";

                //     $db->query($sqlInsert);
                $index++;
            }
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Reevaluted Test",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Student question option selected
     *
     * @param int $row_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_question_option_selected(int $row_id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT test_result.*, test_questions_map.question_number 
        FROM test_result
        join test_questions_map 
        on test_result.test_id = test_questions_map.test_id 
        and test_result.question_id = test_questions_map.question_id 
        where test_result.id = '$row_id' ");
        return $query->getRowArray();
    }
    /*******************************************************/



    /**
     * Update student test options - OMR test only
     *
     * @param array $data
     *
     * @return string
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_student_test_option(array $data)
    {

        $db = \Config\Database::connect();
        $db->transStart();

        $test_id = sanitize_input($data['test_id']);
        $test_name = sanitize_input($data['test_name']);
        $student_id = sanitize_input($data['student_id']);
        $student_name = sanitize_input($data['student_name']);

        $option_selected = sanitize_input($data['option_selected']);
        $row_id = sanitize_input($data['row_id']);


        $prev_result = $this->student_question_option_selected($row_id);
        $question_number = $prev_result['question_number'];
        $prev_option_selected = $prev_result['option_selected'];


        $update_array = array(
            'option_selected' => $option_selected,
            'flagged' => 1,
            'updated_date' => date('Y-m-d H:i:s')
        );
        $db->table('test_result')->update($update_array, ['id' => $row_id]);


        // Update Test Status Entry 
        $update_test_status_array = array(
            'omr_result_corrected' => 1,
            'updated_date' => date('Y-m-d H:i:s')
        );
        $db->table('test_status')->update($update_test_status_array, ['test_id' => $test_id, 'student_id' => $student_id]);



        $msg = "Student selected option $prev_option_selected for the question number $question_number updated to option $option_selected successfully ";

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to update student test option but failed', $log_info);
            return "Cannot Update. There was some error. Try again";
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => $msg . " in test " . $test_name . " for the student: " . $student_name,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return $msg;
        }
    }
    /*******************************************************/




    /**
     * Update Test Question Properties
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_test_question_properties($data)
    {

        $db = \Config\Database::connect();
        $db->transStart();

        $AdminID = decrypt_cipher(session()->get('login_id'));
        $msg = "no entry found";

        if ($data['update'] === 'verifyQuestion') {
            $question_id = sanitize_input($data['question_id']);
            $update_array = array(
                'verified_date' => date('Y-m-d H:i:s'),
                'verified_by' => $AdminID
            );
            $db->table('test_questions')->update($update_array, ['id' => $question_id]);

            $msg = "Question verified successfully";
        }

        if ($data['update'] === 'qType') {
            $newType = sanitize_input($data['newType']);
            $question_id = sanitize_input($data['question_id']);

            $update_array = array(
                'question_type' => $newType,
                'updated_by_admin' => $AdminID
            );
            $db->table('test_questions')->update($update_array, ['id' => $question_id]);

            $msg = "Question Type for question with ID-$question_id updated successfully";
        }

        if ($data['update'] === 'partialMarking') {
            $newMarking = sanitize_input($data['newMarking']);
            $question_id = sanitize_input($data['question_id']);


            $update_array = array(
                'partial' => $newMarking,
                'updated_by_admin' => $AdminID
            );
            $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            $msg = "Question Partial Marking Scheme for question with ID-$question_id updated successfully";
        }

        if ($data['update'] === 'section') {
            $newSection = sanitize_input($data['newSection']);
            $question_id = sanitize_input($data['question_id']);

            $update_array = array(
                'section' => $newSection
            );

            $db->table('test_questions_map')->update($update_array, ['question_id' => $question_id]);
            $msg = "Section for question with ID-$question_id updated successfully";
        }

        if ($data['update'] === 'number') {
            $newNumber = sanitize_input($data['newNumber']);
            $question_id = sanitize_input($data['question_id']);

            $update_array = array(
                'question_number' => $newNumber
            );

            $db->table('test_questions_map')->update($update_array, ['question_id' => $question_id]);
            $msg = "Question Number for question with ID-$question_id updated successfully";
        }

        if ($data['update'] === 'metadataText') {
            $newText = addslashes(sanitize_input($data['newText']));
            $question_id = sanitize_input($data['question_id']);

            $update_array = array(
                'meta_data' => $newText,
                'updated_by_admin' => $AdminID
            );
            $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            $msg = "METADATA for question with ID-$question_id updated successfully";
        }

        if ($data['update'] === 'questionText') {
            $newText = addslashes(sanitize_input($data['newText']));
            $question_id = sanitize_input($data['question_id']);

            $update_array = array(
                'question' => $newText,
                'updated_by_admin' => $AdminID
            );
            $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            $msg = "Question TEXT for question with ID-$question_id updated successfully";
        }

        if ($_POST['update'] === 'option1Text') {

            $newText = addslashes(sanitize_input($data['newText']));
            $question_id = sanitize_input($data['question_id']);

            $update_array = array(
                'option1' => $newText,
                'updated_by_admin' => $AdminID
            );
            $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            $msg = "OPTION-1 for question with ID-$question_id updated successfully";
        }

        if ($data['update'] === 'option2Text') {

            $newText = addslashes(sanitize_input($data['newText']));
            $question_id = sanitize_input($data['question_id']);

            $update_array = array(
                'option2' => $newText,
                'updated_by_admin' => $AdminID
            );
            $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            $msg = "OPTION-2 for question with ID-$question_id updated successfully";
        }

        if ($data['update'] === 'option3Text') {
            $newText = addslashes(sanitize_input($data['newText']));
            $question_id = sanitize_input($data['question_id']);

            $update_array = array(
                'option3' => $newText,
                'updated_by_admin' => $AdminID
            );
            $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            $msg = "OPTION-3 for question with ID-$question_id updated successfully";
        }

        if ($data['update'] === 'option4Text') {
            $newText = addslashes(sanitize_input($data['newText']));
            $question_id = sanitize_input($data['question_id']);

            $update_array = array(
                'option4' => $newText,
                'updated_by_admin' => $AdminID
            );
            $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            $msg = "OPTION-4 for question with ID-$question_id updated successfully";
        }

        if ($data['update'] === 'option5Text') {
            $newText = addslashes(sanitize_input($data['newText']));
            $question_id = sanitize_input($data['question_id']);

            $update_array = array(
                'option5' => $newText,
                'updated_by_admin' => $AdminID
            );
            $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            $msg = "OPTION-5 for question with ID-$question_id updated successfully";
        }

        if ($data['update'] === 'correctAnswerText') {

            $newText = addslashes(sanitize_input($data['newText']));
            $newText = str_replace(' ', '', $newText);
            $question_id = sanitize_input($data['question_id']);

            $update_array = array(
                'correct_answer' => $newText,
                'updated_by_admin' => $AdminID
            );
            $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            $msg = "CORRECT ANSWER for question with ID-$question_id as $newText  updated successfully";
        }

        if ($data['update'] === 'alternateAnswerText') {

            $newText = addslashes(sanitize_input($data['newText']));
            $newText = strtolower($newText);
            $newText = str_replace(' ', '', $newText);
            $question_id = sanitize_input($data['question_id']);

            $update_array = array(
                'alt_answer' => $newText,
                'updated_by_admin' => $AdminID
            );
            $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            $msg = "ALTERNATE ANSWER for question with ID-$question_id as $newText updated successfully";
        }

        if ($data['update'] === 'positiveMarks') {

            $newText = addslashes(sanitize_input($data['newText']));
            $newText = str_replace(' ', '', $newText);
            $question_id = sanitize_input($data['question_id']);
            $test_id = sanitize_input($data['test_id']);

            $update_array = array(
                'weightage' => $newText
            );
            $db->table('test_questions_map')->update($update_array, ['question_id' => $question_id, 'test_id' => $test_id]);
            $msg = "POSITIVE MARKS for question with ID-$question_id updated successfully";
        }

        if ($_POST['update'] === 'negativeMarks') {


            $newText = addslashes(sanitize_input($data['newText']));
            $newText = str_replace(' ', '', $newText);
            $question_id = sanitize_input($data['question_id']);
            $test_id = sanitize_input($data['test_id']);

            $update_array = array(
                'negative_marks' => $newText
            );
            $db->table('test_questions_map')->update($update_array, ['question_id' => $question_id, 'test_id' => $test_id]);
            $msg = "NEGATIVE MARKS for question with ID-$question_id updated successfully";
        }

        if ($data['update'] === 'disableQuestion') {

            $question_id = sanitize_input($data['question_id']);
            $test_id = sanitize_input($data['test_id']);

            $update_array = array(
                'question_disabled' => 1
            );
            $db->table('test_questions_map')->update($update_array, ['question_id' => $question_id, 'test_id' => $test_id]);
            $msg = "Deleted for question with ID - $question_id";
        }



        //Loading all subjects in the dropdown
        if ($data['update'] === 'loadSubjects' && isset($data['subjectName'])) {

            $prev_subject_name = sanitize_input($data['subjectName']);
            $institute_id = sanitize_input($data['instituteId']);


            $query = $db->query("SELECT subject_id, subject FROM test_subjects 
            WHERE (institute_id = '$institute_id' OR institute_id is null)");

            $subject_result = $query->getResultArray();


            $subject_list = "<option value=''>Select subject</option>";
            if (!empty($subject_result)) :
                foreach ($subject_result as $row) :
                    $id = $row['subject_id'];
                    $fetched_subject_name = $row['subject'];

                    $selected = "";
                    // Checking which subject is already selected
                    if (strtolower(trim($prev_subject_name)) == strtolower(trim($fetched_subject_name))) {
                        $selected = "selected";
                    }
                    $subject_list .= "<option value='$id' $selected>$fetched_subject_name</option>";
                endforeach;
            endif;
            $msg = $subject_list;
        }


        //To update the subject id for the question    
        if ($data['update'] === 'subjectId') {
            $question_id = sanitize_input($data['question_id']);

            if ($data['newSubjectId'] != '') {
                $subject_id = sanitize_input($data['newSubjectId']);

                $update_array = array(
                    'subject_id' => $subject_id,
                    'updated_by_admin' => $AdminID
                );
                $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            } else {

                $update_array = array(
                    'subject_id' => NULL,
                    'updated_by_admin' => $AdminID
                );
                $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            }
            $msg = "Subject edited for the question successfully. Please update chapter now.";
        }


        //Loading all chapters in the dropdown
        if ($data['update'] === 'loadChapters' && isset($data['subjectName'])) {

            $subject_name = sanitize_input($data['subjectName']);
            $chapter_id = sanitize_input($data['chapterId']);
            $institute_id = sanitize_input($data['instituteId']);

            //Hard coded subject id values for quick development of feature
            //Make it dynamic later TODO
            if ($subject_name == 'Physics') {
                $subject_id = 1;
            } elseif ($subject_name == 'Math') {
                $subject_id = 2;
            } elseif ($subject_name == 'Chemistry') {
                $subject_id = 3;
            } elseif ($subject_name == 'Biology') {
                $subject_id = 4;
            }


            $query = $db->query("SELECT chapters.*, test_subjects.subject FROM chapters 
            INNER JOIN test_subjects
            ON chapters.subject = test_subjects.subject_id
            WHERE chapters.subject = '$subject_id' AND
            (chapters.institute_id = '$institute_id' OR chapters.institute_id is null)");

            $chapter_result = $query->getResultArray();


            //If no chapters are mapped for this institute, then fetch general common chapters for all institutes
            if (empty($chapter_result)) {
                $query = $db->query("SELECT chapters.*, test_subjects.subject FROM chapters 
                            INNER JOIN test_subjects
                            ON chapters.subject = test_subjects.subject_id
                            WHERE chapters.subject = '$subject_id' AND
                            chapters.institute_id IS NULL");

                $chapter_result = $query->getResultArray();
            }


            $chapter_list =  "<option value=''>Select chapter</option>";
            if (!empty($chapter_result)) :
                foreach ($chapter_result as $row) :
                    $id = $row['id'];

                    $chapter_name = $row['chapter_name'];

                    $selected = "";
                    if ($id == $chapter_id) {
                        $selected = "selected";
                    }
                    $chapter_list  .= "<option value='$id' $selected>$chapter_name</option>";
                endforeach;
            endif;
            $msg =  $chapter_list;
        }


        //To update the chapter id for the question    
        if ($data['update'] === 'chapterId') {
            $question_id = sanitize_input($data['question_id']);

            if ($data['newChapterId'] != '') {
                $chapter_id = sanitize_input($data['newChapterId']);
                $update_array = array(
                    'chapter' => $chapter_id,
                    'updated_by_admin' => $AdminID
                );
                $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            } else {

                $update_array = array(
                    'chapter' => NULL,
                    'updated_by_admin' => $AdminID
                );
                $db->table('test_questions')->update($update_array, ['id' => $question_id]);
            }

            $msg = "Chapter edited for the question successfully.";
        }


        //To update the DIFFICULTY level for the question    
        if ($data['update'] === 'level') {

            $new_difficulty = sanitize_input($data['newDiffi']);
            $question_id = sanitize_input($data['question_id']);

            $update_array = array(
                'level' => $new_difficulty,
                'updated_by_admin' => $AdminID
            );
            $db->table('test_questions')->update($update_array, ['id' => $question_id]);

            $msg = "Difficulty level for the question updated successfully.";
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to update question properties but failed', $log_info);
            return "Cannot Update. There was some error. Try again";
        } else {
            // Activity Log
            if (isset($data['test_id']) && !empty($data['test_id'])) {
                $test_id = sanitize_input($data['test_id']);
                $test_details = $this->get_test_details($test_id);
                $test_name = $test_details['test_name'];
                $log_info =  [
                    'username' =>  session()->get('username'),
                    'item' => "Question Properties updated -" . $msg . " in test " . $test_name,
                    'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                    'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                    'question_id' =>  $question_id,
                    'test_id' => $test_id
                ];
            } else {
                $log_info =  [
                    'username' =>  session()->get('username'),
                    'item' => "Question Properties updated -" . $msg,
                    'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                    'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                    'question_id' =>  $question_id,
                ];
            }

            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return $msg;
        }
    }
    /*******************************************************/



    /**
     * Update Test Properties
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_test_properties($data)
    {

        $db = \Config\Database::connect();
        $db->transStart();

        $AdminID = decrypt_cipher(session()->get('login_id'));


        $test_id = $data['test_id'];
        $test_data = $this->get_test_details($test_id);

        //Updating SHOW RESULT property
        if ($data['update'] === 'showResult') {
            if ($test_data['show_result'] != "Y") {
                $show_result = 'Y';
            } else {
                $show_result = 'N';
            }

            $update_array = array(
                'show_result' => $show_result,
                // 'updated_by_admin' => $AdminID
            );
            $db->table('test')->update($update_array, ['test_id' => $test_id]);
        }

        //Updating SHOW QUESTION PAPER property
        if ($data['update'] === 'showQPaper') {
            if ($test_data['show_question_paper'] != "Y") {
                $show_question_paper = 'Y';
            } else {
                $show_question_paper = 'N';
            }

            $update_array = array(
                'show_question_paper' => $show_question_paper,
                // 'updated_by_admin' => $AdminID
            );
            $db->table('test')->update($update_array, ['test_id' => $test_id]);
        }

        //Updating SHUFFLING QUESTIONS in test property
        if ($data['update'] === 'shuffleQuestions') {
            if ($test_data['random_questions'] != "Y") {
                $random_questions = 'Y';
            } else {
                $random_questions = 'N';
            }

            $update_array = array(
                'random_questions' => $random_questions,
                // 'updated_by_admin' => $AdminID
            );
            $db->table('test')->update($update_array, ['test_id' => $test_id]);
        }



        //Updating TEST START TIME constraint property
        if ($data['update'] === 'testTimeConstraint') {
            if ($test_data['time_constraint'] != "1") {
                $time_constraint = '1';
            } else {
                $time_constraint = '0';
            }

            $update_array = array(
                'time_constraint' => $time_constraint,
                // 'updated_by_admin' => $AdminID
            );
            $db->table('test')->update($update_array, ['test_id' => $test_id]);
        }



        //Updating STUDENT START TIME constraint property
        if ($data['update'] === 'studentTimeConstraint') {
            if ($test_data['student_time_constraint'] != "1") {
                $student_time_constraint = '1';
            } else {
                $student_time_constraint = '0';
            }

            $update_array = array(
                'student_time_constraint' => $student_time_constraint,
                // 'updated_by_admin' => $AdminID
            );
            $db->table('test')->update($update_array, ['test_id' => $test_id]);
        }


        //Updating OFFLINE CONDUCTION property
        if ($data['update'] === 'offlineConduction') {
            if ($test_data['offline_conduction'] != "1") {
                $offline_conduction = '1';
            } else {
                $offline_conduction = '0';
            }

            $update_array = array(
                'offline_conduction' => $offline_conduction,
                // 'updated_by_admin' => $AdminID
            );
            $db->table('test')->update($update_array, ['test_id' => $test_id]);
        }


        //Updating GET GEOLOCATION property
        if ($data['update'] === 'getGeoLocation') {
            if ($test_data['accept_location'] != "1") {
                $accept_location = '1';
            } else {
                $accept_location = '0';
            }

            $update_array = array(
                'accept_location' => $accept_location,
                // 'updated_by_admin' => $AdminID
            );
            $db->table('test')->update($update_array, ['test_id' => $test_id]);
        }


        // Updating GET Video Proctoring Value
        if ($data['update'] === 'getVideoProctoringValue') {
            if ($test_data['video_proctoring'] != "1") {
                $video_proctoring = '1';
            } else {
                $video_proctoring = '0';
            }

            $update_array = array(
                'video_proctoring' => $video_proctoring
            );
            $db->table('test')->update($update_array, ['test_id' => $test_id]);
        }


        // Updating GET Image Proctoring Value
        if ($data['update'] === 'getImgProctoringValue') {
            if ($test_data['img_proctoring'] != "1") {
                $img_proctoring = '1';
            } else {
                $img_proctoring = '0';
            }

            $update_array = array(
                'img_proctoring' => $img_proctoring
            );
            $db->table('test')->update($update_array, ['test_id' => $test_id]);
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return "Cannot Update. There was some error. Try again";
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Test Properties updated",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'question_id' =>  $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return 1;
        }
    }
    /*******************************************************/



    /**
     * Revaluated Result
     *
     * @param integer $test_id
     * @param integer $institute_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function revaluated_result(int $test_id, int $institute_id, $group = "")
    {
        $db = \Config\Database::connect();
        if ($group == "") {
            $sql = "SELECT revaluate_result.*, test.start_date
        FROM revaluate_result
        INNER JOIN test
        ON revaluate_result.test_id = test.test_id
        WHERE revaluate_result.test_id = :test_id: 
        AND test.institute_id = :institute_id:
        GROUP BY revaluate_result.stu_id 
        ORDER BY score DESC";
        } elseif ($group == "Biology") {
            $sql = "SELECT * FROM revaluate_result
    WHERE test_id = :test_id: 
    GROUP BY revaluate_result.stu_id
    ORDER BY score DESC, Math DESC, Biology DESC, Chemistry DESC";
        } else {
            $sql = "SELECT * FROM revaluate_result
    WHERE test_id = :test_id: 
    GROUP BY revaluate_result.stu_id
    ORDER BY score DESC, Math DESC, Biology DESC, Physics DESC";
        }

        $query = $db->query($sql, [
            'institute_id' => sanitize_input($institute_id),
            'test_id' => sanitize_input($test_id)
        ]);
        $result = $query->getRowArray();
        return $result;
    }
    /*******************************************************/






    /**
     * Get Question Detail
     *
     * @param [type] $question_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function question_detail($question_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT * 
        FROM test_questions
        where id= :question_id: ";

        $query = $db->query($sql, [
            'question_id' => sanitize_input($question_id)
        ]);
        $result = $query->getRowArray();
        return $result;
    }
    /*******************************************************/





    /**
     * Test Instruction Submit
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function test_instruction_submit($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $add_instruction_encode = sanitize_input($data['add_instruction']);

        $chr_map = array(
            // Windows codepage 1252
            "\xC2\x82" => "'", // U+0082⇒U+201A single low-9 quotation mark
            "\xC2\x84" => '"', // U+0084⇒U+201E double low-9 quotation mark
            "\xC2\x8B" => "'", // U+008B⇒U+2039 single left-pointing angle quotation mark
            "\xC2\x91" => "'", // U+0091⇒U+2018 left single quotation mark
            "\xC2\x92" => "'", // U+0092⇒U+2019 right single quotation mark
            "\xC2\x93" => '"', // U+0093⇒U+201C left double quotation mark
            "\xC2\x94" => '"', // U+0094⇒U+201D right double quotation mark
            "\xC2\x9B" => "'", // U+009B⇒U+203A single right-pointing angle quotation mark

            // Regular Unicode     // U+0022 quotation mark (")
            // U+0027 apostrophe     (')
            "\xC2\xAB"     => '"', // U+00AB left-pointing double angle quotation mark
            "\xC2\xBB"     => '"', // U+00BB right-pointing double angle quotation mark
            "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
            "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
            "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
            "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
            "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
            "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
            "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
            "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
            "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
            "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
        );
        $chr = array_keys($chr_map); // but: for efficiency you should
        $rpl = array_values($chr_map); // pre-calculate these two arrays
        $add_instruction = str_replace($chr, $rpl, html_entity_decode($add_instruction_encode, ENT_QUOTES, "UTF-8"));


        $test_id = decrypt_cipher($data['test_id']);
        $update_array = array(
            'custom_instructions' => $add_instruction,
        );
        $db->table('test')->update($update_array, ['test_id' => $test_id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return "Cannot Update. There was some error. Try again";
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Test Instruction added",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/





    /**
     * Update Test Question
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_test_question($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();


        $question_id = $data['question_id'];
        $update_array = array(
            'question_type' => sanitize_input($data['Question_type']),
            'meta_data' => sanitize_input($data['meta_data']),
            'question' => sanitize_input($data['Q_MathInput']),
            'option1' => sanitize_input($data['Que_op1']),
            'option2' => sanitize_input($data['Que_op2']),
            'option3' => sanitize_input($data['Que_op3']),
            'option4' => sanitize_input($data['Que_op4']),
            'correct_answer' => sanitize_input($data['Que_correct_ans']),
            'solution' => sanitize_input($data['Que_solutions']),
            'meta_data_img_url' => sanitize_input($data['meta_data_img_url']),
            'question_img_url' => sanitize_input($data['question_img_url']),
            'option1_img_url' => sanitize_input($data['option1_img_url']),
            'option2_img_url' => sanitize_input($data['option2_img_url']),
            'option3_img_url' => sanitize_input($data['option3_img_url']),
            'option4_img_url' => sanitize_input($data['option4_img_url']),
            'solution_img_url' => sanitize_input($data['solution_img_url']),
        );
        $db->table('test_questions')->update($update_array, ['id' => $question_id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Updated Question Details",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $question_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/



    /**
     * Add Test Question
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_test_question($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $question_type = $data['Question_type'];
        //To create string for storing of the CORRECT ANSWER VALUES
        if ($question_type === "SINGLE") {
            $que_correct_ans = sanitize_input($data['Que_correct_ans']);
        } elseif ($question_type === "MULTIPLE" || $question_type === "PASSAGE_MULTIPLE") {
            $que_correct_ans = implode(',', $data['Que_correct_ans']);
        } elseif ($question_type === "NUMBER") {
            $que_correct_ans = implode(',', $data['Que_correct_ans']);
        } elseif ($question_type === "MATCH") {
            $que_op1 = 'a,b,c,d';
            $que_op2 = 'p,q,r,s';
            $que_correct_ans = implode(',', $data['Que_correct_ans']);
        }

        $add_array = array(
            'question_type' => sanitize_input($data['Question_type']),
            'meta_data' => sanitize_input($data['meta_data']),
            'question' => sanitize_input($data['Q_MathInput']),
            'option1' => sanitize_input($data['Que_op1']),
            'option2' => sanitize_input($data['Que_op2']),
            'option3' => sanitize_input($data['Que_op3']),
            'option4' => sanitize_input($data['Que_op4']),
            'correct_answer' => sanitize_input($data['Que_correct_ans']),
            'solution' => sanitize_input($data['Que_solutions']),
            'subject_id' => sanitize_input($data['test_subject']),
            'weightage' => sanitize_input($data['weightage']),
            'negative_marks' => sanitize_input($data['negative_mark']),
            'subject_id' => sanitize_input($data['test_subject'])
        );
        $db->table('test_questions')->insert($add_array);
        $test_question_id = $db->insertID();


        // Map Question to Test
        $test_questions_map_array = array(
            'test_id' => sanitize_input(decrypt_cipher($data['test_id'])),
            'question_id' => $test_question_id,
            'question_number' => sanitize_input($data['question_no']),
            'section' => sanitize_input($data['test_section']),
            'weightage' => sanitize_input($data['weightage']),
            'negative_marks' => sanitize_input($data['negative_mark'])
        );
        $db->table('test_questions_map')->insert($test_questions_map_array);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            return $test_question_id;
        }
    }
    /*******************************************************/









    /**
     * Exam Answer Result
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function examAnswersResult($test_id)
    {
        $db = \Config\Database::connect();
        $sql = "select examFiles.cnt as files,examFiles.student_id,student.name,student.roll_no,student.mobile_no,
        test_status.status,test_status.solved,test_status.correct,test_status.score,test_status.updated_date 
        FROM test_status 
        JOIN student 
        ON student.id = test_status.student_id
        LEFT JOIN (select count(*) as cnt,student_id from exam_answer_files where exam_id = :test_id: group by student_id) as examFiles
        ON test_status.student_id = examFiles.student_id 
        WHERE test_status.test_id = :test_id: 
        ORDER BY roll_no ASC ";

        $query = $db->query($sql, [
            'test_id' => sanitize_input($test_id)
        ]);
        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/


    /**
     * Subjectwise Correctness Percentage
     *
     * @param [type] $test_id
     * @param [type] $student_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function subjectwise_correctness_percentage($test_id, $student_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT *, ((q1.correctCount/q3.totalQuestions) * 100) AS correctness, q3.totalQuestions
    FROM ( SELECT test_result.test_id,test.test_name, count(test_result.id) AS correctCount,test_questions.subject_id,
    test_subjects.subject,wrongQuery.wrongCount
    FROM test_result JOIN test_questions ON test_result.question_id = test_questions.id
    JOIN test ON test_result.test_id = test.test_id
    JOIN test_subjects ON test_subjects.subject_id = test_questions.subject_id
    LEFT JOIN (SELECT count(*) as wrongCount,subject_id from test_result join test_questions on test_result.question_id = test_questions.id
    where student_id = :student_id: AND test_id = :test_id: AND (marks <= 0 AND option_selected <> '') group by subject_id) as wrongQuery on wrongQuery.subject_id = test_subjects.subject_id
    WHERE student_id = :student_id: AND test_result.test_id= :test_id: AND test_result.marks > 0 GROUP BY test_id, test_questions.subject_id HAVING count(test_result.id) > 0) AS q1 JOIN (SELECT count(test_questions_map.test_id) AS totalQuestions,test_id, subject_id FROM test_questions_map JOIN test_questions ON test_questions.id = test_questions_map.question_id GROUP BY test_id, test_questions.subject_id ) AS q3 ON q3.test_id = q1.test_id AND q3.subject_id = q1.subject_id";
        $query = $db->query($sql, [
            'test_id' => sanitize_input($test_id),
            'student_id' => sanitize_input($student_id),
        ]);
        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/


    /**
     * Subjectwise Time Taken Tests
     *
     * @param [type] $test_id
     * @param [type] $student_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function subjectwise_time_taken_tests($test_id, $student_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT test_result.test_id,test.test_name, sum(time_taken) AS time_taken,test_questions.subject_id,test_subjects.subject 
        FROM test_result 
        join test_questions on test_result.question_id = test_questions.id 
        join test on test_result.test_id = test.test_id join test_subjects on test_subjects.subject_id = test_questions.subject_id 
        where student_id = :student_id: AND test_result.test_id = :test_id: 
        group by test_id, test_questions.subject_id HAVING sum(time_taken) > 0";
        $query = $db->query($sql, [
            'test_id' => sanitize_input($test_id),
            'student_id' => sanitize_input($student_id),
        ]);
        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/



    /**
     * Student Proctor Images
     *
     * @param [type] $test_id
     * @param [type] $student_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_proctor_images($test_id, $student_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT * 
        FROM proctor_images 
        WHERE test_id = :test_id: 
        AND student_id = :student_id: 
        ORDER BY created_date DESC";
        $query = $db->query($sql, [
            'test_id' => sanitize_input($test_id),
            'student_id' => sanitize_input($student_id),
        ]);
        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/


    public function student_proctor_avg_score($test_id, $student_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT avg(score) as score
   FROM proctor_images 
   WHERE test_id = :test_id: 
   AND student_id = :student_id: ";
        $query = $db->query($sql, [
            'test_id' => sanitize_input($test_id),
            'student_id' => sanitize_input($student_id),
        ]);
        $result = $query->getRowArray();
        return $result;
    }

    public function proctoring_sessions($test_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT * 
    FROM live_session 
    JOIN packages 
    ON live_session.classroom_id = packages.id 
    WHERE live_session.test_id = :test_id:
    ORDER BY live_session.created_date asc";
        $query = $db->query($sql, [
            'test_id' => sanitize_input($test_id)
        ]);
        $result = $query->getResultArray();
        return $result;
    }

    /*****************************************************************
     *################ START OF SUBMIT FUNCTIONS ###########*
     *****************************************************************/


    /**
     * Add New Test 
     *
     * @param Array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_new_test(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        // Validation to check if the checkbox was checked or not and change values accordingly for unchecked
        $add_random_questions = "";
        if (isset($data['add_random_questions'])) {
            $add_random_questions = $data['add_random_questions'];
        } else {
            $add_random_questions = "N";
        }

        // Validation to check if the checkbox was checked or not and change values accordingly for unchecked
        $add_show_results = "";
        if (isset($data['add_show_results'])) {
            $add_show_results = $data['add_show_results'];
        } else {
            $add_show_results = "N";
        }

        // Validation to check if the checkbox was checked or not and change values accordingly for unchecked
        // Show question paper after test parameter
        $show_ques_paper_post_test = "Y";
        if (isset($data['show_ques_paper_post_test'])) {
            $show_ques_paper_post_test = $data['show_ques_paper_post_test'];
        } else {
            $show_ques_paper_post_test = "N";
        }


        //Align test time with TEST start time so that students cannot appear late for exam
        $align_test_time = 0;
        if (isset($data['align_test_time'])) {
            $align_test_time = 1;
        }

        //Align test time with STUDENT start time so that student will only get total hours based on when he started the exam
        $align_with_student_time = 0;
        if (isset($data['align_with_student_time'])) {
            $align_with_student_time = 1;
        }

        //Flag for offline exam conduction
        //This will reduce student's internet dependency for low internet connectivity but reduce realtime reports reliability
        $offline_conduction = 0;
        if (isset($data['offline_conduction'])) {
            $offline_conduction = 1;
        }

        //Flag for getting student's location while giving exam
        //This will force students to send their geolocation so that admin can track where the students are sitting while giving the exam
        $get_students_geolocation = 0;
        if (isset($data['get_students_geolocation'])) {
            $get_students_geolocation = 1;
        }

        //Flag for showing rank of student on the result page
        $show_student_rank = 0;
        if (isset($data['show_student_rank'])) {
            $show_student_rank = 1;
        }


        // Timeout in seconds to auto submit test if the student goes out of the test window for the provided value
        $test_timeout_value = NULL;
        if (isset($data['test_timeout_value'])) {
            if ($data['test_timeout_value'] != "ALLOW") {
                $test_timeout_value = $data['test_timeout_value'];
            }
        }


        // This sets the maximum number of times student can blur and focus, leave and reenter exam, exit and start exam. After this, the test will be blocked and has to be activated from admin side
        $max_allowed_test_starts = NULL;
        if (isset($data['max_allowed_test_starts'])) {
            if ($data['max_allowed_test_starts'] != "ALLOW") {
                $max_allowed_test_starts = $data['max_allowed_test_starts'];
            }
        }

        //When the test type UI is DPP, then send NULL in test duration
        if ($data['add_test_ui'] === "DPP") {
            $add_test_duration = NULL;
        } else {
            $add_test_duration_hours = $data['add_test_duration_hours'];
            $add_test_duration_minutes = $data['add_test_duration_minutes'];
            // Saving the test duration in seconds after taking in hours and minutes from user
            $add_test_duration = $add_test_duration_hours * 3600 + $add_test_duration_minutes * 60;
        }

        $add_test_package_arr = $data['add_test_package'];
        $first_test_package = $add_test_package_arr[0];


        //Flag for Video Proctoring
        $video_proctoring_check = 0;
        if (isset($data['video_proctoring_check'])) {
            $video_proctoring_check = 1;
        }

        //Flag for Image Proctoring
        $image_proctoring_check = 0;
        if (isset($data['image_proctoring_check'])) {
            $image_proctoring_check = 1;
        }

        //Flag for Random Pool
        $random_pool_check = 0;
        if (isset($data['random_pool_check'])) {
            $random_pool_check = 1;
        }

        $exam_conduction = 'Online';
        if (isset($data['exam_conduction'])) {
            $exam_conduction = sanitize_input($data['exam_conduction']);
        }

        $omr_template = NULL;
        if (isset($data['omr_template']) && !empty($data['omr_template'])) {
            $omr_template = sanitize_input($data['omr_template']);
        }


        $test_data = [
            'test_name' => strtoupper(sanitize_input($data['add_test_name'])),
            'no_of_questions' => sanitize_input($data['add_test_no_questions']),
            'total_marks' => sanitize_input($data['add_test_total_marks']),
            'duration' => sanitize_input($add_test_duration),
            'status' => 'Active',
            'institute_id' =>  sanitize_input(decrypt_cipher($data['institute_id'])),
            'package_id' => strtoupper(sanitize_input($first_test_package)),
            'start_date' => DefaultTimezone($data['start_date']),
            'end_date' => DefaultTimezone($data['end_date']),
            'test_ui' => strtoupper(sanitize_input($data['add_test_ui'])),
            'random_questions' => strtoupper(sanitize_input($add_random_questions)),
            'show_result' => strtoupper(sanitize_input($add_show_results)),
            'time_constraint' => strtoupper(sanitize_input($align_test_time)),
            'student_time_constraint' => strtoupper(sanitize_input($align_with_student_time)),
            'show_question_paper' => strtoupper(sanitize_input($show_ques_paper_post_test)),
            'pause_timeout_seconds' => $test_timeout_value,
            'max_allowed_test_starts' => $max_allowed_test_starts,
            'offline_conduction' => strtoupper(sanitize_input($offline_conduction)),
            'accept_location' => strtoupper(sanitize_input($get_students_geolocation)),
            'show_student_rank' => strtoupper(sanitize_input($show_student_rank)),
            'img_proctoring' => $image_proctoring_check,
            'random_pool' => $random_pool_check,
            'exam_conduction' => $exam_conduction,
            'omr_template' => $omr_template
        ];

        // Map template id if selected
        if (isset($data['template_id']) && !empty($data['template_id'])) {
            $test_data['template_id'] = strtoupper(sanitize_input($data['template_id']));
        }

        $db->table('test')->insert($test_data);

        $test_id = $db->insertID();

        foreach ($add_test_package_arr as $add_test_package) {
            $test_package_map_data = [
                'package_id' => strtoupper(sanitize_input($add_test_package)),
                'test_id' => $test_id
            ];
            $db->table('test_package_map')->insert($test_package_map_data);
        }


        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add new test details but failed', $log_info);
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "New Test with Test Name " . strtoupper(sanitize_input($data['add_test_name'])),
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' =>  $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);
            return $test_id;
        }
    }
    /*******************************************************/


    /**
     * Update Test Details
     *
     * @param Array $data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_test_info(array $data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $decrypted_test_id = decrypt_cipher($data['update_test_id']);
        // Validation to check if the checkbox was checked or not and change values accordingly for unchecked
        $update_random_questions = "";
        if (isset($data['update_random_questions'])) {
            $update_random_questions = $data['update_random_questions'];
        } else {
            $update_random_questions = "N";
        }

        // Validation to check if the checkbox was checked or not and change values accordingly for unchecked
        $update_show_results = "";
        if (isset($data['update_show_results'])) {
            $update_show_results = $data['update_show_results'];
        } else {
            $update_show_results = "N";
        }

        // Validation to check if the checkbox was checked or not and change values accordingly for unchecked
        // Show question paper after test parameter
        $show_ques_paper_post_test = "Y";
        if (isset($data['show_ques_paper_post_test'])) {
            $show_ques_paper_post_test = $data['show_ques_paper_post_test'];
        } else {
            $show_ques_paper_post_test = "N";
        }

        $update_round_marks = 'Y';
        if (isset($data['update_round_marks'])) {
            $update_round_marks = $data['update_round_marks'];
        } else {
            $update_round_marks = "N";
        }
        //Align test time with TEST start time so that students cannot appear late for exam
        $align_test_time = 0;
        if (isset($data['align_test_time'])) {
            $align_test_time = 1;
        }

        //Align test time with STUDENT start time so that student will only get total hours based on when he started the exam
        $align_with_student_time = 0;
        if (isset($data['align_with_student_time'])) {
            $align_with_student_time = 1;
        }

        //Flag for offline exam conduction
        //This will reduce student's internet dependency for low internet connectivity but reduce realtime reports reliability
        $offline_conduction = 0;
        if (isset($data['offline_conduction'])) {
            $offline_conduction = 1;
        }

        //Flag for getting student's location while giving exam
        //This will force students to send their geolocation so that admin can track where the students are sitting while giving the exam
        $get_students_geolocation = 0;
        if (isset($data['get_students_geolocation'])) {
            $get_students_geolocation = 1;
        }

        //Flag for showing rank of student on the result page
        $show_student_rank = 0;
        if (isset($data['show_student_rank'])) {
            $show_student_rank = 1;
        }


        // Timeout in seconds to auto submit test if the student goes out of the test window for the provided value
        $test_timeout_value = NULL;
        if (isset($data['test_timeout_value'])) {
            if ($data['test_timeout_value'] != "ALLOW") {
                $test_timeout_value = $data['test_timeout_value'];
            }
        }


        // This sets the maximum number of times student can blur and focus, leave and reenter exam, exit and start exam. After this, the test will be blocked and has to be activated from admin side
        $max_allowed_test_starts = NULL;
        if (isset($data['max_allowed_test_starts'])) {
            if ($data['max_allowed_test_starts'] != "ALLOW") {
                $max_allowed_test_starts = $data['max_allowed_test_starts'];
            }
        }

        //When the test type UI is DPP, then send NULL in test duration
        if ($data['update_test_ui'] === "DPP") {
            $update_test_duration = NULL;
        } else {
            $update_duration_hours = $data['update_duration_hours'];
            $update_duration_minutes = $data['update_duration_minutes'];
            // Saving the test duration in seconds after taking in hours and minutes from user
            $update_test_duration = $update_duration_hours * 3600 + $update_duration_minutes * 60;
        }

        $update_test_package_arr = $data['update_test_package'];
        $first_test_package = $update_test_package_arr[0];


        //Flag for Video Proctoring
        $video_proctoring_check = 0;
        if (isset($data['video_proctoring_check'])) {
            $video_proctoring_check = 1;
        }

        //Flag for Image Proctoring
        $image_proctoring_check = 0;
        if (isset($data['image_proctoring_check'])) {
            $image_proctoring_check = 1;
        }

        //Flag for Random Pool
        $random_pool_check = 0;
        if (isset($data['random_pool_check'])) {
            $random_pool_check = 1;
        }


        $exam_conduction = 'Online';
        if (isset($data['exam_conduction'])) {
            $exam_conduction = sanitize_input($data['exam_conduction']);
        }


        $omr_template = NULL;
        if (isset($data['omr_template']) && !empty($data['omr_template'])) {
            $omr_template = sanitize_input($data['omr_template']);
        }

        $test_data = [
            'test_name' => strtoupper(sanitize_input($data['update_test_name'])),
            'no_of_questions' => sanitize_input($data['update_no_questions']),
            'total_marks' => sanitize_input($data['update_total_marks']),
            'duration' => sanitize_input($update_test_duration),
            'status' => sanitize_input($data['update_status']),
            'institute_id' =>  sanitize_input(decrypt_cipher($data['institute_id'])),
            'package_id' => strtoupper(sanitize_input($first_test_package)),
            'start_date' => DefaultTimezone($data['update_start_date']),
            'end_date' => DefaultTimezone($data['update_end_date']),
            'test_ui' => strtoupper(sanitize_input($data['update_test_ui'])),
            'random_questions' => strtoupper(sanitize_input($update_random_questions)),
            'show_result' => strtoupper(sanitize_input($update_show_results)),
            'time_constraint' => strtoupper(sanitize_input($align_test_time)),
            'student_time_constraint' => strtoupper(sanitize_input($align_with_student_time)),
            'show_question_paper' => strtoupper(sanitize_input($show_ques_paper_post_test)),
            'pause_timeout_seconds' => $test_timeout_value,
            'max_allowed_test_starts' => $max_allowed_test_starts,
            'offline_conduction' => strtoupper(sanitize_input($offline_conduction)),
            'accept_location' => strtoupper(sanitize_input($get_students_geolocation)),
            // 'created_by' => $AdminID,
            'show_student_rank' => strtoupper(sanitize_input($show_student_rank)),
            'round_marks' => strtoupper(sanitize_input($update_round_marks)),
            'img_proctoring' => $image_proctoring_check,
            'video_proctoring' => $video_proctoring_check,
            'random_pool' => $random_pool_check,
            'exam_conduction' => $exam_conduction,
            'omr_template' => $omr_template
        ];


        if (isset($data['template_id']) && !empty($data['template_id'])) {
            $test_data['template_id'] = strtoupper(sanitize_input($data['template_id']));
        }
        
        $db->table('test')->update($test_data, ['test_id' => $decrypted_test_id]);
        // Disable previous mapped packages
        $db->query("Update test_package_map SET is_disabled ='1' WHERE test_id = '$decrypted_test_id'");
        // Update classrooms
        foreach ($update_test_package_arr as $update_test_package) {
            $query  = $db->query("SELECT * FROM test_package_map WHERE test_id = '$decrypted_test_id' AND package_id = '$update_test_package'");
            $result = $query->getRowArray();
            // Add new entry if result not found else unable the classroom
            if (empty($result)) {
                $test_package_map_data = [
                    'package_id' => $update_test_package,
                    'test_id' => $decrypted_test_id
                ];
                $db->table('test_package_map')->insert($test_package_map_data);
            } else {
                $db->query("Update test_package_map SET is_disabled ='0' WHERE test_id = '$decrypted_test_id' AND package_id = '$update_test_package' ");
            }
        }
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            $test_details = $this->get_test_details($decrypted_test_id);
            $test_name = $test_details['test_name'];
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Updated Test Details in test " . $test_name,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $decrypted_test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Add Bulk Test Questions Images
     *
     * @param Array $data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_test_questions_images(array $data)
    {

        $db = \Config\Database::connect();
        $db->transStart();

        $test_id = $data['test_id'];
        $institute_id = sanitize_input($data['institute_id']);
        $test_id = sanitize_input($data['test_id']);
        $offset = sanitize_input($data['offset']);
        $q_offset = $offset;
        $no_of_questions_added = 0;
        foreach ($data['Que_subject_id'] as $key => $value) {

            $subject_id = $value;
            $weightage = sanitize_input($data['weightage'][$key]);
            $negative_mark = sanitize_input($data['negative_mark'][$key]);
            $Question_type = sanitize_input($data['Question_type'][$key]);
            $section = sanitize_input($data['section'][$key]);

            if ($Question_type === "MATCH") {
                $option1 = sanitize_input($data['matchColumn1'][$key]);
                $option2 = sanitize_input($data['matchColumn2'][$key]);
            } else {
                $option1 = sanitize_input($data['option1'][$key]);
                $option2 = sanitize_input($data['option2'][$key]);
            }

            $option3 = sanitize_input($data['option3'][$key]);
            $option4 = sanitize_input($data['option4'][$key]);
            $Que_correct_ans = sanitize_input($data['Que_correct_ans'][$key]);
            $Que_partial_marking = sanitize_input($data['Que_partial_marking'][$key]);



            $add_test_questions_array = array(
                'subject_id' => $subject_id,
                'question_type' => $Question_type,
                'partial' => $Que_partial_marking,
                'option1' => $option1,
                'option2' => $option2,
                'option3' => $option3,
                'option4' => $option4,
                'correct_answer' => $Que_correct_ans,
                'negative_marks' => $negative_mark,
                'institute_id' => $institute_id,
                'created_by' => decrypt_cipher(session()->get('login_id'))
            );
            $db->table('test_questions')->insert($add_test_questions_array);
            $test_question_id = $db->insertID();


            $add_test_question_map_array = array(
                'test_id' => $test_id,
                'question_id' => $test_question_id,
                'question_number' => $q_offset,
                'section' => $section,
                'weightage' => $weightage,
                'negative_marks' => $negative_mark
            );
            $db->table('test_questions_map')->insert($add_test_question_map_array);
            $no_of_questions_added++;

            // Update test question URL
            $update_test_question_data = [
                'question_img_url' => $data['question_img_urls'][$key]
            ];
            $db->table('test_questions')->update($update_test_question_data, ['id' => $test_question_id]);

            $q_offset++;
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username'),
                'test_id' => $test_id
            ];
            log_message('error', 'User {username} tried to add questions with images but failed for the test ID {test_id}', $log_info);
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Added total $no_of_questions_added questions images in bulk",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /********************************************************/


    /**
     * Clone Test 
     *
     * @param Array $data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function clone_test(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $original_test_id = sanitize_input(decrypt_cipher($data['original_test_id']));
        // Fetch the original test params    
        $row_test_details = $this->get_test_details($original_test_id);


        $duration = $row_test_details['duration'];
        if (empty($duration)) {
            $duration = 'NULL';
        }

        $institute_id = $row_test_details['institute_id'];
        $classrooms = array_map("sanitize_input", $data['update_test_package']);
        $new_cloned_test_ids = array();

        // Copy all test parameters into new cloned tests based on number of classrooms
        foreach ($classrooms as $classroom) {
            $classroom = sanitize_input($classroom);
            $test_data = [
                'test_name' => strtoupper(sanitize_input($data['cloned_test_name'])),
                'no_of_questions' => sanitize_input($row_test_details['no_of_questions']),
                'total_marks' => sanitize_input($row_test_details['total_marks']),
                'duration' => sanitize_input($duration),
                'status' => sanitize_input($row_test_details['status']),
                'institute_id' =>  sanitize_input($institute_id),
                'package_id' => sanitize_input($classroom),
                'start_date' => DefaultTimezone($data['start_date']),
                'end_date' => DefaultTimezone($data['end_date']),
                'test_ui' => sanitize_input($row_test_details['test_ui']),
                'random_questions' => sanitize_input($row_test_details['random_questions']),
                'show_result' => sanitize_input($row_test_details['show_result']),
                'time_constraint' => sanitize_input($row_test_details['time_constraint']),
                'student_time_constraint' => sanitize_input($row_test_details['student_time_constraint']),
                'round_marks' => sanitize_input($row_test_details['round_marks']),
                'random_pool' => sanitize_input($row_test_details['random_pool']),
                'exam_conduction' => sanitize_input($row_test_details['exam_conduction']),
                'omr_template' => sanitize_input($row_test_details['omr_template'])
            ];

            $db->table('test')->insert($test_data);
            // Get new inserted test ID
            $cloned_test_id = $db->insertID();
            array_push($new_cloned_test_ids, $cloned_test_id);


            // Insert into the new test package mapping table for test-multiclassroom mapping logic
            $test_package_map_data = [
                'package_id' => sanitize_input($classroom),
                'test_id' => sanitize_input($cloned_test_id)
            ];
            $db->table('test_package_map')->insert($test_package_map_data);
        }



        // Fetch the original test's questions mapping
        $sql_for_test_questions = $db->query("SELECT * FROM test_questions_map WHERE test_id='$original_test_id' AND question_disabled = 0");
        $result = $sql_for_test_questions->getResultArray();

        foreach ($result as $row) {
            // Loop through all selected tests
            foreach ($new_cloned_test_ids as $new_cloned_test_id) {
                // Mapping the new test id to the question id
                $test_question_map_data = [
                    'test_id' => sanitize_input($new_cloned_test_id),
                    'question_id' => sanitize_input($row['question_id']),
                    'question_number' => sanitize_input($row['question_number']),
                    'section' => sanitize_input($row['section']),
                    'weightage' => sanitize_input($row['weightage']),
                    'negative_marks' => sanitize_input($row['negative_marks']),
                    'question_disabled' => sanitize_input($row['question_disabled']),
                ];
                $db->table('test_questions_map')->insert($test_question_map_data);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Test Cloned",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $cloned_test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/




    /**
     * Clone Test to another institute 
     *
     * @param Array $data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function clone_test_super_admin(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $original_test_id = sanitize_input($data['original_test_id']);
        $institute_id = sanitize_input($data['institute_id']);
        $package_id = sanitize_input($data['package_id']);

        // Fetch the original test params    
        $row_test_details = $this->get_test_details($original_test_id);
        $duration = $row_test_details['duration'];
        if (empty($duration)) {
            $duration = 'NULL';
        }
        $test_data = [
            'test_name' => strtoupper(sanitize_input($row_test_details['test_name'])),
            'no_of_questions' => sanitize_input($row_test_details['no_of_questions']),
            'total_marks' => sanitize_input($row_test_details['total_marks']),
            'duration' => sanitize_input($duration),
            'status' => sanitize_input($row_test_details['status']),
            'institute_id' =>  sanitize_input($institute_id),
            'start_date' => $row_test_details['start_date'],
            'end_date' => $row_test_details['end_date'],
            'test_ui' => sanitize_input($row_test_details['test_ui']),
            'random_questions' => sanitize_input($row_test_details['random_questions']),
            'show_result' => sanitize_input($row_test_details['show_result']),
            'time_constraint' => sanitize_input($row_test_details['time_constraint']),
            'student_time_constraint' => sanitize_input($row_test_details['student_time_constraint']),
            'round_marks' => sanitize_input($row_test_details['round_marks']),
            'random_pool' => sanitize_input($row_test_details['random_pool']),
            'package_id' =>  sanitize_input($package_id)
        ];

        $db->table('test')->insert($test_data);
        // Get new inserted test ID
        $cloned_test_id = $db->insertID();


        // Insert into the new test package mapping table for test-multiclassroom mapping logic
        $test_package_map_data = [
            'package_id' => sanitize_input($package_id),
            'test_id' => sanitize_input($cloned_test_id)
        ];
        $db->table('test_package_map')->insert($test_package_map_data);

        // Fetch the original test's questions mapping
        $sql_for_test_questions = $db->query("SELECT * FROM test_questions_map WHERE test_id='$original_test_id' AND question_disabled = 0");
        $result = $sql_for_test_questions->getResultArray();

        foreach ($result as $row) {

            $test_question_map_data = [
                'test_id' => $cloned_test_id,
                'question_id' => $row['question_id'],
                'question_number' => $row['question_number'],
                'section' => $row['section'],
                'weightage' => $row['weightage'],
                'negative_marks' => $row['negative_marks'],
                'question_disabled' => $row['question_disabled'],
            ];
            $db->table('test_questions_map')->insert($test_question_map_data);
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Test Cloned to another institute",
                'super_admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/



    /**
     * Undo Deleted Test
     *
     * @param Array $data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function unable_deleted_test(array $data)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        $test_id = $data['test_id'];

        $update_array = array(
            'status' => 'Active',
            'updated_by_admin' => decrypt_cipher(session()->get('login_id'))
        );

        $db->table('test')->update($update_array, ['test_id' => $test_id]);

        $db->transComplete();
        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Unabled Test",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /********************************************************/


    /**
     * Archive Test Info 
     *
     * @param Array $data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function archive_test_info(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $test_data = [
            'status' => 'ARCHIVED',
            'updated_by_admin' => NULL
        ];

        $id = sanitize_input(decrypt_cipher($data['test_id']));
        $db->table('test')->update($test_data, ['test_id' => $id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Archived Test",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Upload Test Bulk Solution Images
     *
     * @param Array $data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_bulk_solutions_images(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $test_data = [
            'status' => 'ARCHIVED',
            'updated_by' => NULL
        ];

        $id = sanitize_input(decrypt_cipher($data['test_id']));
        $db->table('test')->update($test_data, ['test_id' => $id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Added Test Bulk Solutions",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Add Test Video Solutions
     *
     * @param Array $data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_test_video_solutions(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $video_solution_data = [
            'video_name' => sanitize_input($data['video_name']),
            'video_url' => getEmbedUrl(sanitize_input($data['video_url'])),
            'institute_id' => sanitize_input($data['institute_id']),
            'test_id' => sanitize_input($data['test_id']),
            'type' => 'SOLUTION'
        ];

        $db->table('video_lectures')->insert($video_solution_data);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Added Video Solutions",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Delete Test Video Solutions
     *
     * @param Array $data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_test_video_solutions($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();


        $video_solution_data = [
            'is_disabled' => '1'
        ];

        $id = sanitize_input(decrypt_cipher($data['video_id']));
        $db->table('video_lectures')->update($video_solution_data, ['id' => $id]);


        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Deleted Video",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/




    /**
     * Add Bulk Answer Key
     *
     * @param Array $data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_bulk_answer_key(array $data)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        $question_ids_array = $data['question_id'];
        foreach ($question_ids_array as $key => $question_id) {
            $correct_answer_selected = "";
            if (isset($data['Que_correct_ans_' . $question_id])) {
                if (gettype($data['Que_correct_ans_' . $question_id]) == "array") {
                    $correct_answer_selected = $data['Que_correct_ans_' . $question_id];
                } else {
                    $correct_answer_selected = $data['Que_correct_ans_' . $question_id];
                }
            }

            // Checking type of the correct answer 
            // Whether string or array
            if (gettype($correct_answer_selected) === "string" || gettype($correct_answer_selected) === "integer" || gettype($correct_answer_selected) === "double") {
                $correct_answer_selected = str_replace(' ', '', $correct_answer_selected);
            } elseif (gettype($correct_answer_selected) === "array") {
                // if correct answer is multiselect, then convert into string and save
                $correct_answer_selected = implode(",", $correct_answer_selected);
            }

            $update_array = array(
                'correct_answer' => $correct_answer_selected
            );

            $db->table('test_questions')->update($update_array, ['id' => $question_id]);
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Test Answer Key",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/



    /**
     * Import Answer Key Excel
     *
     * @param Array $data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function import_excel_answer_key(array $data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $import_test_id = decrypt_cipher($data['import_test_id']);
        $correct_anwser_format = '';
        if(isset($data['correct_anwser_format_excel'])){
            $correct_anwser_format = $data['correct_anwser_format_excel'];
        }

        $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $helper = new Sample();

        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return false;
        }

        // Create new Spreadsheet object
        // $spreadsheet = new Spreadsheet();

        $spreadSheet = $Reader->load($data['file_url']);
        $excelSheet = $spreadSheet->getActiveSheet();
        $spreadSheetAry = $excelSheet->toArray();
        $sheetCount = count($spreadSheetAry);

        //Looping through the file
        for ($i = 1; $i <= $sheetCount; $i++) {

            $question_number = "";
            if (isset($spreadSheetAry[$i][0])) {
                $question_number = $spreadSheetAry[$i][0];
            }

            if ($question_number != "" && !empty($question_number)) {

               
                $correct_answer = format_option($spreadSheetAry[$i][1],$correct_anwser_format);

                $query = $db->query("SELECT question_id 
            FROM test_questions_map 
            WHERE test_id = $import_test_id 
            AND question_disabled = 0 
            AND question_number = $question_number");
                $result = $query->getRow();
                if (!empty($result)) {
                    $question_id = $result->question_id;

                    $update_array = array(
                        'correct_answer' => $correct_answer
                    );

                    $db->table('test_questions')->update($update_array, ['id' => $question_id]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $test_details = $this->get_test_details($import_test_id);
            $test_name = $test_details['test_name'];
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Imported Excel for Answer Key in test " . $test_name,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $import_test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Delete Multiple Test Questions
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_multiple_questions($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        $questions_ids = sanitize_input($data['bulk_delete_question_ids']);
        $test_id = sanitize_input($data['test_id']);

        $sql = "UPDATE test_questions_map
        SET question_disabled = '1' 
        WHERE test_id= '$test_id' 
        AND question_id IN ($questions_ids) ";

        $db->query($sql);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $test_details = $this->get_test_details($test_id);
            $test_name = $test_details['test_name'];
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Deleted Multiple Questions in test " . $test_name,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Reset Test Question Numbers
     *
     * @param [type] $test_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function reset_test_question_numbers($test_id)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        $TestQuestionsMapModel = new TestQuestionsMapModel();
        // Fetch existing test questions sequence
        $result_for_que_map = $TestQuestionsMapModel->fetch_mapped_questions($test_id);
        // Map new question number sequence
        if (!empty($result_for_que_map)) {
            $index = 1;
            foreach ($result_for_que_map as $que_map) {
                if ($que_map['question_number'] != $index) {
                    $update_data['que_map_id'] = $que_map['id'];
                    $update_data['question_number'] = $index;
                    $TestQuestionsMapModel->update_test_question_sequence($update_data);
                }
                $index++;
            }
        }
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Test question numbers reset",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/



    public function add_offline_result(array $post_data)
    {
        $db = \Config\Database::connect();
        $db->transStart();



        $current_date = date('Y-m-d H:i:s');
        $response_arr = array();
        $test_id = sanitize_input($post_data['test_id']);
        $institute_id = sanitize_input($post_data['institute_id']);
        $row_data = $post_data['row_data'];

        $query = $db->query("SELECT question_id,question_number 
        FROM test_questions_map 
        WHERE test_id = '$test_id' 
        AND question_disabled = 0 
        ORDER BY question_number asc");

        $result_get_question_ids = $query->getResultArray();


        $result_get_question_arr = array();
        foreach ($result_get_question_ids as $row_get_question_id) :
            array_push($result_get_question_arr, $row_get_question_id);
        endforeach;

        $student_username = "";
        if (isset($row_data[0])) {
            $student_username = sanitize_input($row_data[0]);
        }

        $successful_count = 0;
        $errors_msgs = "";
        $response_arr['user_name'] = $student_username;
        $response_arr['student_name'] = "NOTFOUND";
        $omr_answer_sheet = NULL;
        if (isset($post_data['omr_check']) && $post_data['omr_check'] == "1") {
            $omr_answer_sheet = "https:".substr($row_data[5], strpos(sanitize_input($row_data[5]), ":") + 1);
        } 

        if (!empty($student_username)) {
            // The student username has some value and not empty
            $db_student_id = "";

            // Get the student ID based on student USERNAME
            $query_get_student_id = $db->query("SELECT student_login.student_id,student.name FROM student_login JOIN student on student.id = student_login.student_id WHERE student_login.username = '$student_username' AND student_login.institute_id = '$institute_id'");

            $result_get_student_id = $query_get_student_id->getRowArray();
            if (!empty($result_get_student_id)) {
                // Student found
                // Get the stuent ID and insert in the results table
                $row_get_student_id = $result_get_student_id;
                // Inserting into the associative array
                $db_student_id = $row_get_student_id['student_id'];
                $response_arr['student_name'] = $row_get_student_id['name'];
                $query_for_test_status =  $db->query("SELECT * FROM test_status WHERE test_id = '$test_id' AND student_id = '$db_student_id'");

                $result_for_test_status = $query_for_test_status->getRowArray();
                if (empty($result_for_test_status)) {
                    // Inserting in the Test Status table
                    $db->query("INSERT INTO test_status (test_id, student_id, status, submission_type, admin_submission_date,omr_answer_sheet) VALUES ('$test_id', '$db_student_id', 'COMPLETED', 'admin', '$current_date', '$omr_answer_sheet')");
                } else {
                    // Updating the Test Status table
                    $db->query("UPDATE test_status SET status = 'COMPLETED', submission_type = 'admin', admin_submission_date = '$current_date', omr_answer_sheet = '$omr_answer_sheet' WHERE  test_id = '$test_id' AND student_id = '$db_student_id' ");
                }
            }



            if (!empty($db_student_id)) {
                if (isset($post_data['omr_check']) && $post_data['omr_check'] == "1") {
                    $j = 7;
                } else {
                    $j = 2;
                }

                if (!empty($result_get_question_arr)) {

                    foreach ($result_get_question_arr as $row_get_question_id) {

                        $j++;

                        // Inserting into the associative array
                        $question_number  = $row_get_question_id['question_number'];
                        $questions_found_array[$question_number] = $row_get_question_id['question_number'];

                        $db_question_id = $row_get_question_id['question_id'];

                        $selected_option = sanitize_input($row_data[$j]);

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
                        $query_check_test_result =   $db->query("SELECT option_selected FROM test_result WHERE test_id = '$test_id' AND student_id = '$db_student_id' AND question_id = '$db_question_id' ");

                        $result_check_test_result = $query_check_test_result->getRowArray();
                        if (!empty($result_check_test_result)) {

                            // Test Result Update
                            if ($db->query("UPDATE test_result SET option_selected = '$option_selected_formatted' WHERE test_id = '$test_id' AND student_id = '$db_student_id' AND question_id = '$db_question_id'")) {

                                $successful_count++;
                            } else {
                                $errors_msgs = $errors_msgs . " Test Result Update failed for student=$student_username for question=$question_number";
                            }
                        } else {
                            // Inserting in the database
                            // Test Result Insert
                            if (
                                $db->query("INSERT INTO test_result (test_id, student_id, question_id, option_selected, flagged) VALUES ('$test_id', '$db_student_id', '$db_question_id', '$option_selected_formatted','0')")

                            ) {
                                $successful_count++;
                            } else {
                                $errors_msgs = $errors_msgs . " Test Result Insert failed for student=$student_username for question=$question_number";
                            }
                        }
                    }
                }
            }
        }

        $response_arr['no_of_successful_entries'] = $successful_count;
        $response_arr['errors'] = $errors_msgs;

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return $response_arr;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Added offline result for the test",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
                'test_id' => $test_id
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return $response_arr;
        }
    }

    /*****************************************************************
     *################ END OF SUBMIT FUNCTIONS ###########*
     *****************************************************************/



    /*****************************************************************
     *############ START OF FUNCTIONS - RETURNS REPORT DATA  #########*
     *****************************************************************/

    /**
     * Weekly Tests Count
     *
     * @param Integer $institute_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function weekly_tests_count($institute_id = "")
    {
        $db = \Config\Database::connect();
        $append_string = "";
        $institute_check_condn = "";
        if ($institute_id != "") {
            $append_string = $this->apply_classroom_filter();
            $institute_check_condn = " AND institute_id = :institute_id:";
        }

        $sql_weekly_tests_count = "SELECT COUNT(test_id) no_of_tests,WEEK(start_date) as week_date
        FROM test 
        $append_string
        WHERE test.status != 'ARCHIVED' 
        AND start_date BETWEEN (NOW() - INTERVAL 5 WEEK) AND NOW() 
        $institute_check_condn
        $append_string
        GROUP by WEEK(start_date),YEAR(start_date)  ";

        $query = $db->query($sql_weekly_tests_count, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/


    /**
     * Load Classrooms Tests
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function load_classrooms_tests(array $data)
    {
        $db = \Config\Database::connect();

        $instituteID = $data['institute_id'];
        $append_string = "";

        if (isset($data['classrooms'])) {
            $classrooms_str = implode(",", $data['classrooms']);
            $append_string = " AND test_package_map.package_id IN ($classrooms_str) ";
        }

        $sql_for_test = $db->query("SELECT test.*, packages.*, questionsCount.questionsAdded , group_concat(packages.package_name) as package_list
        FROM test 
        LEFT JOIN test_package_map 
        ON test.test_id = test_package_map.test_id
        LEFT JOIN packages 
        ON packages.id = test_package_map.package_id
        LEFT JOIN (select count(*) as questionsAdded,test_id FROM test_questions_map WHERE question_disabled != 1 AND test_id in (select test_id from test where test.institute_id = '$instituteID')  group by test_id) as questionsCount on questionsCount.test_id = test.test_id
        where test.institute_id = '$instituteID' AND test.status != 'ARCHIVED' AND test_package_map.is_disabled = 0 
        $append_string
        GROUP BY test_package_map.test_id
        ORDER BY test.created_date DESC");
        $result = $sql_for_test->getResultArray();
        return $result;
    }
    /*******************************************************/


    /*****************************************************************
     *############ END OF FUNCTIONS - RETURNS REPORT DATA  #########*
     *****************************************************************/


    /*****************************************************************
     *##### START OF FUNCTIONS - SUPER ADMIN ALL INSTITUTES DATA  #####*
     *****************************************************************/

    /**
     * Get Total Todays Test Total Student Count 
     *
     * @return Integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function todays_test_total_stu_count()
    {
        $db      = \Config\Database::connect();
        $sql_fetch_std_cnt_exam = "SELECT count(DISTINCT student_id) as std_cnt
        FROM student_institute 
        WHERE package_id in (SELECT package_id FROM `test` where DATE(start_date) = CURDATE()) AND student_institute.is_disabled = 0";
        $query = $db->query($sql_fetch_std_cnt_exam);
        $result = $query->getRowArray();
        if (!empty($result)) {
            return $result['std_cnt'];
        } else {
            return 0;
        }
    }
    /********************************************************/


    /**
     * Get Total tomorrow Test Count 
     *
     * @return Integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function tomorrows_test_count()
    {
        $db  = \Config\Database::connect();
        $sql_fetch_tomorrow_exam = "SELECT COUNT(*) as tomorrow_test_cnt 
        FROM test 
        WHERE DATE(start_date) = (CURDATE() + 1)";
        $query = $db->query($sql_fetch_tomorrow_exam);
        $result = $query->getRowArray();
        if (!empty($result)) {
            return $result['tomorrow_test_cnt'];
        } else {
            return 0;
        }
    }
    /********************************************************/


    /**
     * Get Total Tomorrow Test Student Count
     *
     * @return Integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function tomorrows_exam_stu_cnt()
    {
        $db  = \Config\Database::connect();

        $sql_fetch_tomorrow_exam_std_cnt = "SELECT count(DISTINCT student_id) as tomorrows_exam_stu_cnt
        FROM student_institute 
        WHERE package_id in (SELECT package_id FROM `test` where DATE(start_date) = (CURDATE() +1)) AND student_institute.is_disabled = 0";
        $query = $db->query($sql_fetch_tomorrow_exam_std_cnt);
        $result = $query->getRowArray();
        if (!empty($result)) {
            return $result['tomorrows_exam_stu_cnt'];
        } else {
            return 0;
        }
    }
    /********************************************************/


    /**
     * Get Total Planned Test Count 
     *
     * @return Integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function total_planned_test()
    {
        $db = \Config\Database::connect();
        $sql_fetch_planned_test = "SELECT COUNT(*) as planned_test_cnt 
        FROM test 
        WHERE start_date > (CURDATE() + 1)";
        $query = $db->query($sql_fetch_planned_test);
        $result = $query->getRowArray();
        if (!empty($result)) {
            return $result['planned_test_cnt'];
        } else {
            return 0;
        }
    }
    /********************************************************/


    /**
     * Test attempts date wise count
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function test_attempts_date_wise_count()
    {
        $db = \Config\Database::connect();
        $sql_fetch_todays_exam = "SELECT count(*) cnt,DATE(created_date) created_date
        FROM test_status 
        GROUP BY DATE(created_date) 
        ORDER BY created_date DESC 
        LIMIT 30 ";
        $query = $db->query($sql_fetch_todays_exam);
        $result = $query->getRowArray();
        return $result;
    }
    /********************************************************/

    /**
     * Total Student Count for Planned Test 
     *
     * @return Integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function total_stu_planned_test()
    {
        $db = \Config\Database::connect();
        $sql_fetch_stu_cnt_planned_test = "select count(DISTINCT student_id) exp_stu_cnt from student_institute where package_id in (SELECT package_id FROM `test` where DATE(start_date) = (CURDATE() +1)) AND student_institute.is_disabled = 0";
        $query = $db->query($sql_fetch_stu_cnt_planned_test);
        $result = $query->getRowArray();
        if (!empty($result)) {
            return $result['exp_stu_cnt'];
        } else {
            return 0;
        }
    }
    /********************************************************/




    /**
     * Get Submission Count 
     *
     * @return Integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function submission_count()
    {
        $db = \Config\Database::connect();
        $sql_fetch_submission_test = "SELECT count(*) as submission_test_cnt
        FROM test_status";
        $query = $db->query($sql_fetch_submission_test);
        $result = $query->getRowArray();
        if (!empty($result)) {
            return $result['submission_test_cnt'];
        } else {
            return 0;
        }
    }
    /********************************************************/


    /**
     * Get Ongoing exam Student Count
     *
     * @return Integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function total_stu_ongoing_test()
    {
        $db = \Config\Database::connect();
        $sql_fetch_stu_cnt_onging_test = "SELECT count(*) as cnt FROM test_status where status = 'STARTED' AND updated_date > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        $query = $db->query($sql_fetch_stu_cnt_onging_test);
        $result = $query->getRowArray();
        if (!empty($result)) {
            return $result['cnt'];
        } else {
            return 0;
        }
    }
    /********************************************************/

    /*****************************************************************
     *###### END OF FUNCTIONS - SUPER ADMIN ALL INSTITUTES DATA  #######*
     *****************************************************************/
}
