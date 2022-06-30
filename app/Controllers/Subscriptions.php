<?php

namespace App\Controllers;

use \App\Models\InstituteModel;
use \App\Models\PricingPlansModel;
use \App\Models\InstituteSubscriptionsModel;


class Subscriptions extends BaseController
{

    public function index()
    {
    }


    public function overview(string $institute_id)
    {
        $data['title'] = "Institute Subscriptions";
        $data['institute_id'] = $institute_id;
        $InstituteModel = new InstituteModel();
        $data['institute_data'] = $InstituteModel->get_institute_details(decrypt_cipher($institute_id));
        $InstituteSubscriptionsModel = new InstituteSubscriptionsModel();
        $data['institute_subscriptions'] = $InstituteSubscriptionsModel->institute_subscriptions(decrypt_cipher($institute_id));
        return view('super_admin/subscriptions/overview', $data);
    }

    public function new_subscription(string $institute_id)
    {
        $data['title'] = "New Subscription";
        $data['institute_id'] = decrypt_cipher($institute_id);
        $InstituteModel = new InstituteModel();
        $data['institute_data'] = $InstituteModel->get_institute_details($data['institute_id']);
        return view('super_admin/subscriptions/new_subscription', $data);
    }


    public function get_subcription_plans()
    {
        $max_students = $this->request->getVar('max_students');
        $plan_type = $this->request->getVar('plan_type');
        $PricingPlansModel = new PricingPlansModel();
        $subscription_plans = $PricingPlansModel->get_pricing_plans($max_students, $plan_type);
        print_r(json_encode($subscription_plans));
    }


    public function get_unbilled_entitites(){
        $max_students = $this->request->getVar('max_students');
        $plan_type = $this->request->getVar('plan_type');
        $PricingPlansModel = new PricingPlansModel();
        $unbilled_entitites = $PricingPlansModel->get_unbilled_entitites($max_students, $plan_type);
        print_r(json_encode($unbilled_entitites));
    }

    public function get_selected_pkgs_amt()
    {
        $selected_pkgs = $this->request->getVar('selected_pkgs');
        $no_of_students = $this->request->getVar('no_of_students');
        $PricingPlansModel = new PricingPlansModel();
        $pkgs_amt = $PricingPlansModel->get_selected_pkgs_amt($selected_pkgs, $no_of_students);
        echo  $pkgs_amt;
    }


    public function update_subscription(string $row_id)
    {
        $data['title'] = "Update Subscription";
        $data['subscription_id'] = $row_id;
        $row_id = decrypt_cipher($row_id);
        $InstituteSubscriptionsModel = new InstituteSubscriptionsModel();
        $data['subscription_data'] = $InstituteSubscriptionsModel->subscription_details($row_id);
        // Fetch existing Subscriptions
        $max_students = $data['subscription_data']['no_of_students'];
        $plan_type = $data['subscription_data']['plan_type'];
        $PricingPlansModel = new PricingPlansModel();
        $data['subscription_plans'] = $PricingPlansModel->get_pricing_plans($max_students, $plan_type);
        $InstituteModel = new InstituteModel();
        $data['institute_data'] = $InstituteModel->get_institute_details($data['subscription_data']['institute_id']);
        return view('super_admin/subscriptions/update_subscription', $data);
    }


    public function proposal(string $public_id)
    {
        $data['title'] = "Proposal";
        $InstituteSubscriptionsModel = new InstituteSubscriptionsModel();
        $data['proposal_data'] = $InstituteSubscriptionsModel->subscription_proposal_details($public_id);
        return view('super_admin/subscriptions/proposal', $data);
    }


    public function cancel_subscription_modal(string $row_id)
    {
        $data['title'] = "Cancel Subscription";
        $data['subscription_id'] = $row_id;
        $row_id = decrypt_cipher($row_id);
        $InstituteSubscriptionsModel = new InstituteSubscriptionsModel();
        $data['subscription_data'] = $InstituteSubscriptionsModel->subscription_details($row_id);
        return view('modals/subscriptions/cancel', $data);
    }

    /**
     * Start of Submit Methods
     */

    /**
     * New Subscription Submit Method
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function new_subscription_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'subscription_type' => ['label' => 'name', 'rules' => 'required|string|min_length[1]'],
            'max_students' => ['label' => 'name', 'rules' => 'required|integer'],
            'packages_total_amt' => ['label' => 'name', 'rules' => 'required|decimal'],
            'discount' => ['label' => 'name', 'rules' => 'required'],
            'final_total_amt' => ['label' => 'name', 'rules' => 'required|decimal']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $InstituteSubscriptionsModel = new InstituteSubscriptionsModel();
            if ($InstituteSubscriptionsModel->add_new_subscription($data)) {
                $session->setFlashdata('toastr_success', 'New subscription added successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/



    /**
     * Update Subscription Submit Method
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_subscription_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'subscription_type' => ['label' => 'name', 'rules' => 'required|string|min_length[1]'],
            'max_students' => ['label' => 'name', 'rules' => 'required|integer'],
            'packages_total_amt' => ['label' => 'name', 'rules' => 'required|decimal'],
            'discount' => ['label' => 'name', 'rules' => 'required'],
            'final_total_amt' => ['label' => 'name', 'rules' => 'required|decimal']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $data['subscription_id'] = decrypt_cipher($data['subscription_id']);
            $InstituteSubscriptionsModel = new InstituteSubscriptionsModel();
            if ($InstituteSubscriptionsModel->update_subscription_details($data)) {
                $session->setFlashdata('toastr_success', 'Subscription updated successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/



    /**
     * Cancel Subscription Submit Method
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function cancel_subscription_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'subscription_id' => ['label' => 'id', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $data['subscription_id'] = decrypt_cipher($data['subscription_id']);
            $InstituteSubscriptionsModel = new InstituteSubscriptionsModel();
            if ($InstituteSubscriptionsModel->cancel_subscription($data)) {
                $session->setFlashdata('toastr_success', 'Subscription cancelled successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/


    /**
     * End of Submit Methods
     */
}
