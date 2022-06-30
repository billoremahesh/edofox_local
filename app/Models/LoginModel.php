<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginModel extends Model
{

    protected function hashPassword(array $data)
    {
        /**
         * In this case, we want to increase the default cost for BCRYPT to 12.
         * Note that we also switched to BCRYPT, which will always be 60 characters.
         */
        $options = [
            'cost' => 12,
        ];

        if (!isset($data['data']['password'])) :
            return $data;
        endif;

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT, $options);
        return $data;
    }






    /*******************************************************/
    /**
     * Validate Login Details Submitted
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     * @return mixed
     */
    public function validate_admin_login_details($token)
    {

        $db = \Config\Database::connect();

        // Named Bindings
        // Instead of using the question mark to mark the location of the bound values, you can name the bindings, allowing the keys of the values passed in to match placeholders in the query:

        $sql = "SELECT admin.*
        FROM admin 
        WHERE  admin.admin_token = :token: 
        AND is_disabled = 0 ";

        $query = $db->query($sql, [
            'token' => sanitize_input($token)
        ]);

        if ($query->getNumRows() == 1) {
            return $query->getRowArray();
        } else {
            return false;
        }
    }
    /*******************************************************/




    /****************************************************** */
    /**
     * Validate Super Admin login
     * Added by @RushikeshB
     */
    function validate_super_admin_login($username, $password)
    {
        $db = \Config\Database::connect();

        $builder = $db->table('super_admin');
        $builder->where('username', $username);
        $count = $builder->countAllResults();

        if ($count > 0) {
            $builder = $this->db->table('super_admin');
            $builder->where('username', $username);
            $builder->where('status', 'A');
            $result = $builder->get()->getRowArray();
            // Verify encrpted password
            if (password_verify($password, $result['password'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    /*******************************************************/


    /****************************************************** */
    /**
     * Get Super admin details
     * Added by @RushikeshB
     */
    function get_super_admin_login_details($username)
    {
        $db = \Config\Database::connect();

        $sql = "SELECT * 
        FROM super_admin 
        WHERE username = :username: ";

        $query = $db->query($sql, [
            'username' => sanitize_input($username)
        ]);

        if ($query->getNumRows() == 1) {
            return $query->getRowArray();
        } else {
            return false;
        }
    }
    /*******************************************************/
}
