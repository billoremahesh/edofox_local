<?php

namespace App\Controllers;


use \App\Models\LinkRoutesModel;
use \App\Models\LinkRoutesVisitsModel;

class LinkRoutes extends BaseController
{



    /*****************************************************************
     * #################### SUPER ADMIN CODE START HERE ###############
     *****************************************************************/
    public function index()
    {
        // Check super admin permission 
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }

        $data['title'] = "Routes";
        $LinkRoutesModel = new LinkRoutesModel();
        $data['link_routes_data'] = $LinkRoutesModel->get_all_routes();
        return view('super_admin/link_routes/overview', $data);
    }
    /*******************************************************/

    /**
     * Add New Route Modal
     */
    public function add_new_route($redirect = '/linkRoutes')
    {
        // Check super admin permission 
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Add New Route";
        $data['redirect'] = $redirect;
        echo view('modals/link_routes/add_new_route', $data);
    }
    /*******************************************************/



    /**
     * Update Route Modal
     *
     * @param string $route_id (encrypted string)
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_route($route_id, $redirect = '/linkRoutes')
    {
        // Check super admin permission 
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Update Route Details";
        $data['route_id'] = $route_id;
        $data['redirect'] = $redirect;
        $LinkRoutesModel = new LinkRoutesModel();
        $data['route_details'] = $LinkRoutesModel->get_route_details(decrypt_cipher($route_id));
        echo view('modals/link_routes/update', $data);
    }
    /*******************************************************/


    /**
     * Disable Route Modal
     *
     * @param string $route_id (encrypted string)
     * @param string $redirect
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function disable_route($route_id, $redirect = 'linkRoutes')
    {
        // Check super admin permission 
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Delete Route Details";
        $data['route_id'] = $route_id;
        $data['redirect'] = $redirect;
        $LinkRoutesModel = new LinkRoutesModel();
        $data['route_details'] = $LinkRoutesModel->get_route_details(decrypt_cipher($route_id));
        echo view('modals/link_routes/delete', $data);
    }
    /*******************************************************/

    /**
     * Add Route Submit
     *
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_route_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'route_name' => ['label' => 'Route Name', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $LinkRoutesModel = new LinkRoutesModel();
            if ($LinkRoutesModel->add_route($data)) {
                $session->setFlashdata('toastr_success', 'New Link Route added successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/


    /**
     * Update Route Submit
     *
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_route_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'route_id' => ['label' => 'Route ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $data['route_id'] = decrypt_cipher($data['route_id']);
            $LinkRoutesModel = new LinkRoutesModel();
            if ($LinkRoutesModel->update_route($data)) {
                $session->setFlashdata('toastr_success', 'Link Route updated successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/


    /**
     * Disable Route Submit
     *
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_route_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'route_id' => ['label' => 'Route ID', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $data['route_id'] = decrypt_cipher($data['route_id']);
            $LinkRoutesModel = new LinkRoutesModel();
            if ($LinkRoutesModel->delete_route($data)) {
                $session->setFlashdata('toastr_success', 'Link Route deleted successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
    /*******************************************************/

    /*****************************************************************
     * #################### SUPER ADMIN CODE END HERE ###############
     *****************************************************************/




    /**
     * Search Route Links and User visited routes
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function search_routes()
    {
        $post_data = (array) $this->request->getVar();
        $post_data['institute_id'] = decrypt_cipher(session()->get('instituteID'));
        $post_data['admin_id'] = decrypt_cipher(session()->get('login_id'));
        $post_data['perms'] = isset($_SESSION['perms']) ? $_SESSION['perms'] : array();
        $LinkRoutesVisitsModel = new LinkRoutesVisitsModel();
        $user_visited_search_result = $LinkRoutesVisitsModel->user_visited_links($post_data);
        $LinkRoutesModel = new LinkRoutesModel();
        $search_result = $LinkRoutesModel->search_route_links($post_data);
        if (!empty($user_visited_search_result)) {
            print_r($user_visited_search_result);
        } else {
            print_r($search_result);
        }
    }
    /*******************************************************/
}
