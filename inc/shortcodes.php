<?php
// CCPA Opt-Out Link Shortcode
// Renders a button styled as a link that triggers cookie opt-out when clicked
// Safe for use inside the_content: returns a string (no echo)
add_shortcode('enspyred_cookie_optout', function ($atts = []) {
    $atts = shortcode_atts([
        'text'  => 'Do Not Sell or Share My Personal Information', // link text
        'class' => '',  // optional extra class names (space-delimited)
    ], $atts, 'enspyred_cookie_optout');

    // Sanitize multiple class names while preserving spaces
    $extra_classes = trim(preg_replace('/\s+/', ' ', $atts['class']));
    $class_tokens  = array_filter(explode(' ', $extra_classes));
    $safe_tokens   = array_map('sanitize_html_class', $class_tokens);
    $classes = trim('enspyred-cookie-optout-link ' . implode(' ', $safe_tokens));

    // Return button styled as link (better for accessibility)
    // Inline styles make it look like a link in any theme context
    $html = '<button type="button" class="' . esc_attr($classes) . '" '
          . 'style="background:none;border:none;padding:0;color:inherit;text-decoration:underline;cursor:pointer;font:inherit;">'
          . esc_html($atts['text'])
          . '</button>';

    return $html;
});
