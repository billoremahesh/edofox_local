<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * Basic Login Authentication
 *  @author Rushi B <rushikesh.badadale@mattersoft.xyz>
 */


class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->has('EdofoxAdminLoggedIn') AND !session()->has('isSuperAdminLoggedIn') ) :
            session()->setFlashdata('toastr_error', 'Not logged in or session expired.');
            return redirect()->to(base_url('/login'));
        endif;
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
