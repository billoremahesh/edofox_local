<?php

namespace App\Models;

use CodeIgniter\Model;

class PackagesModel extends Model
{

    /*******************************************************/
    /**
     * Get Packages List
     * @return Array
     * @author PrachiP
     * @since 2021/10/19
     */
    public function get_packages_list($instituteID){
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * 
        FROM packages 
        WHERE institute_id = '$instituteID' 
        AND is_disabled='0' 
        ORDER BY package_name");
        return $query->getResultArray();
    }
    /*******************************************************/
}
?>