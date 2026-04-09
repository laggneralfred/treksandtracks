<?php
/**
 * Set up the default theme options
 *
 * @param	string $name  The option name
 *
 * @since 3.0.0
 */
function bavotasan_default_theme_options() {
	//delete_option( 'theme_mods_magazine_basic' );
	return array(
		'width' => '',
		'layout' => 'right',
		'primary' => 'c6',
		'secondary' => 'c3',
		'tagline' => 'on',
		'link_color' => '#3D97C2',
		'excerpt_content' => 'excerpt',
		'page_background' => '#ffffff',
		'grid' => '3',
		'number' => '6',
		'index_categories' => 'on',
		'display_categories' => 'on',
		'index_date' => 'on',
		'display_date' => 'on',
		'index_author' => 'on',
		'display_author' => 'on',
		'index_comment_count' => 'on',
		'display_comment_count' => 'on',
		'logo' => '',
		'1_image_width' => '560',
		'2_image_width' => '260',
		'3_image_width' => '160',
		'header_alignment' => 'fl',
	);
}

function bavotasan_theme_options() {
	$bavotasan_default_theme_options = bavotasan_default_theme_options();

	$return = array();
	foreach( $bavotasan_default_theme_options as $option => $value ) {
		$return[$option] = get_theme_mod( $option, $value );
	}

	return $return;
}

if ( class_exists( 'WP_Customize_Control' ) ) {
	class Bavotasan_Post_Layout_Control extends WP_Customize_Control {
	    public function render_content() {
			if ( empty( $this->choices ) )
				return;

			$name = '_customize-radio-' . $this->id;

			?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php
			foreach ( $this->choices as $value => $label ) :
				?>
				<label>
					<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
					<?php
					$value = ( is_active_sidebar( 'second-sidebar' ) ) ? $value . ' second-sidebar' : $value;
					echo '<div class="' . esc_attr( $value ) . '"></div>'; ?>
				</label>
				<?php
			endforeach;
			echo '<p class="description">' . sprintf( __( 'For layout options with two sidebars, add at least one widget to the Second Sidebar area on the %sWidgets admin page%s.', 'magazine-basic' ), '<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">', '</a>' ) . '</p>';
	    }
	}
}

class Bavotasan_Customizer {
	public function __construct() {
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_enqueue_scripts' ) );
	}

	/**
	 * Adds theme options to the Customizer screen
	 *
	 * This function is attached to the 'customize_register' action hook.
	 *
	 * @param	class $wp_customize
	 *
	 * @since 1.0.0
	 */
	public function customize_register( $wp_customize ) {
		$bavotasan_default_theme_options = bavotasan_default_theme_options();

		$wp_customize->add_setting( 'tagline', array(
			'default' => $bavotasan_default_theme_options['tagline'],
			'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
		) );

		$wp_customize->add_control( 'tagline', array(
			'label' => __( 'Display Tagline', 'magazine-basic' ),
			'section' => 'title_tagline',
			'type' => 'checkbox',
		) );

		$wp_customize->add_setting( 'logo', array(
			'default' => $bavotasan_default_theme_options['logo'],
			'sanitize_callback' => 'esc_url',
		) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'logo', array(
			'label'   => __( 'Site Logo', 'magazine-basic' ),
			'section' => 'title_tagline',
		) ) );

		$wp_customize->add_setting( 'header_alignment', array(
			'default' => $bavotasan_default_theme_options['header_alignment'],
			'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( 'header_alignment', array(
			'label' => __( 'Header Alignment', 'magazine-basic' ),
			'section' => 'title_tagline',
			'type' => 'select',
			'choices' => array(
				'fl' => __( 'Left', 'magazine-basic' ),
				'fr' => __( 'Right', 'magazine-basic' ),
				'center' => __( 'Center', 'magazine-basic' ),
			),
		) );

		$wp_customize->add_section( 'bavotasan_layout', array(
			'title' => __( 'Layout', 'magazine-basic' ),
			'priority' => 35,
		) );

		$wp_customize->add_setting( 'width', array(
			'default' => $bavotasan_default_theme_options['width'],
			'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( 'width', array(
			'label' => __( 'Site Width', 'magazine-basic' ),
			'section' => 'bavotasan_layout',
			'type' => 'select',
			'priority' => 10,
			'choices' => array(
				'' => '1200px',
				'w960' => '960px',
			),
		) );

		$wp_customize->add_setting( 'layout', array(
			'default' => $bavotasan_default_theme_options['layout'],
			'sanitize_callback' => 'esc_attr',
		) );

		$layout_choices = array(
			'left' => __( 'Left', 'magazine-basic' ),
			'right' => __( 'Right', 'magazine-basic' ),
		);

