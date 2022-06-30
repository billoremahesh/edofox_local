<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturesModel extends Model
{

    /*******************************************************/
    /**
     * Fetch video lectures
     * @author PrachiP
     * @since 2021/09/04
     * @return Array
     */
    public function fetch_video_lectures()
    {
        $db = \Config\Database::connect();
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        $query = $db->query("SELECT video_lectures.id as videoId,video_name,video_lectures.created_date,package_name,subject,size,video_url
        FROM video_lectures
        LEFT JOIN packages ON packages.id = video_lectures.classroom_id
        LEFT JOIN test_subjects ON test_subjects.subject_id = video_lectures.subject_id
        WHERE video_lectures.institute_id = $instituteID AND video_lectures.is_disabled = 0 
        ORDER BY video_lectures.created_date DESC");
        return $query->getResultArray();
    }
    /*******************************************************/



    /**
     * Get Video Details
     *
     * @param integer $video_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_video_details(int $video_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT video_lectures.*
        FROM video_lectures
        WHERE id = :video_id: ";

        $query = $db->query($sql, [
            'video_id' => sanitize_input($video_id)
        ]);


        if ($query->getNumRows() == 1) {
            return $query->getRowArray();
        } else {
            return false;
        }
    }
    /*******************************************************/



    public function get_detailed_analysis(int $video_id, string $type = 'video')
    {
        $db = \Config\Database::connect();

        if($type == 'video'){
            $sql = "SELECT student_id, watched_percent as watched,watched_duration as duration,watched_times,watched_activity, last_watched as lastWatched,student.name,student.mobile_no,student.roll_no,student.created_date 
            FROM activity_summary 
            join student on student.id = student_id
            where video_id = :video_id: group by student_id order by lastWatched desc ";
        }else{
            $sql = "SELECT student_id, watched_percent as watched,watched_duration as duration,watched_times,watched_activity, last_watched as lastWatched,student.name,student.mobile_no,student.roll_no,student.created_date 
            FROM activity_summary 
            join student on student.id = student_id
            where session_id = :video_id: group by student_id order by lastWatched desc ";
        }


        $query = $db->query($sql, [
            'video_id' => sanitize_input($video_id)
        ]);


        if ($query->getNumRows() == 1) {
            return $query->getRowArray();
        } else {
            return false;
        }
    }

 
    
}
