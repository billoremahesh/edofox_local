<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionModel extends Model
{

    /**
     * Delete Question
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_question($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $update_data = [
            'status' => 'D'
        ];

        $id = sanitize_input($data['question_id']);
        $db->table('test_questions')->update($update_data, ['id' => $id]);

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
     * Update Question
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_question($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $correct_ans = "";
        if (isset($data['correct_ans_text'])) {
            $correct_ans = $data['correct_ans_text'];
        } elseif (isset($data['correct_ans_single'])) {
            $correct_ans = $data['correct_ans_single'];
        } elseif (isset($data['correct_ans_multiple']) && !empty($data['correct_ans_multiple'])) {
            $correct_ans = implode(",", $data['correct_ans_multiple']);
        }


        if (isset($data['verifiedCheck']) && isset($data['verified_date']) && $data['verified_date'] == "") {
            $update_data = [
                'level' => sanitize_input($data['update_difficulty_level']),
                'question_type' => sanitize_input($data['update_question_type']),
                'verified_date' => date('Y-m-d H:i:s'),
                'verified_by' => $data['updater'],
                'correct_answer' => $correct_ans
            ];
        } else {
            $update_data = [
                'level' => sanitize_input($data['update_difficulty_level']),
                'question_type' => sanitize_input($data['update_question_type']),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by_admin' => $data['updater'],
                'correct_answer' => $correct_ans
            ];
        }


        $id = sanitize_input($data['question_id']);
        $db->table('test_questions')->update($update_data, ['id' => $id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            return true;
        }
    }
    /*******************************************************/
}
