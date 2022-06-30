<?php

namespace App\Models;

use CodeIgniter\Model;


class AdminModel extends Model
{

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /*******************************************************/
    /**
     * Get Admin Users
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     * @return Array
     */
    public function get_admin_users($institute_id)
    {

        $sql = "SELECT * 
        FROM admin
        WHERE admin.institute_id  = :institute_id: 
        AND is_disabled = '0' ";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/



    /**
     * Get Profile Details
     *
     * @param [type] $profile_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_profile_details($profile_id)
    {
        $sql = "SELECT * 
        FROM admin
        WHERE admin.id = :profile_id: ";

        $query = $this->db->query($sql, [
            'profile_id' => sanitize_input($profile_id)
        ]);

        if ($query->getNumRows() == 1) {
            return $query->getRowArray();
        } else {
            return false;
        }
    }
    /*******************************************************/



    /**
     * Update Profile Details
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_profile_details($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();


        $profile_data = [
            'name' => strtoupper(sanitize_input($data['name'])),
            'email' => sanitize_input($data['email']),
            'mobile' => sanitize_input($data['mobile']),
            'username' => sanitize_input($data['username'])
        ];

        $id = sanitize_input(decrypt_cipher($data['profile_id']));
        $db->table('admin')->update($profile_data, ['id' => $id]);

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
     * Admin - Update Last Login
     *
     * @param [type] $admin_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_last_login($admin_id)
    {

        $db = \Config\Database::connect();
        $db->transStart();

        $update_data = [
            'last_login' => date('Y-m-d H:i:s')
        ];


        $db->table('admin')->update($update_data, ['id' => $admin_id]);

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
     * Get Admin Token Using ID
     *
     * @param [type] $admin_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_admin_token($institute_id)
    {
        $token = "";
        $sql = "SELECT * 
        FROM admin
        WHERE admin.institute_id = :institute_id: ";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        if (!empty($query)) {
            $result = $query->getRowArray();
            $token = $result['admin_token'];
        }

        return $token;
    }
    /*******************************************************/
}
