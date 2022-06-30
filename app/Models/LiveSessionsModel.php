<?php

namespace App\Models;

use CodeIgniter\Model;

class LiveSessionsModel extends Model
{

    /**
     * Get Live Session Details
     *
     * @param integer $live_session_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_live_session_details(int $live_session_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT live_session.*
        FROM live_session
        WHERE id = :live_session_id: ";

        $query = $db->query($sql, [
            'live_session_id' => sanitize_input($live_session_id)
        ]);


        if ($query->getNumRows() == 1) {
            return $query->getRowArray();
        } else {
            return false;
        }
    }


    /**
     * Fetch Live Sessions Records
     *
     * @param [Integer] $institute_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_live_lectures($institute_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT live_session.id as videoId,session_name,live_session.created_date,recording_url,live_session.status, file_size,package_name,joined,student.name as creator 
        FROM live_session
        JOIN packages 
        ON packages.id = live_session.classroom_id
        LEFT JOIN (select count(*) as joined, session_id from activity_summary where status = 'LIVE_JOINED' group by session_id)
        as studentsJoined 
        ON studentsJoined.session_id = live_session.id 
        LEFT JOIN student 
        ON student.id = live_session.created_by
        WHERE packages.institute_id = :institute_id:  
        ORDER BY created_date DESC ";

        $query = $db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);
        return $query->getResultArray();
    }
    /*******************************************************/


    public function live_classrooms(array $request_data)
    {
        $db = \Config\Database::connect();
        $admin_id = sanitize_input($request_data['admin_id']);
        $institute_id = sanitize_input($request_data['institute_id']);
        $current_time = date('Y-m-d H:i:s');
        $status = "";
        $status_check_condition = "";
        if (isset($request_data['status']) && !empty($request_data['status'])) {
            $status = sanitize_input($request_data['status']);
            if ($status == "Todays") {
                $status_check_condition = " AND live_session.status = 'Active' AND date(live_session.start_date) = curdate() AND live_session.end_date >  '$current_time' ";
            }
            if ($status == "Scheduled") {
                $status_check_condition = " AND live_session.status = 'Active' AND date(live_session.start_date) > curdate()";
            }
            if ($status == "Completed") {
                $status_check_condition = " AND live_session.end_date < '$current_time' ";
            }
        }

        $search_string_condition = "";
        if (isset($request_data['search_string']) && !empty($request_data['search_string'])) {
            $search_string = sanitize_input($request_data['search_string']);
            $search_string_condition = " AND (live_session.session_name LIKE '%$search_string%' OR packages.package_name LIKE '%$search_string%')";
        }

        if (in_array("all_perms", $request_data['perms'])) {
            $sql = "SELECT live_session.*,packages.package_name,session_users.stream_id
            FROM live_session
            LEFT JOIN session_users
        ON session_users.session_id = live_session.id
            JOIN packages 
            ON packages.id = live_session.classroom_id
            WHERE packages.institute_id = :institute_id:  
            AND live_session.status != 'Failed'
            AND session_users.admin_id = :admin_id:
            AND live_session.proctoring = 0
            $status_check_condition $search_string_condition
            ORDER BY end_date DESC ";
        } else {

            $classroom_mapped_arr = session()->get('classroom_mapped_arr');
            $sql = "SELECT live_session.*,packages.package_name,session_users.stream_id
            FROM live_session
            LEFT JOIN session_users
        ON session_users.session_id = live_session.id
            JOIN packages 
            ON packages.id = live_session.classroom_id AND live_session.classroom_id IN ($classroom_mapped_arr)
            WHERE packages.institute_id = :institute_id: 
            AND live_session.status != 'Failed' 
            AND session_users.admin_id = :admin_id:
            AND live_session.proctoring = 0
            $status_check_condition $search_string_condition
            ORDER BY end_date DESC ";
        }

        $query = $db->query($sql, [
            'admin_id' => sanitize_input($admin_id),
            'institute_id' => sanitize_input($institute_id),
        ]);
        return $query->getResultArray();
    }


    public function get_live_lecture_details(string $schedule_id, string $stream_id, int $admin_id)
    {
        $sql = "SELECT live_session.*,session_users.stream_id
        FROM live_session 
        LEFT JOIN session_users
        ON session_users.session_id = live_session.id
        WHERE live_session.schedule_id = :schedule_id: 
        AND session_users.stream_id = :stream_id:
        AND session_users.admin_id = :admin_id: ";

        $query = $this->db->query($sql, [
            'schedule_id' => sanitize_input($schedule_id),
            'stream_id' => sanitize_input($stream_id),
            'admin_id' => sanitize_input($admin_id)
        ]);
        return $query->getRowArray();
    }
}
