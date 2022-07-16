<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpParser\Node\Expr\FuncCall;

class SyllabusModel extends Model
{


    /**
     * Get Institute Subject List
     *
     * @param integer $institute_id
     *
     * @return Array
     * @author sunil <sunil@mattersoft.xyz>
     */
    public function add_syllabus($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();
         
     // add syllabus
        $syllabus_data = [
            'subject_id' => strtoupper(sanitize_input($data['subject_name'])),
            'syllabus_name' => $data['syllabus_name'],
            'description' => $data['description'],
            'institute_id' => decrypt_cipher(session()->get('instituteID'))
        ];   
        $db->table('syllabus')->insert($syllabus_data);
        $syllabus_id = $db->insertID();
    // syllabus_classroom_map

        foreach($data['session_classroom'] as $class){
            $syllabus_classroom = [
                'classroom_id' => strtoupper(sanitize_input($class)),
                'syllabus_id' => $syllabus_id, 
            ];   
            $db->table('syllabus_classroom_map')->insert($syllabus_classroom); 
        }

    
      

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Classroom Id " . $syllabus_id,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);
            return true;
        }
    }


    

    /**
     * Classroom Data
     *
     * @param array $requestData
     *
     * @return void
     * @author sunil <sunil@mattersoft.xyz>
     */
    public function fetch_all_syllabus_data(array $requestData)
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
            $orderby = "syllabus_name";
        } else {
            $orderby = $columns_valid[$col];
        }


        $filter_condn = "";
        if (isset($requestData['classroom_status']) && !empty($requestData['classroom_status'])) {
            $access_filter = sanitize_input($requestData['classroom_status']);
            if ($access_filter == "disabled") {
                $filter_condn = " AND is_disabled = '1' ";
            } elseif ($access_filter == "active") {
                $filter_condn = " AND is_disabled = '0' ";
            }
        }

        $searchQuery = "";
        $_SESSION['classroom_search_string'] = "";
        if (!empty($requestData['searchbox'])) {
            $searched_term = sanitize_input($requestData['searchbox']);
            // Saving value in session for loading on reload of page
            $_SESSION['classroom_search_string'] = $searched_term;
            $searchQuery .= " AND ( 
             syllabus_name LIKE '%" . $searched_term . "%'
            )";
        }


        $sql = "SELECT syllabus.*,test_subjects.subject FROM `syllabus` LEFT JOIN test_subjects ON test_subjects.subject_id=syllabus.subject_id WHERE syllabus.institute_id='$instituteID' ";

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

            $active_student_count = 10;
            $disabled_student_count = 5;
 
 


            $nestedData[] = $i;
            $nestedData[] = $row["syllabus_name"]; 
            $nestedData[] = $row["subject"];
            $nestedData[] = $row["description"]; 
          
            if ($row["is_disabled"] == '1') {

                $dropdown_wrapper_code = htmlspecialchars("<div class='dropdown'><button class='btn btn-default dropdown-toggle more_option_button' type='button' id='classroomDropdownMenu' data-bs-toggle='dropdown'  data-bs-auto-close='outside'  aria-expanded='false'><i class='fa fa-ellipsis-h' aria-hidden='true'></i>
                </button><ul class='dropdown-menu dropdown-menu-end' aria-labelledby='classroomDropdownMenu'> <li><a class='btn btn-sm' onclick=" . "show_edit_modal('modal_div','enable_classroom_modal','classrooms/enable_classroom_modal/" . $row['id'] . "');" . ">Enable classroom</a></li> </ul></div>");
            } else {

              
                $view_classroom_students_url = base_url() . '/classrooms/classroom_students/' . $package_id;

              

                $update_btn = "";
                $update_btn = "";
                $syll_config_btn = "";
            
                if (in_array("manage_syllabus", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) {
                    
                    // Update Option
                    $update_btn = "<li><a class='btn btn-sm' onclick=" . "show_edit_modal('modal_div','update_syllabus_modal','syllabus/update_syllabus_modal/" . $package_id . "');" . "> Update Syllabus </a></li>";
                    
                    // Disable Option
                    $delete_btn = "<li role='separator' class='dropdown-divider'></li><li><a class='btn btn-sm' onclick=" . "show_edit_modal('modal_div','delete_syllabus_modal','syllabus/delete_syllabus_modal/" . $package_id . "');" . "> Disable Syllabus </a></li>";
                    
                     // Disable Option
                     $syll_config_btn = "<li role='separator' class='dropdown-divider'></li><li><a class='btn btn-sm' href=". base_url('syllabus/syllabus_configuration/'.$package_id) ." > Syllabus Configuration </a></li>";
          
                    
                }

 
                $dropdown_wrapper_code = htmlspecialchars("<div class='dropdown'><button class='btn btn-default dropdown-toggle more_option_button' type='button' id='classroomDropdownMenu' data-bs-toggle='dropdown'  data-bs-auto-close='outside'  aria-expanded='false'><i class='fa fa-ellipsis-h' aria-hidden='true'></i>
                </button><ul class='dropdown-menu dropdown-menu-end' aria-labelledby='classroomDropdownMenu'>  $update_btn $delete_btn $syll_config_btn  </ul></div>");
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
     * Get Classroom Details
     *
     * @param [Integer] $classroom_id
     *
     * @return Array
     * @author sunil <sunil@mattersoft.xyz>
     */
    public function get_syllabus_details($syllabus_id)
    {
       
        $sql = "SELECT syllabus.*
        FROM syllabus 
        WHERE id = :id: ";

        $query = $this->db->query($sql, [
            'id' => sanitize_input($syllabus_id)
        ]);


        return $query->getRowArray();
    }
    /*******************************************************/

    
    /**
     * Delete Syllabus 
     *
     * @param [Array] $data
     *
     * @return void
     * @author  sunil <sunil@mattersoft.xyz>
     */
    public function delete_syllabus($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();
 
        $syllabus_data = [
            'is_disabled' => '1'
        ]; 
        $id = sanitize_input(decrypt_cipher($data['syllabus_id']));
        $db->table('syllabus')->update($syllabus_data, ['id' => $id]);
       
        $db->table('syllabus_classroom_map')->update($syllabus_data, ['syllabus_id' => $id]);

        
        $syllabus_data = $this->get_syllabus_details($id);
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => strtoupper("Syllabus Name : ") . $syllabus_data['syllabus_name'],
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
     * Get Classroom Details
     *
     * @param [Integer] $classroom_id
     *
     * @return Array
     * @author sunil <sunil@mattersoft.xyz>
     */
    public function get_syllabus_classroom_details($syllabus_id)
    {
       
        $sql = "SELECT classroom_id
        FROM syllabus_classroom_map 
        WHERE syllabus_id = :syllabus_id: AND is_disabled=0";

        $query = $this->db->query($sql, [
            'syllabus_id' => sanitize_input($syllabus_id)
        ]);


        return $query->getResultArray();
    }
    /*******************************************************/

    

    /**
     * Update Syllabus
     *
     * @param [Array] $data
     *
     * @return void
     * @author sunil <sunil@mattersoft.xyz>
     */
    public function update_syllabus($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();
      
        $this->delete_syllabus($data);
        $syllabus_id = decrypt_cipher($data['syllabus_id']);
         // update syllabus  
         $syllabus_data = [
            'subject_id' => strtoupper(sanitize_input($data['subject_name'])),
            'syllabus_name' => $data['syllabus_name'],
            'description' => $data['description'],
            'institute_id' => decrypt_cipher(session()->get('instituteID')),
            'is_disabled' => '0'
        ];   
      
        $id = sanitize_input(decrypt_cipher($data['syllabus_id']));
        $db->table('syllabus')->update($syllabus_data, ['id' => $id]);
  
    // update syllabus_classroom_map

        foreach($data['session_classroom'] as $class){
            $syllabus_classroom = [
                'classroom_id' => strtoupper(sanitize_input($class)),
                'syllabus_id' => $syllabus_id, 
            ];   
            $db->table('syllabus_classroom_map')->insert($syllabus_classroom); 
        }


        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Syllabus Id " . $syllabus_id,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')), 
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/

    public function get_syllabus(int $institute_id)
    {

        $db = \Config\Database::connect();
        $sql = "SELECT * 
        FROM syllabus 
        WHERE ( institute_id = :institute_id: 
        OR institute_id IS NULL ) AND is_disabled = 0
        ORDER BY syllabus_name";

        $query = $db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        return $query->getResultArray();
    }

    
    /**
     * Get Classroom Details
     *
     * @param [Integer] $classroom_id
     *
     * @return Array
     * @author sunil <sunil@mattersoft.xyz>
     */
    public function get_syllabus_record($syllabus_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT syllabus.*,test_subjects.subject
        FROM syllabus LEFT JOIN test_subjects ON test_subjects.subject_id=syllabus.subject_id 
        WHERE syllabus.id = :id: ";
 

        $query = $this->db->query($sql, [
            'id' => sanitize_input($syllabus_id)
        ]); 
        return $query->getRowArray();
    }
    /*******************************************************/

     
}
