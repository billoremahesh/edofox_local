<?php

namespace App\Libraries;

use App\Models\UserActivityModel;
use CodeIgniter\HTTP\RequestInterface;

class Activity
{
    protected  $db; //db connection instance
    protected  $model; //db connection instance
    protected $request;

    function __construct(RequestInterface $request)
    {
        $this->request = $request;
        $this->db = db_connect();
        $this->model = new UserActivityModel($this->db);
    }

    /**
     * Page Access Activity
     *
     * @param string $module
     * @param string $segment
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function page_access_activity(string $module = "", string $segment = "")
    {
        // Add Activity Log
        $log_info =  [
            'username' =>  session()->get('username'),
            'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
            'uri' => $module,
            'admin_id' =>  decrypt_cipher(session()->get('login_id')),
            'link_segment' => $segment
        ];
        $this->model->log('page_access', $log_info);
    }
}
