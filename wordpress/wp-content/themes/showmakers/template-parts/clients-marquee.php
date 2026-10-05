<?php
/** Approved marquee presentation; explicitly curated Clients, independent of Work. */
defined( 'ABSPATH' ) || exit;
$clients = function_exists( 'showmakers_marquee_clients' ) ? showmakers_marquee_clients() : array();
?>
<section class="clients is-static" aria-labelledby="clients-title"><div class="shell clients-heading"><h2 id="clients-title">Selected clients</h2><button hidden id="marquee-control" aria-pressed="false"><span aria-hidden="true" class="pause-symbol">Ⅱ</span> <span class="control-label">Pause logos</span></button></div><div class="marquee" aria-label="ShowMakers clients"><div class="marquee-track"><ul class="logo-list" id="client-list"><?php foreach ( $clients as $client ) :
    $logo = showmakers_public_client_logo( $client->ID );
    $image = wp_get_attachment_image_src( $logo, 'full' );
    if ( ! $image ) continue;
    $alt = trim( (string) get_post_meta( $logo, '_wp_attachment_image_alt', true ) ) ?: $client->post_title;
?><li><img src="<?php echo esc_url( $image[0] ); ?>" alt="<?php echo esc_attr( $alt ); ?>" width="120" height="64" loading="lazy"></li><?php endforeach; ?></ul></div></div></section>
