<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassroomModel extends Model
{

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }


    /**
     * Classroom Count
     *
     * @param integer $institute_id
     * @param string $type
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    function classroom_count(int $institute_id, string $type = '')
    {
        $db = \Config\Database::connect();

        // Check Mapped Classrooms to staff in case of not given global permissions
        $check_access_perms = "";
        $classroom_mapped_ids = session()->get('classroom_mapped_arr');
        if (!empty($classroom_mapped_ids)) {
            $check_access_perms = " AND packages.id IN ($classroom_mapped_ids) ";
        }

        // This condition used for DLP
        $type_condition = "";
        if ($type != "") {
            $type = sanitize_input($type);
            $type_condition = " AND type = '$type' ";
        }


        $sql_fetch_total_classrooms = "SELECT count(*) as classroom_cnt
        FROM packages 
        WHERE institute_id = :institute_id: 
        AND is_disabled='0'
        $type_condition $check_access_perms ";

        $query = $db->query($sql_fetch_total_classrooms, [
            'institute_id' => sanitize_input($institute_id)
        ]);
        $result = $query->getRowArray();

        return $result['classroom_cnt'];
    }
    /****************************************************** */


    /**
     * Fetch all classrooms
     *
     * @param integer $institute_id
     * @param string $type
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_all_classrooms(int $institute_id, string $type = '')
    {

        // Check Mapped Classrooms to staff in case of not given global permissions
        $check_access_perms = "";
        $classroom_mapped_ids = session()->get('classroom_mapped_arr');
        if (!empty($classroom_mapped_ids)) {
            $check_access_perms = " AND packages.id IN ($classroom_mapped_ids) ";
        }


        // This condition used for DLP
        $type_condition = "";
        if ($type != "") {
            $type = sanitize_input($type);
            $type_condition = " AND type = '$type' ";
        }

        $sql = "SELECT packages.*,IF(cnt IS NULL,0,cnt) student_cnt
        FROM packages 
        LEFT JOIN (select count(*) as cnt, package_id 
        from
        student_institute where institute_id = :institute_id: and student_institute.is_disabled = 0 group by package_id ) stu_institute
        ON stu_institute.package_id = packages.id
        WHERE institute_id = :institute_id: 
        AND is_disabled='0' 
        $type_condition  $check_access_perms
        ORDER BY package_name ASC";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/



    public function optimized_classrooms_list(array $requestData)
    {

        $institute_id = $requestData['institute_id'];
        // Check Mapped Classrooms to staff in case of not given global permissions
        $check_access_perms = "";
        $classroom_mapped_ids = session()->get('classroom_mapped_arr');
        if (!empty($classroom_mapped_ids)) {
            $check_access_perms = " AND packages.id IN ($classroom_mapped_ids) ";
        }

        $search_classroom_condn = "";
        if (isset($requestData['search']) && !empty($requestData['search'])) {
            $search = $requestData['search'];
            $search_classroom_condn = " AND packages.package_name LIKE '%$search%'";
        }


        $sql = "SELECT packages.*
        FROM packages 
        WHERE institute_id = :institute_id: 
        AND is_disabled='0' 
        $check_access_perms $search_classroom_condn
        ORDER BY package_name ASC";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        $result = $query->getResultArray();

        $formatted_data = array();

        $formatted_data['incomplete_results'] = false;
        $formatted_data['items'] = array();
        $cnt = 0;
        foreach ($result as $institute_data) {
            $institute_object = array();
            $institute_object['id'] = $institute_data['id'];
            $institute_object['name'] = $institute_data['package_name'];
            array_push($formatted_data['items'], $institute_object);
            $cnt++;
        }

        $formatted_data['total_count'] = $cnt;
        return json_encode($formatted_data);
    }


    /**
     * Classroom Data
     *
     * @param array $requestData
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_all_classrooms_data(array $requestData)
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
            $orderby = "packages.package_name";
        } else {
            $orderby = $columns_valid[$col];
        }


        $filter_condn = "";
        if (isset($requestData['classroom_status']) && !empty($requestData['classroom_status'])) {
            $access_filter = sanitize_input($requestData['classroom_status']);
            if ($access_filter == "disabled") {
                $filter_condn = " AND packages.is_disabled = '1' ";
            } elseif ($access_filter == "active") {
                $filter_condn = " AND packages.is_disabled = '0' ";
            }
        }

        $searchQuery = "";
        $_SESSION['classroom_search_string'] = "";
        if (!empty($requestData['searchbox'])) {
            $searched_term = sanitize_input($requestData['searchbox']);
            // Saving value in session for loading on reload of page
            $_SESSION['classroom_search_string'] = $searched_term;
            $searchQuery .= " AND ( 
            packages.package_name LIKE '%" . $searched_term . "%'
            )";
        }

        // Check Mapped Classrooms to staff in case of not given global permissions
        $check_access_perms = "";
        $classroom_mapped_ids = session()->get('classroom_mapped_arr');
        if (!empty($classroom_mapped_ids)) {
            $check_access_perms = " AND packages.id IN ($classroom_mapped_ids) ";
        }


        $sql = "SELECT packages.*,IF(cnt IS NULL,0,cnt) student_cnt,IF(deletedCount IS NULL,0,deletedCount) deleted_cnt
        FROM packages
        LEFT JOIN (select count(*) as cnt, package_id
        from student_institute join student_login on student_institute.student_id = student_login.student_id
        where student_institute.institute_id='$instituteID' AND (student_login.student_access IS NULL OR student_login.student_access = '') AND
        student_institute.is_disabled = 0 group by package_id ) stu_institute
        ON stu_institute.package_id = packages.id
        LEFT JOIN (select count(*) as deletedCount, package_id
        from student_institute join student_login on student_institute.student_id = student_login.student_id
        where student_institute.institute_id='$instituteID' AND (student_login.student_access IS NOT NULL AND student_login.student_access != '' AND student_login.student_access != 'Teacher' AND student_login.student_access != 'Deleted') AND
        student_institute.is_disabled = 0 group by package_id ) stu_institute_deleted
        ON stu_institute_deleted.package_id = packages.id 
        WHERE institute_id='$instituteID' $check_access_perms ";

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

            $package_id = encrypt_string($row['id']);
            $is_public_badge = "";

            $active_student_count = $row['student_cnt'];
            //Its actually blocked count
            $disabled_student_count = $row['deleted_cnt'];


            if ($row['is_public'] == 1) {
                $is_public_badge_encode =  htmlspecialchars("<span class='material-icons material-icon-small success_badge' data-bs-toggle='tooltip' title='This classroom is public. It will be available in new signup form for students'>public</span>");
                $is_public_badge_decode = htmlspecialchars_decode($is_public_badge_encode);
                $is_public_badge = $is_public_badge_decode;
            }

            if ($row['type'] == "") {
                $type = 'Regular';
            } else {
                $type = $row['type'];
            }


            $nestedData[] = $i;
            $nestedData[] = $row["package_name"] . ' ' . $is_public_badge;
            $nestedData[] = $type;
            $nestedData[] = $row["price"];
            $nestedData[] = $row["offline_price"];

            $student_count_badges_encode =  htmlspecialchars("<span class='badge bg-primary mx-2' data-bs-toggle='tooltip' title='Active Students Count'>$active_student_count</span><span class='badge bg-danger' data-bs-toggle='tooltip' title='Blocked Students Count'>$disabled_student_count</span>");
            $nestedData[] = htmlspecialchars_decode($student_count_badges_encode);

            if ($row["is_disabled"] == '1') {

                $dropdown_wrapper_code = htmlspecialchars("<div class='dropdown'><button class='btn btn-default dropdown-toggle more_option_button' type='button' id='classroomDropdownMenu' data-bs-toggle='dropdown'  data-bs-auto-close='outside'  aria-expanded='false'><i class='fa fa-ellipsis-h' aria-hidden='true'></i>
                </button><ul class='dropdown-menu dropdown-menu-end' aria-labelledby='classroomDropdownMenu'> <li><a class='btn btn-sm' onclick=" . "show_edit_modal('modal_div','enable_classroom_modal','classrooms/enable_classroom_modal/" . $row['id'] . "');" . ">Enable classroom</a></li> </ul></div>");
            } else {


                $view_classroom_students_url = base_url() . '/classrooms/classroom_students/' . $package_id;

                $view_classroom_students = "<li><a class='btn btn-sm' href='" . $view_classroom_students_url . "'> Classroom Students </a></li>";

                $update_btn = "";
                $delete_btn = "";
                $empty_btn = "";

                if (in_array("manage_classrooms", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) {

                    // Update Option
                    $update_btn = "<li><a class='btn btn-sm' onclick=" . "show_edit_modal('modal_div','update_classroom_modal','classrooms/update_classroom_modal/" . $package_id . "');" . "> Update Classroom </a></li>";

                    // Disable Option
                    $delete_btn = "<li role='separator' class='dropdown-divider'></li><li><a class='btn btn-sm' onclick=" . "show_edit_modal('modal_div','delete_classroom_modal','classrooms/delete_classroom_modal/" . $package_id . "');" . "> Disable Classroom </a></li>";

                    //Empty classroom
                    $empty_btn = "<li role='separator' class='dropdown-divider'></li><li><a class='btn btn-sm' onclick=" . "show_edit_modal('modal_div','empty_classroom_modal','classrooms/empty_classroom_modal/" . $package_id . "');" . "> Empty Classroom </a></li>";
                }

                $is_public_share = "";
                if ($row['is_public'] == 1) {
                    $is_public_share = "<li role='separator' class='dropdown-divider'></li><li><a class='btn btn-sm' onclick=" . "show_edit_modal('modal_div','signup_link','students/student_signup_modal/" . encrypt_string($instituteID) . "/" . $package_id . "');" . ">Share classroom</a></li>";
                }


                $dropdown_wrapper_code = htmlspecialchars("<div class='dropdown'><button class='btn btn-default dropdown-toggle more_option_button' type='button' id='classroomDropdownMenu' data-bs-toggle='dropdown'  data-bs-auto-close='outside'  aria-expanded='false'><i class='fa fa-ellipsis-h' aria-hidden='true'></i>
                </button><ul class='dropdown-menu dropdown-menu-end' aria-labelledby='classroomDropdownMenu'> $view_classroom_students $update_btn $delete_btn $empty_btn $is_public_share  </ul></div>");
            }

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



    /**
     * Classroom List - Used for Dropdowns
     *
     * @param integer $institute_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function classroom_list(int $institute_id)
    {


        // Check Mapped Classrooms to staff in case of not given global permissions
        $check_access_perms = "";
        $classroom_mapped_ids = session()->get('classroom_mapped_arr');
        if (!empty($classroom_mapped_ids)) {
            $check_access_perms = " AND packages.id IN ($classroom_mapped_ids) ";
        }

        $sql = "SELECT packages.*
        FROM packages 
        WHERE institute_id = :institute_id: 
        AND is_disabled='0' $check_access_perms 
        ORDER BY package_name ASC";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/


    /**
     * Get Classroom Details
     *
     * @param [Integer] $classroom_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_classroom_details($classroom_id)
    {
        $sql = "SELECT packages.*
        FROM packages 
        WHERE id = :classroom_id: ";

        $query = $this->db->query($sql, [
            'classroom_id' => sanitize_input($classroom_id)
        ]);

        return $query->getRowArray();
    }
    /*******************************************************/


    /**
     * Add Classroom 
     *
     * @param [Array] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_classroom($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();


        $is_public = 0;
        if (isset($data['is_public_check'])) {
            $is_public = 1;
        }

        $classroom_data = [
            'package_name' => strtoupper(sanitize_input($data['package_name'])),
            'institute_id' => sanitize_input(decrypt_cipher($data['institute_id'])),
            'price' => sanitize_input($data['package_price']),
            'offline_price' => sanitize_input($data['package_offline_price']),
            'type' => sanitize_input($data['package_type']),
            'is_public' => $is_public
        ];


        $db->table('packages')->insert($classroom_data);
        $classroom_id = $db->insertID();

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Classroom Id " . $classroom_id,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Update Classroom 
     *
     * @param [Array] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_classroom($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $is_public = 0;
        if (isset($data['is_public_check'])) {
            $is_public = 1;
        }

        $classroom_data = [
            'package_name' => strtoupper(sanitize_input($data['package_name'])),
            'price' => sanitize_input($data['package_price']),
            'offline_price' => sanitize_input($data['package_offline_price']),
            'type' => sanitize_input($data['package_type']),
            'is_public' => $is_public
        ];

        $id = sanitize_input(decrypt_cipher($data['classroom_id']));
        $db->table('packages')->update($classroom_data, ['id' => $id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'item' => "Classroom Id " . $id . ", package_name is " . strtoupper(sanitize_input($data['package_name'])) . ", price " . sanitize_input($data['package_price']) . ", offline_price " . sanitize_input($data['package_offline_price']) . ", type " . sanitize_input($data['package_type']) . ", is_public " . $is_public,
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Delete Classroom 
     *
     * @param [Array] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_classroom($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();


        $classroom_data = [
            'is_disabled' => '1'
        ];

        $id = sanitize_input(decrypt_cipher($data['classroom_id']));
        $db->table('packages')->update($classroom_data, ['id' => $id]);

        $classroom_data = $this->get_classroom_details($id);
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => strtoupper("Classroom Name : ") . $classroom_data['package_name'],
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('deleted', $log_info);
            return true;
        }
    }
    /*******************************************************/




    /**
     * Enable Deleted Classrom
     * @author Pratik <pratik.kulkarni54@gmail.com>
     * @param [type] $data
     * @return void
     */
    public function enable_classroom_submit($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();


        $classroom_data = [
            'is_disabled' => '0'
        ];

        $id = sanitize_input($data['classroom_id']);
        $db->table('packages')->update($classroom_data, ['id' => $id]);

        $classroom_data = $this->get_classroom_details($id);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => strtoupper("Classroom Name : ") . $classroom_data['package_name'] . ("is Enabled."),
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/



    public function block_students_submit($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $block_accounts_student_ids_string = implode(",", $data['block_accounts_student_ids']);

        $block_accounts_student_ids_arr = explode(',', $block_accounts_student_ids_string);
        $package_id = decrypt_cipher($data['block_accounts_package_id']);
        $institute_id = decrypt_cipher(session()->get('instituteID'));

        foreach ($block_accounts_student_ids_arr as $block_accounts_student_id) {

            $students_data = [
                'student_access' => 'Disabled'
            ];

            $db->table('student_login')->update($students_data, ['student_id' => $block_accounts_student_id, 'institute_id' => $institute_id]);
        }



        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => strtoupper("Student Access to Disabled ") . $block_accounts_student_ids_string,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/





    public function unblock_students_submit($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $unblock_accounts_student_ids_string = implode(",", $data['unblock_accounts_student_ids']);

        $unblock_accounts_student_ids_arr = explode(',', $unblock_accounts_student_ids_string);
        $package_id = decrypt_cipher($data['unblock_accounts_package_id']);
        $institute_id = decrypt_cipher(session()->get('instituteID'));

        foreach ($unblock_accounts_student_ids_arr as $unblock_accounts_student_id) {

            $students_data = [
                'student_access' => NULL
            ];

            $db->table('student_login')->update($students_data, ['student_id' => $unblock_accounts_student_id, 'institute_id' => $institute_id]);
        }



        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => strtoupper("Student Access to Enabled ") . $unblock_accounts_student_ids_string,
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
     * Get Classroom Students
     *
     * @param [type] $package_id
     * @param [type] $institute_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function classroom_students($package_id, $institute_id)
    {

        $sql = "SELECT  student_institute.*,student.name,student.roll_no
        FROM student_institute 
        JOIN student
        ON student_institute.student_id = student.id
        INNER JOIN student_login
        ON student.id = student_login.student_id
        WHERE student_institute.institute_id = '$institute_id' AND (student_login.student_access IS NULL OR student_login.student_access = '')
        AND student_institute.package_id = '$package_id' AND student_institute.is_disabled = 0 ";

        $query = $this->db->query($sql, [
            'institute_id' => $institute_id,
            'package_id' => $package_id
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/


    /**
     * Get Blocked Classroom Students
     *
     * @param [type] $package_id
     * @param [type] $institute_id
     *
     * @return void
     * @author Pratik <pratik.kulkarni54@gmail.com>
     */
    public function blocked_classroom_students($package_id, $institute_id)
    {

        $sql = "SELECT student_institute.*,student.name,student.roll_no
        FROM student_institute 
        JOIN student
        ON student_institute.student_id = student.id
        INNER JOIN student_login
        ON student.id = student_login.student_id
        WHERE student_institute.institute_id = '$institute_id' AND (student_login.student_access = 'Disabled') 
        AND student_institute.package_id = '$package_id' AND student_institute.is_disabled = 0  ";

        $query = $this->db->query($sql, [
            'institute_id' => $institute_id,
            'package_id' => $package_id
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/


    /**
     * Delete Classroom Students
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_classroom_students($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();


        $id = sanitize_input(decrypt_cipher($data['classroom_id']));
        if (isset($data['delete_student_ids'])) {
            $student_ids_arr = explode(",", $data['delete_student_ids']);
            if (!empty($student_ids_arr)) {
                foreach ($student_ids_arr as $student_id) {
                    $classroom_data = [
                        'is_disabled' => '1'
                    ];
                    $db->table('student_institute')->update($classroom_data, ['package_id' => $id, 'student_id' => $student_id]);
                }
            }
        }


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
     * Migrate Classroom Students
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function migrate_classroom_students($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $new_classroom_id = sanitize_input(decrypt_cipher($data['new_package_id']));
        $old_classroom_id = sanitize_input(decrypt_cipher($data['classroom_id']));

        if (isset($data['migrate_student_ids'])) {
            $student_ids_arr = explode(",", $data['migrate_student_ids']);
            if (!empty($student_ids_arr)) {
                foreach ($student_ids_arr as $student_id) {

                    $classroom_data = [
                        'package_id' => $new_classroom_id,
                        'student_id' => $student_id,
                        'institute_id' => decrypt_cipher($data['institute_id']),
                        'status' => 'Completed'
                    ];

                    $db->table('student_institute')->insert($classroom_data);

                    if (!isset($data['copy_bulk_students_check'])) {
                        $classroom_data = [
                            'is_disabled' => '1'
                        ];
                        $db->table('student_institute')->update($classroom_data, ['package_id' => $old_classroom_id, 'student_id' => $student_id]);
                    }
                }
            }
        }

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
     * Classroom Subjects
     *
     * @param [type] $institute_id
     * @param [type] $course_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function classroom_subjects($course_id)
    {
        $count = 1;
        $db = \Config\Database::connect();
        $sql = "SELECT test_subjects.* 
        FROM dlp_subjects_classroom_map
        INNER JOIN test_subjects 
        ON dlp_subjects_classroom_map.subject_id = test_subjects.subject_id
        WHERE classroom_id in (:course_id:)
        GROUP by test_subjects.subject_id 
        HAVING COUNT(DISTINCT classroom_id) = $count ";

        $query = $db->query($sql, [
            'course_id' => sanitize_input($course_id)
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/



    /**
     * Classroom Chapters 
     *
     * @param [type] $course_id
     * @param [type] $subject_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function classroom_chapters($course_id, $subject_id)
    {
        $count = 1;
        $db = \Config\Database::connect();
        $sql = "SELECT chapters.id, chapters.chapter_name 
        FROM dlp_chapters_classroom_map
        INNER JOIN chapters 
        ON dlp_chapters_classroom_map.chapter_id = chapters.id 
        AND chapters.subject = :subject_id: 
        AND dlp_chapters_classroom_map.status = 1
        WHERE classroom_id in (:course_id:) 
        GROUP BY chapters.id 
        HAVING COUNT(DISTINCT classroom_id) = $count ";

        $query = $db->query($sql, [
            'course_id' => sanitize_input($course_id),
            'subject_id' => sanitize_input($subject_id)
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/
}
