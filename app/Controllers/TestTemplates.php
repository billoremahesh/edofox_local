<?php

namespace App\Controllers;

use \App\Models\TestTemplatesModel;
use \App\Models\TestTemplateConfigModel;



class TestTemplates extends BaseController
{
    /**
     * Test Templates
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function index()
    {
        $data['title'] = "Test Templates";
        $TestTemplatesModel = new TestTemplatesModel();
        $institute_id =  decrypt_cipher(session()->get('instituteID'));
        $data['test_templates'] = $TestTemplatesModel->fetch_institute_templates($institute_id);
        echo view('pages/tests/test_templates/overview', $data);
    }
    /*******************************************************/

    /**
     * Test Template Data  
     *
     * @return json
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_template_data()
    {
        $post_data = (array) $this->request->getVar();
        $template_id = $post_data['template_id'];
        $TestTemplateConfigModel = new TestTemplateConfigModel();
        $search_result = $TestTemplateConfigModel->fetch_template_config_details($template_id);
        print_r($search_result);
    }
    /*******************************************************/


    /**
     * Test Template Rules 
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function test_template_rules(string $enc_template_id)
    {
        $data['title'] = "Test Templates Rules";
        $TestTemplateConfigModel = new TestTemplateConfigModel();
        $template_id =  decrypt_cipher($enc_template_id);
        $data['templates_rules'] = $TestTemplateConfigModel->fetch_template_rules($template_id);
        echo view('pages/tests/test_templates/template_rules', $data);
    }
    /*******************************************************/




    /**
     *  Add Template - Model
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_template(string $enc_test_id, $redirect = 'tests')
    {
        $data['title'] = "Add Template";
        $data['test_id'] = $enc_test_id;
        $data['institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $data['redirect'] = $redirect;
        echo view('modals/test_templates/add', $data);
    }
    /*******************************************************/


    /**
     *  Update Template - Model
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_template(string $enc_template_id, $redirect = 'testTemplates')
    {
        $data['title'] = "Update Template Details";
        $TestTemplatesModel = new TestTemplatesModel();
        $template_id =  decrypt_cipher($enc_template_id);
        $data['template_id'] = $enc_template_id;
        $data['redirect'] = $redirect;
        $data['templates_details'] = $TestTemplatesModel->fetch_template_details($template_id);
        echo view('modals/test_templates/update', $data);
    }
    /*******************************************************/


    /**
     *  Delete Test Template - Model
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function disable_template(string $enc_template_id, $redirect = "testTemplates")
    {
        $data['title'] = "Delete Test Template";
        $TestTemplatesModel = new TestTemplatesModel();
        $template_id =  decrypt_cipher($enc_template_id);
        $data['template_id'] = $enc_template_id;
        $data['redirect'] = $redirect;
        $data['templates_details'] = $TestTemplatesModel->fetch_template_details($template_id);
        echo view('modals/test_templates/delete', $data);
    }
    /*******************************************************/

    /**
     *  Update Test Template Rule - Model
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_template_rule(string $enc_template_rule_id, string $enc_template_id, $redirect = "testTemplates")
    {
        $data['title'] = "Update Test Template Rule";
        $TestTemplateConfigModel = new TestTemplateConfigModel();
        $template_rule_id =  decrypt_cipher($enc_template_rule_id);
        $data['template_rule_id'] = $enc_template_rule_id;
        $data['template_id'] = $enc_template_id;
        $data['redirect'] = $redirect . "/test_template_rules/" . $enc_template_id;
        $data['templates_rule_details'] = $TestTemplateConfigModel->fetch_rule_details($template_rule_id);
        echo view('modals/test_templates/update_template_rule', $data);
    }
    /*******************************************************/

