<?php
/**
 * URL Helper Functions
 * Provides clean URL generation without .php extensions
 */

/**
 * Generate a clean URL for auth pages
 */
function auth_url($page) {
    $clean_urls = [
        'login' => '/login',
        'register' => '/register',
        'forgot_password' => '/forgot-password',
        'reset_password' => '/reset-password',
        'verify_email' => '/verify-email',
        'verify_otp' => '/verify_otp',
        'logout' => '/logout'
    ];
    
    return $clean_urls[$page] ?? "/src/auth/{$page}.php";
}

/**
 * Generate a clean URL for dashboard pages
 */
function dashboard_url($page) {
    $clean_urls = [
        'index' => '/dashboard',
        'org_add' => '/dashboard/org-add',
        'org_update' => '/dashboard/org-update',
        'org_deactivate' => '/dashboard/org-deactivate',
        'user_update' => '/dashboard/user-update',
        'user_deactivate' => '/dashboard/user-deactivate',
        'business_verifications' => '/dashboard/business-verifications',
        'view_business_verification' => '/dashboard/view-business-verification',
        'download_business_report' => '/dashboard/download-business-report'
    ];
    
    return $clean_urls[$page] ?? "/src/dashboard/{$page}.php";
}

/**
 * Generate a clean URL for embassy pages
 */
function embassy_url($page) {
    $clean_urls = [
            'dashboard' => '/embassy',
    'view_doc' => '/embassy/view_doc',
    'business_verification' => '/embassy/business-verification',
    'view_business_verification' => '/embassy/view-business-verification',
    'all_verifications' => '/embassy/all-verifications',
    'download_logs' => '/embassy/download-logs',
    'download_report' => '/embassy/download-report',
    'verify' => '/embassy/verify',
    'verify_document_content' => '/embassy/verify-document-content',
    'verify_logic' => '/embassy/verify-logic',
    'download' => '/embassy/download',
    'debug_integrity' => '/embassy/debug-integrity',
    'test_insert' => '/embassy/test-insert',
    'test_verification' => '/embassy/test-verification'
    ];
    
    return $clean_urls[$page] ?? "/embassy/{$page}";
}

/**
 * Generate a clean URL for bank pages
 */
function bank_url($page) {
    $clean_urls = [
        'dashboard' => '/bank',
        'upload' => '/bank/upload',
        'view_doc' => '/bank/view_doc'
    ];
    
    return $clean_urls[$page] ?? "/src/bank/{$page}.php";
}

/**
 * Generate a clean URL for s3upload pages
 */
function s3upload_url($page) {
    $clean_urls = [
        'upload' => '/upload',
        'download' => '/download'
    ];
    
    return $clean_urls[$page] ?? "/{$page}";
}

/**
 * Generate a clean URL with parameters
 */
function clean_url($base_url, $params = []) {
    if (empty($params)) {
        return $base_url;
    }
    
    $query_string = http_build_query($params);
    return $base_url . '?' . $query_string;
}

/**
 * Redirect to a clean URL
 */
function redirect_to($url) {
    header("Location: $url");
    exit();
}
?> 