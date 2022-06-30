<?php

namespace App\Models;

use CodeIgniter\Model;

class SuperAdminModel extends Model
{


    protected function hashPassword(string $password)
    {
        /**
         * In this case, we want to increase the default cost for BCRYPT to 12.
         * Note that we also switched to BCRYPT, which will always be 60 characters.
         */
        $options = [
            'cost' => 12,
        ];

        if (!isset($password)) :
            return $password;
        endif;

        $password = password_hash($password, PASSWORD_BCRYPT, $options);
        return $password;
    }


    /**
     * Get All Super Admins
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_super_admins()
    {
        $db = \Config\Database::connect();
        $sql = "SELECT super_admin.*,institute_map.institute_names
        FROM super_admin 
        LEFT JOIN (select GROUP_CONCAT(institute_name) as institute_names,institute.account_manager 
        from institute
        join super_admin on super_admin.id = institute.account_manager
        left join sales_details on sales_details.institute_id = institute.id
        where (sales_details.status is null OR sales_details.status != 'ARCHIVED') 
        group by institute.account_manager ) institute_map
        ON institute_map.account_manager = super_admin.id 
        WHERE super_admin.status = 'A'
        order by super_admin.name asc";

        $query = $db->query($sql);
        return $query->getResultArray();
    }
    /*******************************************************/



    /**
     * Get Super Admin Details
     *
     * @param int $staff_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_super_admin_details(int $staff_id)
    {
        $sql = "SELECT super_admin.*
        FROM super_admin 
        WHERE super_admin.id = :staff_id: ";

        $query = $this->db->query($sql, [
            'staff_id' => sanitize_input($staff_id)
        ]);

        return $query->getRowArray();
    }
    /*******************************************************/


    /**
     * fetch institute account manager
     *
     * @param int $institute_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_institute_account_manager(int $institute_id)
    {
        $sql = "SELECT super_admin.*
        FROM super_admin 
        JOIN institute
        ON institute.account_manager = super_admin.id
        WHERE institute.id = :institute_id: ";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        return $query->getRowArray();
    }
    /*******************************************************/


    /**
     * Add Super Admin
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_super_admin(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $staff_name = "";

        if (isset($data['name'])) {
            $staff_name = $staff_data['name'] = sanitize_input($data['name']);
        }

        if (isset($data['email'])) {
            $staff_data['email'] = sanitize_input($data['email']);
        }

        if (isset($data['mobile_number'])) {
            $staff_data['mobile_number'] = sanitize_input($data['mobile_number']);
        }

        if (isset($data['role'])) {
            $staff_data['role'] = sanitize_input($data['role']);
        }

        if (isset($data['username'])) {
            $staff_data['username'] = sanitize_input($data['username']);
        }

        if (isset($data['password'])) {
            $staff_data['password'] = $this->hashPassword($data['password']);
        }

        // Update Permissions
        $perms_arr = array();
        if (isset($data['perms_all'])) {
            array_push($perms_arr, 'all_super_admin_perms');
        } else {
            foreach ($data['perms'] as $perms_val) {
                array_push($perms_arr, sanitize_input($perms_val));
            }
        }

        $perms_string = json_encode($perms_arr);
        $staff_data['access_perms'] = $perms_string;

        $staff_data['status'] = 'A';
        $staff_data['created_at'] = date('Y-m-d H:i:s');


        $db->table('super_admin')->insert($staff_data);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add sales team but failed', $log_info);

            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'Sales team -  ' . $staff_name . " added",
                'super_admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Update Super Admin Details
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_super_admin_details(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $staff_name = "";

        if (isset($data['name'])) {
            $staff_name = $staff_data['name'] = sanitize_input($data['name']);
        }

        if (isset($data['email'])) {
            $staff_data['email'] = sanitize_input($data['email']);
        }

        if (isset($data['mobile_number'])) {
            $staff_data['mobile_number'] = sanitize_input($data['mobile_number']);
        }

        if (isset($data['role'])) {
            $staff_data['role'] = sanitize_input($data['role']);
        }
        
        // Update Permissions
        $perms_arr = array();
        if (isset($data['perms_all'])) {
            array_push($perms_arr, 'all_super_admin_perms');
        } else {
            foreach ($data['perms'] as $perms_val) {
                array_push($perms_arr, sanitize_input($perms_val));
            }
        }

        $perms_string = json_encode($perms_arr);
        $staff_data['access_perms'] = $perms_string;

        $staff_data['status'] = 'A';
        $staff_data['created_at'] = date('Y-m-d H:i:s');


        $id = sanitize_input(decrypt_cipher($data['staff_id']));
        $db->table('super_admin')->update($staff_data, ['id' => $id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to update sales team details but failed', $log_info);

            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'Sales team -  ' . $staff_name . " details updated",
                'super_admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Delete Sales Team 
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_super_admin_details(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $staff_name = $data['staff_name'];

        $staff_data = [
            'status' => 'D',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $id = sanitize_input(decrypt_cipher($data['staff_id']));
        $db->table('super_admin')->update($staff_data, ['id' => $id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to delete sales team but failed', $log_info);

            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'Sales team -  ' . $staff_name . " deleted",
                'super_admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('deleted', $log_info);
            return true;
        }
    }
    /*******************************************************/
}
