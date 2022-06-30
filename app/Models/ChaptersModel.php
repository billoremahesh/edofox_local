<?php

namespace App\Models;

use CodeIgniter\Model;

class ChaptersModel extends Model
{

    /**
     * Get Subject Chapters
     *
     * @param [type] $subject_id
     * @param [type] $institute_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_subject_chapters($subject_id, $institute_id)
    {

        $db = \Config\Database::connect();
        $sql = "SELECT chapters.*,IF(questionsCount.que_count IS NULL, 0,questionsCount.que_count) que_count 
        FROM chapters
        LEFT JOIN (select count(*) as que_count,chapter from test_questions where (status = 'A' or status is NULL) and institute_id = :institute_id: and is_dummy = 0  group by chapter) as questionsCount 
        ON questionsCount.chapter = chapters.id 
        WHERE subject = :subject_id: 
        AND (institute_id = :institute_id: OR institute_id IS NULL) 
        AND (status != 'D' OR status is null) 
        ORDER BY chapter_name";

        $query = $db->query($sql, [
            'institute_id' => sanitize_input($institute_id),
            'subject_id' => sanitize_input($subject_id)
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/


    /**
     * Get Chapter Details
     *
     * @param [type] $chapter_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_chapter_details($chapter_id)
    {

        $db = \Config\Database::connect();
        $sql = "SELECT chapters.*
        FROM chapters 
        WHERE chapters.id = :chapter_id: ";

        $query = $db->query($sql, [
            'chapter_id' => sanitize_input($chapter_id)
        ]);

        return $query->getRowArray();
    }
    /*******************************************************/



    /**
     * Get DLP Classroom Chapters
     *
     * @param integer $classroom_id
     * @param integer $subject_id
     * @param integer $is_disabled
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_dlp_chapters(int $classroom_id, int $subject_id, int $is_disabled = 1)
    {

        $db = \Config\Database::connect();
        $sql = "SELECT chapters.*, dlp_chapters_classroom_map.classroom_id,dlp_chapters_classroom_map.chapter_no,dlp_chapters_classroom_map.status
        FROM dlp_chapters_classroom_map
        INNER JOIN chapters
        ON chapters.id = dlp_chapters_classroom_map.chapter_id
        WHERE dlp_chapters_classroom_map.classroom_id = :classroom_id:
        AND dlp_chapters_classroom_map.status = :is_disabled:
        AND (chapters.status != 'D' OR chapters.status is null) 
        AND chapters.subject = :subject_id:
        ORDER BY chapter_no";

        $query = $db->query($sql, [
            'classroom_id' => sanitize_input($classroom_id),
            'subject_id' => sanitize_input($subject_id),
            'is_disabled' => sanitize_input($is_disabled)
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/



    /**
     * Get DLP Classroom Chapters
     *
     * @param integer $classroom_id
     * @param integer $subject_id
     * @param integer $is_disabled
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_dlp_not_mapped_chapters(int $classroom_id, int $subject_id)
    {

        $db = \Config\Database::connect();
        $sql = "SELECT chapters.*
        FROM chapters
        WHERE chapters.subject = :subject_id:
        AND chapters.id NOT IN (select chapter_id from dlp_chapters_classroom_map where classroom_id = :classroom_id:)
        AND (chapters.status != 'D' OR chapters.status is null) 
        ORDER BY chapter_name";

        $query = $db->query($sql, [
            'classroom_id' => sanitize_input($classroom_id),
            'subject_id' => sanitize_input($subject_id)
        ]);

        return $query->getResultArray();
    }
    /*******************************************************/


