<?php

namespace App\Models;

use CodeIgniter\Model;

use PhpOffice\PhpSpreadsheet\Helper\Sample;

class TestQuestionsMapModel extends Model
{


    /**
     * Fetch Mapped Questions
     *
     * @param [type] $test_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_mapped_questions($test_id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT test_questions_map.*
        FROM test_questions_map  
        WHERE test_questions_map.test_id = '$test_id' 
        AND test_questions_map.question_disabled = 0 
        ORDER BY test_questions_map.question_number asc");
        $result_for_que_map = $query->getResultArray();
        return $result_for_que_map;
    }
    /*******************************************************/






    /**
     * Update test question sequence
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_test_question_sequence($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $que_map_id = $data['que_map_id'];
        $question_number = $data['question_number'];
        $sql = "UPDATE test_questions_map
                    SET question_number = '$question_number' 
                    WHERE id = '$que_map_id'  ";
        $db->query($sql);

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
