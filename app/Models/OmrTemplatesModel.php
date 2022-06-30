<?php

namespace App\Models;

use CodeIgniter\Model;

class OmrTemplatesModel extends Model
{


    /**
     * Get OMR Templates
     *
     * @param int $institute_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_omr_templates(int $institute_id)
    {
        $sql = "SELECT omr_templates.*
        FROM omr_templates 
        WHERE ( institute_id = :institute_id: OR institute_id IS NULL )";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);
        return $query->getResultArray();
    }

    /**
     * Get OMR Template
     *
     * @param Integer $template_id
     *
     * @return Array
     * @author Ajinkya K <Ajinkya.Kulkarni@mattersoft.xyz>
     */
    public function get_omr_template(int $template_id)
    {
        $sql = "SELECT * from omr_templates where id = :id: ";

        $query = $this->db->query($sql, [
            'id' => sanitize_input($template_id)
        ]);

        return $query->getRowArray();
    }
    /*******************************************************/
}
