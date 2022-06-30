<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Models\UserActivityModel;

class StaffModel extends Model
{

    /**
     * Fetch All Staff
     *
     * @param [type] $institute_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_all_staff($institute_id)
    {
        $sql = "SELECT admin.*,admin_packages.packages_name
        FROM admin 
        LEFT JOIN 
        ( select group_concat(packages.package_name) packages_name,admin_package_map.admin_id
            from admin_package_map
            join packages on packages.id = admin_package_map.package_id
            where admin_package_map.is_disabled = 0
            group by admin_package_map.admin_id
        ) admin_packages
        ON admin_packages.admin_id = admin.id
        WHERE admin.institute_id = :institute_id: 
        AND admin.is_disabled = 0
        ORDER BY admin.username ASC";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/



    /**
     * Get Staff Details
     *
     * @param [type] $staff_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_staff_details($staff_id)
    {
        $sql = "SELECT admin.*
        FROM admin 
        WHERE admin.id = :staff_id: 
        ORDER BY admin.username ASC";

        $query = $this->db->query($sql, [
            'staff_id' => sanitize_input($staff_id)
        ]);

        return $query->getRowArray();
    }
    /*******************************************************/


    /**
     * Staff Mapped Packages
     *
     * @param [type] $staff_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function staff_mapped_packages($staff_id)
    {
        $sql = "select GROUP_CONCAT(admin_package_map.package_id) pkgids
        FROM admin_package_map
        WHERE admin_package_map.admin_id = :staff_id: 
        AND admin_package_map.is_disabled = 0";

        $query = $this->db->query($sql, [
            'staff_id' => sanitize_input($staff_id)
        ]);

        return $query->getRowArray();
    }
    /*******************************************************/

    /**
     * Update Staff 
     *
     * @param [Array] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_staff($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        // Update Permissions
        $perms_arr = array();
        if (isset($data['perms_all'])) {
            array_push($perms_arr, 'all_perms');
        } else {
            foreach ($data['perms'] as $perms_val) {
                array_push($perms_arr, sanitize_input($perms_val));
            }
        }
        $perms_string = json_encode($perms_arr);

        $staff_data = [
            'name' => strtoupper(sanitize_input($data['name'])),
            'email' => sanitize_input($data['email']),
            'mobile' => sanitize_input($data['mobile']),
            'username' => sanitize_input($data['username']),
            'perms' => $perms_string
        ];

        $id = sanitize_input(decrypt_cipher($data['staff_id']));
        $db->table('admin')->update($staff_data, ['id' => $id]);


        if (isset($data['classrooms'])) :
            $selected_classrooms = $data['classrooms'];
            if (!empty($selected_classrooms)) :
                $update_package_data = [
                    'is_disabled' => '1'
                ];
                $db->table('admin_package_map')->update($update_package_data, ['admin_id' => $id]);
                foreach ($selected_classrooms as $classroom_id) :
                    $package_data = array(
                        'package_id' => $classroom_id,
                        'admin_id' => $id
                    );
                    $db->table('admin_package_map')->insert($package_data);
                endforeach;
            endif;
        endif;

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => 'staff id ' . $id,
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
     * Delete Staff 
     *
     * @param [Array] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_staff($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();


        $staff_data = [
            'is_disabled' => '1'
        ];

        $id = sanitize_input(decrypt_cipher($data['staff_id']));
        $db->table('admin')->update($staff_data, ['id' => $id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
             // Activity Log
             $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'staff id '.$id,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('deleted', $log_info);
            return true;
        }
    }
    /*******************************************************/
}
