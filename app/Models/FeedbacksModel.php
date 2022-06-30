<?php

namespace App\Models;

use CodeIgniter\Model;

class FeedbacksModel extends Model
{


    public function load_feedbacks(array $requestData)
    {
        $db = \Config\Database::connect();

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
            $orderby = "feedback_rating.created_date";
        } else {
            $orderby = $columns_valid[$col];
        }



        $searchQuery = "";

        if (!empty($requestData['search']['value'])) {
            $searched_term = sanitize_input($requestData['search']['value']);
            $searchQuery .= " AND ( 
            admin.name LIKE '%" . $searched_term . "%'
            )";
        }


        $sql = "SELECT feedback_rating.*,admin.name as admin_name 
        FROM feedback_rating 
        LEFT JOIN admin
        ON admin.id = feedback_rating.admin_id ";

        $totalquery = $db->query($sql);
        $resultTotalData = $totalquery->getResultArray();
        $totalData = count($resultTotalData);


        $sql .= " $searchQuery ";
        $total_filtered_query = $db->query($sql);
        $resultTotalDataFiltered = $total_filtered_query->getResultArray();
        $totalDataFiltered = count($resultTotalDataFiltered);

        // Result with filtered data with limit
        $sql .= " ORDER BY $orderby $dir LIMIT " . $requestData['start'] . " ," . $requestData['length'];

        $total_filtered_limit_query = $db->query($sql);
        $filter_result = $total_filtered_limit_query->getResultArray();

        $data = array();


        foreach ($filter_result as $row) {

            $nestedData = array();

            $admin_name = $row["admin_name"];
            $feedback = $row["feedback"];
            $rating = $row["rating"];

            $created_date = changeDateTimezone($row["created_date"]);
            $feedback_encode = htmlspecialchars("<div class='card shadow p-2' style='width:100%;'><div>$admin_name</div><div>$rating</div>
            <div>$feedback</div><div class='d-flex flex-row-reverse'><p class='text-end'>$created_date</p></div></div>");
            $nestedData[] = htmlspecialchars_decode($feedback_encode);

            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalDataFiltered),
            "data"            => $data
        );

        return $json_data;
    }

    public function add_feedback(array $data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $rating = sanitize_input($data['rating']);

        // Device Information
        $device_info = $_SERVER['HTTP_USER_AGENT'];
        if (isset($data['device_info'])) {
            $device_info = sanitize_input($data['device_info']);
        }

        $feedback_data = [
            'rating' => $rating,
            'device' => $device_info,
        ];

        if (isset($data['feedback_text'])) {
            $feedback_data['feedback'] =   sanitize_input($data['feedback_text']);
        }

        if (isset($data['admin_id'])) {
            $feedback_data['admin_id'] =   sanitize_input($data['admin_id']);
        }

        if (isset($data['module'])) {
            $feedback_data['module'] =   sanitize_input($data['module']);
        }


        $db->table('feedback_rating')->insert($feedback_data);


        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            return true;
        }
    }
    /*******************************************************/
}
