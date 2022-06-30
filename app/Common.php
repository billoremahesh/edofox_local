<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the frameworks
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @link: https://codeigniter4.github.io/CodeIgniter4/
 */


// Function to get the client IP address
function get_client_ip()
{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}


/************************************************************/
/**
 * Format number in indian currency format
 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
 * @return string
 */
function indian_number_format($number): string
{

    $decimal = (string) ($number - floor($number));
    $money = floor($number);
    $length = strlen($money);
    $delimiter = '';
    $money = strrev($money);

    for ($i = 0; $i < $length; $i++) {
        if (($i == 3 || ($i > 3 && ($i - 1) % 2 == 0)) && $i != $length) {
            $delimiter .= ',';
        }
        $delimiter .= $money[$i];
    }

    $result = strrev($delimiter);
    $decimal = preg_replace("/0\./i", ".", $decimal);
    $decimal = substr($decimal, 0, 3);

    if ($decimal != '0') {
        $result = $result . $decimal;
    }

    return $result;
}

/************************************************************/






/************************************************************/
/**
 * Remove Special Chars
 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
 * @return string
 */
function remove_special_chars($string)
{
    if (!empty($string) && !is_array($string)) {
        $string = str_ireplace('\'', ' ', $string);
        $string = str_ireplace('"', ' ', $string);
        $string = str_ireplace('<', ' ', $string);
        $string = str_ireplace('>', ' ', $string);
        $string = str_ireplace('&lt;', ' ', $string);
        $string = str_ireplace('&gt;', ' ', $string);
        $string = str_ireplace('&quot;', ' ', $string);
        $string = str_ireplace('&amp;', ' and ', $string);
        $string = str_ireplace('&', ' and ', $string);
    }
    return $string;
}
/*******************************************************/




/************************************************************/
/**
 * Sanitize input
 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
 * @return string
 */
function sanitize_input($data)
{
    if (!empty($data) && !is_array($data)) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
    }
    return $data;
}
/*******************************************************/





/************************************************************/
/**
 * Sanitize input and allow slashes
 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
 * @return string
 */
function sanitize_input_allow_slashes($data)
{
    if (!empty($data) && !is_array($data)) {
        $data = trim($data);
        $data = htmlspecialchars($data);
    }
    return $data;
}
/*******************************************************/




/****************************************************************/
/**
 * Encrypts a given string using the CI method
 * Added by Hemant
 */
function encrypt_string($string)
{
    $encrypter = \Config\Services::encrypter();
    return bin2hex($encrypter->encrypt($string));
}
/****************************************************************/



/****************************************************************/
/**
 * Decrypts a given string using the CI method
 * Added by Hemant
 */
function decrypt_cipher($string)
{
    $encrypter = \Config\Services::encrypter();
    try {
        return $encrypter->decrypt(hex2bin($string));
    } catch (\Exception $e) {
        die("There was some error in security. Please try again. ");
    }
}
/****************************************************************/


function secToHR($seconds)
{
    $minutes = floor($seconds / 60);
    $seconds = $seconds % 60;
    return "$minutes:$seconds";
}


// Function to get all the dates in given range
function getDatesFromRange($start, $end, $format = 'Y-m-d')
{

    // Declare an empty array
    $array = array();

    // Variable that store the date interval
    // of period 1 day
    $interval = new DateInterval('P1D');

    $realEnd = new DateTime($end);
    $realEnd->add($interval);

    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

    // Use loop to store date into array
    foreach ($period as $date) {
        $array[] = $date->format($format);
    }

    // Return the array elements
    return $array;
}

/**
 * Check Super Admin Session 
 *
 * @return boolean
 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
 */
function isAuthorizedSuperAdmin()
{
    $userType = session()->get('user_type');
    if ($userType != "super_admin") {
        session()->setFlashdata('toastr_error', 'UnAuthorized access.');
        return false;
    }
    return true;
}

// Check Authorized User
function isAuthorized($section)
{
    $session = session();
    // Get Perms
    if (session()->has('perms')) :
        $perms = $session->get('perms');
    else :
        $perms = array();
    endif;
    if (in_array($section, $perms) or in_array("all_perms", $perms)) {
        return true;
    } else {
        $session->setFlashdata('toastr_error', 'UnAuthorized access.');
        return false;
    }
}
/*******************************************************/

