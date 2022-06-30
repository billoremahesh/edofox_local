<?php

namespace App\Models;

use CodeIgniter\Model;

class InstituteSubscriptionsModel extends Model
{

    /**
     * Get Institute Subscriptions Details
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function institute_subscriptions(int $institute_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT institute_subscriptions.*
        FROM institute_subscriptions 
        WHERE institute_subscriptions.institute_id= :institute_id:
        GROUP BY institute_subscriptions.id
        ORDER BY institute_subscriptions.plan_name asc";
        $query = $this->db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);
        return $query->getResultArray();
    }
    /*******************************************************/


    /**
     * Get Subscriptions Details based on ID
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function subscription_details(int $subscription_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT institute_subscriptions.*,institute.institute_name,institute.max_students,institute.address as institute_address,IF(institute.gst_no IS NULL,'NA', institute.gst_no) as institute_gst_no
        FROM institute_subscriptions 
        JOIN institute
        ON institute.id = institute_subscriptions.institute_id
        WHERE institute_subscriptions.id= :subscription_id: ";
        $query = $this->db->query($sql, [
            'subscription_id' => sanitize_input($subscription_id)
        ]);
        return $query->getRowArray();
    }
    /*******************************************************/


    /**
     * Get Subscriptions Details based on Public ID
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function subscription_proposal_details(string $public_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT institute_subscriptions.*,institute.institute_name,institute.max_students,institute.address as institute_address,IF(institute.gst_no IS NULL,'NA', institute.gst_no) as institute_gst_no
        FROM institute_subscriptions 
        JOIN institute
        ON institute.id = institute_subscriptions.institute_id
        WHERE institute_subscriptions.public_id = :public_id: ";
        $query = $db->query($sql, [
            'public_id' => sanitize_input($public_id)
        ]);
        return $query->getRowArray();
    }
    /*******************************************************/


