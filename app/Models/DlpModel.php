<?php

namespace App\Models;

use CodeIgniter\Model;

class DlpModel extends Model
{

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * DLP Content Count
     *
     * @param integer $institute_id
     * @param string $content_type
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    function dlp_content_count(int $institute_id, string $content_type)
    {
        $db = \Config\Database::connect();
        $sql_fetch_total_classrooms = "SELECT count(content_id) AS dlp_content_count 
        FROM dlp_chp_cls_content_map
        INNER JOIN video_lectures 
        ON dlp_chp_cls_content_map.content_id = video_lectures.id
        WHERE video_lectures.institute_id = :institute_id:
        AND video_lectures.type = :content_type:
        AND video_lectures.is_disabled = '0' ";

        $query = $db->query($sql_fetch_total_classrooms, [
            'institute_id' => sanitize_input($institute_id),
            'content_type' => sanitize_input($content_type)
        ]);
        $result = $query->getRowArray();
        return $result['dlp_content_count'];
    }
    /*******************************************************/



    /**
     * DLP Assignment Count
     *
     * @param integer $institute_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    function dlp_assignments_count(int $institute_id)
    {
        $db = \Config\Database::connect();
        $sql_fetch_total_classrooms = "SELECT count(dlp_chp_cls_content_map.test_id) AS dlp_assignments_count 
        FROM dlp_chp_cls_content_map
        INNER JOIN test 
        ON dlp_chp_cls_content_map.test_id = test.test_id
        WHERE dlp_chp_cls_content_map.test_id IS NOT NULL 
        AND test.institute_id = :institute_id:
        AND test.status = 'Active' ";

        $query = $db->query($sql_fetch_total_classrooms, [
            'institute_id' => sanitize_input($institute_id)
        ]);
        $result = $query->getRowArray();
        return $result['dlp_assignments_count'];
    }
    /*******************************************************/


    /**
     * DLP Resource Details
     *
     * @param integer $resource_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    function dlp_resource_details(int $resource_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT video_lectures.video_name, video_lectures.video_url, video_lectures.activation_date,video_lectures.expiry_date FROM video_lectures
        WHERE video_lectures.id= :resource_id: ";

        $query = $db->query($sql, [
            'resource_id' => sanitize_input($resource_id)
        ]);
        $result = $query->getRowArray();
        return $result;
    }
    /*******************************************************/


