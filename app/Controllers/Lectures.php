<?php

namespace App\Controllers;

use \App\Models\LecturesModel;
use \App\Models\ActivitySummaryModel;
use \App\Models\LiveSessionsModel;

class Lectures extends BaseController
{
    /*******************************************************/
    /**
     * Video Lectures 
     * @author PrachiP
     * @since 2021/09/20
     * @return view
     */
    public function index()
    {
        $session = session();
        $data['title'] = "Video Lectures";
        $data['live_count'] = $session->get('live_count');
        $data['userType'] = $session->get('userType');
        $data['video_constraint'] = $session->get('video_constraint');
        $data['screeningTestStudent'] = 1;
        return view('pages/lectures/video_overview', $data);
    }
    /*******************************************************/





    /*******************************************************/
    /**
     * Video Lecture Analysis
     * @author PrachiP
     * @since 2021/08/20
     * @return view
     */
    public function video_analysis()
    {
        $data['title'] = "Video Lecture Analysis";
        $instituteID = decrypt_cipher(session()->get('instituteID'));
        // Activity Log
		$log_info =  [
			'username' =>  session()->get('username'),
			'institute_id' =>  decrypt_cipher(session()->get('instituteID')),
			'uri' => "Video Lecture Analysis",
            'admin_id' =>  decrypt_cipher(session()->get('login_id'))
		];
		$this->userActivity->log('page_access', $log_info);
        $db = \Config\Database::connect();
        $quotaUsed = $db->query("SELECT sum(size) as used 
        FROM video_lectures 
        WHERE institute_id = '$instituteID'");
        $quotaUsedRow = $quotaUsed->getRowArray();
        $quotaUsedValue = 0;
        if (!empty($quotaUsedRow)) {
            if (isset($quotaUsedRow['used'])) {
                $quotaUsedValue = $quotaUsedRow['used'];
                if ($quotaUsedValue > 0) {
                    $quotaUsedValue = round($quotaUsedValue / (1024 * 1024 * 1024), 3);
                }
            }
        }
        $data['quotaUsedValue'] = $quotaUsedValue;
        $LecturesModel = new LecturesModel();
        $data['video_lectures'] = $LecturesModel->fetch_video_lectures();

        $ActivitySummaryModel = new ActivitySummaryModel();

        //Started count
        $data['startedResult'] = $ActivitySummaryModel->startedResultCount($instituteID);

        //Completed count
        $data['completedResult'] = $ActivitySummaryModel->completedResultCount($instituteID);

        //Averages
        $data['avgResult'] = $ActivitySummaryModel->averageResultCount();

        return view('pages/reports/video_lecture_analysis', $data);
    }
    /*******************************************************/


    /**
     * Video Detailed Analysis
     *
     * @param [Integer] $video_id
     * @param [String] $requestType
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function video_detailed_analysis($video_id, $requestType = NULL)
    {
        $data['requestType'] = $requestType;
        $video_id = decrypt_cipher($video_id);
        $videoName = "";
        if ($requestType == 'Live') {
            $LiveSessionModel = new LiveSessionsModel();
            $session_details = $LiveSessionModel->get_live_session_details($video_id);
            if (!empty($session_details)){
                $videoName = $session_details['session_name'];
            }
        } else {
            $LecturesModel = new LecturesModel();
            $video_details = $LecturesModel->get_video_details($video_id);
            if (!empty($video_details)) {
                $videoName = $video_details['video_name'];
            }
        }
        $data['detailed_analysis'] = $LecturesModel->get_detailed_analysis($video_id);
        $data['title'] = "Analysis Of " . $videoName;
        return view('pages/reports/video_detailed_analysis', $data);
    }
    /*******************************************************/
}