		if ( is_active_sidebar( 'second-sidebar' ) )
			$layout_choices['separate'] = __( 'Separate', 'magazine-basic' );

		$wp_customize->add_control( new Bavotasan_Post_Layout_Control( $wp_customize, 'layout', array(
			'label' => __( 'Sidebar Layout', 'magazine-basic' ),
			'section' => 'bavotasan_layout',
			'size' => false,
			'priority' => 15,
			'choices' => $layout_choices,
		) ) );

		$choices = array(
			'c2' => '17%',
			'c3' => '25%',
			'c4' => '34%',
			'c5' => '42%',
			'c6' => '50%',
			'c7' => '58%',
			'c8' => '66%',
			'c9' => '75%',
			'c10' => '83%',
		);

		$wp_customize->add_setting( 'primary', array(
			'default' => $bavotasan_default_theme_options['primary'],
			'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( 'primary', array(
			'label' => __( 'Main Content Width', 'magazine-basic' ),
			'section' => 'bavotasan_layout',
			'priority' => 20,
			'type' => 'select',
			'choices' => $choices,
		) );

		if ( is_active_sidebar( 'second-sidebar' ) ) {
			$wp_customize->add_setting( 'secondary', array(
				'default' => $bavotasan_default_theme_options['secondary'],
				'sanitize_callback' => 'esc_attr',
			) );

			$wp_customize->add_control( 'secondary', array(
				'label' => __( 'First Sidebar Width', 'magazine-basic' ),
				'section' => 'bavotasan_layout',
				'priority' => 25,
				'type' => 'select',
				'choices' => $choices,
			) );
		}

		$wp_customize->add_setting( 'display_categories', array(
			'default' => $bavotasan_default_theme_options['display_categories'],
			'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
		) );

		$wp_customize->add_control( 'display_categories', array(
			'label' => __( 'Display Categories on inner pages', 'magazine-basic' ),
			'section' => 'bavotasan_layout',
			'priority' => 30,
			'type' => 'checkbox',
		) );

		$wp_customize->add_setting( 'display_author', array(
			'default' => $bavotasan_default_theme_options['display_author'],
			'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
		) );

		$wp_customize->add_control( 'display_author', array(
			'label' => __( 'Display Author on inner pages', 'magazine-basic' ),
			'section' => 'bavotasan_layout',
			'priority' => 35,
			'type' => 'checkbox',
		) );

		$wp_customize->add_setting( 'display_date', array(
			'default' => $bavotasan_default_theme_options['display_date'],
			'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
		) );

		$wp_customize->add_control( 'display_date', array(
			'label' => __( 'Display Date on inner pages', 'magazine-basic' ),
			'section' => 'bavotasan_layout',
			'priority' => 40,
			'type' => 'checkbox',
		) );

		$wp_customize->add_setting( 'display_comment_count', array(
			'default' => $bavotasan_default_theme_options['display_comment_count'],
			'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
		) );

		$wp_customize->add_control( 'display_comment_count', array(
			'label' => __( 'Display Comment Count on inner pages', 'magazine-basic' ),
			'section' => 'bavotasan_layout',
			'priority' => 45,
			'type' => 'checkbox',
		) );

		$wp_customize->add_section( 'bavotasan_front_page', array(
			'title' => __( 'Front Page', 'magazine-basic' ),
			'priority' => 40,
		) );

		$wp_customize->add_setting( 'excerpt_content', array(
			'default' => $bavotasan_default_theme_options['excerpt_content'],
			'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( 'excerpt_content', array(
			'label' => __( 'Post Content Display', 'magazine-basic' ),
			'section' => 'bavotasan_front_page',
			'type' => 'radio',
			'choices' => array(
				'excerpt' => __( 'Teaser Excerpt', 'magazine-basic' ),
				'content' => __( 'Full Content', 'magazine-basic' ),
			),
			'priority' => 25,
		) );

		$wp_customize->add_setting( 'grid', array(
			'default' => $bavotasan_default_theme_options['grid'],
			'sanitize_callback' => array( $this, 'sanitize_int' ),
		) );

		$wp_customize->add_control( 'grid', array(
			'label' => __( 'Grid Layout', 'magazine-basic' ),
			'section' => 'bavotasan_front_page',
			'type' => 'radio',
			'choices' => array(
				'1' => __( 'Single', 'magazine-basic' ),
				'2' => __( 'Single - Two Columns', 'magazine-basic' ),
				'3' => __( 'Single - Two Columns - Three Columns', 'magazine-basic' ),
				'4' => __( 'Single - Three Columns', 'magazine-basic' ),
			),
			'priority' => 30,
		) );

