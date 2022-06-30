<?php

namespace App\Models;

use CodeIgniter\Model;


class TestTemplateConfigModel extends Model
{



    /**
     * Fetch Template Rules based on template id 
     *
     * @param int $template_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_template_rules(int $template_id)
    {
        $sql = "SELECT test_template_config.*
        FROM test_template_config 
        WHERE template_id = :template_id: 
        AND is_disabled='0' 
        ORDER BY rule_name ASC";

        $query = $this->db->query($sql, [
            'template_id' => sanitize_input($template_id)
        ]);

        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/



 /**
     * Fetch Template Rules based on template id ORDER BY Section
     *
     * @param int $template_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_template_section_rules(int $template_id)
    {
        $sql = "SELECT test_template_config.*
        FROM test_template_config 
        WHERE template_id = :template_id: 
        AND is_disabled='0' 
        AND value is not null and value != ''
        AND rule_name = 'SECTION_QUESTIONS'";

        $query = $this->db->query($sql, [
            'template_id' => sanitize_input($template_id)
        ]);

        $result = $query->getResultArray();
        return $result;
    }
 /*******************************************************/



    /**
     * Fetch Template Config Details based on template id - JSON Format
     *
     * @param int $template_id
     *
     * @return json
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_template_config_details(int $template_id)
    {
        $sql = "SELECT test_template_config.*
        FROM test_template_config 
        WHERE template_id= :template_id:  AND is_disabled='0' ";

        $query = $this->db->query($sql, [
            'template_id' => sanitize_input($template_id)
        ]);

        $result = $query->getResultArray();
        return json_encode($result);
    }
    /*******************************************************/


    /**
     * Fetch Rule Details based on id
     *
     * @param int $template_rule_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_rule_details(int $template_rule_id)
    {
        $sql = "SELECT test_template_config.*
        FROM test_template_config 
        WHERE id= :template_rule_id: ";

        $query = $this->db->query($sql, [
            'template_rule_id' => sanitize_input($template_rule_id)
        ]);

        $result = $query->getRowArray();
        return $result;
    }
    /*******************************************************/


    /**
     * Add Template Config Values 
     *
     * @param array $request_data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_template_config_data(array $request_data)
    {

        $db = \Config\Database::connect();

        $db->transStart();

        // Template ID check 
        if (isset($request_data['template_id']) && !empty($request_data['template_id'])) {
            $template_id = sanitize_input($request_data['template_id']);
            $add_data['template_id'] = $template_id;
        } else {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add template config details but failed due to template id missing', $log_info);
            return false;
        }

        // Template Rule name check 
        if (isset($request_data['rule_name']) && !empty($request_data['rule_name'])) {
            $add_data['rule_name'] = sanitize_input($request_data['rule_name']);
        } else {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add template config details but failed due to template rule name missing', $log_info);
            return false;
        }

        // We can't use !empty() function beacuse its value may be 0 which is considered as empty
        if (isset($request_data['value'])) {
            $add_data['value'] = sanitize_input($request_data['value']);
        }

        if (isset($request_data['section']) && !empty($request_data['section'])) {
            $add_data['section'] = sanitize_input($request_data['section']);
        }

        if (isset($request_data['from_question']) && !empty($request_data['from_question'])) {
            $add_data['from_question'] = sanitize_input($request_data['from_question']);
        }

        if (isset($request_data['to_question']) && !empty($request_data['to_question'])) {
            $add_data['to_question'] = sanitize_input($request_data['to_question']);
        }

        if (isset($request_data['display_name']) && !empty($request_data['display_name'])) {
            $add_data['display_name'] = sanitize_input($request_data['display_name']);
        }

        // Insert Template Config data
        $db->table('test_template_config')->insert($add_data);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add template config details but failed', $log_info);
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Template config added for the template:  " . $template_id,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);
            return true;
        }
    }
    /*******************************************************/



    /**
     * Update Template Rules
     *
     * @param array $request_data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_template_config_data(array $request_data)
    {

        $db = \Config\Database::connect();

        $db->transStart();

        // Template ID check 
        $template_id = "";
        if (isset($request_data['template_id']) && !empty($request_data['template_id'])) {
            $template_id = sanitize_input($request_data['template_id']);
        }

        // Template Rule ID check 
        if (isset($request_data['template_rule_id']) && !empty($request_data['template_rule_id'])) {
            $template_rule_id = sanitize_input($request_data['template_rule_id']);
        } else {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to update template rule but failed due to template rule id missing', $log_info);
            return false;
        }

        if (isset($request_data['display_name']) && !empty($request_data['display_name'])) {
            $update_data['display_name'] = sanitize_input($request_data['display_name']);
        }

        // We can't use !empty() function beacuse its value may be 0 which is considered as empty
        if (isset($request_data['template_rule_value'])) {
            $update_data['value'] =  sanitize_input($request_data['template_rule_value']);
        }

        if (isset($request_data['from_question']) && !empty($request_data['from_question'])) {
            $update_data['from_question'] =  sanitize_input($request_data['from_question']);
        }

        if (isset($request_data['to_question']) && !empty($request_data['to_question'])) {
            $update_data['to_question'] =  sanitize_input($request_data['to_question']);
        }

        if (isset($request_data['is_disabled']) && !empty($request_data['is_disabled'])) {
            $update_data['is_disabled'] =  sanitize_input($request_data['is_disabled']);
        }

        // Update Template Rule 
        $db->table('test_template_config')->update($update_data, ["id" => $template_rule_id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to update template rule but failed', $log_info);
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Template rule updated for the template ID" . $template_id,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);
            return $template_rule_id;
        }
    }
    /*******************************************************/
}
