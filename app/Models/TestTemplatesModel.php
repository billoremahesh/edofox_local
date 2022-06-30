<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\TestsModel;
use App\Models\TestTemplateConfigModel;

class TestTemplatesModel extends Model
{


    /**
     * Fetch Institutes Templates Count
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function institute_templates_count(int $institute_id)
    {

        $sql = "SELECT COUNT(test_templates.id) template_counts
        FROM test_templates 
        WHERE institute_id = :institute_id: 
        AND is_disabled='0' 
        ORDER BY template_name ASC";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        $result = $query->getRowArray();
        return $result['template_counts'];
    }
    /*******************************************************/


    /**
     * Fetch Template Details based on template id 
     *
     * @param int $template_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_template_details(int $template_id)
    {
        $sql = "SELECT test_templates.*
        FROM test_templates 
        WHERE id= :template_id:  ";

        $query = $this->db->query($sql, [
            'template_id' => sanitize_input($template_id)
        ]);

        $result = $query->getRowArray();
        return $result;
    }
    /*******************************************************/




    /**
     * Fetch Institutes Templates and  Null institute templates
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_test_templates(int $institute_id)
    {

        $sql = "SELECT test_templates.*
        FROM test_templates 
        WHERE (institute_id = :institute_id: OR institute_id IS NULL)
        AND is_disabled='0' 
        ORDER BY template_name ASC";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/

    /**
     * Fetch Institutes Templates
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_institute_templates(int $institute_id)
    {

        $sql = "SELECT test_templates.*
        FROM test_templates 
        WHERE institute_id = :institute_id: 
        AND is_disabled='0' 
        ORDER BY template_name ASC";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/

    /**
     * Add New Template
     *
     * @param array $request_data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_template(array $request_data)
    {

        $db = \Config\Database::connect();

        $db->transStart();


        // Template name check 
        if (isset($request_data['template_name']) && !empty($request_data['template_name'])) {
            $template_name = sanitize_input($request_data['template_name']);
            $add_data['template_name'] = $template_name;
        } else {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add new template details but failed due to template name missing', $log_info);
            return false;
        }

        // Test ID check 
        if (isset($request_data['test_id']) && !empty($request_data['test_id'])) {
            $test_id = sanitize_input($request_data['test_id']);
        } else {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add new template details but failed due to test id missing', $log_info);
            return false;
        }

        // Check Institute id set
        if (isset($request_data['institute_id']) && !empty($request_data['institute_id'])) {
            $add_data['institute_id'] = sanitize_input($request_data['institute_id']);
        }


        // Insert Template data
        $db->table('test_templates')->insert($add_data);
        // Newly created templete id
        $template_id = $db->insertID();


        // Fetch Test Details
        $TestsModel = new TestsModel();
        $test_data = $TestsModel->get_test_details($test_id);

        if (empty($test_data)) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add new template details but failed due to test data missing', $log_info);
            return false;
        }


        // Template Config Rules
        /**
         * TEST_INTERFACE, TOTAL_MARKS, TEST_DURATION, RANDOM_QUESTIONS (Y/N, SHOW_RESULT (Y/N), SHOW_QUESTION_PAPER (Y/N), ALIGN_TIME_TEST (Y/N), ALIGN_TIME_STUDENT (Y/N), SHOW_RANK (Y/N) -The value Y/N or text value will go into value column
         */
        $TestTemplateConfigModel = new TestTemplateConfigModel();

        // Test Conduction template config
        $add_template_config_arr = array();
        $add_template_config_arr['template_id'] = $template_id;
        $add_template_config_arr['rule_name'] = 'EXAM_CONDUCTION';
        $add_template_config_arr['display_name'] = 'EXAM CONDUCTION';
        $add_template_config_arr['value'] = sanitize_input($test_data['exam_conduction']);