    /**
     * Filtered Chapters
     *
     * @param array $postData
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function filtered_chapters($postData = array())
    {

        $db = \Config\Database::connect();
        $response = array();
        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value
        $instituteID = $postData['instituteID'];


        // Server Side Processing - ORDER BY and ASC,DESC
        $order = array();
        if (isset($postData['order'])) {
            $order = $postData['order'];
        }


        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "asc";
        }

        $columns_valid = array();

        if (!isset($columns_valid[$col])) {
            $orderby = "test_subjects.subject,chapter_name";
        } else {
            $orderby = $columns_valid[$col];
        }

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " AND ( 
                test_subjects.subject LIKE '%" . $searchValue . "%' OR
                chapter_name LIKE '%" . $searchValue . "%'
                )";
        }


        ## Fetch records

        $sql = "SELECT chapters.*,test_subjects.subject 
        FROM chapters
        JOIN test_subjects
        ON chapters.subject = test_subjects.subject_id
        WHERE ( chapters.institute_id = '$instituteID' or chapters.institute_id IS NULL)
         AND (chapters.status != 'D' OR chapters.status is null) ";

        $totalRecords = $db->query($sql)->getNumRows();
        $sql .= $searchQuery;

        $totalRecordwithFilter = $db->query($sql)->getNumRows();
        $sql .= " ORDER BY $orderby $dir LIMIT $start,$rowperpage";

        // Result with filtered data with limit
        $records = $db->query($sql)->getResult();

        $data = array();
        $sr_no = $start + 1;
        foreach ($records as $record) {

            $nestedData = array();
            $encrypted_chapter_id = encrypt_string($record->id);

            $action_btn_div = "";
            $edit_option = "";
            $delete_option = "";
            if (!empty($record->institute_id)) {
                $edit_option_encode =  htmlspecialchars("<a class='btn btn-sm'   onclick=" . "show_edit_modal('modal_div','update_chapter_modal','chapters/update_chapter_modal/" . $encrypted_chapter_id . "');" . " data-bs-toggle='tooltip' title='Update chapter details'><i class='material-icons material-icon-small'>edit</i></a>");
                $edit_option_decode = htmlspecialchars_decode($edit_option_encode);
                $edit_option = $edit_option_decode;


                $delete_option_encode =  htmlspecialchars("<a class='btn btn-sm'    onclick=" . "show_edit_modal('modal_div','delete_chapter_modal','chapters/delete_chapter_modal/" . $encrypted_chapter_id . "');" . "  data-bs-toggle='tooltip' title='Delete Chapter'><i class='material-icons material-icon-small text-danger'>delete</i></a>");
                $delete_option_decode = htmlspecialchars_decode($delete_option_encode);
                $delete_option = $delete_option_decode;


                $action_btn_div = $edit_option . " " . $delete_option;
            } else {
                $action_btn_div = "<i class='fas fa-exclamation-circle' data-bs-toggle='tooltip' title='Default Chapters, not editable'></i>";
            }




            $nestedData["sr_no"] = $sr_no++;
            $nestedData["subject"] = $record->subject;
            $nestedData["chapter_name"] = $record->chapter_name;
            $nestedData["created_date"] =  changeDateTimezone($record->created_date);
            $nestedData["action"] =  $action_btn_div;
            $data[] = $nestedData;
        }
        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $response;
    }
    /*******************************************************/




    /*******************************************************/
    /**
     * Add New Chapters
     * @return void
     * @author PrachiP
     * @since 2021-11-16
     */
    public function add_new_chapters($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $institute_id = decrypt_cipher($_POST['institute_id']);
        $dlp_subject = sanitize_input($_POST['dlp_subject']);
        $chapter_names = sanitize_input($_POST['chapter_names']);
        $chapter_names_array = explode(",", $chapter_names);

        foreach ($chapter_names_array as $chapter) {
            $query = $db->query("SELECT * 
            FROM chapters 
            WHERE chapter_name = '$chapter' 
            AND subject= '$dlp_subject' 
            AND institute_id = '$institute_id'");
            $row = $query->getResultArray();
            $rowcount = count($row);
            if ($rowcount < 1) {
                $insert_array = array(
                    'chapter_name' => $chapter,
                    'subject' => $dlp_subject,
                    'institute_id' => $institute_id
                );

                $db->table('chapters')->insert($insert_array);
            }
        }
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
     * Add new chapter
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_new_chapter($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $institute_id = sanitize_input($data['institute_id']);
        $chapter_name = sanitize_input($data['chapter_name']);
        $subject_id = sanitize_input($data['subject_id']);


        $insert_array = array(
            'chapter_name' => $chapter_name,
            'subject' => $subject_id,
            'institute_id' => $institute_id
        );

        $db->table('chapters')->insert($insert_array);


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
     * Update chapter details
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_chapter($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $chapter_name = sanitize_input($data['chapter_name']);
        $chapter_id = sanitize_input($data['chapter_id']);
        $subject_id = sanitize_input($data['subject_id']);


        $update_array = array(
            'chapter_name' => $chapter_name,
            'subject' => $subject_id
        );

        $db->table('chapters')->update($update_array, ['id' => $chapter_id]);


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
     * Delete chapter
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_chapter($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $chapter_id = sanitize_input($data['chapter_id']);


        $update_array = array(
            'status' => 'D'
        );

        $db->table('chapters')->update($update_array, ['id' => $chapter_id]);


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
