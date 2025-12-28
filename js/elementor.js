jQuery(document).ready(function($) {
    $(document).ready(function() {
        // Get the default post ID for the active Elementor kit
        var default_post_id = '<?php echo get_option('elementor_active_kit'); ?>';

        if (default_post_id) {
            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    action: 'get_elementor_global_typography',
                    post_id: default_post_id,
                    nonce: '<?php echo wp_create_nonce( 'get_elementor_global_typography' ); ?>' 
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var style = '';
                        $.each(response.data, function(key, typography) {
                            var css_variable_name = '--ane-typography-' + key.replace('_', '-'); 
                            style += 
                                css_variable_name + '--family: ' + typography.font_family + ';' + 
                                css_variable_name + '--size: ' + typography.font_size + 'px;' + 
                                css_variable_name + '--weight: ' + typography.font_weight + ';' + 
                                css_variable_name + '--line-height: ' + typography.line_height + ';' + 
                                css_variable_name + '--letter-spacing: ' + typography.letter_spacing + 'px;' + 
                                css_variable_name + '--text-transform: ' + typography.text_transform + ';' + 
                                css_variable_name + '--text-decoration: ' + typography.text_decoration + ';' + 
                                css_variable_name + '--color: ' + typography.color + ';';
                        });
                        $('head').append('<style>:root {' + style + '}</style>');
                    }
                }
            });
        }
    });
});