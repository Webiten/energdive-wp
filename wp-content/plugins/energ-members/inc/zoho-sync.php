<?php
defined('ABSPATH') || exit;

/**
 * ================================================================
 * ZOHO TOKEN
 * ================================================================
 */
function energ_zoho_get_access_token() {
    $cached = get_transient('energ_zoho_access_token');
    if ($cached) return $cached;

    $url = "https://accounts.zoho.in/oauth/v2/token";

    $res = wp_remote_post($url, [
        'body' => [
            'grant_type'    => 'refresh_token',
            'refresh_token' => ENERG_ZOHO_REFRESH_TOKEN,
            'client_id'     => ENERG_ZOHO_CLIENT_ID,
            'client_secret' => ENERG_ZOHO_CLIENT_SECRET
        ]
    ]);

    if (is_wp_error($res)) return false;

    $body = json_decode(wp_remote_retrieve_body($res), true);

    if (!empty($body['access_token'])) {
        set_transient('energ_zoho_access_token', $body['access_token'], 50 * MINUTE_IN_SECONDS);
        return $body['access_token'];
    }

    return false;
}

/**
 * ================================================================
 * ZOHO API REQUEST GENERIC FUNCTION
 * ================================================================
 */
function energ_zoho_api_request($method, $endpoint, $data = null) {

    $token = energ_zoho_get_access_token();
    if (!$token) return false;

    $url = ENERG_ZOHO_API_BASE . "/crm/v2.1" . $endpoint;

    $args = [
        'headers' => [
            'Authorization' => "Zoho-oauthtoken $token",
            'Content-Type'  => 'application/json'
        ],
        'timeout' => 20
    ];

    if ($data) {
        $args['body'] = json_encode(['data' => [$data]]);
    }

    switch ($method) {
        case "POST":  return wp_remote_post($url, $args);
        case "PUT":   $args['method'] = "PUT"; return wp_remote_request($url, $args);
        case "DELETE":$args['method']="DELETE";return wp_remote_request($url, $args);
        default:      return wp_remote_get($url, $args);
    }
}

/**
 * ================================================================
 * FIND EXISTING CONTACT
 * ================================================================
 */
function energ_zoho_find_record($module, $email='', $phone='') {

    $criteria = [];

    if ($email) $criteria[] = "Email:equals:$email";
    if ($phone) $criteria[] = "Phone:equals:$phone";

    if (!$criteria) return false;

    $endpoint = "/$module/search?criteria=(" . rawurlencode(implode(" OR ", $criteria)) . ")";
    $res = energ_zoho_api_request("GET", $endpoint);

    $json = json_decode(wp_remote_retrieve_body($res), true);

    return $json['data'][0] ?? false;
}

/**
 * ================================================================
 * CREATE / UPDATE CONTACT
 * ================================================================
 */
function energ_zoho_contact_sync($payload) {

    $existing = energ_zoho_find_record("Contacts", $payload["Email"], $payload["Phone"]);

    if ($existing && $existing['id']) {
        $id = $existing['id'];
        return energ_zoho_api_request("PUT", "/Contacts/$id", $payload);
    }

    // Create new
    $res = energ_zoho_api_request("POST", "/Contacts", $payload);

    // If duplicate → update
    $body = json_decode(wp_remote_retrieve_body($res), true);

    if (!empty($body['data'][0]['code']) &&
        $body['data'][0]['code'] === "DUPLICATE_DATA") {

        $dup_id = $body['data'][0]['details']['id'];
        return energ_zoho_api_request("PUT", "/Contacts/$dup_id", $payload);
    }

    return $res;
}

/**
 * ================================================================
 * SEND ACTIVATION EMAIL
 * ================================================================
 */
function energ_send_member_email($name, $email) {

    if (!$email) return;

    $subject = "Your ENERGDIVE Member Account is Active";

    $message = "
        <h2>Hello {$name},</h2>
        <p>Your ENERGDIVE member account is active now.</p>
        <p><a href='https://energ.energdive.com/dashboard'
            style='padding:10px 20px; background:#005bbb; color:#fff; text-decoration:none;'>
            Go to Dashboard
        </a></p>
        <p>Regards,<br>Team ENERGDIVE</p>
    ";

    wp_mail($email, $subject, $message, ['Content-Type: text/html']);
}

/**
 * ================================================================
 * MAIN: SYNC MEMBER → ZOHO CONTACT
 * ================================================================
 */
function energ_sync_member_to_zoho($member_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'energ_members';

    // Fetch member record
    $m = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", $member_id));
    if (!$m) return;

    /* ----------------------------
        Convert IDs → Names
    -----------------------------*/
    $industry_name = $m->industry ? (get_term($m->industry)->name ?? '') : '';
    $sub_industry_name = $m->sub_industry ? (get_term($m->sub_industry)->name ?? '') : '';

    /* ----------------------------
        Community (multiple → first)
    -----------------------------*/
    $community_first = '';
    if (!empty($m->community)) {
        $community_first = trim(explode(",", $m->community)[0]);
    }

    $sub_community_first = '';
    if (!empty($m->sub_community)) {
        $sub_community_first = trim(explode(",", $m->sub_community)[0]);
    }

    /* ----------------------------
        Build final Zoho payload
    -----------------------------*/
    $payload = [
        "Salutation"            => $m->salutation,
        "First_Name"            => $m->first_name,
        "Last_Name"             => $m->last_name,
        "Email"                 => $m->email,
        "Phone"                 => $m->phone,
        "Country"               => $m->country,
        "Company"               => $m->organization,
        "Title"                 => $m->designation,
        "Industry_Category"     => $industry_name,
        "Industry_Sub_Category" => $sub_industry_name,
        "Community"             => $community_first,
        "sub_community"         => $sub_community_first,
        "Show"                  => "EnergDive",
        "Lead_Source"           => "Website Registration"
    ];

    error_log("ZOHO PAYLOAD FIXED: " . json_encode($payload));

    $token = get_option('zoho_access_token');
    if (!$token) {
        error_log("ZOHO ERROR: No token available");
        return;
    }

    $url = "https://www.zohoapis.in/crm/v2/Contacts";

    $response = wp_remote_post($url, [
        'headers' => [
            'Authorization' => "Zoho-oauthtoken $token",
            'Content-Type'  => 'application/json'
        ],
        'body' => json_encode(["data" => [$payload]])
    ]);

    error_log("ZOHO RESPONSE FIXED: " . wp_remote_retrieve_body($response));

    /* ----------------------------
        AUTO-DELETE corresponding Lead
    -----------------------------*/
    if (!empty($m->email)) {
        $lead = energ_zoho_find_record("Leads", $m->email, $m->phone);
        if ($lead && !empty($lead['id'])) {
            energ_zoho_delete_lead($lead['id']);
            error_log("ZOHO LEAD DELETED: " . $lead['id']);
        }
    }
}