    /**
     * Add New Subscription
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_new_subscription(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();


        if (isset($data['institute_id'])) {
            $subscription_data['institute_id'] = sanitize_input($data['institute_id']);
        }

        if (isset($data['institute_name'])) {
            $institute_name = sanitize_input($data['institute_name']);
        }

        if (isset($data['plan_name'])) {
            $subscription_data['plan_name'] = sanitize_input($data['plan_name']);
        }


        if (isset($data['manual_plan_name']) && !empty($data['manual_plan_name'])) {
            $subscription_data['plan_name'] = sanitize_input($data['manual_plan_name']);
        }

        if (isset($data['subscription_type'])) {
            $subscription_data['plan_type'] = sanitize_input($data['subscription_type']);
        }

        if (isset($data['comments'])) {
            $subscription_data['comments'] = sanitize_input($data['comments']);
        }

        if (isset($data['max_students'])) {
            $subscription_data['no_of_students'] = sanitize_input($data['max_students']);
        }


        if (isset($data['next_invoice_date'])) {
            $next_invoice_date = "";
            $next_invoice_month = sanitize_input($data['next_invoice_date']);
            $next_invoice_year = date('Y');
            $next_invoice_date =  $next_invoice_year . "-" . date("m", strtotime($next_invoice_month)) . "-01";
            $subscription_data['next_invoice_date'] = $next_invoice_date;
        }

        if (isset($data['check_pkg']) && !empty($data['check_pkg'])) {

            $check_vals = implode(",", $data['check_pkg']);

            $plans_selected_query = $this->db->query("select * from edofox_pricing_plans where id IN ($check_vals) ");

            $edofox_pricing_plans_selected = $plans_selected_query->getResultArray();

            if (!empty($edofox_pricing_plans_selected)) {
                foreach ($edofox_pricing_plans_selected as $selected_plan) {

                    if ($selected_plan['module'] == "Exam") {
                        if (isset($subscription_data['exam']) &&  $subscription_data['exam'] == '2') {
                            $subscription_data['exam'] = 3;
                        } else {
                            $subscription_data['exam'] = 1;
                        }
                        $subscription_data['exam_amount'] =  ($subscription_data['no_of_students'] * $selected_plan['price']);
                    } elseif (!isset($subscription_data['exam'])) {
                        $subscription_data['exam'] = 0;
                        $subscription_data['exam_amount'] = 0;
                    }


                    if ($selected_plan['module'] == "OMR") {
                        if (isset($subscription_data['exam']) &&  $subscription_data['exam'] == '1') {
                            $subscription_data['exam'] = 3;
                        } else {
                            $subscription_data['exam'] = 2;
                        }
                        $subscription_data['omr_amount'] = $selected_plan['price'];
                    } elseif (!isset($subscription_data['exam'])) {
                        $subscription_data['exam'] = 0;
                        $subscription_data['omr_amount'] = 0;
                    }


                    if ($selected_plan['module'] == "Live") {
                        $subscription_data['live'] = 1;
                        $subscription_data['live_amount'] =  $subscription_data['no_of_students'] * $selected_plan['price'];
                    } elseif (!isset($subscription_data['live'])) {
                        $subscription_data['live'] = 0;
                        $subscription_data['live_amount'] = 0;
                    }

                    if ($selected_plan['module'] == "DLP") {
                        $subscription_data['dlp'] = 1;
                        $subscription_data['dlp_amount'] =  $subscription_data['no_of_students'] * $selected_plan['price'];
                    } elseif (!isset($subscription_data['dlp'])) {
                        $subscription_data['dlp'] = 0;
                        $subscription_data['dlp_amount'] = 0;
                    }

                    if ($selected_plan['module'] == "Support") {
                        $subscription_data['support'] = 1;
                        $subscription_data['support_amount'] = $selected_plan['price'];
                    } elseif (!isset($subscription_data['support'])) {
                        $subscription_data['support'] = 0;
                        $subscription_data['support_amount'] = 0;
                    }
                }
            }
            $subscription_data['manual_plan'] = 0;
        }

        if (isset($data['add_amount_manual']) && $data['add_amount_manual'] == 1) {


            if (isset($data['exam_amount'])  && !empty($data['exam_amount']) && $data['exam_amount'] != "0.00") {
                if (isset($subscription_data['exam']) &&  $subscription_data['exam'] == '2') {
                    $subscription_data['exam'] = 3;
                } else {
                    $subscription_data['exam'] = 1;
                }
                $subscription_data['exam_amount'] =  sanitize_input($data['exam_amount']);
            } else {
                if (!isset($subscription_data['exam'])) {
                    $subscription_data['exam'] = 0;
                }
                $subscription_data['exam_amount'] = 0;
            }


            if (isset($data['omr_amount']) && !empty($data['omr_amount']) && $data['omr_amount'] != "0.00") {
                if (isset($subscription_data['exam']) &&  $subscription_data['exam'] == '1') {
                    $subscription_data['exam'] = 3;
                } else {
                    $subscription_data['exam'] = 2;
                }
                $subscription_data['omr_amount'] = sanitize_input($data['omr_amount']);
            } else {
                if (!isset($subscription_data['exam'])) {
                    $subscription_data['exam'] = 0;
                }
                $subscription_data['omr_amount'] = 0;
            }

            if (isset($data['dlp_amount']) && !empty($data['dlp_amount']) && $data['dlp_amount'] != "0.00") {
                $subscription_data['dlp'] = 1;
                $subscription_data['dlp_amount'] = sanitize_input($data['dlp_amount']);
            } else {
                $subscription_data['dlp'] = 0;
                $subscription_data['dlp_amount'] = 0;
            }


            if (isset($data['live_amount']) && !empty($data['live_amount']) && $data['live_amount'] != "0.00") {
                $subscription_data['live'] = 1;
                $subscription_data['live_amount'] = sanitize_input($data['live_amount']);
            } else {
                $subscription_data['live'] = 0;
                $subscription_data['live_amount'] = 0;
            }

            if (isset($data['support_amount']) && !empty($data['support_amount']) && $data['support_amount'] != "0.00") {
                $subscription_data['support'] = 1;
                $subscription_data['support_amount'] = sanitize_input($data['support_amount']);
            } else {
                $subscription_data['support'] = 0;
                $subscription_data['support_amount'] = 0;
            }
            $subscription_data['manual_plan'] = 1;
        }

        if (isset($data['discount'])) {
            $subscription_data['discount'] = sanitize_input($data['discount']);
        }

        if (isset($data['final_total_amt'])) {
            $subscription_data['amount'] = sanitize_input($data['final_total_amt']);
        }

        $subscription_data['status'] = 'Active';
        $subscription_data['created_date'] = date('Y-m-d H:i:s');
        $subscription_data['created_by'] = decrypt_cipher(session()->get('login_id'));


        $db->table('institute_subscriptions')->insert($subscription_data);
        // Get inserted  ID
        $subscription_id = $db->insertID();
        $subscription_update_data['public_id'] =  encrypt_string($subscription_id);
        $subscription_update_data['quotation_ref'] = 'SAL-QTN-' . date('Y') . "-" . sprintf('%06d', $subscription_id);
        $db->table('institute_subscriptions')->update($subscription_update_data, ['id' => $subscription_id]);


        // Update Institute Module Feature 
        $institute_data['institute_id'] = $subscription_data['institute_id'];
        $institute_data['max_students'] = sanitize_input($data['max_students']);
        $institute_data['exam'] = sanitize_input($subscription_data['exam']);
        $institute_data['live'] = sanitize_input($subscription_data['live']);
        $institute_data['dlp'] = sanitize_input($subscription_data['dlp']);
        $institute_data['support'] = sanitize_input($subscription_data['support']);

        $expiry_date = $subscription_data['next_invoice_date'];
        $expiry_date = strtotime($expiry_date);
        $expiry_date =  date('Y-m-d', strtotime('+7 days', $expiry_date));
        $institute_data['expiry_date'] = date_format_custom($expiry_date, "Y-m-d H:i:s");


        // Check Unbilled Entitites
        if (isset($data['unbilled_entitites']) && !empty($data['unbilled_entitites'])) {
            foreach ($data['unbilled_entitites'] as $key => $value) {
                if ($value == "MaxLiveInteractive") {
                    $institute_data['max_concurrent_interactive'] = $data['unbilled_entitites_vals'][$key];
                }

                if ($value == "MaxLiveStudents") {
                    $institute_data['max_concurrent_live'] = $data['unbilled_entitites_vals'][$key];
                }

                if ($value == "StorageQuota") {
                    $institute_data['storage_quota'] = $data['unbilled_entitites_vals'][$key];
                }
            }
        }

        $InstituteModel = new InstituteModel();
        $InstituteModel->update_institute_module_feature($institute_data);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add sales team but failed', $log_info);

            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'New subscription added for the institute ' . $institute_name,
                'super_admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('added', $log_info);
            return true;
        }
    }
    /*******************************************************/



