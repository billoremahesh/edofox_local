<?php

namespace App\Models;

use CodeIgniter\Model;

class InstituteModel extends Model
{

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }



    public function get_newly_created_institute_id()
    {
        $db = \Config\Database::connect();
        $sql_fetch_todays_exam = "SELECT institute.id 
        FROM institute order by id desc LIMIT 1";
        $query = $db->query($sql_fetch_todays_exam);
        $result = $query->getRowArray();
        return $result['id'];
    }

    /**
     * Get All Institutes Details - Json
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_all_institutes(array $requestData)
    {


        // Check Role
        $role_condition = "";
        if (isset($requestData['super_admin_role']) && isset($requestData['login_id'])) {
            $role = sanitize_input($requestData['super_admin_role']);
            $login_id = sanitize_input($requestData['login_id']);
            if ($role != 'Super Admin') {
                $role_condition = " AND institute.account_manager = '$login_id' ";
            }
        }

        $sql = "SELECT institute.*,studentCount.studentsRegistered total_active_students
        FROM institute
        left join (SELECT count(distinct student_id) as studentsRegistered,institute_id FROM student_institute Where student_institute.is_disabled = 0 group by institute_id)
        as studentCount on studentCount.institute_id = institute.id
        left join sales_details on sales_details.institute_id = institute.id
        where (sales_details.status is null OR sales_details.status != 'ARCHIVED')  
        $role_condition ORDER BY institute.institute_name ASC";
        $total_filtered_limit_query = $this->db->query($sql);
        $filter_result = $total_filtered_limit_query->getResultArray();

        $data = array();
        $i = 1;
        foreach ($filter_result as $row) {

            $nestedData = array();

            $instituteid = encrypt_string($row['id']);
            $nestedData[] = $i;
            $nestedData[] = $row["institute_name"];
            $nestedData[] = $row["total_active_students"];
            $nestedData[] = $row["alias_name"];
            $nestedData[] = $row["contact_number"];
            $nestedData[] = $row["storage_quota"];
            $nestedData[] = $row["expiry_date"];
            $nestedData[] = $row["status"];


            $update_institue_link = base_url('institutes/update_institute/' . $instituteid);

            $subscriptions_link = base_url('subscriptions/overview/' . $instituteid);

            $upgrade_institue_link = base_url('institutes/upgrade_institute/' . $instituteid);

            $update_institue_div = htmlspecialchars("<li class='drop_institues'><a class='drop_institues_links' href='$update_institue_link' >Update Details</a></li><li role='separator' class='dropdown-divider'></li><li class='drop_institues'><a  href='$subscriptions_link' class='drop_institues_links'>Subscriptions</a></li><li class='drop_institues'><a  href='$upgrade_institue_link' class='drop_institues_links'>Upgrade Institute</a></li>");

            //  External link
            $external_link_btn = htmlspecialchars("<li role='separator' class='dropdown-divider'></li><li class='drop_institues'><a class='drop_institues_links'  onclick=" . "show_edit_modal('modal_div','external_link','/institutes/old_admin_indirect_login/" . $instituteid . "');" . ">Old Institute Pannel</a></li><li role='separator' class='dropdown-divider'></li><li class='drop_institues'><a class='drop_institues_links' onclick=" . "show_edit_modal('modal_div','external_link','/institutes/new_admin_indirect_login/" . $instituteid . "');" . ">New Institute Pannel</a></li>");

            $nestedData[] = htmlspecialchars_decode("<div class='dropdown'><button class='btn btn-default dropdown-toggle more_option_button' type='button' id='classroomDropdownMenu' data-bs-toggle='dropdown'  data-bs-auto-close='outside'  aria-expanded='false'><i class='fa fa-ellipsis-h' aria-hidden='true'></i>
            </button><ul class='dropdown-menu dropdown-menu-end' aria-labelledby='classroomDropdownMenu'>$update_institue_div $external_link_btn</ul></div>");
            $data[] = $nestedData;
            $i++;
        }

        $json_data = $data;
        return $json_data;
    }
    /*******************************************************/



    /**
     * Get Institutes Count
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_institutes_count()
    {
        $db      = \Config\Database::connect();
        $sql_fetch_todays_exam = "SELECT COUNT(institute.id) as cnt 
        FROM institute   
        left join sales_details on sales_details.institute_id = institute.id
        where (sales_details.status is null OR sales_details.status != 'ARCHIVED') ";
        $query = $db->query($sql_fetch_todays_exam);
        $result = $query->getRowArray();
        return $result['cnt'];
    }
    /*******************************************************/






    /*******************************************************/
    /**
     * Get Institute Details
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     * @return Array
     */
    public function get_institute_details($institute_id)
    {

        $sql = "SELECT * FROM institute
        WHERE institute.id = :institute_id: ";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        if ($query->getNumRows() == 1) {
            return $query->getRowArray();
        } else {
            return false;
        }
    }
    /*******************************************************/


    /**
     * Registered Count
     *
     * @param [type] $institute_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function registeredCount($institute_id)
    {

        $sql = "select count(distinct student_id) as registeredCount
        FROM student_login where institute_id = :institute_id: 
        AND (student_login.student_access IS NULL or student_login.student_access != 'Deleted' ) ";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        $result = $query->getRowArray();
        if (!empty($result)) {
            return $result['registeredCount'];
        } else {
            return 0;
        }
    }
    /*******************************************************/





    /**
     * Update Institute details
     *
     * @param [Array] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_institute_details($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        if (isset($data['institute_id']) && !empty($data['institute_id'])) {
            $id = sanitize_input($data['institute_id']);
        } else {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to update instiute details but failed due to institute id missing', $log_info);
        }

        if (isset($data['institute_name']) && !empty($data['institute_name'])) {
            $update_data['institute_name'] = strtoupper(sanitize_input($data['institute_name']));
        } else {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to update instiute details but failed due to institute name missing', $log_info);
        }

        if (isset($data['alias_name']) && !empty($data['alias_name'])) {
            $update_data['alias_name'] = strtoupper(sanitize_input($data['alias_name']));
        }

        if (isset($data['contact']) && !empty($data['contact'])) {
            $update_data['contact_number'] = sanitize_input($data['contact']);
        }

        if (isset($data['email']) && !empty($data['email'])) {
            $update_data['email'] = strtoupper(sanitize_input($data['email']));
        }

        if (isset($data['institute_address']) && !empty($data['institute_address'])) {
            $update_data['address'] = sanitize_input($data['institute_address']);
        }

        if (isset($data['storage_quota']) && !empty($data['storage_quota'])) {
            $update_data['storage_quota'] = sanitize_input($data['storage_quota']);
        }

        if (isset($data['institute_gst_no']) && !empty($data['institute_gst_no'])) {
            $update_data['gst_no'] = sanitize_input($data['institute_gst_no']);
        }


        if (isset($data['web_url']) && !empty($data['web_url'])) {
            $update_data['web_url'] = sanitize_input($data['web_url']);
        }

        if (isset($data['app_url']) && !empty($data['app_url'])) {
            $update_data['app_url'] = sanitize_input($data['app_url']);
        }

        if (isset($data['app_version']) && !empty($data['app_version'])) {
            $update_data['app_version'] = sanitize_input($data['app_version']);
        }


        if (isset($data['latitude']) && !empty($data['latitude'])) {
            $update_data['latitude'] = sanitize_input($data['latitude']);
        }

        if (isset($data['longitude']) && !empty($data['longitude'])) {
            $update_data['longitude'] = sanitize_input($data['longitude']);
        }


        if (isset($data['account_manager']) && !empty($data['account_manager'])) {
            $update_data['account_manager'] = sanitize_input($data['account_manager']);
        }


        if (isset($data['video_streaming_condition']) && !empty($data['video_streaming_condition'])) {
            $update_data['video_constraint'] = sanitize_input($data['video_streaming_condition']);
        }

        if (isset($data['timezone']) && !empty($data['timezone'])) {
            $update_data['timezone'] = sanitize_input($data['timezone']);
        }

        if (isset($data['whatsapp_credits']) && !empty($data['whatsapp_credits'])) {
            $update_data['whatsapp_credits'] = sanitize_input($data['whatsapp_credits']);
        }

        $db->table('institute')->update($update_data, ['id' => $id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to update instiute details but failed', $log_info);

            return false;
        } else {
            return true;
        }
    }
    /*******************************************************/


    /**
     * Update Institute Module Feature like Exam Module, DLP, LIVE , Support 
     *
     * @param [Array] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_institute_module_feature(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        if (isset($data['institute_id']) && !empty($data['institute_id'])) {
            $id = sanitize_input($data['institute_id']);
        } else {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to update instiute details but failed due to institute id missing', $log_info);
        }



        if (isset($data['exam'])) {
            $update_data['exam'] = sanitize_input($data['exam']);
        }

        if (isset($data['live'])) {
            $update_data['live'] = sanitize_input($data['live']);
        }

        if (isset($data['dlp'])) {
            $update_data['dlp'] = sanitize_input($data['dlp']);
        }

        if (isset($data['support'])) {
            $update_data['support'] = sanitize_input($data['support']);
        }


        if (isset($data['expiry_date']) && !empty($data['expiry_date'])) {
            $update_data['expiry_date'] = sanitize_input($data['expiry_date']);
        }


        if (isset($data['max_students'])) {
            $update_data['max_students'] = sanitize_input($data['max_students']);
        }


        if (isset($data['max_concurrent_interactive'])) {
            $update_data['max_concurrent_interactive'] = sanitize_input($data['max_concurrent_interactive']);
        }

        if (isset($data['max_concurrent_live'])) {
            $update_data['max_concurrent_live'] = sanitize_input($data['max_concurrent_live']);
        }

        if (isset($data['storage_quota'])) {
            $update_data['storage_quota'] = sanitize_input($data['storage_quota']);
        }

        $db->table('institute')->update($update_data, ['id' => $id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to update instiute details but failed', $log_info);

            return false;
        } else {
            return true;
        }
    }
    /*******************************************************/




    /**
     * Search Institutes
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function search_institutes(array $data)
    {

        $db = \Config\Database::connect();
        $search = $data['search'];
        $sql = "SELECT institute.*
        FROM institute
        left join sales_details on sales_details.institute_id = institute.id
        where (sales_details.status is null OR sales_details.status != 'ARCHIVED')
        and institute_name LIKE '%$search%' 
        ORDER BY institute_name ASC";

        $query = $db->query($sql, [
            'search' => sanitize_input($search)
        ]);

        $result = $query->getResultArray();
        $formatted_data = array();

        $formatted_data['incomplete_results'] = false;
        $formatted_data['items'] = array();
        $cnt = 0;
        foreach ($result as $institute_data) {
            $institute_object = array();
            $institute_object['id'] = $institute_data['id'];
            $institute_object['name'] = $institute_data['institute_name'];
            array_push($formatted_data['items'], $institute_object);
            $cnt++;
        }

        $formatted_data['total_count'] = $cnt;
        return json_encode($formatted_data);
    }
    /*******************************************************/


    /**
     * Institute Classroom
     *
     * @param [type] $institute_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function institute_classrooms($institute_id)
    {
        $sql = "SELECT * 
        FROM packages 
        WHERE is_disabled='0' 
        AND institute_id = :institute_id: 
        ORDER BY package_name ASC";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/



    /**
     * Get Timezones 
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_timezones()
    {
        $db = \Config\Database::connect();
        $sql = "SELECT * 
        FROM timezones";
        $query = $db->query($sql);
        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/
}
