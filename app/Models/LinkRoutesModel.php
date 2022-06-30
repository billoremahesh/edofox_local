<?php

namespace App\Models;

use CodeIgniter\Model;

class LinkRoutesModel extends Model
{
    /**
     * Function used for search route links based on tags, links, shortcut keys provided
     *
     * @param array $data
     *
     * @return json
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function search_route_links(array $data)
    {

        $db = \Config\Database::connect();

        $search_string = "";
        if (isset($data['search']) && !empty($data['search'])) {
            $search = sanitize_input($data['search']);
            $search_string = " AND ( 
                link_routes.route LIKE '%" . $search . "%' OR
                link_routes.shortcut_key LIKE '%" . $search . "%' OR
                link_routes.tags LIKE '%" . $search . "%' OR
                link_routes.route_name LIKE '%" . $search . "%'
                )";
        }


        // Check Institute 
        $institute_cond = " AND link_routes.institute_id IS NULL ";
        if (isset($data['institute_id']) && !empty($data['institute_id'])) {
            $institute_id = $data['institute_id'];
            $institute_cond = " AND (link_routes.institute_id IS NULL OR link_routes.institute_id = '$institute_id' )";
        }



        $sql = "SELECT link_routes.*
        FROM link_routes 
        WHERE link_routes.is_disabled = 0 $search_string 
        $institute_cond 
        ORDER BY link_routes.created_date DESC LIMIT 10";

        $query = $db->query($sql);

        $result = $query->getResultArray();
        $formatted_data = array();

        $formatted_data['incomplete_results'] = false;
        $formatted_data['items'] = array();
        $cnt = 0;
        if (!empty($result)) {
            foreach ($result as $route_links_data) {
                if (isset($data['perms']) && !empty($data['perms'])) {
                    if (in_array($route_links_data['perm_key'], $data['perms']) or in_array("all_perms", $data['perms'])) {
                        $route_link_object = array();
                        $route_link_object['name'] = $route_links_data['route_name'];
                        $route_link_object['route_link'] = base_url() . $route_links_data['route'];
                        array_push($formatted_data['items'], $route_link_object);
                        $cnt++;
                    }
                }
            }
        }

        $formatted_data['total_count'] = $cnt;
        return json_encode($formatted_data);
    }
    /*******************************************************/


    /**
     * Get link route date based on route provided
     *
     * @param string $route
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_link_route_data(string $route)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT link_routes.*
        FROM link_routes 
        WHERE  link_routes.route = :route: ";

        $query = $db->query($sql, [
            'route' => sanitize_input($route)
        ]);

        return $query->getRowArray();
    }
    /*******************************************************/



    /**
     * Get all routes data
     *
     * @param int $route_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_all_routes()
    {
        $db = \Config\Database::connect();
        $sql = "SELECT link_routes.*, SUM(link_routes_visits.visit_count) visit_total_count
        FROM link_routes 
        LEFT JOIN link_routes_visits
        ON link_routes_visits.link_id = link_routes.id
        WHERE  is_disabled = 0 
        GROUP BY link_routes.id 
        ORDER BY link_routes.route_name";

        $query = $db->query($sql);
        return $query->getResultArray();
    }
    /*******************************************************/



    /**
     * Get route details
     *
     * @param int $route_id
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_route_details(int $route_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT link_routes.*
        FROM link_routes 
        WHERE  link_routes.id = :route_id: ";

        $query = $db->query($sql, [
            'route_id' => sanitize_input($route_id)
        ]);

        return $query->getRowArray();
    }
    /*******************************************************/


    /**
     * Add Route 
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_route(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        if (isset($data['route_name']) && !empty($data['route_name'])) {
            $add_data['route_name'] = sanitize_input($data['route_name']);
        } else {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add new route details but failed due to route name missing', $log_info);
            return false;
        }

        if (isset($data['route']) && !empty($data['route'])) {
            $add_data['route'] = sanitize_input($data['route']);
        }

        if (isset($data['tags']) && !empty($data['tags'])) {
            $add_data['tags'] = sanitize_input($data['tags']);
        }


        if (isset($data['shortcut_key']) && !empty($data['shortcut_key'])) {
            $add_data['shortcut_key'] = sanitize_input($data['shortcut_key']);
        }

        if (isset($data['perm_key']) && !empty($data['perm_key'])) {
            $add_data['perm_key'] = sanitize_input($data['perm_key']);
        }


        $db->table('link_routes')->insert($add_data);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add new route details but failed', $log_info);
            return false;
        } else {
            return true;
        }
    }
    /*******************************************************/


    /**
     * Update Route 
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_route(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        if (isset($data['route_id']) && !empty($data['route_id'])) {
            $id = sanitize_input($data['route_id']);
        } else {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to update route details but failed due to route id missing', $log_info);
            return false;
        }

        if (isset($data['route_name']) && !empty($data['route_name'])) {
            $update_data['route_name'] = sanitize_input($data['route_name']);
        } else {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to update route details but failed due to route name missing', $log_info);
            return false;
        }

        if (isset($data['route']) && !empty($data['route'])) {
            $update_data['route'] = sanitize_input($data['route']);
        }

        if (isset($data['tags']) && !empty($data['tags'])) {
            $update_data['tags'] = sanitize_input($data['tags']);
        }


        if (isset($data['shortcut_key']) && !empty($data['shortcut_key'])) {
            $update_data['shortcut_key'] = sanitize_input($data['shortcut_key']);
        }

        if (isset($data['perm_key']) && !empty($data['perm_key'])) {
            $update_data['perm_key'] = sanitize_input($data['perm_key']);
        }

        $db->table('link_routes')->update($update_data, ['id' => $id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to update route details but failed', $log_info);
            return false;
        } else {
            return true;
        }
    }
    /*******************************************************/

    /**
     * Delete Route 
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function delete_route(array $data)
    {
        $db = \Config\Database::connect();

        $db->transStart();



        if (isset($data['route_id']) && !empty($data['route_id'])) {
            $id = sanitize_input($data['route_id']);
        } else {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to delete route details but failed due to route id missing', $log_info);
            return false;
        }

        $update_data = [
            'is_disabled' => '1'
        ];


        $db->table('link_routes')->update($update_data, ['id' => $id]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to delete route details but failed', $log_info);
            return false;
        } else {
            return true;
        }
    }
    /*******************************************************/
}
