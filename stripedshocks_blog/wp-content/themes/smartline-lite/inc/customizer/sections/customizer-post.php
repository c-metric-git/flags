<?php
/**
 * Register Post Settings section, settings and controls for Theme Customizer
 *
 */

// Add Theme Colors section to Customizer
add_action( 'customize_register', 'smartline_customize_register_post_settings' );

function smartline_customize_register_post_settings( $wp_customize ) {

	// Add Sections for Post Settings
	$wp_customize->add_section( 'smartline_section_post', array(
        'title'    => __( 'Post Settings', 'smartline-lite' ),
        'priority' => 30,
		'panel' => 'smartline_options_panel' 
		)
	);

	// Add Settings and Controls for Posts
	$wp_customize->add_setting( 'smartline_theme_options[posts_length]', array(
        'default'           => 'index',
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'smartline_sanitize_post_length'
		)
	);
    $wp_customize->add_control( 'smartline_control_posts_length', array(
        'label'    => __( 'Post Length on archives', 'smartline-lite' ),
        'section'  => 'smartline_section_post',
        'settings' => 'smartline_theme_options[posts_length]',
        'type'     => 'radio',
		'priority' => 1,
        'choices'  => array(
            'index' => __( 'Show full posts', 'smartline-lite' ),
            'excerpt' => __( 'Show post summaries (excerpt)', 'smartline-lite' )
			)
		)
	);
	
	// Add Setting and Control for Excerpt Length
	$wp_customize->add_setting( 'smartline_theme_options[excerpt_length]', array(
        'default'           => 60,
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint'
		)
	);
    $wp_customize->add_control( 'smartline_control_excerpt_length', array(
        'label'    => __( 'Excerpt Length', 'smartline-lite' ),
        'section'  => 'smartline_section_post',
        'settings' => 'smartline_theme_options[excerpt_length]',
        'type'     => 'text',
		'active_callback' => 'smartline_control_posts_length_callback',
		'priority' => 2
		)
	);
	
	// Add Excerpt Text setting
	$wp_customize->add_setting( 'smartline_theme_options[excerpt_text_headline]', array(
        'default'           => '',
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'esc_attr'
        )
    );
    $wp_customize->add_control( new Smartline_Customize_Header_Control(
        $wp_customize, 'smartline_control_excerpt_text_headline', array(
            'label' => __( 'Excerpt More Text', 'smartline-lite' ),
            'section' => 'smartline_section_post',
            'settings' => 'smartline_theme_options[excerpt_text_headline]',
            'priority' => 3
            )
        )
    );
	$wp_customize->add_setting( 'smartline_theme_options[excerpt_text]', array(
        'default'           => false,
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'smartline_sanitize_checkbox'
		)
	);
    $wp_customize->add_control( 'smartline_control_excerpt_text', array(
        'label'    => __( 'Display [...] after text excerpts.', 'smartline-lite' ),
        'section'  => 'smartline_section_post',
        'settings' => 'smartline_theme_options[excerpt_text]',
        'type'     => 'checkbox',
		'priority' => 4
		)
	);
	
	// Add Post Images Headline
	$wp_customize->add_setting( 'smartline_theme_options[post_images]', array(
        'default'           => '',
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'esc_attr'
        )
    );
    $wp_customize->add_control( new Smartline_Customize_Header_Control(
        $wp_customize, 'smartline_control_post_images', array(
            'label' => __( 'Post Images', 'smartline-lite' ),
            'section' => 'smartline_section_post',
            'settings' => 'smartline_theme_options[post_images]',
            'priority' => 5
            )
        )
    );
	$wp_customize->add_setting( 'smartline_theme_options[post_thumbnails_index]', array(
        'default'           => true,
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'smartline_sanitize_checkbox'
		)
	);
    $wp_customize->add_control( 'smartline_control_posts_thumbnails_index', array(
        'label'    => __( 'Display featured images on archive pages', 'smartline-lite' ),
        'section'  => 'smartline_section_post',
        'settings' => 'smartline_theme_options[post_thumbnails_index]',
        'type'     => 'checkbox',
		'priority' => 6
		)
	);

	$wp_customize->add_setting( 'smartline_theme_options[post_thumbnails_single]', array(
        'default'           => true,
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'smartline_sanitize_checkbox'
		)
	);
    $wp_customize->add_control( 'smartline_control_posts_thumbnails_single', array(
        'label'    => __( 'Display featured images on single posts', 'smartline-lite' ),
        'section'  => 'smartline_section_post',
        'settings' => 'smartline_theme_options[post_thumbnails_single]',
        'type'     => 'checkbox',
		'priority' => 7
		)
	);
	
	// Add Postmeta Settings
	$wp_customize->add_setting( 'smartline_theme_options[postmeta_headline]', array(
        'default'           => '',
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'esc_attr'
        )
    );
    $wp_customize->add_control( new Smartline_Customize_Header_Control(
        $wp_customize, 'smartline_control_postmeta_headline', array(
            'label' => __( 'Postmeta', 'smartline-lite' ),
            'section' => 'smartline_section_post',
            'settings' => 'smartline_theme_options[postmeta_headline]',
            'priority' => 8
            )
        )
    );
	$wp_customize->add_setting( 'smartline_theme_options[meta_date]', array(
        'default'           => true,
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'smartline_sanitize_checkbox'
		)
	);
    $wp_customize->add_control( 'smartline_control_meta_date', array(
        'label'    => __( 'Display date on posts.', 'smartline-lite' ),
        'section'  => 'smartline_section_post',
        'settings' => 'smartline_theme_options[meta_date]',
        'type'     => 'checkbox',
		'priority' => 9
		)
	);
	$wp_customize->add_setting( 'smartline_theme_options[meta_author]', array(
        'default'           => true,
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'smartline_sanitize_checkbox'
		)
	);
    $wp_customize->add_control( 'smartline_control_meta_author', array(
        'label'    => __( 'Display author on posts.', 'smartline-lite' ),
        'section'  => 'smartline_section_post',
        'settings' => 'smartline_theme_options[meta_author]',
        'type'     => 'checkbox',
		'priority' => 10
		)
	);
	$wp_customize->add_setting( 'smartline_theme_options[meta_category]', array(
        'default'           => true,
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'smartline_sanitize_checkbox'
		)
	);
    $wp_customize->add_control( 'smartline_control_meta_category', array(
        'label'    => __( 'Display categories on posts.', 'smartline-lite' ),
        'section'  => 'smartline_section_post',
        'settings' => 'smartline_theme_options[meta_category]',
        'type'     => 'checkbox',
		'priority' => 11
		)
	);
	$wp_customize->add_setting( 'smartline_theme_options[meta_tags]', array(
        'default'           => true,
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'smartline_sanitize_checkbox'
		)
	);
    $wp_customize->add_control( 'smartline_control_meta_tags', array(
        'label'    => __( 'Display tags on posts.', 'smartline-lite' ),
        'section'  => 'smartline_section_post',
        'settings' => 'smartline_theme_options[meta_tags]',
        'type'     => 'checkbox',
		'priority' => 12
		)
	);

}

?>