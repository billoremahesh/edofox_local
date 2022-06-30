<?php

namespace App\Models;

use CodeIgniter\Model;

class InstituteScheduleModel extends Model
{

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }


     /** holiday delete  */ 
        public function delete_holiday($data)
        {
            $db = \Config\Database::connect();
    
            $db->transStart();
    
    
            $holiday_data = [
                'is_disabled' => '1'
            ];
    
            $id = $data['holiday_id'];
            $db->table('institute_schedule')->update($holiday_data, ['id' => $id]); 
    
            // $holiday_data = $this->get_classroom_details($id);
            $db->transComplete();
    
            if ($db->transStatus() === FALSE) {  
                // generate an error... or use the log_message() function to log your error
                return false;
            } else { 
                // Activity Log
                // $log_info =  [
                //     'username' =>  session()->get('username'),
                //     'item' => strtoupper("Classroom Name : ") . $classroom_data['package_name'],
                //     'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                //     'admin_id' =>  decrypt_cipher(session()->get('login_id'))
                // ];
                // $UserActivityModel = new UserActivityModel();
                // $UserActivityModel->log('deleted', $log_info);
                return true;
            }
        } 

    
    public function fetch_holiday_list(array $postData){
        $db = \Config\Database::connect();
        $institute_id_filter_check = "";
        if (isset($postData['institute_id']) && !empty($postData['institute_id'])) {
            $institute_id = $postData['institute_id'];
            $institute_id_filter_check = " AND institute_schedule.institute_id = $institute_id "; 
        }

        
            $is_disable_filter_check = " AND institute_schedule.is_disabled = 0 "; 
         

        $sql_fetch_data = "SELECT institute_schedule.*,packages.package_name FROM institute_schedule LEFT JOIN packages ON packages.id = institute_schedule.classroom_id  WHERE institute_schedule.type ='Holiday' $institute_id_filter_check $is_disable_filter_check";
        $query = $db->query($sql_fetch_data);
        $result = $query->getResultArray();
        return $result;
    }


    public function fetch_schedule_events(array $postData)
    {
        $db = \Config\Database::connect();

        // Check Mapped Classrooms to staff in case of not given global permissions
        $check_access_perms = "";
        $classroom_mapped_ids = session()->get('classroom_mapped_arr');
        if (!empty($classroom_mapped_ids)) {
            $check_access_perms = " AND institute_schedule.classroom_id IN ($classroom_mapped_ids) ";
        }


        $institute_condn = "";

        if (isset($postData['institute_id']) && !empty($postData['institute_id'])) {
            $institute_id = $postData['institute_id'];
            $institute_condn = " AND institute_schedule.institute_id= '$institute_id' ";
        }


        $classroom_filter_check = "";
        if (isset($postData['classroom']) && !empty($postData['classroom'])) {
            $classroom_id = $postData['classroom'];
            $classroom_filter_check = " AND institute_schedule.classroom_id = '$classroom_id' ";
        } 


        $day_check = "";
        if (isset($postData['day']) && !empty($postData['day'])) {
            $day = $postData['day'];
            $day_check = " AND institute_schedule.day = '$day' ";
        }

        $start_date = '';
        if (isset($postData['start']) && !empty($postData['start'])) {
            $start_date = $postData['start'];
        }

        $end_date = '';
        if (isset($postData['end']) && !empty($postData['end'])) {
            $end_date = $postData['end'];
        } 

        $sql_fetch_data = "SELECT institute_schedule.*,packages.package_name,test_subjects.subject,institute_schedule_data.date,attendance.total_students,present_attendance.present_students FROM institute_schedule LEFT JOIN packages ON packages.id = institute_schedule.classroom_id LEFT JOIN test_subjects ON test_subjects.subject_id = institute_schedule.subject_id LEFT JOIN institute_schedule_data on institute_schedule_data.schedule_id = institute_schedule.id LEFT JOIN (select count(*) as total_students,schedule_data_id from institute_schedule_attendance where is_disabled = 0 group by schedule_data_id) as attendance on attendance.schedule_data_id = institute_schedule_data.id LEFT JOIN (select count(*) as present_students,schedule_data_id from institute_schedule_attendance where is_disabled = 0 and is_present = 1 group by schedule_data_id) as present_attendance on present_attendance.schedule_data_id = institute_schedule_data.id where institute_schedule.frequency IN ('weekly','date') AND institute_schedule.is_disabled = 0  $institute_condn  $classroom_filter_check $check_access_perms $day_check AND (institute_schedule_data.date IS NULL OR institute_schedule_data.date BETWEEN '$start_date' AND '$end_date') ORDER BY institute_schedule.day,institute_schedule.starts_at ASC";
        $query = $db->query($sql_fetch_data);
        $result = $query->getResultArray();
        return $result;
    }

    public function fetch_institute_schedule_data($schedule_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT institute_schedule.*,packages.package_name,test_subjects.subject
        FROM institute_schedule 
        LEFT JOIN packages
        ON packages.id = institute_schedule.classroom_id
        LEFT JOIN test_subjects
        ON test_subjects.subject_id = institute_schedule.subject_id
        where institute_schedule.id = '$schedule_id' ";
        $query = $db->query($sql);
        $result = $query->getRowArray();
        return $result;
    }

    
    public function fetch_institute_holiday_data($holiday_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT institute_schedule.*,packages.package_name
        FROM institute_schedule 
        LEFT JOIN packages
        ON packages.id = institute_schedule.classroom_id 
        where institute_schedule.id = '$holiday_id' ";
        $query = $db->query($sql);
        $result = $query->getRowArray(); 
        return $result;
    }


    /**
     * Add New Schedule
     *
     * @param array $data
     *
     * @return void
     * @author Hemant K <hemant.kulkarni@mattersoft.xyz>
     */
    public function add_new_schedule(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        if (isset($data['institute_id']) && !empty($data['institute_id'])) {
            $insert_data['institute_id'] = sanitize_input($data['institute_id']);
        }

        if (isset($data['session_title']) && !empty($data['session_title'])) {
            $insert_data['title'] = sanitize_input($data['session_title']);
        }

        if (isset($data['session_classroom']) && !empty($data['session_classroom'])) {
            $insert_data['classroom_id'] = sanitize_input($data['session_classroom']);
        }

        if (isset($data['session_subject']) && !empty($data['session_subject'])) {
            $insert_data['subject_id'] = sanitize_input($data['session_subject']);
        }

        if (isset($data['session_frequency']) && !empty($data['session_frequency'])) {
            $insert_data['frequency'] = sanitize_input($data['session_frequency']);
        }

        // Add Multiple Schdules
        if (isset($data['session_week_days']) && !empty($data['session_week_days'])) {

            foreach ($data['session_week_days'] as $key => $value) {
                $insert_data['day'] = sanitize_input($value);

                if (isset($data['session_start_time'][$key]) && !empty($data['session_start_time'][$key])) {
                    $insert_data['starts_at'] = sanitize_input($data['session_start_time'][$key]);
                }

                if (isset($data['session_end_time'][$key]) && !empty($data['session_end_time'][$key])) {
                    $insert_data['ends_at'] = sanitize_input($data['session_end_time'][$key]);
                }

                $add_duration = 0;
                if (isset($data['session_start_time'][$key]) && !empty($data['session_start_time'][$key]) && isset($data['session_end_time'][$key]) && !empty($data['session_end_time'][$key])) {
                    $start_time = strtotime($data['session_start_time'][$key] . ":00");
                    $end_time = strtotime($data['session_end_time'][$key] . ":00");
                    $add_duration = abs($end_time - $start_time);
                }

                $insert_data['duration'] =  $add_duration;
                $db->table('institute_schedule')->insert($insert_data);
            }
        } else {
            if (isset($data['session_week_day']) && !empty($data['session_week_day'])) {
                $insert_data['day'] = sanitize_input($data['session_week_day']);
            }

            if (isset($data['session_start_time']) && !empty($data['session_start_time'])) {
                $insert_data['starts_at'] = sanitize_input($data['session_start_time']);
            }

            if (isset($data['session_end_time']) && !empty($data['session_end_time'])) {
                $insert_data['ends_at'] = sanitize_input($data['session_end_time']);
            }

            $add_duration = 0;
            if (isset($data['session_start_time']) && !empty($data['session_start_time']) && isset($data['session_end_time']) && !empty($data['session_end_time'])) {
                $start_time = strtotime($data['session_start_time'] . ":00");
                $end_time = strtotime($data['session_end_time'] . ":00");
                $add_duration = abs($end_time - $start_time);
            }

            $insert_data['duration'] =  $add_duration;
            $db->table('institute_schedule')->insert($insert_data);
        }


        $schedule_id = $db->insertID();

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Session Schedule Id " . $schedule_id,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);
            return true;
        }
    }
    /*******************************************************/

    /** add new holiday start */
    public function add_new_holiday(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();
        // $data['classroom_list']
        $classroom_arr = []; 
        if(count($data['session_classroom'])==count($data['classroom_list'])){
            $classroom_arr[] =0;
            $data['session_classroom'] = $classroom_arr;
        }  
 
        foreach ($data['session_classroom'] as $class_value) {
          
            if (isset($data['institute_id']) && !empty($data['institute_id'])) {
                $insert_data['institute_id'] = sanitize_input($data['institute_id']);
            }

            if (isset($data['session_title']) && !empty($data['session_title'])) {
                $insert_data['title'] = sanitize_input($data['session_title']);
            }


            if (isset($class_value) && !empty($class_value)) {
                $insert_data['classroom_id'] = sanitize_input($class_value);
            } 
            if(count($data['session_classroom'])==count($data['classroom_list'])){
                $insert_data['classroom_id'] =0;
            }  

            if (isset($data['session_frequency']) && !empty($data['session_frequency'])) {
                $insert_data['frequency'] = sanitize_input($data['session_frequency']);
            }
            if (isset($data['session_start_time']) && !empty($data['session_start_time'])) {
                $insert_data['starts_at'] = sanitize_input($data['session_start_time']);
                $insert_data['date'] = sanitize_input($data['session_start_time']);
            }

            if (isset($data['session_end_time']) && !empty($data['session_end_time'])) {
                $insert_data['ends_at'] = sanitize_input($data['session_end_time']);
                $insert_data['to_date'] = sanitize_input($data['session_end_time']);
            }
            if (isset($data['duration']) && !empty($data['duration'])) {
                $insert_data['duration'] = sanitize_input($data['duration']);
                $insert_data['day'] = sanitize_input($data['duration']);
            }

            $insert_data['type'] = 'Holiday';
            $db->table('institute_schedule')->insert($insert_data);
        }

        // Add Multiple Schdules
        // if(isset($data['session_week_days']) && !empty($data['session_week_days'])){

        //     foreach($data['session_week_days'] as $key=> $value){
        //         $insert_data['day'] = sanitize_input($value);

        //         if (isset($data['session_start_time'][$key]) && !empty($data['session_start_time'][$key])) {
        //             $insert_data['starts_at'] = sanitize_input($data['session_start_time'][$key]);
        //         }

        //         if (isset($data['session_end_time'][$key]) && !empty($data['session_end_time'][$key])) {
        //             $insert_data['ends_at'] = sanitize_input($data['session_end_time'][$key]);
        //         }

        //         $add_duration = 0;
        //         if (isset($data['session_start_time'][$key]) && !empty($data['session_start_time'][$key]) && isset($data['session_end_time'][$key]) && !empty($data['session_end_time'][$key])) {
        //             $start_time = strtotime($data['session_start_time'][$key] . ":00");
        //             $end_time = strtotime($data['session_end_time'][$key]. ":00");
        //             $add_duration = abs($end_time - $start_time);
        //         }

        //         $insert_data['duration'] =  $add_duration;
        //         $db->table('institute_schedule')->insert($insert_data);
        //     }
        // }else{
        //     if (isset($data['session_week_day']) && !empty($data['session_week_day'])) {
        //         $insert_data['day'] = sanitize_input($data['session_week_day']);
        //     }

        //     if (isset($data['session_start_time']) && !empty($data['session_start_time'])) {
        //         $insert_data['starts_at'] = sanitize_input($data['session_start_time']);
        //     }

        //     if (isset($data['session_end_time']) && !empty($data['session_end_time'])) {
        //         $insert_data['ends_at'] = sanitize_input($data['session_end_time']);
        //     }

        //     $add_duration = 0;
        //     if (isset($data['session_start_time']) && !empty($data['session_start_time']) && isset($data['session_end_time']) && !empty($data['session_end_time'])) {
        //         $start_time = strtotime($data['session_start_time'] . ":00");
        //         $end_time = strtotime($data['session_end_time'] . ":00");
        //         $add_duration = abs($end_time - $start_time);
        //     }

        //     $insert_data['duration'] =  $add_duration;
        //     $db->table('institute_schedule')->insert($insert_data);
        // }


        $schedule_id = $db->insertID();

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Session Schedule Id " . $schedule_id,
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);
            return true;
        }
    }

    /** add new holiday end */
    /**
     * Update Schedule
     *
     * @param array $data
     *
     * @return void
     * @author Rushikesh B
     */
    public function update_schedule(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        if (isset($data['is_disabled']) && !empty($data['is_disabled'])) {
            $update_data['is_disabled'] = '1';
        } else {
            if (isset($data['institute_id']) && !empty($data['institute_id'])) {
                $update_data['institute_id'] = sanitize_input($data['institute_id']);
            }

            if (isset($data['session_title']) && !empty($data['session_title'])) {
                $update_data['title'] = sanitize_input($data['session_title']);
            }

            if (isset($data['session_classroom']) && !empty($data['session_classroom'])) {
                $update_data['classroom_id'] = sanitize_input($data['session_classroom']);
            }

            if (isset($data['session_subject']) && !empty($data['session_subject'])) {
                $update_data['subject_id'] = sanitize_input($data['session_subject']);
            }

            if (isset($data['session_frequency']) && !empty($data['session_frequency'])) {
                $update_data['frequency'] = sanitize_input($data['session_frequency']);
            }

            if (isset($data['session_week_day']) && !empty($data['session_week_day'])) {
                $update_data['day'] = sanitize_input($data['session_week_day']);
            }

            if (isset($data['session_start_time']) && !empty($data['session_start_time'])) {
                $update_data['starts_at'] = sanitize_input($data['session_start_time']);
            }

            if (isset($data['session_end_time']) && !empty($data['session_end_time'])) {
                $update_data['ends_at'] = sanitize_input($data['session_end_time']);
            }

            $add_duration = 0;
            if (isset($data['session_start_time']) && !empty($data['session_start_time']) && isset($data['session_end_time']) && !empty($data['session_end_time'])) {
                $start_time = strtotime($data['session_start_time'] . ":00");
                $end_time = strtotime($data['session_end_time'] . ":00");
                $add_duration = abs($end_time - $start_time);
            }

            $update_data['duration'] =  $add_duration;
        }

        $schedule_id = $data['schedule_id'];

        $db->table('institute_schedule')->update($update_data, ['id' => $schedule_id]);


        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' => "Session Schedule with Id " . $schedule_id . " updated",
                'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
                'admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);
            return true;
        }
    }
    /*******************************************************/

   

}