    /**
     * DLP Resource Mapping Details
     *
     * @param integer $resource_mapping_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    function dlp_resource_mapping_details(int $resource_mapping_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT dlp_chapters_classroom_map.chapter_id, chapters.chapter_name FROM dlp_chp_cls_content_map
        INNER JOIN dlp_chapters_classroom_map
        ON dlp_chp_cls_content_map.classroom_id = dlp_chapters_classroom_map.classroom_id
        INNER JOIN chapters
        ON dlp_chapters_classroom_map.chapter_id = chapters.id AND dlp_chapters_classroom_map.status = 1
        WHERE dlp_chp_cls_content_map.id='$resource_mapping_id'";

        $query = $db->query($sql, [
            'resource_mapping_id' => sanitize_input($resource_mapping_id)
        ]);
        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/



    /**
     * Load DLP Chapter Content
     *
     * @param integer $chapter_id
     * @param integer $classroom_id
     * @param integer $institute_id
     * @param string $content_type
     * @param integer $is_disabled
     *
     * @return Array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    function dlp_chapter_content(int $chapter_id, int $classroom_id, int $institute_id, string $content_type, int $is_disabled = 0)
    {
        $db = \Config\Database::connect();

        if ($content_type == 'Test') {
            $sql = "SELECT dlp_chp_cls_content_map.*, test.test_name, test.start_date FROM dlp_chp_cls_content_map
            INNER JOIN test
            ON dlp_chp_cls_content_map.test_id = test.test_id AND test.institute_id = :institute_id: AND test.status='Active'
            INNER JOIN chapters
            ON dlp_chp_cls_content_map.chapter_id = chapters.id
            WHERE dlp_chp_cls_content_map.chapter_id = :chapter_id: AND
            dlp_chp_cls_content_map.classroom_id = :classroom_id:
            AND  dlp_chp_cls_content_map.is_disabled = $is_disabled
            ORDER BY dlp_chp_cls_content_map.content_order ";
        } else {
            $sql = "SELECT dlp_chp_cls_content_map.*, video_lectures.id AS resource_id, video_lectures.video_name, video_lectures.video_url, 
            video_lectures.type, video_lectures.test_id, video_lectures.created_date, video_lectures.status, video_lectures.progress, video_lectures.activation_date 
            FROM dlp_chp_cls_content_map
            INNER JOIN video_lectures
            ON dlp_chp_cls_content_map.content_id = video_lectures.id AND video_lectures.institute_id = :institute_id: AND video_lectures.is_disabled= 0 AND video_lectures.type = :content_type: 
            INNER JOIN chapters
            ON dlp_chp_cls_content_map.chapter_id = chapters.id
            WHERE dlp_chp_cls_content_map.chapter_id = :chapter_id: AND
            dlp_chp_cls_content_map.classroom_id = :classroom_id: AND
            dlp_chp_cls_content_map.is_disabled = $is_disabled
            ORDER BY dlp_chp_cls_content_map.content_order ";
        }


        $query = $db->query($sql, [
            'institute_id' => sanitize_input($institute_id),
            'chapter_id' => sanitize_input($chapter_id),
            'classroom_id' => sanitize_input($classroom_id),
            'content_type' => sanitize_input($content_type),
            'is_disabled' => sanitize_input($is_disabled)
        ]);
        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/




    /**
     * Deleted DLP Content
     *
     * @param array $postData
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    function deleted_dlp_content(array $postData)
    {
        $db = \Config\Database::connect();
        $response = array();
        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
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
            $orderby = " video_lectures.created_date ";
        } else {
            $orderby = $columns_valid[$col];
        }

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " AND (
                video_lectures.video_name like '%" . $searchValue . "%' or 
                video_lectures.video_url like '%" . $searchValue . "%') ";
        }

        ## Fetch records
        $sql = "SELECT * 
        FROM video_lectures 
        WHERE is_disabled = '1' 
        AND institute_id = '$instituteID' ";

        $totalRecords = $db->query($sql)->getNumRows();
        $sql .= $searchQuery;

        $totalRecordwithFilter = $db->query($sql)->getNumRows();

        $sql .= " ORDER BY " . $orderby . " " . $dir . " LIMIT $start,$rowperpage ";
        // Result with filtered data with limit
        $records = $db->query($sql)->getResult();


        $data = array();
        $i = $start + 1;
        foreach ($records as $record) {
            $nestedData = array();
            $nestedData["id"] =  $record->id;
            $nestedData["sr_no"] =  $i++;
            $nestedData["video_name"] = strtoupper($record->video_name);
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



    /**
     * Update Chapters Entities
     *
     * @param [type] $data
     *
     * @return String
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_chapter_entities($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        $msg = "FAILED";
        // Disabling or deleting the chapter
        if (isset($data['disableClassroomId'])) {
            $disableClassroomId = sanitize_input($data['disableClassroomId']);

            $update_array = array(
                'status' => 0
            );
            $db->table('dlp_chapters_classroom_map')->update($update_array, ['id' => $disableClassroomId]);

            $msg = "SUCCESS";
        }
        // Enabling the chapter
        if (isset($data['enableChapterClassroomMappingId'])) {
            $enableChapterClassroomMappingId = sanitize_input($data['enableChapterClassroomMappingId']);

            $update_array = array(
                'status' => 1
            );
            $db->table('dlp_chapters_classroom_map')->update($update_array, ['id' => $enableChapterClassroomMappingId]);

            $msg = "SUCCESS";
        }

        // Updating the chapter order in the DLP classroom

        if (isset($data['resourceMappingId']) && isset($data['chapterOrder'])) {


            $resourceMappingId = sanitize_input($data['resourceMappingId']);
            $chapterOrder = sanitize_input($data['chapterOrder']);

            $update_array = array(
                'chapter_no' => $chapterOrder
            );
            $db->table('dlp_chapters_classroom_map')->update($update_array, ['id' => $resourceMappingId]);

            $msg = "SUCCESS";
        }

        // Updating the chapter content order in the DLP classroom
        if (isset($data['resourceMappingId']) && isset($data['contentOrder'])) {


            $resourceMappingId = sanitize_input($data['resourceMappingId']);
            $contentOrder = sanitize_input($data['contentOrder']);

            $update_array = array(
                'content_order' => $contentOrder
            );
            $db->table('dlp_chp_cls_content_map')->update($update_array, ['id' => $resourceMappingId]);

            $msg = "SUCCESS";
        }

        // Disabling or deleting the resource
        if (isset($data['resourceMappingId']) && isset($data['resourceType']) && $data['resourceType'] === "resourceType") {
            $resourceMappingId = sanitize_input($data['resourceMappingId']);

            $update_array = array(
                'is_disabled' => 1
            );
            $db->table('dlp_chp_cls_content_map')->update($update_array, ['id' => $resourceMappingId]);

            $msg = "SUCCESS";
        }

        // Enable the resource
        if (isset($data['resourceMappingId']) && isset($data['processType']) && $data['processType'] === "enableResource") {
            $resourceMappingId = sanitize_input($data['resourceMappingId']);

            $update_array = array(
                'is_disabled' => 0
            );
            $db->table('dlp_chp_cls_content_map')->update($update_array, ['id' => $resourceMappingId]);

            $msg = "SUCCESS";
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return "FAILED";
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'DLP Chapter',
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return $msg;
        }
    }
    /*******************************************************/




