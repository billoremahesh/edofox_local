<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\LinkRoutesModel;
use App\Models\LinkRoutesVisitsModel;

class UserActivityModel extends Model
{

    protected $table      = 'admin_activity';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['activity_log', 'created_date', 'module', 'admin_id', 'test_id', 'question_id', 'classroom_id', 'student_id', 'content_id', 'institute_id','super_admin_id'];


    /**
     * Main activity logging function
     *
     * @param string $action
     * @param array $arr
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function log(string $action, $arr = array())
    {

        switch ($action) {
            default:
                return;
            case 'logged_in':
                $msg = "{username} logged in";
                break;
            case 'login_failed':
                $msg = "Someone tried to login with username {username}";
                break;
            case 'logout':
                $msg = "{username} logged out";
                break;
            case 'user_forgotten':
                $msg = "{username} requested for new password";
                break;
            case 'user_registered':
                $msg = "{first_name} {last_name} registered with username: {username}";
                break;
            case 'added':
                $msg = "{username} added {item}";
                break;
            case 'modified':
                $msg = "{username} modified {item}";
                break;
            case 'deleted':
                $msg = "{username} deleted {item}";
                break;
            case 'page_access':
                $msg = "{username} accessed a page {uri}";
                break;
        }

        // Get IP address
        if (!isset($arr['ip'])) {
            $arr['ip'] = get_client_ip();
        }

        // Replace placeholders
        $username = isset($arr['username']) ? $arr['username'] : "";
        $first_name = isset($arr['first_name']) ? $arr['first_name'] : "";
        $last_name = isset($arr['last_name']) ? $arr['last_name'] : "";
        $item = isset($arr['item']) ? $arr['item'] : "";
        $uri = isset($arr['uri']) ? $arr['uri'] : "";

        // Replace Message Dynamic Data
        $search = array('{username}', '{first_name}', '{last_name}', '{item}', '{uri}');
        $replace = array($username, $first_name, $last_name, $item, $uri);
        $msg = str_replace($search, $replace, $msg);

        $this->add($action, $msg, $arr);
        return true;
    }
    /*******************************************************/




    /**
     * Insert log
     *
     * @param string $module
     * @param string $msg
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    private function add(string $module, string $msg, array $data = array())
    {
        $db = \Config\Database::connect();

        // Log Data
        $log_data = [
            'activity_log' => $msg,
            'created_date' => date('Y-m-d H:i:s'),
            'module' => $module,
            'ip_address' => $data['ip']
        ];

        if (isset($data['admin_id'])) {
            $log_data['admin_id'] = sanitize_input($data['admin_id']);
            // Check if link segment available
            if (isset($data['link_segment']) && !empty($data['link_segment'])) {
                $link_segment = sanitize_input($data['link_segment']);
                $LinkRoutesModel = new LinkRoutesModel();
                $link_route_data = $LinkRoutesModel->get_link_route_data($link_segment);
                if (!empty($link_route_data)) {
                    // Add Update entry in link route visit model
                    $LinkRoutesVisitsModel = new LinkRoutesVisitsModel();
                    $visit_data['link_id'] = $link_route_data['id'];
                    $visit_data['admin_id'] = $log_data['admin_id'];
                    $LinkRoutesVisitsModel->add_visited_links($visit_data);
                }
            }
        }

        if (isset($data['test_id'])) {
            $log_data['test_id'] = sanitize_input($data['test_id']);
        }

        if (isset($data['question_id'])) {
            $log_data['question_id'] = sanitize_input($data['question_id']);
        }

        if (isset($data['classroom_id'])) {
            $log_data['classroom_id'] = sanitize_input($data['classroom_id']);
        }

        if (isset($data['student_id'])) {
            $log_data['student_id'] = sanitize_input($data['student_id']);
        }

        if (isset($data['content_id'])) {
            $log_data['content_id'] = sanitize_input($data['content_id']);
        }

        if (isset($data['institute_id'])) {
            $log_data['institute_id'] = sanitize_input($data['institute_id']);
        }

        if (isset($data['super_admin_id'])) {
            $log_data['super_admin_id'] = sanitize_input($data['super_admin_id']);
        }

        $db->table('admin_activity')->insert($log_data);
    }
    /*******************************************************/




