<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{



    /**
     * Check Mapped Classrooms to staff in case of not given global permissions
     *
     * @return string
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function apply_classroom_filter()
    {

        $classroom_mapped_ids = session()->get('classroom_mapped_arr');
        $append_string = "";
        if (!empty($classroom_mapped_ids)) {
            $append_string = " JOIN student_institute 
            ON student.id = student_institute.student_id 
            AND student_institute.package_id  IN ($classroom_mapped_ids) AND student_institute.is_disabled = 0 ";
        }
        return $append_string;
    }
    /*******************************************************/



    /*****************************************************************
     *############### START OF FUNCTIONS - RETURNS COUNT  ############*
     *****************************************************************/


    public function get_all_students_count()
    {
        $db  = \Config\Database::connect();

        $sql_fetch_todays_exam = "SELECT count(*) as student_cnt 
        FROM student
        JOIN student_login on student.id = student_login.student_id 
        WHERE (student_login.student_access = '' OR student_login.student_access is NULL) ";

        $query = $db->query($sql_fetch_todays_exam);

        $result = $query->getRowArray();
        if (!empty($result)) {
            return $result['student_cnt'];
        } else {
            return 0;
        }
    }

    /**
     * Student Count
     *
     * @param integer $institute_id
     *
     * @return integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_students_count(int $institute_id)
    {
        $db  = \Config\Database::connect();

        $sql_student_cnt = "select COUNT(DISTINCT(student_institute.student_id)) student_cnt 
        from student 
        join student_login on student.id = student_login.student_id 
        left join student_institute on student_institute.student_id = student.id 
        left join packages on packages.id = student_institute.package_id
        where student_institute.institute_id = :institute_id: AND (student_login.student_access = '' OR student_login.student_access is NULL or student_login.student_access = 'Teacher') AND student_institute.is_disabled = '0' ";

        $query = $db->query($sql_student_cnt, [
            'institute_id' => sanitize_input($institute_id)
        ]);

        $result = $query->getRowArray();
        if (!empty($result)) {
            return $result['student_cnt'];
        } else {
            return 0;
        }
    }
    /*******************************************************/


    /*****************************************************************
     *############### END OF FUNCTIONS - RETURNS COUNT  ############*
     *****************************************************************/


    /*****************************************************************
     *################# START OF FUNCTIONS - SINGLE STUDENT DATA ###############*
     *****************************************************************/


    /**
     * Get Single Student Details
     *
     * @param integer $student_id
     * @param integer $instituteID
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_student_details(int $student_id, int $instituteID)
    {
        ## Fetch records
        $db = \Config\Database::connect();
        $builder = $db->table("student");
        $builder->select("student.*,student_institute.student_id,student_login.student_access,student_login.universal_token,student.previous_marks,count.testsTaken as testsTaken,totalTest.totalTests as totalTests,student_login.username,packages.package_name,student_institute.created_date");
        $builder->join("student_login", "student.id = student_login.student_id AND student_login.institute_id = '$instituteID' ", "inner");
        $builder->join("student_institute", "student.id = student_institute.student_id AND student_institute.is_disabled = 0", "inner");
        $builder->join("packages", "packages.id = student_institute.package_id", "inner");
        $builder->join("(select count(*) as testsTaken,student_id from test_status join test on test_status.test_id = test.test_id where institute_id = '$instituteID' group by student_id) as count", "count.student_id = student.id", "left");
        $builder->join("(select count(*) as totalTests,package_id from test where institute_id = '$instituteID' group by package_id) as totalTest", "totalTest.package_id = student_institute.package_id", "left");
        $builder->where("student_institute.institute_id = '$instituteID'");
        $builder->where("packages.is_disabled = 0");
        $builder->where("student.id = '$student_id'");
        $records = $builder->get()->getRowArray();
        return $records;
    }

    public function get_attend_student_details(int $student_id, int $instituteID)
    {
        ## Fetch records
         ## Fetch records
         $db = \Config\Database::connect(); 

        $student_id=13369;
        $instituteID=3; 
       $sql_fetch_data="SELECT count(*) as is_present,date FROM `institute_schedule_data` join institute_schedule_attendance on institute_schedule_data.schedule_id = institute_schedule_attendance.schedule_data_id where institute_schedule_attendance.student_id = $student_id and institute_schedule_data.is_disabled = 0 and institute_schedule_attendance.is_disabled = 0 and institute_schedule_attendance.is_present = 1 group by week(date)";
       $query = $db->query($sql_fetch_data); 
       $weekly_present = $query->getResultArray();
       
       $sql_fetch_data="SELECT count(*) as is_absent,date FROM `institute_schedule_data` join institute_schedule_attendance on institute_schedule_data.schedule_id = institute_schedule_attendance.schedule_data_id where institute_schedule_attendance.student_id = $student_id and institute_schedule_data.is_disabled = 0 and institute_schedule_attendance.is_disabled = 0 and institute_schedule_attendance.is_present = 0 group by week(date)";
       $query = $db->query($sql_fetch_data); 
       $weekly_absend = $query->getResultArray();

       $absent_week_temp=[];
       $absent_temp=[];
       foreach($weekly_absend as $w_abs){
        $absent_week_temp[]=$w_abs['date'];
        $absent_temp[$w_abs['date']]=$w_abs;
       }

       $week_date=[];
       $week_persent=[];
        foreach($weekly_present as $value){
          
            if(in_array($value['date'],$absent_week_temp)){
                $absent = $absent_temp[$value['date']]['is_absent'];
                $present = $value['is_present'];
                $total_class=$present+$absent;
                $week_date[]=$value['date'];
                $week_persent[]=round(($present*100)/$total_class);
            }else{
                $week_date[]=$value['date'];
                $week_persent[]=100;
            }
        }

        $data['week_date']=$week_date;
        $data['week_per']=$week_persent;

    
        $sql_fetch_data="SELECT count(*)
        as present_count,test_subjects.subject_id,test_subjects.subject,packages.package_name FROM institute_schedule_data
       join institute_schedule on institute_schedule_data.schedule_id = institute_schedule.id
       join packages on institute_schedule.classroom_id = packages.id
       join test_subjects on institute_schedule.subject_id = test_subjects.subject_id
       left join institute_schedule_attendance on institute_schedule_attendance.schedule_data_id = institute_schedule_data.id and institute_schedule_attendance.student_id = $student_id and institute_schedule_attendance.is_present = 1
       where institute_schedule.is_disabled = 0 and institute_schedule_data.is_disabled = 0 and institute_schedule.classroom_id in (select package_id from student_institute where is_disabled = 0 and student_id = $student_id) and institute_schedule_attendance.is_disabled = 0 group by subject_id";
         $query = $db->query($sql_fetch_data); 
         $data['subject_attendanc_present'] = $query->getResultArray();   


         $sql_fetch_data="SELECT count(*)
         as present_count,test_subjects.subject_id,test_subjects.subject,packages.package_name FROM institute_schedule_data
        join institute_schedule on institute_schedule_data.schedule_id = institute_schedule.id
        join packages on institute_schedule.classroom_id = packages.id
        join test_subjects on institute_schedule.subject_id = test_subjects.subject_id
        left join institute_schedule_attendance on institute_schedule_attendance.schedule_data_id = institute_schedule_data.id and institute_schedule_attendance.student_id = $student_id and institute_schedule_attendance.is_present = 0
        where institute_schedule.is_disabled = 0 and institute_schedule_data.is_disabled = 0 and institute_schedule.classroom_id in (select package_id from student_institute where is_disabled = 0 and student_id = $student_id) and institute_schedule_attendance.is_disabled = 0 group by subject_id";
          $query = $db->query($sql_fetch_data); 
          $subject_attendanc_absent = $query->getResultArray();  
         $subject_abs=[];
         $abset_tem=[];
        foreach($subject_attendanc_absent as $subject_ab){
         $subject_abs[$subject_ab['subject_id']]=$subject_ab;
         $abset_tem[]=$subject_ab['subject_id'];
        }
        $data['abset_tem']=$abset_tem;
        $data['subject_attend_abs']=$subject_abs;
      

        $sql_fetch_data="SELECT institute_schedule.id,institute_schedule.title as session_name,institute_schedule_data.DATE as session_date ,institute_schedule.starts_at as session_time, test_subjects.subject as session_subject ,
        packages.package_name as classroom FROM `institute_schedule` 
        JOIN test_subjects ON test_subjects.subject_id=institute_schedule.subject_id 
        JOIN institute_schedule_data ON institute_schedule_data.schedule_id=institute_schedule.id 
        JOIN packages ON packages.id = institute_schedule.classroom_id 
        JOIN student_institute ON student_institute.package_id=institute_schedule.classroom_id 
        WHERE institute_schedule.frequency='weekly' AND student_institute.is_disabled=0 
        AND institute_schedule.is_disabled=0 AND institute_schedule_data.is_disabled=0
        AND student_institute.student_id=$student_id AND institute_schedule.institute_id =$instituteID order by institute_schedule.id desc";
         $query = $db->query($sql_fetch_data); 
         $data['reqular_session'] = $query->getResultArray();   
       
         $sql_fetch_data="SELECT test_name  as session_name
         ,DATE(test.start_date) as session_date
         ,TIME(test.end_date) as session_time , packages.package_name as session_subject,packages.package_name as classroom,test_status.status FROM `test` join test_package_map on test_package_map.test_id = test.test_id and test_package_map.package_id in (select package_id from student_institute where is_disabled = 0 and student_id = $student_id) join packages on test_package_map.package_id = packages.id left join test_status on test.test_id = test_status.test_id and test_status.student_id = $student_id where test.status = 'Active' and test.test_id in (select test_id from test_package_map where package_id in (select package_id from student_institute where is_disabled = 0 and student_id = $student_id)) and test.end_date < now() group by test_package_map.package_id order by test.start_date desc";
         $query = $db->query($sql_fetch_data); 
         $data['exam'] = $query->getResultArray();    
         $sql_fetch_data="SELECT institute_schedule.id,institute_schedule_attendance.is_present FROM `institute_schedule` LEFT JOIN institute_schedule_data ON institute_schedule_data.schedule_id=institute_schedule.id LEFT JOIN institute_schedule_attendance ON institute_schedule_attendance.schedule_data_id=institute_schedule_data.id 
         WHERE institute_schedule.is_disabled=0 
         AND institute_schedule_attendance.is_disabled=0 
         AND institute_schedule_data.is_disabled=0 AND institute_schedule_attendance.is_present=1
         AND institute_schedule_attendance.student_id=$student_id order by institute_schedule.id desc";
         $query = $db->query($sql_fetch_data); 
         $session_attendance = $query->getResultArray();
         $tem_arr=[];
    
         foreach($session_attendance as $value){  
                // $ses_status[$value['id']]=$value['is_present']; 
                $tem_arr[]=$value['id'];
         } 
      
         $data['attendance_list']=$tem_arr;
         
        //  $data['records_table'] = array_merge($reqular_session,$reqular_session1);  
        return $data;
    }
    /*******************************************************/



    /**
     * Student Details
     *
     * @param integer $student_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_details(int $student_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT student.*,student_login.student_access,student_login.universal_token,student_login.username
        FROM student_login 
        INNER JOIN student
        ON student_login.student_id = student.id
        WHERE student.id = :student_id: ";

        $query = $db->query($sql, [
            'student_id' => sanitize_input($student_id)
        ]);
        $result = $query->getRowArray();
        return $result;
    }
    /*******************************************************/



    /**
     * Student Details based on username
     *
     * @param string $username
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_username_details(string $username)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT student.*
        FROM student_login
        join student
        on student.id = student_login.student_id
        where student_login.username = '$username' AND
        (student_login.student_access IS NULL OR student_login.student_access != 'Deleted')
        ");
        return $query->getRowArray();
    }
    /*******************************************************/



    /**
     * Fetch Student Data Using Token
     *
     * @param [type] $token
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_student_data($token)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT student.name, student_login.username 
        FROM student_login 
        INNER JOIN student
        ON student_login.student_id = student.id
        WHERE universal_token = '$token'");
        $result = $query->getRowArray();
        return $result;
    }
    /*******************************************************/


    /**
     * GET Student Package Data
     *
     * @param integer $stu_pkg_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_student_package_data(int $stu_pkg_id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT package_id, status ,student_id
        FROM student_institute 
        WHERE id= $stu_pkg_id 
        AND student_institute.is_disabled = 0");
        $result = $query->getRowArray();
        return $result;
    }
    /*******************************************************/



    /**
     * Fetch Student Classrooms
     *
     * @param integer $student_id
     * @param integer $institute_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_student_classrooms(int $student_id, int $institute_id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT student_institute.id, student_institute.status, packages.package_name 
        FROM student_institute 
        INNER JOIN packages 
        ON student_institute.package_id=packages.id 
        WHERE student_institute.student_id='$student_id' 
        AND student_institute.institute_id='$institute_id' 
        AND packages.is_disabled = 0 
        AND student_institute.is_disabled = 0");
        $result = $query->getResultArray();
        return $result;
    }
    /*******************************************************/


    /*****************************************************************
     *################# END OF FUNCTIONS - SINGLE STUDENT DATA  ######*
     *****************************************************************/



    /*****************************************************************
     *################# START OF FUNCTIONS - FILTERED DATA ###############*
     *****************************************************************/




    /**
     * Get Filtered Students Data
     *
     * @param array $postData
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_students_data($requestData = array())
    {

        $db = \Config\Database::connect();

        $instituteID = $requestData['instituteID'];
        $institute_details = $requestData['institute_details'];
        $whatsapp_credits = 0;
        if (!empty($institute_details['whatsapp_credits']) && $institute_details['whatsapp_credits'] > 0) {
            $whatsapp_credits = 1;
        }
        // Server Side Processing - ORDER BY and ASC,DESC
        $order = array();
        if (isset($requestData['order'])) {
            $order = $requestData['order'];
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
            $orderby = "student.roll_no";
        } else {
            $orderby = $columns_valid[$col];
        }

        $package_disabled_condition = " AND student_institute.is_disabled = 0";
        if (isset($filtertype) && $filtertype == 'Deleted') {
            $package_disabled_condition = "";
        }



        $searchQuery = "";
        $packageQuery = "";
        $typeQuery = " AND ( (student_login.student_access IS NULL OR student_login.student_access = '') OR (student_login.student_access = 'Disabled') )";

        if (isset($requestData['searchbox'])) {
            $search_string =  sanitize_input($requestData['searchbox']);
            // Saving value in session for loading on reload of page
            $_SESSION['student_search_string'] = $search_string;
            $searchQuery .= " AND ( 
            student.name LIKE '%" . $search_string . "%' OR
            student.roll_no LIKE '%" . $search_string . "%' OR
            student.mobile_no LIKE '%" . $search_string . "%' OR
            student.gender LIKE '%" . $search_string . "%' OR
            student.email LIKE '%" . $search_string . "%' OR
            student_login.username LIKE '%" . $search_string . "%' OR
            student.extra_details LIKE '%" . $search_string . "%'
            )";
        }

        // Saving value in session for loading on reload of page
        $_SESSION['student_list_classroom_filter'] = (isset($requestData['package'])) ? $requestData['package'] : "";
        if (isset($requestData['package'])) {
            $numItems = count($requestData['package']);
            $i = 0;
            $packageQuery .= " AND ( ";
            foreach ($requestData['package'] as &$value) {
                $_packageId = $value;
                if (++$i === $numItems) {
                    $packageQuery .= " student_institute.package_id = '$_packageId' ";
                } else {
                    $packageQuery .= " student_institute.package_id = '$_packageId' OR ";
                }
            }
            $packageQuery .= " ) ";
        }


        // Check Filter Type
        $filtertype = sanitize_input($requestData['filtertype']);
        // Saving value in session for loading on reload of page
        $_SESSION['student_list_filtertype'] = $filtertype;
        if (isset($filtertype) && $filtertype == 'Teacher') {
            $typeQuery = " AND student_login.student_access = 'Teacher' ";
        }

        if (isset($filtertype) && $filtertype == 'student') {
            $typeQuery = " AND (student_login.student_access IS NULL OR student_login.student_access = '') ";
        }

        if (isset($filtertype) && $filtertype == 'Disabled') {
            $typeQuery = " AND student_login.student_access = 'Disabled' ";
        }

        if (isset($filtertype) && $filtertype == 'Deleted') {
            $typeQuery = " AND student_login.student_access = 'Deleted' ";
        }

        // Paid and Unpaid Students Filter
        if (isset($filtertype) && $filtertype == 'UnPaid') {
            $typeQuery = " AND student_institute.status = 'Created' ";
        }

        if (isset($filtertype) && $filtertype == 'Paid') {
            $typeQuery = " AND student_institute.status = 'Completed' ";
        }

        //Total number of records for institute
        $totalsql = "select COUNT(DISTINCT(student_institute.student_id)) student_cnt from student 
        join student_login on student.id = student_login.student_id 
        left join student_institute on student_institute.student_id = student.id 
        left join packages on packages.id = student_institute.package_id
        where student_institute.institute_id = $instituteID $searchQuery $typeQuery $packageQuery $package_disabled_condition";


        $totalquery = $this->db->query($totalsql);
        $totalresult = $totalquery->getRowArray();
        $totalData = $totalresult['student_cnt'];

        $sql = "SELECT student_institute.id as stu_inst_id, student_institute.created_date, packages.package_name,packages.is_disabled package_is_disabled,  student.name, student.mobile_no, student.roll_no, student.email,
        student.parent_mobile_no,student.password,student.gender,student.username,student.id as stud_id,student.date_of_birth,student.caste_category,student.profile_pic,student.previous_marks,count.testsTaken as testsTaken,totalTest.totalTests as totalTests,student.student_access,student_login.universal_token,student.extra_details,student.whatsapp_opt_in 
        from (select distinct student_institute.student_id,name,mobile_no,roll_no,email,parent_mobile_no,gender,date_of_birth,caste_category,previous_marks,profile_pic,student.id, username, student_login.password, student_login.student_access,student.extra_details,student.whatsapp_opt_in 
        from student join student_institute on student.id = student_institute.student_id 
        join student_login on student.id = student_login.student_id 
        join packages on packages.id = student_institute.package_id
        where student_institute.institute_id = '$instituteID' $searchQuery  $typeQuery $packageQuery $package_disabled_condition ORDER BY FIELD(student_login.student_access, NULL,'','Teacher','Disabled','Deleted') ASC LIMIT " . $requestData['start'] . " ," . $requestData['length'] . " ) as student 
        join student_institute on student.student_id = student_institute.student_id
        join student_login on student.student_id = student_login.student_id
        join packages on packages.id = student_institute.package_id
        left join (select count(*) as testsTaken,student_id from test_status join test on test_status.test_id = test.test_id where institute_id = $instituteID group by student_id) as count on count.student_id = student.student_id
        left join (select count(*) as totalTests,package_id from test where institute_id = '$instituteID' group by package_id) as totalTest on totalTest.package_id = student_institute.package_id
        where student_institute.institute_id = '$instituteID' $package_disabled_condition";
        // Result with filtered data with limit
        $sql .= " ORDER BY FIELD(student_login.student_access, NULL,'','Teacher','Disabled','Deleted') ASC, $orderby $dir";
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        $data = array();

        $i =  1;
        foreach ($result as $row) {
            // preparing an array
            //If already exists, then just add the package
            $key = searchForId($row['stud_id'], $data);
            $student_id = $row['stud_id'];
            //echo "Key found as =>". $key;
            if ($key != -1) {
                $nestedData = $data[$key];

                if (strpos($nestedData[5], $row["package_name"]) === false && $row["package_is_disabled"] == 0) {
                    if (empty($nestedData[5])) {
                        $nestedData[5] =  $row["package_name"];
                    } else {
                        $nestedData[5] = $nestedData[5] . "," . $row["package_name"];
                    }
                    //Update tests taken and total tests
                    //Split existing string by '/'
                    if (isset($row["testsTaken"]) && isset($row["totalTests"]) && isset($row["totalTests"]) > 0) {
                        $testsTakenDisplay = $row["testsTaken"] . "/" . $row["totalTests"];
                        $values = explode("/", $nestedData[7]);
                        // $testsTaken = intval($values[0]) + intval($row["testsTaken"]);
                        // $totalTests = intval($values[1]) + intval($row["totalTests"]);
                        $nestedData[7] = $row["testsTaken"] . "/" . $row["totalTests"];
                    }


                    $data[$key] = $nestedData;
                }
                continue;
            }
            //echo "No key found. ". $key. "Adding data to new => ". $row["name"];
            $nestedData = array();

            $whatsapp_badge = "";
            if ($row['whatsapp_opt_in'] == 1) {
                $whatsapp_badge_encode =  htmlspecialchars("<span class='success_badge' data-bs-toggle='tooltip'  title='This student eligible to receive WhatsApp message'><i class='fab fa-whatsapp'></i></span>");
                $whatsapp_badge_decode = htmlspecialchars_decode($whatsapp_badge_encode);
                $whatsapp_badge = $whatsapp_badge_decode;
            }

            $nestedData[] = $row["stud_id"];
            $nestedData[] = $i;
            $nestedData[] = sanitize_input($row["roll_no"]);
            $nestedData[] = sanitize_input($row["name"])." ". $whatsapp_badge;
            $nestedData[] = sanitize_input($row["mobile_no"]);
            if ($row["package_is_disabled"] == 0) {
                $nestedData[] = $row["package_name"];
            } else {
                $nestedData[] = "";
            }
            $nestedData[] = sanitize_input($row["username"]);
            $testsTakenDisplay = "0/0";
            if (isset($row["testsTaken"]) && isset($row["totalTests"]) && isset($row["totalTests"]) > 0) {
                $testsTakenDisplay = $row["testsTaken"] . "/" . $row["totalTests"];
            }
            $nestedData[] = $testsTakenDisplay;
            $nestedData[] = changeDateTimezone($row["created_date"]);
            $nestedData[] = sanitize_input($row["email"]);
            $nestedData[] = sanitize_input($row["gender"]);
            $nestedData[] = sanitize_input($row["caste_category"]);
            $nestedData[] = sanitize_input($row["previous_marks"]);
            $nestedData[] = sanitize_input($row["parent_mobile_no"]);

            if (isset($row["profile_pic"]) && !empty($row["profile_pic"])) {
                $stu_profile_img_url = base_url() . $row["profile_pic"];
            } else {
                $stu_profile_img_url = base_url() . "dist/img/blank-profile-picture.png";
            }

            $profile_pic_wrapper = htmlspecialchars("<a href='" . $stu_profile_img_url . "' target='_blank'><div class='profile-image' style='background-image: url(" . $stu_profile_img_url . ");'></div></a>");
            $nestedData[] = htmlspecialchars_decode($profile_pic_wrapper);


            // Styling account type badge
            $badge_class = "bg-success";
            $student_access = "Student";
            if (!empty($row["student_access"])) {
                $student_access = $row["student_access"];
                if ($student_access == "Teacher") {
                    $badge_class = "bg-info";
                } else {
                    $badge_class = "bg-danger";
                }
            }
            $student_access_tag = "<span class='badge rounded-pill $badge_class'>$student_access</span>";
            $nestedData[] = htmlspecialchars_decode(htmlspecialchars($student_access_tag));

            // Extra/Additional Details
            $additional_details_arr = explode("|", $row['extra_details']);
            $additional_details_desc = "";
            if (!empty($additional_details_arr)) {
                foreach ($additional_details_arr as $additional_detail) {
                    $additional_details_desc .= "<b>" . $additional_detail . "</b><br/>";
                }
            }
            $nestedData[] = htmlspecialchars_decode(htmlspecialchars($additional_details_desc));

            $update_permission_button = '';

            $update_student_id = $row['stud_id'];
            $encrypted_student_id = encrypt_string($update_student_id);


            $check_performance_form = "<li><a  class='dropdown-item' href=" . base_url('students/performance_report/' . $encrypted_student_id) . "> Check Performance </a></li>";
            $check_attendance_form = "<li><a  class='dropdown-item' href=" . base_url('students/attendance_performance_report/' . $encrypted_student_id) . ">Attendance Report </a></li>";


            $edit_students_classroom_form = "<li><a  class='dropdown-item' href=" . base_url('students/students_classroom/' . $encrypted_student_id) . "> Edit Student's Classrooms </a></li>";





            $blockButtonText = 'Block Student Account';
            $blockType = 'disable';
            if ($row['student_access'] == 'Disabled') {
                $blockButtonText = 'Unblock Student account';
                $blockType = 'unblock';
            }


            $send_account_invite = "<li><a  class='dropdown-item more_options_btn_link' onclick=" . "send_account_invite('" . $row['username'] . "');" . "> Send Account Invite </a></li>";

            $whatsapp_invite = "";
            if ($whatsapp_credits == 1) {
                $whatsapp_invite = "<li><a  class='dropdown-item more_options_btn_link' onclick=" . "send_whatsapp_invite('" . $student_id . "');" . "> Send WhatsApp Invite </a></li>";
            }


            $dropdown_wrapper_code = htmlspecialchars("<div class='dropdown'><button class='btn btn-default dropdown-toggle more_option_button' type='button' id='studentDropdownMenu' data-bs-toggle='dropdown'  data-bs-auto-close='outside'  aria-expanded='false'><i class='fa fa-ellipsis-h' aria-hidden='true'></i>
            </button><ul class='dropdown-menu dropdown-menu-end' aria-labelledby='studentDropdownMenu'> $check_performance_form $check_attendance_form <li role='separator' class='divider'></li> $update_permission_button  $edit_students_classroom_form <li role='separator' class='divider'></li><li><a  class='dropdown-item more_options_btn_link' onclick=" . "show_edit_modal('modal_div','update_student_details_modal','students/update_student_details_modal/" . $encrypted_student_id . "');" . "> Update Student Details </a></li><li><a  class='dropdown-item more_options_btn_link' href=" . base_url('students/update_password/' . $encrypted_student_id) . "> Update Student Password</a></li>$send_account_invite<li>$whatsapp_invite<a  class='dropdown-item more_options_btn_link' onclick=" . "show_edit_modal('modal_div','disable_student_modal','students/disable_student_modal/" . $encrypted_student_id . "/" . $blockType . "');" . "> $blockButtonText </a></li><li><a class='dropdown-item more_options_btn_link' onclick=" . "show_edit_modal('modal_div','delete_student_modal','students/delete_student_modal/" . $encrypted_student_id . "');" . "> Delete Student </a></li></ul></div>");


            if ($row['student_access'] != "Deleted") {
                $nestedData[] = htmlspecialchars_decode($dropdown_wrapper_code);
            } else {
                $nestedData[] = "";
            }

            $data[] = $nestedData;

            $i++;
        }




        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalData),
            "data"            => $data
        );
        return $json_data;
    }
    /*******************************************************/

    /**
     * Get Student Tokens
     *
     * @param array $requestData
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function student_tokens($requestData = array())
    {

        $db = \Config\Database::connect();

        $instituteID = $requestData['instituteID'];


        // Server Side Processing - ORDER BY and ASC,DESC
        $order = array();
        if (isset($requestData['order'])) {
            $order = $requestData['order'];
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
            $dir = "desc";
        }

        $columns_valid = array();

        if (!isset($columns_valid[$col])) {
            $orderby = "created_date";
        } else {
            $orderby = $columns_valid[$col];
        }


        $filter_condn = "";
        if (isset($requestData['access_filter']) && !empty($requestData['access_filter'])) {
            $access_filter = sanitize_input($requestData['access_filter']);
            if ($access_filter == "deleted") {
                $filter_condn = " AND student_tokens.is_disabled = '1' ";
            } elseif ($access_filter == "allowed") {
                $filter_condn = " AND student_tokens.is_blocked = '0' AND student_tokens.is_disabled = '0' ";
            } elseif ($access_filter == "blocked") {
                $filter_condn = " AND student_tokens.is_blocked = '1' AND student_tokens.is_disabled = '1' ";
            }
        }

        $searchQuery = "";
        $_SESSION['student_search_string'] = "";
        if (!empty($requestData['searchbox'])) {
            $searched_term = sanitize_input($requestData['searchbox']);
            $_SESSION['student_search_string'] = $searched_term;
            $searchQuery .= " AND ( 
            student.name LIKE '%" . $searched_term . "%' OR
            student_login.username LIKE '%" . $searched_term . "%'
            )";
        }


        $sql = "select student_tokens.*, student.name, student_login.username
        from student_tokens 
        join student 
        on student.id = student_tokens.student_id
        join student_login 
        on student_login.student_id = student_tokens.student_id 
        where student_login.institute_id = '$instituteID' ";

        $totalquery = $this->db->query($sql);
        $resultTotalData = $totalquery->getResultArray();
        $totalData = count($resultTotalData);


        $sql .= " $searchQuery $filter_condn ";
        $total_filtered_query = $this->db->query($sql);
        $resultTotalDataFiltered = $total_filtered_query->getResultArray();
        $totalDataFiltered = count($resultTotalDataFiltered);

        // Result with filtered data with limit
        $sql .= " ORDER BY $orderby $dir LIMIT " . $requestData['start'] . " ," . $requestData['length'];

        $total_filtered_limit_query = $this->db->query($sql);
        $filter_result = $total_filtered_limit_query->getResultArray();

        $data = array();

        $i =  1;
        foreach ($filter_result as $row) {

            $nestedData = array();

            $badge_class = "badge bg-success";
            $student_access = "Allowed";

            if ($row["is_disabled"] == '1') {
                $badge_class = "badge bg-warning";
                $student_access = 'Deleted';
            }

            if ($row["is_blocked"] == '1') {
                $badge_class = "badge bg-danger";
                $student_access = 'Blocked';
            }

            if (isset($requestData['access_filter']) && !empty($requestData['access_filter']) && $requestData['access_filter'] == "deleted" && $row["is_disabled"] == '1') {
                $badge_class = "label-warning";
                $student_access = 'Deleted';
            }

            $student_access_encode = "<span class='label $badge_class'>$student_access</span>";
            $student_access_tag = htmlspecialchars_decode(htmlspecialchars($student_access_encode));


            $nestedData[] = $i;
            $nestedData[] = $row["name"];
            $nestedData[] = $row["username"];
            $nestedData[] = $row["token"];
            $nestedData[] = $row["created_date"];
            $nestedData[] = $row["device_type"];
            $nestedData[] = $row["device_info"];
            $nestedData[] = $row["ip_address"];
            $nestedData[] = $student_access_tag;

            $update_token_id = $row['id'];


            if ($row["is_disabled"] == '1') {
                $nestedData[] = "";
            } else {

                $token_delete_url = base_url() . "/students/delete_student_token_submit/" . $update_token_id;
                $wrapper_code = htmlspecialchars("<a href='" . $token_delete_url . "' ><i class='material-icons material-icon-small text-danger'>delete</i></a>");
                $nestedData[] = htmlspecialchars_decode($wrapper_code);
            }



            $data[] = $nestedData;
            $i++;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalDataFiltered),
            "data"            => $data
        );

        return $json_data;
    }
    /*******************************************************/


    /**
     * Proctoring Students List
     *
     * @param array $postData
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_proctoring_students_data($postData = array())
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
        $testID = $postData['testID'];


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
            $dir = "desc";
        }

        $columns_valid = array();

        if (!isset($columns_valid[$col])) {
            $orderby = "score";
        } else {
            $orderby = $columns_valid[$col];
        }

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " AND ( 
                student.name LIKE '%" . $searchValue . "%' OR
                student.roll_no LIKE '%" . $searchValue . "%' OR
                student.mobile_no LIKE '%" . $searchValue . "%' OR
                student.gender LIKE '%" . $searchValue . "%' OR
                student.email LIKE '%" . $searchValue . "%' 
                )";
        }

        ## Fetch records
        $sql = "SELECT student.id as student_id, student.name,student.roll_no,test_status.solved,test_status.correct,test_status.score,proctor.score as proctor_score,test_status.updated_date,proctoring_remarks as remarks FROM test_status 
        JOIN student 
        ON student.id = test_status.student_id 
        LEFT JOIN (select avg(score) as score,student_id from proctor_images where test_id = '$testID' group by student_id) as proctor 
        on proctor.student_id = test_status.student_id 
        WHERE test_id = '$testID' 
        $searchQuery  ";

        $totalRecords = $db->query($sql)->getNumRows();
        $sql .= $searchQuery;

        $totalRecordwithFilter = $db->query($sql)->getNumRows();
        $sql .= " ORDER BY $orderby $dir LIMIT $start,$rowperpage";

        // Result with filtered data with limit
        $records = $db->query($sql)->getResult();

        $data = array();
        $testID =  encrypt_string($testID);
        foreach ($records as $record) {
            $studentId =  encrypt_string($record->student_id);


            $dropdown_option_encode = htmlspecialchars("<a  class='dropdown-item' href=" . base_url('tests/student_proctor_activity/' . $testID . '/' . $studentId) . "> <i class='fa fa-eye' aria-hidden='true'></i></a>");
            $dropdown_option_decode = htmlspecialchars_decode($dropdown_option_encode);
            $dropdown_option = $dropdown_option_decode;

            $nestedData = array();

            $nestedData["name"] = $record->name;
            $nestedData["roll_no"] = $record->roll_no;
            $nestedData["solved"] =  $record->solved;
            $nestedData["correct"] =  $record->correct;
            $nestedData["score"] =  $record->score;
            $nestedData["proctor_score"] =  number_format((float)$record->proctor_score, 2, '.', '');
            $nestedData["remarks"] =  $record->remarks;
            $nestedData["more_button"] =  $dropdown_option;


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





    /*****************************************************************
     *################# END OF FUNCTIONS - FILTERED DATA ###############*
     *****************************************************************/




    /*****************************************************************
     *################ START OF SUBMIT FUNCTIONS ###########*
     *****************************************************************/

    /**
     * Block/ Disable Student 
     *
     * @param [Array] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function disable_student($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $student_id = sanitize_input(decrypt_cipher($data['student_id']));
        $block_type = sanitize_input($data['block_type']);
        if ($block_type == 'disable') {
            $student_login_data = [
                'student_access' => 'Disabled'
            ];
        } else {
            $student_login_data = [
                'student_access' => NULL
            ];
        }

        $db->table('student_login')->update($student_login_data, ['student_id' => $student_id]);



        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'item' => "Student Id " . $student_id . " student_access " . $student_login_data['student_access'],
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/



    /**
     * Delete Student 
     *
     * @param [Array] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_student($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $student_id = sanitize_input(decrypt_cipher($data['student_id']));

        $student_login_data = [
            'student_access' => 'Deleted'
        ];

        $db->table('student_login')->update($student_login_data, ['student_id' => $student_id]);


        $student_package_data = [
            'is_disabled' => '1'
        ];

        $db->table('student_institute')->update($student_package_data, ['student_id' => $student_id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            $UserActivityModel = new UserActivityModel();
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'item' => 'student with Student ID ' . $student_id,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel->log('deleted', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Update Student Details
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_student_details($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $student_id = sanitize_input(decrypt_cipher($data['student_id']));


        // Extra Details Update 
        $extra_details_desc = "";
        if (isset($data['extra_details_keys']) && !empty($data['extra_details_keys'])) {
            foreach ($data['extra_details_keys'] as $key => $extra_detail) {
                $extra_details_desc .= $extra_detail . ":";
                $extra_details_desc .= $data['extra_details_val'][$key] . " | ";
            }
        }

        $student_data = [
            'name' => sanitize_input($data['name']),
            'email' => sanitize_input($data['email']),
            'roll_no' => sanitize_input($data['roll_no']),
            'parent_mobile_no' => sanitize_input($data['parent_mobile_no']),
            'gender' => sanitize_input($data['gender']),
            'mobile_no' => sanitize_input($data['mobile_no']),
            'previous_marks' => sanitize_input($data['previous_marks']),
            'caste_category' => sanitize_input($data['caste_category']),
            'extra_details' => sanitize_input($extra_details_desc),
        ];

        $db->table('student')->update($student_data, ['id' => $student_id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'item' => "Student Id " . $student_id . " Name " . sanitize_input($data['name']) . ", email " . sanitize_input($data['email']) . ", roll_no " . sanitize_input($data['roll_no']) . ", parent_mobile_no " . sanitize_input($data['parent_mobile_no']) . ", gender " . sanitize_input($data['gender']) . ", mobile_no " . sanitize_input($data['mobile_no']) . ", previous_marks " . sanitize_input($data['previous_marks']) . ", caste_category " . sanitize_input($data['caste_category']),
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /*******************************************************/
    /**
     * Update Student Classroom
     * @return Void
     * @author PrachiP
     * @since 2021/10/19
     */
    public function update_student_classroom($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $to_update_package_id = $data['to_update_package_id'];
        $payment_status = $data['payment_status'];
        $update_stu_package_id = decrypt_cipher($data['update_stu_package_id']);
        $update_data = array(
            'package_id' => $to_update_package_id,
            'status' => $payment_status
        );
        $db->table('student_institute')->update($update_data, ['id' => $update_stu_package_id]);
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'item' => "Student Institute Id " . $update_stu_package_id . " package_id " . $to_update_package_id . " status " . $payment_status,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * Delete Student Classroom
     * @return Void
     * @author PrachiP
     * @since 2021/10/19
     */
    public function delete_student_classroom($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $stu_pkg_id = decrypt_cipher($data['stu_pkg_id']);
        $institute_id = decrypt_cipher($data['institute_id']);
        $update_data = array(
            'is_disabled' => '1'
        );
        $array = array('id' => $stu_pkg_id, 'institute_id' => $institute_id);
        $db->table('student_institute')->update($update_data, $array);
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'item' => "Student Institute Id " . $stu_pkg_id,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('deleted', $log_info);
            return true;
        }
    }
    /*******************************************************/

    /*******************************************************/
    /**
     * Add Student Classroom
     * @return Void
     * @author PrachiP
     * @since 2021/10/19
     */
    public function add_student_classroom($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $student_id = decrypt_cipher($data['student_id']);
        $institute_id = decrypt_cipher($data['institute_id']);
        $package_id = $data['add_package_id'];
        $status = 'Completed';

        $insert_data = array(
            'student_id' => $student_id,
            'institute_id' => $institute_id,
            'package_id' => $package_id,
            'status' => $status
        );
        $db->table('student_institute')->insert($insert_data);
        $student_institute_id = $db->insertID();
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'item' => "Student Institute Id " . $student_institute_id,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Disable Student Token
     *
     * @param [type] $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function disable_student_token($data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $token_id = sanitize_input($data['token']);

        $update_data = [
            'is_disabled' => 1
        ];

        $db->table('student_tokens')->update($update_data, ['id' => $token_id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            $UserActivityModel = new UserActivityModel();
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'item' => 'Student with student token disabled' . $token_id,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel->log('deleted', $log_info);
            return true;
        }
    }
    /*******************************************************/


    /**
     * Delete Student Live Login Session
     *
     * @param string $live_session_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_student_live_login_session(string $live_session_ids)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $live_session_ids = sanitize_input($live_session_ids);

        $update_data = [
            'is_disabled' => 1,
            'is_live' => 0
        ];
        $ids = explode(",", $live_session_ids);
        $builder = $db->table('student_login_sessions');
        $builder->whereIn('id', $ids);
        $builder->update($update_data);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            $UserActivityModel = new UserActivityModel();
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'item' => 'Student login session deleted with ID: ' . $live_session_ids,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel->log('deleted', $log_info);
            return true;
        }
    }
    /*******************************************************/

    /*****************************************************************
     *################ END OF SUBMIT FUNCTIONS ###########*
     *****************************************************************/
}