    /**
     * Update Subject Entities
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_subject_entities($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        $msg = "FAILED";
        // Disabling or deleting the subject
        if (isset($data['disableSubjectMapId'])) {
            $disableSubjectMapId = sanitize_input($data['disableSubjectMapId']);
            $disableCourseId = sanitize_input($data['disableCourseId']);

            $update_array = array(
                'is_disabled' => 1
            );
            $db->table('dlp_subjects_classroom_map')->update($update_array, ['classroom_id' => $disableCourseId, 'subject_id' => $disableSubjectMapId]);

            $msg = "SUCCESS";
        }

        // Enabling the subject
        if (isset($data['enableSubjectMapId'])) {
            $enableSubjectMapId = sanitize_input($data['enableSubjectMapId']);
            $enableCourseId = sanitize_input($data['enableCourseId']);

            $update_array = array(
                'is_disabled' => 0
            );
            $db->table('dlp_subjects_classroom_map')->update($update_array, ['classroom_id' => $enableCourseId, 'subject_id' => $enableSubjectMapId]);

            $msg = "SUCCESS";
        }
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return "FAILED";
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'DLP Subject',
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return $msg;
        }
    }
    /*******************************************************/


    /**
     * Add DLP Subject 
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_dlp_subject($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $classroom_id = sanitize_input($data['classroom_id']);

        $dlp_subjects = array_map("sanitize_input", $data['dlp_subjects']);

        foreach ($dlp_subjects as $subject_id) {
            $add_array = array(
                'classroom_id' => $classroom_id,
                'subject_id' => $subject_id
            );
            $db->table('dlp_subjects_classroom_map')->insert($add_array);
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return 0;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'Subject in Classroom',
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
     * Add DLP Content
     * It is only added for resource type - test
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_dlp_content($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $classrooms = array_map("sanitize_input", $data['classrooms']);
        $chapter_id = sanitize_input($data['chapter_id']);
        $resource_type = sanitize_input($data['resource_type']);

        //If the resource is video, then save first in content table
        if ($resource_type == "VIDEO") {
            $type = "DLPVIDEO";
            if (isset($data['activation_date']) && $data['activation_date'] != '') {
                $activation_date = $data['activation_date'];
                $add_video_array['activation_date'] = DefaultTimezone(sanitize_input($activation_date));
            }


            // Expiry Date
            $expiry_date = NULL;
            if (isset($data['expiry_date']) && $data['expiry_date'] != '') {
                $expiry_date = sanitize_input($data['expiry_date']);
                $add_video_array['expiry_date'] = DefaultTimezone(sanitize_input($expiry_date));
            }

            $add_video_array['video_name'] = sanitize_input(strtoupper($data['video_name']));
            $add_video_array['subject_id'] = sanitize_input($data['subject_id']);
            $add_video_array['video_url'] = sanitize_input($data['video_url']);
            $add_video_array['institute_id'] = sanitize_input($data['institute_id']);
            $add_video_array['type'] = sanitize_input($type);
            $db->table('video_lectures')->insert($add_video_array);
            $resource_id = $db->insertID();
            $add_array['content_id'] = $resource_id;
        }

        if ($resource_type == "TEST") {

            //If the resource is TEST then update mapping directly
            if (isset($data['test_id']) && $data['test_id'] != '') {
                $test_id = sanitize_input($data['test_id']);
            } else {
                //Setting NULL in string when no value selected for avoid foreign key bug
                $test_id = 'NULL';
            }


            // Checking if the test and chapter mapping is unique or not. 
            $ids = join("','", $classrooms);
            $sql_check_chapter_test_unique = "SELECT id FROM dlp_chp_cls_content_map WHERE chapter_id='$chapter_id' AND classroom_id in ($ids) AND test_id='$test_id'";
            $result = $db->query($sql_check_chapter_test_unique)->getRowArray();
            if (!empty($result)) {
                return 0;
            }
            $add_array['test_id'] = $test_id;
        }

        foreach ($classrooms as $classroom_id) {
            $add_array['chapter_id'] = $chapter_id;
            $add_array['classroom_id'] = $classroom_id;
            $db->table('dlp_chp_cls_content_map')->insert($add_array);
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return 0;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'DLP Content',
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
     * Add DLP Chapter
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_dlp_chapter($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $classroom_id = sanitize_input($data['classroom_id']);

        $dlp_chapters = array_map("sanitize_input", $data['dlp_chapters']);

        foreach ($dlp_chapters as $chapter_id) {
            $add_array = array(
                'classroom_id' => $classroom_id,
                'chapter_id' => $chapter_id
            );
            $db->table('dlp_chapters_classroom_map')->insert($add_array);
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return 0;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'DLP Chapter',
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
     * Update DLP Resource
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_dlp_resource($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $resource_id = sanitize_input($data['resource_id']);
        $resource_mapping_id = sanitize_input($data['resource_mapping_id']);
        $resource_name = sanitize_input($data['resource_name']);
        $chapter_id = sanitize_input($data['chapter_id']);


        // Activation Date
        $activation_date = NULL;
        if (isset($data['activation_date']) && $data['activation_date'] != '') {
            $activation_date = DefaultTimezone(sanitize_input($data['activation_date']));
        }

        // Expiry Date
        $expiry_date = NULL;
        if (isset($data['expiry_date']) && $data['expiry_date'] != '') {
            $expiry_date = sanitize_input($data['expiry_date']);
        }

        //Updating video data
        if (isset($data['video_url'])) {

            $video_url = sanitize_input($data['video_url']);

            //Updating resource VIDEO data
            if (isset($data['activation_date']) && $data['activation_date'] != '') {
                $update_array = array(
                    'video_name' => $resource_name,
                    'video_url' => $video_url,
                    'activation_date' => $activation_date,
                    'expiry_date' => $expiry_date
                );
                $db->table('video_lectures')->update($update_array, ['id' => $resource_id]);
            } else {
                $update_array = array(
                    'video_name' => $resource_name,
                    'video_url' => $video_url
                );
                $db->table('video_lectures')->update($update_array, ['id' => $resource_id]);
            }
        }

        //Updating document data
        if (isset($data['document_url'])) {

            $document_url = sanitize_input($data['document_url']);



            // Updating resource DOC data
            if (isset($data['activation_date']) && $data['activation_date'] != '') {
                $update_array = array(
                    'video_name' => $resource_name,
                    'video_url' => $document_url,
                    'activation_date' => $activation_date
                );
                $db->table('video_lectures')->update($update_array, ['id' => $resource_id]);
            } else {

                $update_array = array(
                    'video_name' => $resource_name,
                    'video_url' => $document_url
                );
                $db->table('video_lectures')->update($update_array, ['id' => $resource_id]);
            }
        }


        // Updating resource mapping to change chapter for the resource
        $update_array = array(
            'chapter_id' => $chapter_id
        );
        $db->table('dlp_chp_cls_content_map')->update($update_array, ['id' => $resource_mapping_id]);


        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return 0;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'DLP Resource',
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return 1;
        }
    }
    /*******************************************************/


