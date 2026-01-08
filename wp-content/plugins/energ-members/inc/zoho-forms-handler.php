<?php
defined('ABSPATH') || exit;

add_action('init', function () {

    if ($_SERVER['REQUEST_URI'] !== '/zoho-form-lead') return;

    $raw = file_get_contents("php://input");
    error_log("\n--- ZOHO FORM LEAD RAW ---\n$raw\n------------------------");

    $d = json_decode($raw, true);
    if (!$d) {
        error_log("[ZohoForm] INVALID JSON");
        wp_send_json_error("Invalid JSON");
        exit;
    }

    /**
     * COMMUNITY / SUB-COMMUNITY SPLIT
     */
    $rawChoices = isset($d['Field_15']) ? $d['Field_15'] : [];
    $communities = [];
    $subCommunities = [];

    foreach ($rawChoices as $v) {
        if (strpos($v, '-') !== false) {
            list($parent, $child) = array_map('trim', explode('-', $v));
            if ($parent) $communities[] = $parent;
            if ($child)  $subCommunities[] = $child;
        } else {
            $communities[] = trim($v);
        }
    }

    $communityStr    = implode(", ", $communities);
    $subCommunityStr = implode(", ", $subCommunities);

    /**
     * FINAL PAYLOAD
     */
    $payload = [
        'First_Name' => $d['Field_2'] ?? '',
        'Last_Name'  => $d['Field_3'] ?? '',
        'Email'      => $d['Field_6'] ?? '',

        'Mobile'     => preg_replace('/\D+/', '', $d['Field_4'] ?? ''),
        'Country'    => $d['Field_8'] ?? '',
        'Company'    => $d['Field_5'] ?? '',
        'Designation'=> $d['Field_7'] ?? '',

        'Industry'               => $d['Field_12'] ?? '',
        'Industry_Sub_Category'  => $d['Field_13'] ?? '',

        'Community'      => $communityStr,
        'Sub_Community'  => $subCommunityStr,

        'Lead_Source' => $d['Field_11'] ?? 'Website Visit (Zoho Form)',
        'Show'        => 'Energdive',
        'Tag'         => ['WebLead']
    ];

    error_log("[ZohoForm] PAYLOAD: " . print_r($payload, true));


    /**
     * ALWAYS SEND WELCOME EMAIL (OPTION A)
     */
    if (!empty($payload['Email'])) {

        $name  = $payload['First_Name'];
        $email = $payload['Email'];

        $subject = "Welcome to ENERGDIVE – Your Member Access";

        $message = "
        <div style='font-family: Arial; padding:20px;'>
            <h2 style='color:#005bbb;'>Hello {$name},</h2>
            <p>Thank you for your submission.</p>
            <p>You can now access your Member Dashboard using the link below:</p>

            <a href='https://energ.energdive.com/login'
               style='display:inline-block; padding:12px 22px; background:#005bbb; color:white;
                      text-decoration:none; border-radius:6px;'>
                Login to Dashboard
            </a>

            <p style='margin-top:20px;'>Regards,<br>Team ENERGDIVE</p>
        </div>";

        wp_mail($email, $subject, $message, ['Content-Type: text/html; charset=UTF-8']);
        error_log("[ZohoForm] HTML Welcome email sent to: {$email}");
    }


    /**
     * SMART AUTO-MERGE LOGIC (EMAIL + MOBILE)
     */
    $email  = $payload['Email'];
    $mobile = $payload['Mobile'];

    $lead_by_email  = energ_zoho_find_record("Leads", $email, '');
    $lead_by_mobile = energ_zoho_find_record("Leads", '', $mobile);

    // Same lead for both
    if ($lead_by_email && $lead_by_mobile && $lead_by_email['id'] === $lead_by_mobile['id']) {
        energ_zoho_api_request("PUT", "/Leads/{$lead_by_email['id']}", $payload);
        wp_send_json_success("Lead updated (single match)");
        exit;
    }

    // Both exist but different → merge
    if ($lead_by_email && $lead_by_mobile && $lead_by_email['id'] !== $lead_by_mobile['id']) {

        // Email wins, mobile duplicate gets deleted
        energ_zoho_api_request("PUT", "/Leads/{$lead_by_email['id']}", $payload);
        energ_zoho_delete_lead($lead_by_mobile['id']);

        wp_send_json_success("Merged duplicate leads");
        exit;
    }

    // Email-only match
    if ($lead_by_email) {
        energ_zoho_api_request("PUT", "/Leads/{$lead_by_email['id']}", $payload);
        wp_send_json_success("Lead updated (email)");
        exit;
    }

    // Mobile-only match
    if ($lead_by_mobile) {
        energ_zoho_api_request("PUT", "/Leads/{$lead_by_mobile['id']}", $payload);
        wp_send_json_success("Lead updated (mobile)");
        exit;
    }

    // Create NEW Lead
    energ_zoho_api_request("POST", "/Leads", $payload);
    wp_send_json_success("Lead created");
    exit;
});
