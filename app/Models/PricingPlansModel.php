<?php

namespace App\Models;

use CodeIgniter\Model;

class PricingPlansModel extends Model
{


    /**
     * Get Pricing Plans
     *
     * @param int $max_students, string $plan_type
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_pricing_plans(int $max_students, string $plan_type)
    {
        $sql = "SELECT edofox_pricing_plans.*
        FROM edofox_pricing_plans 
        WHERE edofox_pricing_plans.max_students >= :max_students:
        AND plan_type = :plan_type: 
        AND is_billing = 1
        ORDER BY max_students ASC LIMIT 5";

        $query = $this->db->query($sql, [
            'max_students' => sanitize_input($max_students),
            'plan_type' => sanitize_input($plan_type)
        ]);
        return $query->getResultArray();
    }


    /**
     * Get Unbilled Entitites
     *
     * @param int $max_students, string $plan_type
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_unbilled_entitites(int $max_students, string $plan_type)
    {
        $sql = "SELECT edofox_pricing_plans.*
        FROM edofox_pricing_plans 
        WHERE edofox_pricing_plans.max_students >= :max_students:
        AND plan_type = :plan_type: 
        AND is_billing = 0
        ORDER BY max_students ASC LIMIT 3";

        $query = $this->db->query($sql, [
            'max_students' => sanitize_input($max_students),
            'plan_type' => sanitize_input($plan_type)
        ]);
        return $query->getResultArray();
    }



    /**
     * Selected Packages Amount
     *
     * @param string $selected_pkgs
     * @param int $no_of_students
     *
     * @return decimal
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function get_selected_pkgs_amt(string $selected_pkgs, int $no_of_students)
    {
        $total_amount = 0;
        $sql = "SELECT edofox_pricing_plans.*
        FROM edofox_pricing_plans 
        WHERE edofox_pricing_plans.id IN ( $selected_pkgs )
        AND is_billing = 1 ";

        $query = $this->db->query($sql, [
            'selected_pkgs' => $selected_pkgs
        ]);
        $result = $query->getResultArray();
        if (!empty($result)) {
            foreach ($result as $plan) {
                if ($plan['module'] == "OMR" or $plan['module'] == "Support") {
                    $total_amount = $total_amount + $plan['price'];
                } else {
                    $total_amount = $total_amount + ($plan['price'] * $no_of_students);
                }
            }
        }
        return $total_amount;
    }
}
