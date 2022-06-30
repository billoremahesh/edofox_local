<?php

$serverName = $_SERVER['HTTP_HOST'];

/**
 *  Setting the service base URL right from the host server
 */
if ($serverName === "dev.edofox.com" || $serverName == "web-demo.edofox.com" || $serverName === "localhost" || $serverName === "localhost:8012") {
    $host = "https://dev.edofox.com:8443/edofox";

    /**
     * Service urls for Localhost server
     */
    // $host = "http://localhost:8080/edofox";

} elseif ($serverName === "edofoxaws.com") {
    $host = "https://edofoxaws.com:8443/edofox";
} else {
    $host = "https://test.edofox.com:8443/edofox";
}


/**
 * API Service Urls
 */
$root = $host . "/service";
$rootAdmin = $host . "/admin";
$fetchTestDataUrl = $root . "/getTest";
$fetchStudentTestActivityUrl = $root . "/getStudentExamActivity";
$fetchTestWithResultDataUrl = $root . "/getTestResult";
$fetchFeedbackData = $rootAdmin . "/getFeedbackData";
$fetchFeedbackSummary = $rootAdmin . "/getFeedbackSummary";
$createAdminUrl = $rootAdmin . "/createAdmin";
$fetchStudentPerformanceUrl = $root . "/getStudentPerformance";
$sendNotificationUrl = $rootAdmin . "/sendNotification";
$forgotPasswordStudentUrl = $root . "/forgotPassword";
$loadQuestionBankUrl = $rootAdmin . "/loadQuestionBank";

// Invoice Service Links
$generateInvoicesUrl = $host . "/super/generateInvoices";
$sendPaymentRemindersUrl = $host . "/super/sendPaymentReminders";
$suspendAccountsUrl = $host . "/super/suspendAccounts";

