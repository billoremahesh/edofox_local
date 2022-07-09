<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailSmsLogsModel extends Model
{
    /**
     *  Fetch Email/ SMS logs data
     *
     * @param array $requestData
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function email_sms_logs(array $requestData)
    {

        $instituteID = $requestData['institute_id'];
        $searchQuery = "";
        $_SESSION['email_sms_logs_session_search'] = "";
        if (!empty($requestData['searchbox'])) {
            $searched_term = sanitize_input($requestData['searchbox']);
            // Saving value in session for loading on reload of page
            $_SESSION['email_sms_logs_session_search'] = $searched_term;
            $searchQuery .= " AND ( 
            student.name LIKE '%" . $searched_term . "%' OR
            test.test_name LIKE '%" . $searched_term . "%' OR 
            video_lectures.video_name LIKE '%" . $searched_term . "%'
            )";
        }

        if (!empty($requestData['channel_filter'])) {
            $channel_filter = $requestData['channel_filter'];
            $searchQuery .= " AND email_sms_logs.channel = '$channel_filter' ";
        }

        $sql = "SELECT email_sms_logs.*,student.name,IF(test.test_name IS NULL,video_lectures.video_name,test.test_name) module
        FROM email_sms_logs
        join student on email_sms_logs.student_id = student.id
        LEFT JOIN test
        ON email_sms_logs.exam_id = test.test_id
        LEFT JOIN video_lectures
        ON email_sms_logs.classwork_id = video_lectures.id
        where email_sms_logs.institute_id = '$instituteID' $searchQuery ";

        $totalquery = $this->db->query($sql);
        $resultTotalData = $totalquery->getResultArray();
        $totalData = $totalDataFiltered = count($resultTotalData);

        // Result with filtered data with limit
        $sql .= " ORDER BY email_sms_logs.id desc LIMIT " . $requestData['start'] . " ," . $requestData['length'];

        $total_filtered_limit_query = $this->db->query($sql);
        $filter_result = $total_filtered_limit_query->getResultArray();

        $data = array();

        $i =  $requestData['start'] + 1;
        foreach ($filter_result as $row) {

            $nestedData = array();
            $nestedData[] = $i;
            $nestedData[] = $row["name"];
            $nestedData[] =  $row["sent_to"];
            $nestedData[] = $row["module"];
            $msg_text_wrap = htmlspecialchars($row["text"]);
            $nestedData[] = htmlspecialchars_decode($msg_text_wrap);
            $nestedData[] = $row["channel"];
            $nestedData[] = $row["status"];
            $nestedData[] = $row["created_date"];
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