//Ref: https://stackoverflow.com/a/41910059
//To change the added link into an embed link
function getEmbedUrl($url)
{
    if (strpos($url, 'youtu.be/') !== false || strpos($url, 'youtube.com/') !== false) {
        //it is youtube video

        $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
        $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';
        $youtube_id = "";
        if (preg_match($longUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }

        if (preg_match($shortUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }
        return 'https://www.youtube.com/embed/' . $youtube_id;
    }

    if (strpos($url, 'https://vimeo.com/') !== false || strpos($url, 'www.vimeo.com') !== false) {
        //it is Vimeo video
        $videoId = explode("vimeo.com/", $url)[1];
        if (strpos($videoId, '&') !== false) {
            $videoId = explode("&", $videoId)[0];
        }
        return 'https://player.vimeo.com/video/' . $videoId;
    }
}



function searchForId($id, $array)
{
    if (!empty($array)) {
        foreach ($array as $key => $val) {
            //echo "Comparing=>". $id . " with => ". $val[0] . " .... ";
            if (isset($val[0])) {
                if ($val[0] === $id) {
                    //echo "Found match ". $key;
                    return $key;
                }
            }
        }
    }
    return -1;
}


function searchForkey($id, $findk, $array)
{
    if (!empty($array)) {
        foreach ($array as $key => $val) {
            //echo "Comparing=>". $id . " with => ". $val[0] . " .... ";
            if (isset($val[$findk])) {
                if ($val[$findk] === $id) {
                    //echo "Found match ". $key;
                    return $key;
                }
            }
        }
    }
    return -1;
}


/**
 * Convert date to specific format
 * @RushikeshB
 * @return string
 */
function date_format_custom($date, $format): string
{
    if ($date != "0000-00-00" && isset($date)) {
        $formatted_date = date($format, strtotime($date));
        return $formatted_date;
    }
    return "--";
}
/*******************************************************/


// Format Test Options
function format_option($selected_option, $format): string
{
    if ($format == 'A B C D') {
        if (strlen($selected_option) == 1) {
            switch ($selected_option) {
                case "A":
                    $option_selected_formatted = "option1";
                    break;
                case "B":
                    $option_selected_formatted = "option2";
                    break;
                case "C":
                    $option_selected_formatted = "option3";
                    break;
                case "D":
                    $option_selected_formatted = "option4";
                    break;
                default:
                    $option_selected_formatted = $selected_option;
            }
        } else {
            // Else the answer may be multiple, number or match type
            $option_selected_formatted = $selected_option;
            $option_selected_formatted = str_replace(" ", "", $selected_option);

            // Selected Answer string DOES NOT have hyphen so it is NOT Match type
            if (strpos($selected_option, '-') === false) {
                $option_selected_formatted = str_replace("A", "option1", $option_selected_formatted);
                $option_selected_formatted = str_replace("B", "option2", $option_selected_formatted);
                $option_selected_formatted = str_replace("C", "option3", $option_selected_formatted);       
                $option_selected_formatted = str_replace("D", "option4", $option_selected_formatted);
            }
        }

        return $option_selected_formatted;
    } else {
        return strtolower($selected_option);
    }
}
/*******************************************************/


/**
 * Get DLP Classroom Chapters
 *
 * @param integer $classroom_id
 * @param integer $subject_id
 * @param integer $is_disabled
 *
 * @return Array
 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
 */
function dlp_chapters(int $classroom_id, int $subject_id, int $is_disabled = 0)
{

    $db = \Config\Database::connect();
    $sql = "SELECT dlp_chapters_classroom_map.*, chapters.chapter_name
        FROM dlp_chapters_classroom_map
        INNER JOIN chapters
        ON dlp_chapters_classroom_map.chapter_id = chapters.id 
        AND dlp_chapters_classroom_map.status = :is_disabled:
        WHERE dlp_chapters_classroom_map.classroom_id = :classroom_id:
        AND chapters.subject = :subject_id:
        ORDER BY chapter_no";

    $query = $db->query($sql, [
        'classroom_id' => sanitize_input($classroom_id),
        'subject_id' => sanitize_input($subject_id),
        'is_disabled' => sanitize_input($is_disabled)
    ]);

    return $query->getResultArray();
}

function subject_chapters($subject_id)
{
    $db = \Config\Database::connect();
    $sql = "SELECT chapters.*
        FROM chapters 
        WHERE chapters.subject = :subject_id:
        ORDER BY chapter_name";

    $query = $db->query($sql, [
        'subject_id' => sanitize_input($subject_id)
    ]);

    return $query->getResultArray();
}

function object_to_array($data)
{
    if (is_array($data) || is_object($data)) {
        $result = [];
        foreach ($data as $key => $value) {
            $result[$key] = (is_array($data) || is_object($data)) ? object_to_array($value) : $value;
        }
        return $result;
    }
    return $data;
}

// Date timestamp for dynamic timezones
function changeDateTimezone($date, $targetFormat = "Y-m-d H:i:s")
{
    if (isset($_SESSION['timezone']) && $_SESSION['timezone'] != "") {
        $from = 'Asia/Kolkata';
        $to = $_SESSION['timezone'];
        $formatted_date = new DateTime($date, new DateTimeZone($from));
        $formatted_date->setTimeZone(new DateTimeZone($to));
        return $formatted_date->format($targetFormat);
    } else {
        return $date;
    }
}

// Convert time to default timezone
function DefaultTimezone($date, $targetFormat = "Y-m-d H:i:s")
{
    if (isset($_SESSION['timezone']) && $_SESSION['timezone'] != "") {
        $from = $_SESSION['timezone'];
        $to = 'Asia/Kolkata';
        $formatted_date = new DateTime($date, new DateTimeZone($from));
        $formatted_date->setTimeZone(new DateTimeZone($to));
        return $formatted_date->format($targetFormat);
    } else {
        return $date;
    }
}

/**
 * Unread Activity Logs
 *
 * @param [type] $admin_id
 * @param string $datetime
 *
 * @return array
 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
 */
function unread_activity_logs($admin_id, $datetime = "")
{
    $db = \Config\Database::connect();
    $sql_fetch_todays_exam = "SELECT *
                FROM admin_activity
        WHERE created_date >= :datetime: and admin_id = :admin_id: and module != 'page_access' LIMIT 25 ";

    $query = $db->query($sql_fetch_todays_exam, [
        'admin_id' => sanitize_input($admin_id),
        'datetime' => sanitize_input($datetime)
    ]);
    $result = $query->getResultArray();
    return $result;
}
/*******************************************************/



/**
 * Get Template Rule
 *
 * @param int $template_id
 * @param string $rule_name
 * @param int $question_no
 *
 * @return array
 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
 */
function get_template_rule(int $template_id, string $rule_name, int $question_no)
{
    $db = \Config\Database::connect();
    $sql_fetch_todays_exam = "SELECT *
                FROM test_template_config
        WHERE template_id = :template_id: and rule_name = :rule_name: and from_question <= :question_no: and to_question >= :question_no: and is_disabled = '0'  LIMIT 1";

    $query = $db->query($sql_fetch_todays_exam, [
        'template_id' => sanitize_input($template_id),
        'rule_name' => sanitize_input($rule_name),
        'question_no' => sanitize_input($question_no)
    ]);
    $result = $query->getRowArray();
    return $result;
}
/*******************************************************/



/**
 * Get Plan price - Needed in proposal
 *
 * @param string $plan_name
 * @param string $module
 *
 * @return int
 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
 */
function fetch_plan_price(string $plan_name, string $module)
{
    $db = \Config\Database::connect();
    $sql_fetch_todays_exam = "SELECT *
                FROM edofox_pricing_plans
        WHERE plan_name = :plan_name: and module = :module: LIMIT 1";

    $query = $db->query($sql_fetch_todays_exam, [
        'plan_name' => sanitize_input($plan_name),
        'module' => sanitize_input($module)
    ]);
    $result = $query->getRowArray();
    return $result['price'];
}
/*******************************************************/


/**
 * Get Plan price - Needed in proposal
 *
 * @param string $plan_name
 * @param string $module
 *
 * @return int
 * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
 */
function check_active_invoice_subscription(int $subscription_id)
{
    $db = \Config\Database::connect();
    $sql_1 = "SELECT id
                FROM edofox_invoices
        WHERE subscription_id = :subscription_id:";
    $query1 = $db->query($sql_1, [
        'subscription_id' => sanitize_input($subscription_id)
    ]);
    $result1 = $query1->getResultArray();
    if (count($result1) > 0) {
        $sql_2 = "SELECT id
                FROM edofox_invoices
        WHERE status = 'Pending' and payment_type = 'Yearly' and subscription_id = :subscription_id:";

        $query2 = $db->query($sql_2, [
            'subscription_id' => sanitize_input($subscription_id)
        ]);

        $result2 = $query2->getResultArray();
        if (count($result2) > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}
/*******************************************************/

/**
 * Converting Currency Numbers to words currency format
 * @RushikeshB
 * @return string
 */
function getIndianCurrencyInWords($amount): string
{
    $number = $amount;
    $no = floor($number);
    $point = round($number - $no, 2) * 100;
    $hundred = null;
    $digits_1 = strlen($no);
    $i = 0;
    $str = array();
    $words = array(
        '0' => '', '1' => 'one', '2' => 'two',
        '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
        '7' => 'seven', '8' => 'eight', '9' => 'nine',
        '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
        '13' => 'thirteen', '14' => 'fourteen',
        '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
        '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
        '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
        '60' => 'sixty', '70' => 'seventy',
        '80' => 'eighty', '90' => 'ninety'
    );
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            // $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $plural = (($counter = count($str)) && $number > 9) ? '' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str[] = ($number < 21) ? $words[$number] .
                " " . $digits[$counter] . $plural . " " . $hundred
                :
                $words[floor($number / 10) * 10]
                . " " . $words[$number % 10] . " "
                . $digits[$counter] . $plural . " " . $hundred;
        } else $str[] = null;
    }
    $str = array_reverse($str);
    $result = implode('', $str);
    $points = ($point) ?
        "." . $words[$point / 10] . " " .
        $words[$point = $point % 10] : '';
    // return $result . "Rupees  " . $points . " ";
    return $result . " " . $points . " ";
}
/************************************************************/
