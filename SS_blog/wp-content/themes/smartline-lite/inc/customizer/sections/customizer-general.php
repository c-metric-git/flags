<?php
/**
 * Register General section, settings and controls for Theme Customizer
 *
 */

// Add Theme Colors section to Customizer
add_action( 'customize_register', 'smartline_customize_register_general_settings' );

function smartline_customize_register_general_settings( $wp_customize ) {

	// Add General Section
	$wp_customize->add_section( 'smartline_section_general', array(
        'title'    => __( 'General Settings', 'smartline-lite' ),
        'priority' => 10,
		'panel' => 'smartline_options_panel' 
		)
	);
	
	// Add Settings and Controls for Layout
	$wp_customize->add_setting( 'smartline_theme_options[layout]', array(
        'default'           => 'right-sidebar',
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'smartline_sanitize_layout'
		)
	);
    $wp_customize->add_control( 'smartline_control_layout', array(
        'label'    => __( 'Theme Layout', 'smartline-lite' ),
        'section'  => 'smartline_section_general',
        'settings' => 'smartline_theme_options[layout]',
        'type'     => 'radio',
		'priority' => 1,
        'choices'  => array(
            'left-sidebar' => __( 'Left Sidebar', 'smartline-lite' ),
            'right-sidebar' => __( 'Right Sidebar', 'smartline-lite')
			)
		)
	);
	
	// Add Default Fonts Header
	$wp_customize->add_setting( 'smartline_theme_options[default_fonts]', array(
        'default'           => '',
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'esc_attr'
        )
    );
    $wp_customize->add_control( new Smartline_Customize_Header_Control(
        $wp_customize, 'smartline_control_default_fonts', array(
            'label' => __( 'Default Fonts', 'smartline-lite' ),
            'section' => 'smartline_section_general',
            'settings' => 'smartline_theme_options[default_fonts]',
            'priority' => 2
            )
        )
    );
	
	// Add Settings and Controls for Deactivate Google Font setting
	$wp_customize->add_setting( 'smartline_theme_options[deactivate_google_fonts]', array(
        'default'           => false,
		'type'           	=> 'option',
        'transport'         => 'refresh',
        'sanitize_callback' => 'smartline_sanitize_checkbox'
		)
	);
    $wp_customize->add_control( 'smartline_control_deactivate_google_fonts', array(
        'label'    => __( 'Deactivate Google Fonts in case your language is not compatible.', 'smartline-lite' ),
        'section'  => 'smartline_section_general',
        'settings' => 'smartline_theme_options[deactivate_google_fonts]',
        'type'     => 'checkbox',
		'priority' => 3
		)
	);

}


?>