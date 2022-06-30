<?php

namespace App\Models;

use CodeIgniter\Model;

class NoticesModel extends Model
{


    public function get_all_notices(array $requestData)
    {


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
            $orderby = "notices.start_date";
        } else {
            $orderby = $columns_valid[$col];
        }


        $filter_condn = "";
        if (isset($requestData['start_date']) && !empty($requestData['start_date']) && isset($requestData['end_date']) && !empty($requestData['end_date'])) {
            $start_date = sanitize_input($requestData['start_date']);
            $end_date = sanitize_input($requestData['end_date']);
            $filter_condn = " AND start_date <= $start_date AND end_date >= $end_date ";
        }

        $institute_check_condn = "";
        if (isset($requestData['institute_id']) && !empty($requestData['institute_id'])) {
            $instituteID = sanitize_input($requestData['institute_id']);
            $institute_check_condn = " AND (institute_id = '$instituteID' OR institute_id IS NULL )";
        }



        $searchQuery = "";

        if (!empty($requestData['search']['value'])) {
            $searched_term = sanitize_input($requestData['search']['value']);
            $searchQuery .= " AND ( 
       notices.title LIKE '%" . $searched_term . "%'
       )";
        }


        $sql = "SELECT notices.*
   FROM notices
   WHERE  status= 'A' $institute_check_condn ";

        $totalquery = $this->db->query($sql);
        $resultTotalData = $totalquery->getResultArray();
        $totalData = count($resultTotalData);
        $sql .= " $searchQuery $filter_condn ";
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

            $notice_id = encrypt_string($row['id']);


            $nestedData[] = $i;
            $nestedData[] = $row["title"];
            $nestedData[] = $row["start_date"];
            $nestedData[] = $row["end_date"];


            // View Notice 
            $view_notice_url = base_url() . '/notices/view_notice/' . $notice_id;
            $view_notice = htmlspecialchars("<a class='btn btn-sm' href='" . $view_notice_url . "' data-bs-toggle='tooltip' title='View Notice'><i class='material-icons material-icon-small'>visibility</i></a>");

            //   Update & Disable Option
            $update_btn = "";
            $delete_btn = "";



            // Update Option
            $update_btn = htmlspecialchars("<a class='btn btn-sm data-bs-toggle='tooltip' title='Update Notice details' onclick=" . "show_edit_modal('modal_div','update_notice_modal','notices/update_notice_modal/" . $notice_id . "');" . "><i class='material-icons material-icon-small'>edit</i></a>");

            // Disable Option
            $delete_btn = htmlspecialchars("<a class='btn btn-sm data-bs-toggle='tooltip' title='Disable Notice' onclick=" . "show_edit_modal('modal_div','delete_notice_modal','notices/delete_notice_modal/" . $notice_id . "');" . "><i class='material-icons material-icon-small text-danger'>delete</i></a>");


            $nestedData[] = htmlspecialchars_decode("$view_notice $update_btn $delete_btn");




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


    /**
     * Get Notice Details
     *
     * @param [Integer] $notice_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_notice_details($notice_id)
    {
        $sql = "SELECT notices.*
        FROM notices 
        WHERE id = :notice_id: ";

        $query = $this->db->query($sql, [
            'notice_id' => sanitize_input($notice_id)
        ]);

        return $query->getRowArray();
    }
    /*******************************************************/




    /**
     * Add Notice 
     *
     * @param [Array] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_notice($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        if (isset($data['title']) && !empty($data['title'])) {
            $notice_data['title'] =  strtoupper(sanitize_input($data['title']));
        }

        if (isset($data['description']) && !empty($data['description'])) {
            $notice_data['description'] =  strtoupper(sanitize_input($data['description']));
        }

        if (isset($data['start_date']) && !empty($data['start_date'])) {
            $notice_data['start_date'] =  strtoupper(sanitize_input($data['start_date']));
        }

        if (isset($data['end_date']) && !empty($data['end_date'])) {
            $notice_data['end_date'] =  strtoupper(sanitize_input($data['end_date']));
        }


        $db->table('notices')->insert($notice_data);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            return true;
        }
    }
    /*******************************************************/




    /**
     * Update Notice details
     *
     * @param [Array] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_notice_details($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        if (isset($data['title']) && !empty($data['title'])) {
            $notice_data['title'] =  strtoupper(sanitize_input($data['title']));
        }

        if (isset($data['description']) && !empty($data['description'])) {
            $notice_data['description'] =  strtoupper(sanitize_input($data['description']));
        }

        if (isset($data['start_date']) && !empty($data['start_date'])) {
            $notice_data['start_date'] =  strtoupper(sanitize_input($data['start_date']));
        }

        if (isset($data['end_date']) && !empty($data['end_date'])) {
            $notice_data['end_date'] =  strtoupper(sanitize_input($data['end_date']));
        }

        $notice_data['updated_at'] =  date('Y-m-d H:i:s');
        $id = decrypt_cipher($data['notice_id']);
        $db->table('notices')->update($notice_data, ['id' => $id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            return true;
        }
    }
    /*******************************************************/


    /**
     * Disable Notice 
     *
     * @param [Array] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function disable_notice($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $notice_data['status'] =  'D';
        $notice_data['updated_at'] =  date('Y-m-d H:i:s');
        $id = decrypt_cipher($data['notice_id']);
        $db->table('notices')->update($notice_data, ['id' => $id]);

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