    /**
     * Clone DLP Chapter Content
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function clone_dlp_chapter_content($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        $package_id = sanitize_input($data['package_id']);
        $chapter_ids = sanitize_input($data['chapter_ids']);

        $classroom_ids_arr = $data['classroom_ids'];


        foreach ($classroom_ids_arr as $new_classroom_id) {

            // DLP Chapter Classroom Map Query
            $query_dlp_chapters_classroom_map = "INSERT INTO dlp_chapters_classroom_map (chapter_id, classroom_id, status, chapter_no, created_date) SELECT chapter_id, $new_classroom_id, status, chapter_no, created_date FROM dlp_chapters_classroom_map WHERE classroom_id = '$package_id' AND chapter_id IN ($chapter_ids) ";
            $db->query($query_dlp_chapters_classroom_map);


            // DLP Chapter Classroom Content Map
            $query_dlp_chp_cls_content_map = "INSERT INTO dlp_chp_cls_content_map (chapter_id, classroom_id, content_id, test_id, content_order, created_date, is_disabled) SELECT chapter_id, $new_classroom_id, content_id, test_id, content_order, created_date, is_disabled FROM dlp_chp_cls_content_map WHERE classroom_id = '$package_id' AND chapter_id IN ($chapter_ids)";

            $db->query($query_dlp_chp_cls_content_map);
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return 0;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'DLP clone chapter content',
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);
            return 1;
        }
    }
    /*******************************************************/
}
