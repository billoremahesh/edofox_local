<?php

namespace App\Models;

use CodeIgniter\Model;

class RoutineResultsModel extends Model
{

    public function fetch_results(string $unique_identifier)
    {
        $sql = "SELECT count(routine_results.id) as cnt,if(routine_results.success ='0', 'Failed','Success') as success_status
        FROM routine_results 
        JOIN edofox_routines 
        ON routine_results.routine_id = edofox_routines.id 
        WHERE edofox_routines.routine_identifier = :unique_identifier: group by success";

        $query = $this->db->query($sql, [
            'unique_identifier' => sanitize_input($unique_identifier)
        ]);

        return $query->getResultArray();
    }


    public function fetch_failed_results(string $unique_identifier)
    {
        $sql = "SELECT routine_results.student_username,routine_results.result
        FROM routine_results 
        JOIN edofox_routines 
        ON routine_results.routine_id = edofox_routines.id 
        WHERE edofox_routines.routine_identifier = :unique_identifier: AND success='0' ";

        $query = $this->db->query($sql, [
            'unique_identifier' => sanitize_input($unique_identifier)
        ]);

        return $query->getResultArray();
    }
}
