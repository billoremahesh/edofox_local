<?php

namespace App\Models;

use CodeIgniter\Model;

class LinkRoutesVisitsModel extends Model
{
    /**
     * function used for the visited links by user based on admin_id
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function user_visited_links(array $data)
    {
        $db = \Config\Database::connect();
        $admin_id = sanitize_input($data['admin_id']);
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
        JOIN link_routes_visits
        ON link_routes_visits.link_id = link_routes.id
        WHERE link_routes.is_disabled = 0 
        AND link_routes_visits.admin_id = '$admin_id'
        $search_string $institute_cond
        ORDER BY link_routes_visits.visit_count DESC LIMIT 10";

        $query = $db->query($sql);

        $result = $query->getResultArray();
        $formatted_data = array();

        $formatted_data['incomplete_results'] = false;
        $formatted_data['items'] = array();
        $cnt = 0;
        if (!empty($result)) {
            // link label
            // $route_object = array();
            // $route_object['name'] = "Most visited";
            // array_push($formatted_data['items'], $route_object);
            foreach ($result as $route_links_data) {
                $route_link_object = array();
                $route_link_object['name'] = $route_links_data['route_name'];
                $route_link_object['route_link'] = base_url() . $route_links_data['route'];
                array_push($formatted_data['items'], $route_link_object);
                $cnt++;
            }
        }
        $formatted_data['total_count'] = $cnt;
        return json_encode($formatted_data);
    }
    /*******************************************************/



    /**
     * Get Visited Link Data
     *
     * @param integer $link_id
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_visited_link_data(int $link_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT link_routes_visits.*
        FROM link_routes_visits 
        WHERE  link_routes_visits.link_id = :link_id: ";

        $query = $db->query($sql, [
            'link_id' => sanitize_input($link_id)
        ]);

        return $query->getRowArray();
    }
    /*******************************************************/



    /**
     *  Add Visited Links
     *
     * @param array $data
     *
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_visited_links(array $data)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        if (isset($data['link_id']) && !empty($data['link_id']) && isset($data['admin_id']) && !empty($data['admin_id'])) {
            $link_id = sanitize_input($data['link_id']);
            $link_data = $this->get_visited_link_data($link_id);
            // Check already visited
            if (!empty($link_data)) {
                $visited_link_count = $link_data['visit_count'];
                $update_arr['last_updated'] = date('Y-m-d H:i:s');
                $update_arr['visit_count'] = $visited_link_count + 1;
                $db->table('link_routes_visits')->update($update_arr, ['link_id' => $link_id]);
            } else {
                $admin_id = sanitize_input($data['admin_id']);
                $add_arr['link_id'] = $link_id;
                $add_arr['admin_id'] = $admin_id;
                $add_arr['visit_count'] = 1;
                $db->table('link_routes_visits')->insert($add_arr);
            }
        } else {
            return false;
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            return false;
        } else {
            return true;
        }
    }
    /*******************************************************/
}
