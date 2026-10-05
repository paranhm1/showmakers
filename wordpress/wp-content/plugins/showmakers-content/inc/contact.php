<?php
/** Email-only public enquiry processing; no enquiry records or message logs. */
defined( 'ABSPATH' ) || exit;
function showmakers_contact_token() {
    $token = time() . '.' . wp_generate_uuid4();
    return array( 'contact_token' => $token, 'contact_signature' => hash_hmac( 'sha256', $token, wp_salt( 'nonce' ) ) );
}
function showmakers_contact_result( $status, $message, $errors = array() ) {
    return array( 'status' => $status, 'success' => $status === 200, 'message' => $message, 'errors' => $errors );
}
function showmakers_contact_process( $data, $server ) {
    $fail = 'Your inquiry could not be sent. Please try again or contact us by email.';
    if ( ! is_array( $data ) ) return showmakers_contact_result( 400, $fail );
    foreach ( $data as $value ) if ( ! is_string( $value ) ) return showmakers_contact_result( 400, 'Please check the form values.' );
    if ( ! wp_verify_nonce( $data['contact_nonce'] ?? '', 'showmakers_contact' ) ) return showmakers_contact_result( 403, 'Please reload this page before sending your inquiry.' );
    // Reject cross-origin browser requests; anonymous WP nonces are not authentication.
    $origin = $server['HTTP_ORIGIN'] ?? '';
    $expected = wp_parse_url( home_url() );
    if ( $origin !== '' ) {
        $actual = wp_parse_url( $origin );
        if ( ! is_array( $actual ) || ( $actual['host'] ?? '' ) !== $expected['host'] || ( $actual['scheme'] ?? '' ) !== $expected['scheme'] || ( $actual['port'] ?? null ) !== ( $expected['port'] ?? null ) ) return showmakers_contact_result( 403, $fail );
    }
    $token = $data['contact_token'] ?? '';
    if ( ! preg_match( '/^([0-9]{10})\.[a-f0-9-]{36}$/', $token, $match ) || ! hash_equals( hash_hmac( 'sha256', $token, wp_salt( 'nonce' ) ), $data['contact_signature'] ?? '' ) ) return showmakers_contact_result( 403, 'Please reload this page before sending your inquiry.' );
    $age = time() - (int) $match[1];
    if ( $age < 2 ) return showmakers_contact_result( 429, 'Please wait a moment, then send your inquiry.' );
    if ( $age > 7200 ) return showmakers_contact_result( 403, 'Please reload this page before sending your inquiry.' );
    if ( trim( $data['website_honeypot'] ?? '' ) !== '' ) return showmakers_contact_result( 400, $fail );
    $identity = hash_hmac( 'sha256', (string) ( $server['REMOTE_ADDR'] ?? 'unknown' ), wp_salt( 'auth' ) );
    $rate_key = 'sm_contact_rate_' . $identity;
    $rate = get_transient( $rate_key );
    if ( ! is_array( $rate ) || $rate['until'] < time() ) $rate = array( 'count' => 0, 'until' => time() + 900 );
    if ( $rate['count'] >= 5 ) return showmakers_contact_result( 429, 'Too many attempts. Please try later or contact us by email.' );
    ++$rate['count'];
    set_transient( $rate_key, $rate, max( 1, $rate['until'] - time() ) );
    $errors = array();
    $clean = array();
    foreach ( array( 'name' => 254, 'company' => 254, 'goal' => 6000, 'additionalDetails' => 6000 ) as $field => $max ) {
        $raw = $data[$field] ?? '';
        $clean[$field] = trim( $field === 'goal' || $field === 'additionalDetails' ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw ) );
        if ( mb_strlen( $raw ) > $max ) $errors[$field] = 'Please shorten this field.';
        if ( in_array( $field, array( 'name', 'goal' ), true ) && $clean[$field] === '' ) $errors[$field] = $field === 'name' ? 'Please enter your name.' : 'Please tell us what you would like to achieve.';
    }
    $raw_email = trim( $data['email'] ?? '' );
    if ( strlen( $raw_email ) > 254 || preg_match( '/[\r\n]/', $raw_email ) || ! is_email( $raw_email ) ) $errors['email'] = 'Enter a valid email address.';
    $clean['email'] = sanitize_email( $raw_email );
    $slug = $data['service'] ?? '';
    $term = get_term_by( 'slug', $slug, 'service' );
    if ( $slug !== 'not-sure' && ( ! $term || ! get_term_meta( $term->term_id, 'visible', true ) || $term->slug !== $slug ) ) $errors['service'] = 'Please select a service or choose “Not sure yet”.';
    $clean['service'] = $slug === 'not-sure' ? 'Not sure yet' : ( $term ? sanitize_text_field( html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ) ) : '' );
    $project = null;
    $project_slug = $data['projectReference'] ?? '';
    if ( $project_slug !== '' ) {
        $project = get_page_by_path( $project_slug, OBJECT, 'project' );
        if ( ! $project || $project->post_name !== $project_slug || ! showmakers_public_project( $project->ID ) ) return showmakers_contact_result( 422, 'The project reference is no longer available. Remove it or reload the page.', $errors );
    }
    $allowed_sources = array( '/contact/', '/services/' );
    if ( $project ) $allowed_sources[] = wp_parse_url( get_permalink( $project ), PHP_URL_PATH );
    $source = $data['sourcePage'] ?? '/contact/';
    if ( ! in_array( $source, $allowed_sources, true ) ) return showmakers_contact_result( 422, 'Please reload the page to check the inquiry context.', $errors );
    if ( $errors ) return showmakers_contact_result( 422, 'Please check the highlighted fields.', $errors );
    $recipient = showmakers_site_setting( 'contact_email' );
    if ( ! is_email( $recipient ) || preg_match( '/[\r\n]/', $recipient ) ) return showmakers_contact_result( 503, $fail );
    $request_key = 'sm_contact_sent_' . hash_hmac( 'sha256', $token, wp_salt( 'auth' ) );
    if ( get_transient( $request_key ) ) return showmakers_contact_result( 409, 'This inquiry has already been sent. Reload the page to start another.' );
    // Brief atomic lock prevents concurrent duplicate sends; stores only expiry time.
    $lock_key = 'sm_contact_lock_' . hash_hmac( 'sha256', $token, wp_salt( 'auth' ) );
    $lock = (int) get_option( $lock_key );
    if ( $lock && $lock < time() ) delete_option( $lock_key );
    if ( ! add_option( $lock_key, time() + 60, '', false ) ) return showmakers_contact_result( 409, 'This inquiry is being processed. Please wait.' );
    try {
        // Recheck after acquiring the lock: another request may have completed first.
        wp_cache_delete( '_transient_' . $request_key, 'options' );
        wp_cache_delete( 'notoptions', 'options' );
        if ( get_transient( $request_key ) ) return showmakers_contact_result( 409, 'This inquiry has already been sent. Reload the page to start another.' );
        $subject = 'ShowMakers Website Enquiry — ' . $clean['name'] . ( $clean['company'] ? ' / ' . $clean['company'] : '' );
        $body = "Name: {$clean['name']}\nEmail: {$clean['email']}\nCompany: {$clean['company']}\nService: {$clean['service']}\n";
        if ( $project ) $body .= 'Project reference: ' . sanitize_text_field( $project->post_title ) . "\n";
        $body .= "Source page: {$source}\n\nI’m hoping to:\n{$clean['goal']}\n\nAdditional details:\n{$clean['additionalDetails']}\n";
        $sent = wp_mail( $recipient, $subject, $body, array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $clean['email'] ) );
        if ( ! $sent ) return showmakers_contact_result( 503, $fail );
        set_transient( $request_key, 1, 7200 );
        return array_merge( showmakers_contact_result( 200, 'Thank you. Your inquiry has been sent.' ), showmakers_contact_token() );
    } finally { delete_option( $lock_key ); }
}
function showmakers_contact_action() {
    if ( ( $_SERVER['REQUEST_METHOD'] ?? '' ) !== 'POST' ) wp_send_json( showmakers_contact_result( 405, 'Please submit the contact form.' ), 405 );
    if ( (int) ( $_SERVER['CONTENT_LENGTH'] ?? 0 ) > 32768 ) wp_send_json( showmakers_contact_result( 413, 'Please shorten your inquiry.' ), 413 );
    $result = showmakers_contact_process( wp_unslash( $_POST ), $_SERVER );
    wp_send_json( $result, $result['status'] );
}
add_action( 'wp_ajax_showmakers_contact', 'showmakers_contact_action' );
add_action( 'wp_ajax_nopriv_showmakers_contact', 'showmakers_contact_action' );