    /**
     * Update Subscription Details
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_subscription_details(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();


        if (isset($data['institute_id'])) {
            $subscription_data['institute_id'] = sanitize_input($data['institute_id']);
        }


        if (isset($data['plan_name'])) {
            $subscription_data['plan_name'] = sanitize_input($data['plan_name']);
        }

        if (isset($data['manual_plan_name']) && !empty($data['manual_plan_name'])) {
            $subscription_data['plan_name'] = sanitize_input($data['manual_plan_name']);
        }

        if (isset($data['subscription_type'])) {
            $subscription_data['plan_type'] = sanitize_input($data['subscription_type']);
        }

        if (isset($data['comments'])) {
            $subscription_data['comments'] = sanitize_input($data['comments']);
        }

        if (isset($data['max_students'])) {
            $subscription_data['no_of_students'] = sanitize_input($data['max_students']);
        }


        if (isset($data['next_invoice_date'])) {
            $next_invoice_date = "";
            $next_invoice_month = sanitize_input($data['next_invoice_date']);
            $next_invoice_year = date('Y');
            $next_invoice_date =  $next_invoice_year . "-" . date("m", strtotime($next_invoice_month)) . "-01";
            $subscription_data['next_invoice_date'] = $next_invoice_date;
        }

        if (isset($data['check_pkg']) && !empty($data['check_pkg'])) {

            $check_vals = implode(",", $data['check_pkg']);

            $plans_selected_query = $this->db->query("select * from edofox_pricing_plans where id IN ($check_vals) ");

            $edofox_pricing_plans_selected = $plans_selected_query->getResultArray();

            if (!empty($edofox_pricing_plans_selected)) {
                foreach ($edofox_pricing_plans_selected as $selected_plan) {

                    if ($selected_plan['module'] == "Exam") {
                        if (isset($subscription_data['exam']) &&  $subscription_data['exam'] == '2') {
                            $subscription_data['exam'] = 3;
                        } else {
                            $subscription_data['exam'] = 1;
                        }
                        $subscription_data['exam_amount'] =  ($subscription_data['no_of_students'] * $selected_plan['price']);
                    } elseif (!isset($subscription_data['exam'])) {
                        $subscription_data['exam'] = 0;
                        $subscription_data['exam_amount'] = 0;
                    }


                    if ($selected_plan['module'] == "OMR") {
                        if (isset($subscription_data['exam']) &&  $subscription_data['exam'] == '1') {
                            $subscription_data['exam'] = 3;
                        } else {
                            $subscription_data['exam'] = 2;
                        }
                        $subscription_data['omr_amount'] = $selected_plan['price'];
                    } elseif (!isset($subscription_data['exam'])) {
                        $subscription_data['exam'] = 0;
                        $subscription_data['omr_amount'] = 0;
                    }


                    if ($selected_plan['module'] == "Live") {
                        $subscription_data['live'] = 1;
                        $subscription_data['live_amount'] =  $subscription_data['no_of_students'] * $selected_plan['price'];
                    } elseif (!isset($subscription_data['live'])) {
                        $subscription_data['live'] = 0;
                        $subscription_data['live_amount'] = 0;
                    }

                    if ($selected_plan['module'] == "DLP") {
                        $subscription_data['dlp'] = 1;
                        $subscription_data['dlp_amount'] =  $subscription_data['no_of_students'] * $selected_plan['price'];
                    } elseif (!isset($subscription_data['dlp'])) {
                        $subscription_data['dlp'] = 0;
                        $subscription_data['dlp_amount'] = 0;
                    }

                    if ($selected_plan['module'] == "Support") {
                        $subscription_data['support'] = 1;
                        $subscription_data['support_amount'] = $selected_plan['price'];
                    } elseif (!isset($subscription_data['support'])) {
                        $subscription_data['support'] = 0;
                        $subscription_data['support_amount'] = 0;
                    }
                }
            }
            $subscription_data['manual_plan'] = 0;
        }

        if (isset($data['add_amount_manual']) && $data['add_amount_manual'] == 1) {


            if (isset($data['exam_amount'])  && !empty($data['exam_amount']) && $data['exam_amount'] != "0.00") {
                if (isset($subscription_data['exam']) &&  $subscription_data['exam'] == '2') {
                    $subscription_data['exam'] = 3;
                } else {
                    $subscription_data['exam'] = 1;
                }
                $subscription_data['exam_amount'] =  sanitize_input($data['exam_amount']);
            } else {
                if (!isset($subscription_data['exam'])) {
                    $subscription_data['exam'] = 0;
                }
                $subscription_data['exam_amount'] = 0;
            }


            if (isset($data['omr_amount']) && !empty($data['omr_amount']) && $data['omr_amount'] != "0.00") {
                if (isset($subscription_data['exam']) &&  $subscription_data['exam'] == '1') {
                    $subscription_data['exam'] = 3;
                } else {
                    $subscription_data['exam'] = 2;
                }
                $subscription_data['omr_amount'] = sanitize_input($data['omr_amount']);
            } else {
                if (!isset($subscription_data['exam'])) {
                    $subscription_data['exam'] = 0;
                }
                $subscription_data['omr_amount'] = 0;
            }

            if (isset($data['dlp_amount']) && !empty($data['dlp_amount']) && $data['dlp_amount'] != "0.00") {
                $subscription_data['dlp'] = 1;
                $subscription_data['dlp_amount'] = sanitize_input($data['dlp_amount']);
            } else {
                $subscription_data['dlp'] = 0;
                $subscription_data['dlp_amount'] = 0;
            }


            if (isset($data['live_amount']) && !empty($data['live_amount']) && $data['live_amount'] != "0.00") {
                $subscription_data['live'] = 1;
                $subscription_data['live_amount'] = sanitize_input($data['live_amount']);
            } else {
                $subscription_data['live'] = 0;
                $subscription_data['live_amount'] = 0;
            }

            if (isset($data['support_amount']) && !empty($data['support_amount']) && $data['support_amount'] != "0.00") {
                $subscription_data['support'] = 1;
                $subscription_data['support_amount'] = sanitize_input($data['support_amount']);
            } else {
                $subscription_data['support'] = 0;
                $subscription_data['support_amount'] = 0;
            }
            $subscription_data['manual_plan'] = 1;
        }

        if (isset($data['discount'])) {
            $subscription_data['discount'] = sanitize_input($data['discount']);
        }

        if (isset($data['final_total_amt'])) {
            $subscription_data['amount'] = sanitize_input($data['final_total_amt']);
        }

        $subscription_data['last_updated'] = date('Y-m-d H:i:s');
        $db->table('institute_subscriptions')->update($subscription_data, ['id' => $data['subscription_id']]);


        // Update Institute Module Feature 
        $institute_data['institute_id'] = $subscription_data['institute_id'];
        $institute_data['max_students'] = sanitize_input($data['max_students']);
        $institute_data['exam'] = sanitize_input($subscription_data['exam']);
        $institute_data['live'] = sanitize_input($subscription_data['live']);
        $institute_data['dlp'] = sanitize_input($subscription_data['dlp']);
        $institute_data['support'] = sanitize_input($subscription_data['support']);

        $expiry_date = $subscription_data['next_invoice_date'];
        $expiry_date = strtotime($expiry_date);
        $expiry_date =  date('Y-m-d', strtotime('+7 days', $expiry_date));
        $institute_data['expiry_date'] = date_format_custom($expiry_date, "Y-m-d H:i:s");
        $InstituteModel = new InstituteModel();
        $InstituteModel->update_institute_module_feature($institute_data);



        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add sales team but failed', $log_info);

            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'Subscription details updated for the institute with ID: ' . $subscription_data['institute_id'],
                'super_admin_id' =>  decrypt_cipher(session()->get('login_id'))
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('modified', $log_info);
            return true;
        }
    }
    /*******************************************************/


