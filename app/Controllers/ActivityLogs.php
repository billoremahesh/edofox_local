<?php

namespace App\Controllers;

use \App\Models\UserActivityModel;
use \App\Models\AdminModel;

class ActivityLogs extends BaseController
{

    public function index()
    {
        $data['title'] = "User Activity Logs";
        // Add Last Login Datetime
        $AdminModel = new AdminModel();
        $AdminModel->update_last_login(decrypt_cipher(session()->get('login_id')));
        $data['staff_id'] = decrypt_cipher(session()->get('login_id'));
        $data['instituteID'] = decrypt_cipher(session()->get('instituteID'));
        $data['perms'] = isset($_SESSION['perms']) ? $_SESSION['perms'] : array();
        $data['user_list'] = $AdminModel->get_admin_users(decrypt_cipher(session()->get('instituteID')));
        echo view('pages/reports/admin_activity_logs.php', $data);
    }
    /*******************************************************/


    /**
     * Activity Data - JSON ASYNC CALL
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function activity_logs_data()
    {
        // POST data
        $postData = $this->request->getVar();
        $UserActivityModel = new UserActivityModel();
        $activity_data = $UserActivityModel->get_activity_data($postData);
        echo json_encode($activity_data);
    }
    /*******************************************************/
}