        if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add template config details but failed', $log_info);
            return false;
        }



        // Test interface template config
        $add_template_config_arr = array();
        $add_template_config_arr['template_id'] = $template_id;
        $add_template_config_arr['rule_name'] = 'TEST_INTERFACE';
        $add_template_config_arr['display_name'] = 'TEST INTERFACE';
        $add_template_config_arr['value'] = strtoupper(sanitize_input($test_data['test_ui']));

        if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add template config details but failed', $log_info);
            return false;
        }


        // Total marks template config
        $add_template_config_arr = array();
        $add_template_config_arr['template_id'] = $template_id;
        $add_template_config_arr['rule_name'] = 'TOTAL_MARKS';
        $add_template_config_arr['display_name'] = 'TOTAL MARKS';
        $add_template_config_arr['value'] = strtoupper(sanitize_input($test_data['total_marks']));
        if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add template config details but failed', $log_info);
            return false;
        }

        // Test duration template config
        $add_template_config_arr = array();
        $add_template_config_arr['template_id'] = $template_id;
        $add_template_config_arr['rule_name'] = 'TEST_DURATION';
        $add_template_config_arr['display_name'] = 'TEST DURATION';
        $add_template_config_arr['value'] = sanitize_input($test_data['duration']);
        if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add template config details but failed', $log_info);
            return false;
        }

        // Test No of Questions config
        $add_template_config_arr = array();
        $add_template_config_arr['template_id'] = $template_id;
        $add_template_config_arr['rule_name'] = 'NO_OF_QUESTIONS';
        $add_template_config_arr['display_name'] = 'NO OF QUESTIONS';
        $add_template_config_arr['value'] = sanitize_input($test_data['no_of_questions']);
        if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add template config details but failed', $log_info);
            return false;
        }


        // Random questions template config
        $add_template_config_arr = array();
        $add_template_config_arr['template_id'] = $template_id;
        $add_template_config_arr['rule_name'] = 'RANDOM_QUESTIONS';
        $add_template_config_arr['display_name'] = 'RANDOM QUESTIONS';
        $add_template_config_arr['value'] = strtoupper(sanitize_input($test_data['random_questions']));
        if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add template config details but failed', $log_info);
            return false;
        }

        // Show result template config
        $add_template_config_arr = array();
        $add_template_config_arr['template_id'] = $template_id;
        $add_template_config_arr['rule_name'] = 'SHOW_RESULT';
        $add_template_config_arr['display_name'] = 'SHOW RESULT';
        $add_template_config_arr['value'] = strtoupper(sanitize_input($test_data['show_result']));
        if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add template config details but failed', $log_info);
            return false;
        }


        // Show question paper template config
        $add_template_config_arr = array();
        $add_template_config_arr['template_id'] = $template_id;
        $add_template_config_arr['rule_name'] = 'SHOW_QUESTION_PAPER';
        $add_template_config_arr['display_name'] = 'SHOW QUESTION PAPER';
        $add_template_config_arr['value'] = strtoupper(sanitize_input($test_data['show_question_paper']));
        if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add template config details but failed', $log_info);
            return false;
        }

        // Align with test time template config
        $add_template_config_arr = array();
        $add_template_config_arr['template_id'] = $template_id;
        $add_template_config_arr['rule_name'] = 'ALIGN_TIME_TEST';
        $add_template_config_arr['display_name'] = 'ALIGN TIME TEST';
        $add_template_config_arr['value'] = strtoupper(sanitize_input($test_data['time_constraint']));
        if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add template config details but failed', $log_info);
            return false;
        }

        // Align with student time template config
        $add_template_config_arr = array();
        $add_template_config_arr['template_id'] = $template_id;
        $add_template_config_arr['rule_name'] = 'ALIGN_TIME_STUDENT';
        $add_template_config_arr['display_name'] = 'ALIGN TIME STUDENT';
        $add_template_config_arr['value'] = strtoupper(sanitize_input($test_data['student_time_constraint']));
        if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add template config details but failed', $log_info);
            return false;
        }

        // Show rank template config
        $add_template_config_arr = array();
        $add_template_config_arr['template_id'] = $template_id;
        $add_template_config_arr['rule_name'] = 'SHOW_RANK';
        $add_template_config_arr['display_name'] = 'SHOW RANK';
        $add_template_config_arr['value'] = strtoupper(sanitize_input($test_data['show_student_rank']));
        if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add template config details but failed', $log_info);
            return false;
        }


        // Fetch test mapped questions
        $TestsModel = new TestsModel();
        $test_questions_mapped_data = $TestsModel->test_questions_mapped($test_id);


        $question_map_arr = array();
        if (!empty($test_questions_mapped_data)) {
            $section = "";
            foreach ($test_questions_mapped_data as $test_question) {
                if ($section != $test_question['section']) {
                    $section =  $test_question['section'];
                    $question_map_arr[$section] = array();
                    $question_map_arr[$section]['section'] =  $test_question['section'];
                    $question_map_arr[$section]['from_question'] =  $test_question['question_number'];
                    $question_map_arr[$section]['weightage'] =  $test_question['weightage'];
                    $question_map_arr[$section]['negative_marks'] =  $test_question['negative_marks'];
                    $question_map_arr[$section]['question_type'] =  $test_question['question_type'];
                    $question_map_arr[$section]['to_question'] =  $test_question['question_number'];
                    $question_map_arr[$section]['subject_id'] =  $test_question['subject_id'];
                } else {
                    $question_map_arr[$section]['to_question'] =  $test_question['question_number'];
                }
            }
        }

        if (!empty($question_map_arr)) {
            foreach ($question_map_arr as $question_map) {

                $add_template_config_arr = array();
                $add_template_config_arr['template_id'] = $template_id;
                $add_template_config_arr['rule_name'] = 'SECTION_QUESTIONS';
                $add_template_config_arr['display_name'] = 'QUESTION SECTION';
                $add_template_config_arr['from_question'] = $question_map['from_question'];
                $add_template_config_arr['to_question'] = $question_map['to_question'];
                $add_template_config_arr['value'] = $question_map['section'];
                if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
                    // Log Message
                    $log_info =  [
                        'username' =>  $this->session->get('username')
                    ];
                    log_message('error', 'User {username} tried to add template config details but failed', $log_info);
                    return false;
                }

                $add_template_config_arr = array();
                $add_template_config_arr['template_id'] = $template_id;
                $add_template_config_arr['rule_name'] = 'SECTION_WEIGHTAGE';
                $add_template_config_arr['display_name'] = 'SECTION WEIGHTAGE';
                $add_template_config_arr['from_question'] = $question_map['from_question'];
                $add_template_config_arr['to_question'] = $question_map['to_question'];
                $add_template_config_arr['value'] = $question_map['weightage'];
                if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
                    // Log Message
                    $log_info =  [
                        'username' =>  $this->session->get('username')
                    ];
                    log_message('error', 'User {username} tried to add template config details but failed', $log_info);
                    return false;
                }


                $add_template_config_arr = array();
                $add_template_config_arr['template_id'] = $template_id;
                $add_template_config_arr['rule_name'] = 'SECTION_NEGATIVE_MARKS';
                $add_template_config_arr['display_name'] = 'SECTION NEGATIVE MARKS';
                $add_template_config_arr['from_question'] = $question_map['from_question'];
                $add_template_config_arr['to_question'] = $question_map['to_question'];
                $add_template_config_arr['value'] = $question_map['negative_marks'];
                if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
                    // Log Message
                    $log_info =  [
                        'username' =>  $this->session->get('username')
                    ];
                    log_message('error', 'User {username} tried to add template config details but failed', $log_info);
                    return false;
                }

                $add_template_config_arr = array();
                $add_template_config_arr['template_id'] = $template_id;
                $add_template_config_arr['rule_name'] = 'SECTION_QUESTION_TYPE';
                $add_template_config_arr['display_name'] = 'SECTION QUESTION TYPE';
                $add_template_config_arr['from_question'] = $question_map['from_question'];
                $add_template_config_arr['to_question'] = $question_map['to_question'];
                $add_template_config_arr['value'] = $question_map['question_type'];
                if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
                    // Log Message
                    $log_info =  [
                        'username' =>  $this->session->get('username')
                    ];
                    log_message('error', 'User {username} tried to add template config details but failed', $log_info);
                    return false;
                }

                //Added by Ajinkya to also add subject ID rule
                $add_template_config_arr = array();
                $add_template_config_arr['template_id'] = $template_id;
                $add_template_config_arr['rule_name'] = 'SECTION_SUBJECT';
                $add_template_config_arr['display_name'] = 'SECTION SUBJECT';
                $add_template_config_arr['from_question'] = $question_map['from_question'];
                $add_template_config_arr['to_question'] = $question_map['to_question'];
                $add_template_config_arr['value'] = $question_map['subject_id'];
                if (!$TestTemplateConfigModel->add_template_config_data($add_template_config_arr)) {
                    // Log Message
                    $log_info =  [
                        'username' =>  $this->session->get('username')
                    ];
                    log_message('error', 'User {username} tried to add template config details but failed', $log_info);
                    return false;
                }
            }
        }
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add new template details but failed', $log_info);
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "New Template created with template name " . $template_name,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);
            return $template_id;
        }
    }
    /*******************************************************/




    /**
     * Update Template Details
     *
     * @param array $request_data
     *
     * @return Boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_template_details(array $request_data)
    {

        $db = \Config\Database::connect();

        $db->transStart();

        // Template ID check 
        if (isset($request_data['template_id']) && !empty($request_data['template_id'])) {
            $template_id = sanitize_input($request_data['template_id']);
        } else {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add new template details but failed due to template id missing', $log_info);
            return false;
        }

        if (isset($request_data['template_name']) && !empty($request_data['template_name'])) {
            $template_name = sanitize_input($request_data['template_name']);
            $update_data['template_name'] = $template_name;
        }

        if (isset($request_data['is_disabled']) && !empty($request_data['is_disabled'])) {
            $update_data['is_disabled'] =  sanitize_input($request_data['is_disabled']);
        }


        // Update Template data
        $db->table('test_templates')->update($update_data, ["id" => $template_id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to update template details but failed', $log_info);
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Template details updated with template ID " . $template_id,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id')),
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);
            return $template_id;
        }
    }
    /*******************************************************/
}
