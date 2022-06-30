<?php

namespace App\Models;

use CodeIgniter\Model;

class SubjectsModel extends Model
{


    /**
     * Get Institute Subject List
     *
     * @param integer $institute_id
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_subjects(int $institute_id)
    {

        $db = \Config\Database::connect();
        $sql = "SELECT * 
        FROM test_subjects 
        WHERE ( institute_id = :institute_id: 
        OR institute_id IS NULL ) AND ( test_subjects.status != 'D' OR test_subjects.status IS NULL )
        ORDER BY subject";

        $query = $db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/


    /**
     * Get DLP Classroom Subjects
     *
     * @param integer $classroom_id
     * @param integer $is_disabled
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_dlp_classroom_subjects(int $classroom_id, int $is_disabled = 0)
    {

        $db = \Config\Database::connect();
        $sql = "SELECT test_subjects.subject_id, test_subjects.subject FROM dlp_subjects_classroom_map
        INNER JOIN test_subjects
        ON dlp_subjects_classroom_map.subject_id = test_subjects.subject_id
        WHERE classroom_id = :classroom_id: AND is_disabled= :is_disabled: AND ( test_subjects.status != 'D' OR test_subjects.status IS NULL )";

        $query = $db->query($sql, [
            'is_disabled' => sanitize_input($is_disabled),
            'classroom_id' => sanitize_input($classroom_id)
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/


    public function dlp_not_mapped_subjects(int $institute_id, int $classroom_id)
    {

        $db = \Config\Database::connect();

        $sql = "SELECT * 
        FROM test_subjects 
        WHERE (institute_id = :institute_id: 
        OR institute_id IS NULL)
        AND  subject_id NOT IN (select subject_id from dlp_subjects_classroom_map where classroom_id = :classroom_id)
        AND ( test_subjects.status != 'D' OR test_subjects.status IS NULL )
        ORDER BY subject";

        $query = $db->query($sql, [
            'institute_id' => sanitize_input($institute_id),
            'classroom_id' => sanitize_input($classroom_id)
        ]);

        return $query->getResultArray();
    }


    /**
     * Get Subject Details
     *
     * @param integer $subject_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_subject_detail(int $subject_id)
    {

        $db = \Config\Database::connect();
        $sql = "SELECT * 
        FROM test_subjects 
        WHERE subject_id = :subject_id: ";

        $query = $db->query($sql, [
            'subject_id' => sanitize_input($subject_id)
        ]);

        return $query->getRowArray();
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * Add New Subjects
     * @return void
     * @author PrachiP
     * @since 2021-11-16
     */
    public function add_new_subjects($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $institute_id = decrypt_cipher($data['institute_id']);

        $subject_names = sanitize_input($data['subject_names']);
        $subject_names_array = explode(",", $subject_names);

        foreach ($subject_names_array as $subject) {
            $query = $db->query("SELECT * 
            FROM test_subjects 
            WHERE subject = '$subject'
            AND ( test_subjects.status != 'D' OR test_subjects.status IS NULL )
            AND (institute_id = '$institute_id' 
            OR institute_id IS NULL)");
            $row = $query->getResultArray();
            $rowcount = count($row);
            if ($rowcount < 1) {
                $insert_array = array(
                    'subject' => $subject,
                    'institute_id' => $institute_id
                );

                $db->table('test_subjects')->insert($insert_array);
            }
        }
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return 0;
        } else {

            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'New Subject from DLP',
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);

            return 1;
        }
    }
    /*******************************************************/


    /**
     * Add new subject
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_new_subject($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $institute_id = sanitize_input($data['institute_id']);
        $subject_name = sanitize_input($data['subject_name']);


        $insert_array = array(
            'subject' => $subject_name,
            'institute_id' => $institute_id
        );

        $db->table('test_subjects')->insert($insert_array);


        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return 0;
        } else {
            return 1;
        }
    }
    /*******************************************************/

    /**
     * Update Subject Details
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_subject($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $subject_name = sanitize_input($data['subject_name']);
        $subject_id = sanitize_input($data['subject_id']);


        $update_array = array(
            'subject' => $subject_name
        );

        $db->table('test_subjects')->update($update_array, ['subject_id' => $subject_id]);


        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return 0;
        } else {
            return 1;
        }
    }
    /*******************************************************/


    /**
     * Delete Subject
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_subject($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $subject_id = sanitize_input($data['subject_id']);


        $update_array = array(
            'status' => 'D'
        );

        $db->table('test_subjects')->update($update_array, ['subject_id' => $subject_id]);


        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return 0;
        } else {
            return 1;
        }
    }
    /*******************************************************/
}
