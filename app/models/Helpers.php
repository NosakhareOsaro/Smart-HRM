<?php

/**
 * Get the base URL.
 *
 * @param string $uri  URI to append to the base URL
 *
 * @return string  The complete URL
 */
function base_url($uri = '')
{
    global $config;
    $base_url = $config["base_url"] . $uri;
    return $base_url;
}

/**
 * Set a flash alert message.
 *
 * @param string $message  Alert message
 * @param string $type     Alert type (default, success, error, warning, info)
 *
 * @return void
 */
function alert_flash($message, $type = 'default')
{
    $alert['type'] = $type;
    $alert['message'] = $message;
    // Send alert data to session
    $_SESSION['alert'] = $alert;
}

/**
 * Display the alert message.
 *
 * @return void
 */
function alert_display()
{
    if (isset($_SESSION['alert']['message']) && isset($_SESSION['alert']['type'])) {
        // Get data from session
        $message = $_SESSION['alert']['message'];
        $type = $_SESSION['alert']['type'];

        // Determine alert style based on type
        $alert_styles = [
            'success' => 'alert alert-success',
            'error' => 'alert alert-danger',
            'warning' => 'alert alert-warning',
            'info' => 'alert alert-info',
            'default' => 'alert alert-default'
        ];
        $alert_style = isset($alert_styles[$type]) ? $alert_styles[$type] : $alert_styles['default'];

        // Echo the alert message in bootstrap alert format
        echo "<div class=\"$alert_style\">$message</div>";

        // Flush/unset the alert session data after echoing
        unset($_SESSION['alert']);
    }
}

/**
 * Get the CSRF token.
 *
 * @return string  The CSRF token
 */
function csrf_token()
{
    if (isset($_SESSION['csrf_token']) && !empty($_SESSION['csrf_token'])) {
        $csrf_token = trim($_SESSION['csrf_token']);
    } else {
        // Generate random token
        $token = mt_rand((int)1000, (int)99999999) . time();
        $csrf_token = sha1($token);
        $_SESSION['csrf_token'] = $csrf_token; // Save to session
    }
    return $csrf_token;
}

/**
 * Generate a CSRF field.
 *
 * @param bool $return_as_value  Whether to return the field as a value or echo it
 *
 * @return string  The CSRF field HTML
 */
function csrf_field($return_as_value = false)
{
    $csrf_token = csrf_token(); // Call function to generate/get the CSRF token

    $form_field = "<input type=\"hidden\" name=\"_csrf_token\" value=\"$csrf_token\">";
    if ($return_as_value === true) {
        return $form_field;
    } else {
        echo $form_field;
    }
}

/**
 * Get the CSRF token as a query string.
 *
 * @return string  The CSRF token as a query string
 */
function csrf_querystring()
{
    $csrf_token = csrf_token(); // Call function to generate/get the CSRF token

    return "token=$csrf_token";
}

/**
 * Verify the CSRF token.
 *
 * @param string $request_method  Request method (post or get)
 *
 * @return bool  True if the token is verified, false otherwise
 */
function csrf_verify($request_method = 'post')
{
    $csrf_token = csrf_token(); // Call function to generate/get the CSRF token

    if ($request_method === 'get') {
        $request_token = isset($_GET['token']) ? trim($_GET['token']) : null;
    } else {
        $request_token = isset($_POST['_csrf_token']) ? trim($_POST['_csrf_token']) : null;
    }

    return $request_token === $csrf_token;
}

/**
 * Create a slug from a string.
 *
 * @param string $str        String to create a slug from
 * @param string $separator  Separator to use (dash or underscore)
 * @param bool   $lowercase  Whether to convert the slug to lowercase
 *
 * @return string  The generated slug
 */
if (!function_exists('create_slug')) {
    function create_slug($str, $separator = '-', $lowercase = true)
    {
        if ($separator === 'dash') {
            $separator = '-';
        } elseif ($separator === 'underscore') {
            $separator = '_';
        }

        $q_separator = preg_quote($separator);
        $trans = array(
            '&.+?;'                 => '',
            '[^a-z0-9 _-]'          => '',
            '\s+'                   => $separator,
            '(' . $q_separator . ')+'   => $separator
        );
        $str = strip_tags($str);
        foreach ($trans as $key => $val) {
            $str = preg_replace("#" . $key . "#i", $val, $str);
        }
        if ($lowercase === true) {
            $str = mb_strtolower($str);
        }
        return trim($str, $separator);
    }
}
