<?php
/** One native settings record for shared public company information. */
defined( 'ABSPATH' ) || exit;
function showmakers_site_setting( $key ) {
    $values = get_option( 'showmakers_site_settings', array() );
    return isset( $values[$key] ) && is_string( $values[$key] ) ? $values[$key] : '';
}
function showmakers_contact_phone_uri() {
    return preg_replace( '/[^0-9+]/', '', showmakers_site_setting( 'phone' ) );
}
function showmakers_settings_fields() {
    return array(
        'contact_email' => array( 'Contact Email', 'Website enquiries are sent to this address. Also shown on Contact and in the Footer.', 'email', 254 ),
        'phone' => array( 'Phone', 'Approved contact number, including the country code.', 'text', 64 ),
        'address' => array( 'Address', 'Approved company address shown on Contact and in the Footer.', 'textarea', 600 ),
        'footer_copyright' => array( 'Footer Copyright', 'Existing company wording at the bottom of the website.', 'text', 160 ),
        'footer_note' => array( 'Footer Note', 'Short supporting note at the bottom of the website.', 'text', 200 ),
    );
}
function showmakers_sanitize_settings( $input ) {
    $old = get_option( 'showmakers_site_settings', array() );
    if ( ! is_array( $input ) ) return $old;
    $output = array();
    foreach ( showmakers_settings_fields() as $key => $field ) {
        $raw = isset( $input[$key] ) && is_string( $input[$key] ) ? trim( $input[$key] ) : '';
        $valid = mb_strlen( $raw ) <= $field[3];
        if ( $key === 'contact_email' ) $valid = $valid && ! preg_match( '/[\r\n]/', $raw ) && is_email( $raw );
        if ( $key === 'phone' && $raw !== '' ) $valid = $valid && preg_match( '/^\+?[0-9 ()\-.]+$/', $raw ) && strlen( preg_replace( '/\D/', '', $raw ) ) >= 7;
        if ( ! $valid ) {
            add_settings_error( 'showmakers_site_settings', $key, 'Please check ' . $field[0] . '. The previous value was retained.' );
            $output[$key] = $old[$key] ?? '';
        } else $output[$key] = $key === 'contact_email' ? sanitize_email( $raw ) : ( $key === 'address' ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw ) );
    }
    return $output;
}
add_action( 'admin_menu', function () {
    add_menu_page( 'ShowMakers Settings', 'ShowMakers Settings', 'manage_options', 'showmakers-settings', 'showmakers_settings_screen', 'dashicons-admin-generic', 81 );
} );
add_action( 'admin_init', function () {
    register_setting( 'showmakers_settings', 'showmakers_site_settings', array( 'type' => 'array', 'sanitize_callback' => 'showmakers_sanitize_settings', 'show_in_rest' => false ) );
    add_settings_section( 'company', 'Company and Footer', '__return_false', 'showmakers-settings' );
    foreach ( showmakers_settings_fields() as $key => $field ) add_settings_field( $key, $field[0], function () use ( $key, $field ) {
        $name = 'showmakers_site_settings[' . $key . ']';
        $value = showmakers_site_setting( $key );
        if ( $field[2] === 'textarea' ) echo '<textarea class="large-text" rows="3" id="' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" maxlength="' . $field[3] . '">' . esc_textarea( $value ) . '</textarea>';
        else echo '<input class="regular-text" id="' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" type="' . esc_attr( $field[2] ) . '" maxlength="' . $field[3] . '" value="' . esc_attr( $value ) . '"' . ( $key === 'contact_email' ? ' required' : '' ) . '>';
        echo '<p class="description">' . esc_html( $field[1] ) . '</p>';
    }, 'showmakers-settings', 'company', array( 'label_for' => $key ) );
} );
function showmakers_settings_screen() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    echo '<div class="wrap"><h1>ShowMakers Settings</h1>';
    settings_errors();
    echo '<form method="post" action="options.php">';
    settings_fields( 'showmakers_settings' );
    do_settings_sections( 'showmakers-settings' );
    submit_button();
    echo '</form></div>';
}
