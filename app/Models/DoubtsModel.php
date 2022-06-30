<?php

namespace App\Models;

use CodeIgniter\Model;

class DoubtsModel extends Model
{

    /*******************************************************/
    /**
     * Resolve Bulk Doubts Submit
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     * @since 2021/09/23
     * @return view
     */
    public function resolve_bulk_doubts_submit(array $data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $video_url = sanitize_input($data['video_url']);
        $doubt_resolution_text = sanitize_input($data['doubt_resolution_text']);
        $doubt_question_id = sanitize_input($data['doubt_question_id']);
        $answeredBy =  sanitize_input(decrypt_cipher(session()->get('login_id')));
        $current_date = date('Y-m-d H:i:s');
        $resource_url = $data['doubt_response_file'];


        $feedback_data = [
            'feedback_resolution_video_url' => $video_url,
            'feedback_resolution_image_url' => $resource_url,
            'feedback_resolution_text' => $doubt_resolution_text,
            'updated_date' => $current_date,
            'resolution' => 'Resolved',
            'answered_by' => $answeredBy
        ];

        if (isset($data['doubt_question_type']) && $data['doubt_question_type'] == 'video') {
            $col = 'video_id';
        } else if (isset($data['doubt_question_type']) && $data['doubt_question_type'] == 'general') {
            $col = 'id';
        } else {
            $col = 'question_id';
        }

        $db->table('exam_feedback')->update($feedback_data, [$col => $doubt_question_id]);

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
     * Move Doubt to Pending State
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function move_doubt_to_pending(array $data)
    {
        $db = \Config\Database::connect();
        $db->transStart();


        if (isset($data['doubt_question_type']) && $data['doubt_question_type'] == 'video') {
            $col = 'video_id';
        } else if (isset($data['doubt_question_type']) && $data['doubt_question_type'] == 'general') {
            $col = 'id';
        } else {
            $col = 'question_id';
        }

        $doubt_question_id = sanitize_input($data['doubt_question_id']);

        $feedback_data = [
            'resolution' => NULL
        ];

        $db->table('exam_feedback')->update($feedback_data, [$col => $doubt_question_id]);


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
