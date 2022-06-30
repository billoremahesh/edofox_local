<?php

namespace App\Controllers;

use \App\Models\TicketsModel;

class Tickets extends BaseController
{

    /***************************************************************************************/
    /**
     * Get Tickets List
     * @author PrachiP
     * @since 2021-12-11
     * @return View
     */
    public function index()
    {
        // Check permission to view Super Admin Dashboard
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Help Desk";
        $TicketsModel = new TicketsModel();
        $data['tickets_data'] = $TicketsModel->fetch_tickets();
        return view('super_admin/tickets/overview', $data);
    }
    /***************************************************************************************/


    /***************************************************************************************/
    /**
     * Edit Tickets
     * @author PrachiP
     * @since 2021-12-11
     * @return View
     */
    public function edit_ticket($ticket_id)
    {
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }
        $data['title'] = "Edit Ticket";
        $TicketsModel = new TicketsModel();
        $data['ticket_data'] = $TicketsModel->get_ticket_data($ticket_id);
        $data['ticket_replies_data'] = $TicketsModel->get_tickets_replies_data($ticket_id);
        return view('super_admin/tickets/edit_ticket', $data);
    }
    /***************************************************************************************/

}
