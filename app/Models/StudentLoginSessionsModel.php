<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentLoginSessionsModel extends Model
{


    /**
     * Students login counts - Needed for render graph
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function students_login_counts($institute_id = "")
    {
        $db = \Config\Database::connect();
        $institute_check_condn = "";
        if ($institute_id != "") {
            $institute_check_condn = " AND student_login.institute_id = :institute_id: ";
        }

        $sql_fetch_total_exam = "SELECT COUNT(DISTINCT student_login_sessions.student_id) cnt,date(student_login_sessions.created_date) login_date
        FROM student_login_sessions 
        JOIN student_login
        ON student_login.student_id = student_login_sessions.student_id
        WHERE (student_login.student_access = '' OR student_login.student_access is NULL) 
        AND student_login_sessions.created_date BETWEEN (NOW() - INTERVAL 1 WEEK) AND NOW()
        $institute_check_condn
        GROUP by date(student_login_sessions.created_date)";

        $query = $db->query($sql_fetch_total_exam, [
            'institute_id' => sanitize_input($institute_id)
        ]);
        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/






    /**
     * Student Live Login Sessions
     *
     * @param array $requestData
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_live_login_sessions(array $requestData)
    {

        $instituteID = $requestData['institute_id'];

        // Server Side Processing - ORDER BY and ASC,DESC
        $order = array();
        if (isset($requestData['order'])) {
            $order = $requestData['order'];
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
            $orderby = "student.name";
        } else {
            $orderby = $columns_valid[$col];
        }

        $searchQuery = "";
        $_SESSION['student_login_session_search'] = "";
        if (!empty($requestData['searchbox'])) {
            $searched_term = sanitize_input($requestData['searchbox']);
            // Saving value in session for loading on reload of page
            $_SESSION['student_login_session_search'] = $searched_term;
            $searchQuery .= " AND ( 
            student.name LIKE '%" . $searched_term . "%' OR
            student_login.username LIKE '%" . $searched_term . "%'
            )";
        }


        $sql = "SELECT student.name,student_login.username,student_login_sessions.ws_session_id,student_login_sessions.id,student_login_sessions.created_date,student_login_sessions.device_info,student_login_sessions.device_type,student_login_sessions.module 
        FROM student_login_sessions
        join student on student_login_sessions.student_id = student.id
        join student_login on student.id = student_login.student_id 
        where institute_id = '$instituteID' and is_live = 1 and is_disabled = 0 ";

        $totalquery = $this->db->query($sql);
        $resultTotalData = $totalquery->getResultArray();
        $totalData = count($resultTotalData);
        $sql .= " $searchQuery";
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
            // $row_id = encrypt_string($row['id']);
            $nestedData[] = $i;
            $nestedData[] = $row["name"];
            $nestedData[] =  $row["username"];
            $nestedData[] = $row["created_date"];
            $nestedData[] = $row["device_type"];
            $nestedData[] = $row["device_info"];
            $nestedData[] = $row["module"];
            // $delete_session_url =  base_url('students/delete_student_live_login_session/' . $row_id);
            // $dropdown_wrapper_code = htmlspecialchars("<a href='$delete_session_url'>Delete Token</a>");

            $dropdown_wrapper_code = htmlspecialchars("<input type='checkbox' name='student_sessions_check' onclick='selected_student_login_sessions();' class='student_sessions_check' value=" . $row['id'] . " >");

            $nestedData[] = htmlspecialchars_decode($dropdown_wrapper_code);

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
    /*******************************************************/
}