    /**********************************************************
     *########### START OF FUNCTIONS - FILTERED DATA #########*
     **********************************************************/
    /**
     * Get Filtered Data
     *
     * @param array $postData
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_activity_data($requestData = array())
    {


        $db = \Config\Database::connect();
        $response = array();

        $instituteID = $requestData['instituteID'];
        $start =  $requestData['start'];
        $length =  $requestData['length'];

        // Server Side Processing - ORDER BY and ASC,DESC
        $order = array();
        if (isset($postData['order'])) {
            $order = $postData['order'];
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
            $orderby = "admin_activity.created_date";
        } else {
            $orderby = $columns_valid[$col];
        }

        // Search Filter  
        $searchQuery = "";
        if (isset($requestData['search']['value'])) {
            $searchValue = $requestData['search']['value'];
            $searchQuery = " AND ( 
                admin_activity.activity_log LIKE '%" . $searchValue . "%' OR
                 admin_activity.module LIKE '%" . $searchValue . "%' OR
                 admin_activity.ip_address LIKE '%" . $searchValue . "%' OR
                 admin_activity.created_date LIKE '%" . $searchValue . "%'
                )";
        }


        // User Filter 
        $perms_check = "";
        if (isset($requestData['staff_id']) && $requestData['staff_id'] != "") {
            $staff_id = $requestData['staff_id'];
            $perms_check = " AND admin_activity.admin_id = '$staff_id' ";
        }

        ## Fetch records
        $sql = "SELECT admin_activity.*
        FROM admin_activity
        WHERE institute_id = '$instituteID' and module != 'page_access' $perms_check ";

        $totalRecords = $db->query($sql)->getNumRows();
        $sql .= $searchQuery;

        $totalRecordwithFilter = $db->query($sql)->getNumRows();
        $sql .= " order by $orderby $dir LIMIT  $start,$length ";

        // Result with filtered data with limit
        $records = $db->query($sql)->getResult();

        $data = array();
        foreach ($records as $record) {
            $nestedData = array();
            $activity_badge = "";
            $ip_address = $record->ip_address;
            if ($record->module == "logged_in") {
                $activity_badge = "<span class='badge bg-primary'>Logged In</span>";
            }

            if ($record->module == "login_failed") {
                $activity_badge = "<span class='badge bg-danger'>Login Failed</span>";
            }

            if ($record->module == "logout") {
                $activity_badge = "<span class='badge bg-secondary'>Logout</span>";
            }

            if ($record->module == "added") {
                $activity_badge = "<span class='badge bg-primary'>Added</span>";
            }

            if ($record->module == "modified") {
                $activity_badge = "<span class='badge bg-primary'>Modified</span>";
            }

            if ($record->module == "page_access") {
                $activity_badge = "<span class='badge bg-success'>Page Access</span>";
            }

            if ($record->module == "deleted") {
                $activity_badge = "<span class='badge bg-danger'>Deleted</span>";
            }

            $created_date = changeDateTimezone($record->created_date);
            $activity_log_code = htmlspecialchars("<div class='card m-0 mb-1 p-2'><div><p class='fs-6 fw-bolder'>$record->activity_log</p><div><div class='d-flex justify-content-between'><p class='fst-normal'>$activity_badge</p><p class='text-end'>$created_date</p></div><div class='d-flex flex-row-reverse'><p>$ip_address</p></div></div>");
            $nestedData[] = htmlspecialchars_decode($activity_log_code);
            $data[] = $nestedData;
        }
        ## Response
        $response = array(
            "draw" =>  intval($requestData['draw']),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "data" => $data
        );

        return $response;
    }
    /*******************************************************/



    /********************************************************
     *########### END OF FUNCTIONS - FILTERED DATA #########*
     ********************************************************/



    /**
     * Admin Unread Activity Count 
     *
     * @param string $datetime
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function unread_activity_log_count($admin_id, $datetime = "")
    {
        $db = \Config\Database::connect();
        $sql_fetch_todays_exam = "SELECT COUNT(*) as cnt 
                FROM admin_activity
        WHERE created_date >= :datetime: and admin_id = :admin_id: and module != 'page_access' ";

        $query = $db->query($sql_fetch_todays_exam, [
            'admin_id' => sanitize_input($admin_id),
            'datetime' => sanitize_input($datetime)
        ]);
        $result = $query->getRowArray();
        return $result['cnt'];
    }
    /*******************************************************/
}