    public function enable_subscription(int $subscription_id)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        $subscription_data['status'] = 'Active';
        $subscription_data['last_updated'] = date('Y-m-d H:i:s');
        $db->table('institute_subscriptions')->update($subscription_data, ['id' => $subscription_id]);
        $db->transComplete();
        if ($db->transStatus() === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Cancel Subscription
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function cancel_subscription(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();


        if (isset($data['institute_id'])) {
            $update_data['institute_id'] = sanitize_input($data['institute_id']);
        }
        $update_data['status'] = 'Cancelled';
        $update_data['last_updated'] = date('Y-m-d H:i:s');
        $db->table('institute_subscriptions')->update($update_data, ['id' => $data['subscription_id']]);


        // Update Institute Table 
        $subscription_data = $this->subscription_details($data['subscription_id']);
        $institute_data['institute_id'] = $subscription_data['institute_id'];

        if ($subscription_data['exam'] != 0) {
            $institute_data['exam'] = 0;
        }

        if ($subscription_data['live'] != 0) {
            $institute_data['live'] = 0;
        }

        if ($subscription_data['dlp'] != 0) {
            $institute_data['dlp'] = 0;
        }

        if ($subscription_data['support'] != 0) {
            $institute_data['support'] = 0;
        }

        $InstituteModel = new InstituteModel();
        $InstituteModel->update_institute_module_feature($institute_data);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add sales team but failed', $log_info);

            return false;
        } else {
            // Activity Log
            $log_info =  [
                'username' =>  session()->get('username'),
                'item' =>  'Subscription cancelled for the institute with ID: ' . $subscription_data['institute_id']
            ];
            $UserActivityModel = new UserActivityModel();
            $UserActivityModel->log('deleted', $log_info);
            return true;
        }
    }
    /*******************************************************/
}
