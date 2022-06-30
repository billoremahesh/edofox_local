<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use \App\Models\UserActivityModel;

/**
 * Set User Session
 *  @author Rushi B <rushikesh.badadale@mattersoft.xyz>
 */

class UserData implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userType = session()->get('user_type');
        if ($userType != "super_admin") {
            // Update Perms
            $admin_id = session()->get('login_id');

            $db = \Config\Database::connect();
            $perms = array();

            $sql = "SELECT admin.*
                    FROM admin 
                    WHERE  admin.id = :admin_id: 
                    AND is_disabled = 0 ";

            $query = $db->query($sql, [
                'admin_id' => sanitize_input(decrypt_cipher($admin_id))
            ]);
            $admin_data_result  = $query->getRowArray();

            if (!empty($admin_data_result)) {
                if (isset($admin_data_result['perms']) and $admin_data_result['perms'] != "") {
                    $admin_perms = str_replace("[", '', $admin_data_result['perms']);
                    $admin_perms = str_replace("]", '', $admin_perms);
                    $admin_perms = str_replace('"', '', $admin_perms);
                    $perms = explode(",", $admin_perms);
                }
                session()->set('username', $admin_data_result['username']);
                session()->set('name', $admin_data_result['name']);
                session()->set('email', $admin_data_result['email']);
                session()->set('mobile', $admin_data_result['mobile']);
                session()->set('profile_img_url', $admin_data_result['profile_img_url']);
                session()->set('perms', $perms);
                session()->set('admin_token', encrypt_string($admin_data_result['admin_token']));
                session()->set('last_login', $admin_data_result['last_login']);
                if (isset($admin_data_result['last_login']) && $admin_data_result['last_login'] != "") {
                    $UserActivityModel = new UserActivityModel();
                    session()->set('unread_activity_count',  $UserActivityModel->unread_activity_log_count(decrypt_cipher($admin_id), $admin_data_result['last_login']));
                } else {
                    session()->set('unread_activity_count', 0);
                }
            } else {
                session()->setFlashdata('toastr_error', 'Account deleted or disabled');
                return redirect()->to(base_url('/login/user_logout/'));
            }




            // Update Institute Data

            $instituteID = session()->get('instituteID');
            $institute_sql = "SELECT institute.*
                    FROM institute 
                    WHERE  institute.id = :institute_id:";

            $query = $db->query($institute_sql, [
                'institute_id' => sanitize_input(decrypt_cipher($instituteID))
            ]);
            $institute_data_result  = $query->getRowArray();

            if (!empty($institute_data_result)) {

                    session()->set('instituteName', $institute_data_result['institute_name']);
                    session()->set('dlp_count', $institute_data_result['dlp']);
                    session()->set('live_count', $institute_data_result['live']);
                    session()->set('exam_feature', $institute_data_result['exam']);
                    session()->set('support_feature', $institute_data_result['support']);
                    session()->set('max_student_tokens', $institute_data_result['max_student_tokens']);
                    session()->set('maxStudents', $institute_data_result['max_students']);
                    session()->set('logo_path', $institute_data_result['logo_path']);
                    session()->set('max_dlp_tokens', $institute_data_result['max_dlp_tokens']);
                    session()->set('whatsapp_credits', $institute_data_result['whatsapp_credits']);
                    session()->set('expiry_date', $institute_data_result['expiry_date']);
                    if (!empty($institute_data_result['timezone']) && $institute_data_result['timezone'] != "") {
                        session()->set('timezone', $institute_data_result['timezone']);
                    }
                $current_date = date('Y-m-d H:i:s');
                if($institute_data_result['expiry_date'] < $current_date) {
                    $instituteID = session()->get('instituteID');
                    $institute_subscriptions_sql = "SELECT institute_subscriptions.id,institute_subscriptions.next_invoice_date
                    FROM institute_subscriptions 
                    WHERE  institute_subscriptions.institute_id = :institute_id: 
                    AND institute_subscriptions.status ='Active'  ";
                    $query = $db->query($institute_subscriptions_sql, [
                        'institute_id' => sanitize_input(decrypt_cipher($instituteID))
                    ]);
                    $institute_subscriptions_result  = $query->getResultArray();
                    if(empty($institute_subscriptions_result)){
                        session()->setFlashdata('toastr_error', 'Account Expired. Please renew.');
                        return redirect()->to(base_url('/login'));
                    }else{
                        session()->set('expiry_date', $institute_subscriptions_result[0]['next_invoice_date']);
                    }
                }
            } else {
                session()->setFlashdata('toastr_error', 'Institute Not Found');
                return redirect()->to(base_url('/login'));
            }


            // Update Classroom Mapped
            $classroom_mapped_arr = "";
            if (!in_array("all_perms", session()->get('perms'))) {
                $sql = "select GROUP_CONCAT(admin_package_map.package_id) pkgids
            FROM admin_package_map
            WHERE admin_package_map.admin_id = :admin_id: 
            AND admin_package_map.is_disabled = 0";

                $query = $db->query($sql, [
                    'admin_id' => sanitize_input(decrypt_cipher($admin_id))
                ]);
                $classroom_mapped_arr = $query->getRowArray();
                if (!empty($classroom_mapped_arr)) {
                    session()->set('classroom_mapped_arr', $classroom_mapped_arr['pkgids']);
                }
            }
        } else {
            if (!session()->has('EdofoxAdminLoggedIn') and !session()->has('isSuperAdminLoggedIn')) :
                session()->setFlashdata('toastr_error', 'Not logged in or session expired.');
                return redirect()->to(base_url('/login'));
            endif;

 // Update Perms
            $admin_id = session()->get('login_id');

            $db = \Config\Database::connect();
            $perms = array();

            $sql = "SELECT super_admin.*
                    FROM super_admin 
                    WHERE  super_admin.id = :admin_id: ";

            $query = $db->query($sql, [
                'admin_id' => sanitize_input(decrypt_cipher($admin_id))
            ]);
            $admin_data_result  = $query->getRowArray();

            if (!empty($admin_data_result)) {
                if (isset($admin_data_result['access_perms']) and $admin_data_result['access_perms'] != "") {
                    $admin_perms = str_replace("[", '', $admin_data_result['access_perms']);
                    $admin_perms = str_replace("]", '', $admin_perms);
                    $admin_perms = str_replace('"', '', $admin_perms);
                    $perms = explode(",", $admin_perms);
                }
                session()->set('username', $admin_data_result['username']);
                session()->set('name', $admin_data_result['name']);
                session()->set('email', $admin_data_result['email']);
                session()->set('mobile', $admin_data_result['mobile_number']);
                session()->set('profile_img_url', $admin_data_result['profile_img_url']);
                session()->set('perms', $perms);
                session()->set('last_login', $admin_data_result['last_login']);
                session()->set('super_admin_role', $admin_data_result['role']);
                if (isset($admin_data_result['last_login']) && $admin_data_result['last_login'] != "") {
                    $UserActivityModel = new UserActivityModel();
                    session()->set('unread_activity_count',  $UserActivityModel->unread_activity_log_count(decrypt_cipher($admin_id), $admin_data_result['last_login']));
                } else {
                    session()->set('unread_activity_count', 0);
                }
            }
        }
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