		$wp_customize->add_setting( 'number', array(
			'default' => $bavotasan_default_theme_options['number'],
			'sanitize_callback' => array( $this, 'sanitize_int' ),
		) );

		$wp_customize->add_control( 'number', array(
			'label' => __( 'Number of Posts', 'magazine-basic' ),
			'section' => 'bavotasan_front_page',
			'priority' => 35,
		) );

		$wp_customize->add_setting( 'index_categories', array(
			'default' => $bavotasan_default_theme_options['index_categories'],
			'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
		) );

		$wp_customize->add_control( 'index_categories', array(
			'label' => __( 'Display Categories', 'magazine-basic' ),
			'section' => 'bavotasan_front_page',
			'type' => 'checkbox',
			'priority' => 40,
		) );

		$wp_customize->add_setting( 'index_author', array(
			'default' => $bavotasan_default_theme_options['index_author'],
			'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
		) );

		$wp_customize->add_control( 'index_author', array(
			'label' => __( 'Display Author', 'magazine-basic' ),
			'section' => 'bavotasan_front_page',
			'type' => 'checkbox',
			'priority' => 45,
		) );

		$wp_customize->add_setting( 'index_date', array(
			'default' => $bavotasan_default_theme_options['index_date'],
			'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
		) );

		$wp_customize->add_control( 'index_date', array(
			'label' => __( 'Display Date', 'magazine-basic' ),
			'section' => 'bavotasan_front_page',
			'type' => 'checkbox',
			'priority' => 50,
		) );

		$wp_customize->add_setting( 'index_comment_count', array(
			'default' => $bavotasan_default_theme_options['index_comment_count'],
			'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
		) );

		$wp_customize->add_control( 'index_comment_count', array(
			'label' => __( 'Display Comment Count', 'magazine-basic' ),
			'section' => 'bavotasan_front_page',
			'type' => 'checkbox',
			'priority' => 55,
		) );

		// Image sizes
		$wp_customize->add_setting( '1_image_width', array(
			'default' => $bavotasan_default_theme_options['1_image_width'],
			'sanitize_callback' => array( $this, 'sanitize_int' ),
		) );

		$wp_customize->add_control( '1_image_width', array(
			'label' => __( '1 Column Image Width (pixels)', 'magazine-basic' ),
			'section' => 'bavotasan_front_page',
			'priority' => 60,
		) );

		$wp_customize->add_setting( '2_image_width', array(
			'default' => $bavotasan_default_theme_options['2_image_width'],
			'sanitize_callback' => array( $this, 'sanitize_int' ),
		) );

		$wp_customize->add_control( '2_image_width', array(
			'label' => __( '2 Column Image Width (pixels)', 'magazine-basic' ),
			'section' => 'bavotasan_front_page',
			'priority' => 70,
		) );

		$wp_customize->add_setting( '3_image_width', array(
			'default' => $bavotasan_default_theme_options['3_image_width'],
			'sanitize_callback' => array( $this, 'sanitize_int' ),
		) );

		$wp_customize->add_control( '3_image_width', array(
			'label' => __( '3 Column Image Width (pixels)', 'magazine-basic' ),
			'section' => 'bavotasan_front_page',
			'priority' => 80,
		) );

		// Colors
		$wp_customize->add_setting( 'page_background', array(
			'default' => $bavotasan_default_theme_options['page_background'],
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'page_background', array(
			'label' => __( 'Page Background', 'magazine-basic' ),
			'section' => 'colors',
		) ) );

		$wp_customize->add_setting( 'link_color', array(
			'default' => $bavotasan_default_theme_options['link_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_color', array(
			'label' => __( 'Link Color', 'magazine-basic' ),
			'section' => 'colors',
		) ) );
	}

	/**
	 * Sanitize integers
	 *
	 * @since 3.0.0
	 */
	public function sanitize_int( $int ) {
		if ( '' === $int )
			return '';

		return (int) $int;
	}

	/**
	 * Sanitize checkbox options
	 *
	 * @since 3.0.5
	 */
    public function sanitize_checkbox( $value ) {
        if ( 'on' != $value )
            $value = false;

        return $value;
    }

	public function customize_controls_enqueue_scripts() {
		wp_enqueue_script( 'bavotasan-customizer', BAVOTASAN_THEME_URL . '/library//js/admin/customizer.js', array( 'jquery' ), '', true );
        wp_localize_script( 'bavotasan-customizer', 'Bavotasan_Customizer', array(
            'upgradeAd' => __( 'Upgrade to premium version', 'magazine-basic' ),
        ));

		wp_enqueue_style( 'bavotasan-customizer-styles', BAVOTASAN_THEME_URL . '/library/css/admin/customizer.css' );
	}
}
$bavotasan_customizer = new Bavotasan_Customizer;
