<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketsModel extends Model
{

    /***************************************************************************************/
    /**
     * Get Tickets List
     * @author PrachiP
     * @since 2021-12-11
     * @return Array
     */
    public function fetch_tickets(){
        $db = \Config\Database::connect();
        $sql_fetch_total_tickets =  $db->query("SELECT tickets.*,ticket_reasons.reason_name,super_admin.username,student.name as student_name,institute.institute_name
        FROM tickets 
        INNER JOIN ticket_reasons 
        ON ticket_reasons.id = tickets.reason_id
        INNER JOIN student on student.id = tickets.student_id
        INNER JOIN student_login on student_login.student_id = tickets.student_id
        INNER JOIN institute on student_login.institute_id = institute.id
        LEFT JOIN super_admin 
        ON tickets.staff_id = super_admin.id
        WHERE tickets.deleted_at IS NULL 
        ORDER BY tickets.`created_at`");
        return $sql_fetch_total_tickets->getResultArray();
    }
    /***************************************************************************************/

    /***************************************************************************************/
    /**
     * Get Ticket Data
     * @param Integer $ticket_id
     * @author PrachiP
     * @since 2021-12-13
     * @return Array
     */
    public function get_ticket_data($ticket_id){
        $db = \Config\Database::connect();
        $sql_fetch_total_tickets = $db->query("SELECT tickets.*,ticket_reasons.reason_name,student.name as student_name,student.email as student_email,
        student.mobile_no as student_mobile_number,test.test_name,institute_name,test.test_id,student.id as student_id
        FROM tickets
        INNER JOIN ticket_reasons 
        ON ticket_reasons.id = tickets.reason_id
        INNER JOIN student 
        ON student.id = tickets.student_id
        INNER JOIN student_login on student.id = student_login.student_id
        INNER JOIN institute on student_login.institute_id = institute.id
        LEFT JOIN test on test.test_id = tickets.test_id
        WHERE tickets.id = '$ticket_id' 
        AND tickets.deleted_at IS NULL");
        return $sql_fetch_total_tickets->getRowArray();
    }
    /***************************************************************************************/

    /***************************************************************************************/
    /**
     * Get ticket replies data
     * @param Integer $ticket_id
     * @author PrachiP
     * @since 2021-12-13
     * @return Array
     */
    public function get_tickets_replies_data($ticket_id){
        $db = \Config\Database::connect();
        $sql_query = $db->query("SELECT * , stud.student_name,s_admin.username
        FROM ticket_replies
        LEFT JOIN (select student.name as student_name,id  from student) stud
        ON stud.id = ticket_replies.student_id
        LEFT JOIN (select username ,id from super_admin ) s_admin
        ON s_admin.id = ticket_replies.staff_id
        WHERE ticket_replies.ticket_id = '$ticket_id' ");
        return $sql_query->getResultArray();
    }
    /***************************************************************************************/
}

?>