    /**
     *  Delete Test Template Rule - Model
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function disable_template_rule(string $enc_template_rule_id, string $enc_template_id, $redirect = "testTemplates")
    {
        $data['title'] = "Delete Test Template Rule";
        $TestTemplateConfigModel = new TestTemplateConfigModel();
        $template_rule_id =  decrypt_cipher($enc_template_rule_id);
        $data['template_rule_id'] = $enc_template_rule_id;
        $data['template_id'] = $enc_template_id;
        $data['redirect'] = $redirect . "/test_template_rules/" . $enc_template_id;
        $data['templates_rule_details'] = $TestTemplateConfigModel->fetch_rule_details($template_rule_id);
        echo view('modals/test_templates/delete_template_rule', $data);
    }
    /*******************************************************/


    /**
     * Add Template Details - Submit Method
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_template_details_submit()
    {
        $session = session();
        $data = $this->request->getVar();
        $data['test_id'] = decrypt_cipher($data['test_id']);
        $redirect =  $data['redirect'];
        $TestTemplatesModel = new TestTemplatesModel();
        $template_id = $TestTemplatesModel->add_template($data);
        if ($template_id) {
            session()->setFlashdata('toastr_success', 'Template details added successfully.');
        } else {
            $session->setFlashdata('toastr_error', 'Error in processing');
        }
        $redirect = '/testTemplates/test_template_rules/' . encrypt_string($template_id);
        return redirect()->to(base_url($redirect));
    }
    /*******************************************************/

    /**
     * Update Template Details - Submit Method
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_template_details_submit()
    {
        $session = session();
        $data = $this->request->getVar();
        $data['template_id'] = decrypt_cipher($data['template_id']);
        $redirect =  $data['redirect'];
        $TestTemplatesModel = new TestTemplatesModel();
        if ($TestTemplatesModel->update_template_details($data)) {
            session()->setFlashdata('toastr_success', 'Template details updated successfully.');
        } else {
            $session->setFlashdata('toastr_error', 'Error in processing');
        }
        return redirect()->to(base_url($redirect));
    }
    /*******************************************************/


    /**
     * Delete Template Details - Submit Method
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_template_details_submit()
    {
        $session = session();
        $data = $this->request->getVar();
        $data['template_id'] = decrypt_cipher($data['template_id']);
        $redirect =  $data['redirect'];
        $TestTemplatesModel = new TestTemplatesModel();
        if ($TestTemplatesModel->update_template_details($data)) {
            session()->setFlashdata('toastr_success', 'Test Template deleted successfully.');
        } else {
            $session->setFlashdata('toastr_error', 'Error in processing');
        }
        return redirect()->to(base_url($redirect));
    }
    /*******************************************************/


    /**
     * Update Template Rule Details - Submit Method
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_template_rule_submit()
    {
        $session = session();
        $data = $this->request->getVar();
        $data['template_id'] = decrypt_cipher($data['template_id']);
        $data['template_rule_id'] = decrypt_cipher($data['template_rule_id']);
        $redirect =  $data['redirect'];
        $TestTemplateConfigModel = new TestTemplateConfigModel();
        if ($TestTemplateConfigModel->update_template_config_data($data)) {
            session()->setFlashdata('toastr_success', 'Template Rule updated successfully.');
        } else {
            $session->setFlashdata('toastr_error', 'Error in processing');
        }
        return redirect()->to(base_url($redirect));
    }

    /**
     * Delete Template Rule Details - Submit Method
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_template_rule_submit()
    {
        $session = session();
        $data = $this->request->getVar();
        $data['template_id'] = decrypt_cipher($data['template_id']);
        $data['template_rule_id'] = decrypt_cipher($data['template_rule_id']);
        $redirect =  $data['redirect'];
        $TestTemplateConfigModel = new TestTemplateConfigModel();
        if ($TestTemplateConfigModel->update_template_config_data($data)) {
            session()->setFlashdata('toastr_success', 'Template Rule deleted successfully.');
        } else {
            $session->setFlashdata('toastr_error', 'Error in processing');
        }
        return redirect()->to(base_url($redirect));
    }
    /*******************************************************/
}
