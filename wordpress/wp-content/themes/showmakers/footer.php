<?php
/** Approved Footer composition with canonical shared company settings. */
defined( 'ABSPATH' ) || exit;
$email = showmakers_site_setting( 'contact_email' );
$phone = showmakers_site_setting( 'phone' );
$address = showmakers_site_setting( 'address' );
$contact_lines = array();
if ( $address !== '' ) $contact_lines[] = esc_html( $address );
if ( $email !== '' ) $contact_lines[] = '<a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a>';
if ( $phone !== '' ) $contact_lines[] = '<a href="' . esc_url( 'tel:' . showmakers_contact_phone_uri() ) . '">' . esc_html( $phone ) . '</a>';
?>
</main>
<footer class="site-footer"><div class="shell footer-inner"><a class="brand brand-light" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="ShowMakers home"><img src="<?php echo esc_url( showmakers_asset_url( 'images/showmakers-logo-light.png' ) ); ?>" width="681" height="609" alt="ShowMakers"></a><?php if ( $contact_lines ) : ?><address><?php echo implode( '<br>', $contact_lines ); ?></address><?php endif; ?><nav class="footer-links" aria-label="Footer navigation"><a href="<?php echo esc_url( home_url( '/work/' ) ); ?>" data-event="nav_work" >Work</a><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>" data-event="nav_services" >Services</a><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" data-event="nav_about" >About</a><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" data-event="nav_contact" >Contact</a></nav></div><div class="shell footer-bottom"><span><?php echo esc_html( showmakers_site_setting( 'footer_copyright' ) ); ?></span><span><?php echo esc_html( showmakers_site_setting( 'footer_note' ) ); ?></span><a class="privacy-notice" href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>">Privacy Notice</a></div></footer>
<?php wp_footer(); ?>
</body>
</html>
