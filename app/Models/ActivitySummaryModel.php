<?php

namespace App\Models;

use CodeIgniter\Model;


class ActivitySummaryModel extends Model
{

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }


    public function startedResultCount($institute_id){
        $sql = "SELECT count(student_id) as startedCount,video_id 
        FROM activity_summary 
        WHERE watch_status = 1 
        AND video_id IN (select id from video_lectures where institute_id = :institute_id: ) 
        GROUP BY video_id";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        if ($query->getNumRows() > 0) {
            return $query->getResultArray();
        } else {
            return false;
        }
    }

    public function completedResultCount($institute_id){
        $sql = "SELECT count(student_id)  as completedCount,video_id 
        FROM activity_summary
        WHERE watch_status = 2 
        AND video_id IN (select id from video_lectures where institute_id = :institute_id: ) 
        GROUP BY video_id";

        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        if ($query->getNumRows() > 0) {
            return $query->getResultArray();
        } else {
            return false;
        }
    }

    public function averageResultCount(){
        $sql = "SELECT AVG(watched_percent) as avg,video_id 
        FROM activity_summary
        GROUP BY video_id";

        $query = $this->db->query($sql);

        if ($query->getNumRows() > 0) {
            return $query->getResultArray();
        } else {
            return false;
        }
    }
}
?>