<?php
/**
 * Functions and definitions
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package euterpe-nourd-font
 * @since 2.0.0
 */

/* Sets up theme defaults */
function blank_setup() {

		// Enqueue editor styles.
		add_editor_style(
			array(
				'./style.css',
			)
		);
 
		// Remove core block patterns.
		remove_theme_support( 'core-block-patterns' );

	}

add_action( 'after_setup_theme', 'blank_setup' );

// Loads styles and scripts
function blank_enqueue_scripts() {
    
	wp_enqueue_style( 'blank-css', get_stylesheet_uri(), [], filemtime(get_stylesheet_directory() . '/style.css') );

      // Encolar Lenis desde CDN
       wp_enqueue_script(
        'lenis', 
        'https://cdn.jsdelivr.net/npm/@studio-freight/lenis@latest/bundled/lenis.min.js',
        array(), 
        null,
        true 
    );
	
    wp_enqueue_script('blank-js', get_stylesheet_directory_uri() . '/assets/js/main.js', array(), '1.0', true);
}
add_action( 'wp_enqueue_scripts', 'blank_enqueue_scripts' );


// Example of a Block Style - Button

if ( function_exists( 'register_block_style' ) ) {
	 register_block_style(
        'core/button',
        [
            'name'  => 'primary',
            'label' => __('Primary', 'mi-tema'),
			 'is_default'   => true,
        ]
    );
    register_block_style(
        'core/button',
        [
            'name'  => 'secondary',
            'label' => __('Secondary', 'mi-tema'),
        ]
    );
    register_block_style(
        'core/button',
        [
            'name'  => 'primary-outline',
            'label' => __('Primary Outline', 'mi-tema'),
        ]
    );
    register_block_style(
        'core/button',
        [
            'name'  => 'secondary-outline',
            'label' => __('Secundary Outline', 'mi-tema'),
        ]
    );
	register_block_style(
        'core/group',
        array(
            'name'         => 'blank-group',
            'label'        => __( 'margin-top-0', 'textdomain' ),
            'is_default'   => false,
            'inline_style' => '
			.is-style-blank-group { margin-block-start: 0 !important; }
			',
        ) 
    );
}

// Example of a Block Style for the Gallery - this sets the aspect ratio for the grid and as original on popup
if ( function_exists( 'register_block_style' ) ) {
    register_block_style(
        'core/gallery',
        array(
            'name'         => 'blank-gallery',
            'label'        => __( 'Square', 'textdomain' ),
            'is_default'   => false,
            'inline_style' => '
			.is-style-blank-gallery > figure, .is-style-blank-gallery img { aspect-ratio: 1 !important; object-fit: cover !important; }
			.lightbox-image-container, .lightbox-image-container * { aspect-ratio: auto !important; object-fit: contain !important; }
			',
        ) 
    );
}

// Example of Register block pattern categories 
function blank_register_block_pattern_categories() {

	
	 // Elimina los estilos "fill" y "outline"
    unregister_block_style('core/button', 'fill');
    unregister_block_style('core/button', 'outline');
	
	register_block_pattern_category(
		'blank_page',
		array(
			'label'       => __( 'Page', 'blank' ),
		)
	);
	register_block_pattern_category(
		'blank_hero',
		array(
			'label'       => __( 'Hero', 'blank' ),
		)
	);
    
}
add_action( 'init', 'blank_register_block_pattern_categories' );

function mostrar_programacion_por_mes($atts) {
    $atts = shortcode_atts(array(
        'tipo' => 'simple', // por defecto simple
    ), $atts, 'programacion_por_mes');

    $hoy = current_time('Y-m-d');

    $args = array(
        'post_type'      => 'actividad',
        'posts_per_page' => -1,
        'meta_key'       => 'fecha',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_type'      => 'NUMERIC',
        'meta_query'     => array(
            array(
                'key'     => 'fecha',
                'value'   => str_replace('-', '', $hoy),
                'compare' => '>=',
                'type'    => 'NUMERIC'
            )
        )
    );

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return '<p>No hay actividades programadas próximamente.</p>';
    }

    $salida = '';
    $mes_actual = '';
    $meses_mostrados = array();
    while ($query->have_posts()) {
        $query->the_post();

        $fecha_raw = trim(get_post_meta(get_the_ID(), 'fecha', true));
        if (empty($fecha_raw)) continue;

        $fecha_formateada = substr($fecha_raw, 0, 4) . '-' . substr($fecha_raw, 4, 2) . '-' . substr($fecha_raw, 6, 2);
        $timestamp = strtotime($fecha_formateada);
        if (!$timestamp) continue;

        $mes_clave   = date('Y-m', $timestamp);
        $mes_titulo  = date_i18n('F Y', $timestamp);
        $fecha_legible = date_i18n('j \d\e F', $timestamp);

        if (!in_array($mes_clave, $meses_mostrados, true)) {
            if ($mes_actual !== '') {
                $salida .= '</ul></div>';
            }
            
            $clase_ul = ($atts['tipo'] === 'completa') ? 'lista-programacion grid-container post-overlay' : 'lista-programacion';
            $salida .= '<div class="mes-wrapper"><h2 class="mes-programacion">' . esc_html(ucfirst($mes_titulo)) . '</h2>';
            $salida .= '<ul class="' . esc_attr($clase_ul) . '">';

            $mes_actual = $mes_clave;
            $meses_mostrados[] = $mes_clave;
        }

        if ($atts['tipo'] === 'completa') {
            $hora = get_post_meta(get_the_ID(), 'hora', true);

            $salida .= '<li class="item-programacion">';
            $salida .= '<figure><a href="' . esc_url(get_permalink()) . '">' . get_the_post_thumbnail(get_the_ID(), 'medium') . '</a></figure>';
            $salida .= '<div class="info">';
            $salida .= '<h3>' . get_the_title() . '</h3>';
            $salida .= '<div class="fecha-hora"><p class="fecha">' . esc_html($fecha_legible) . '</p>';
            if (!empty($hora)) {
                $salida .= '<p class="hora">' . date_i18n('g:i a', strtotime($hora)) . '</p>';
            }
            $salida .= '</div></div>';
            $salida .= '</li>';
        } else {
            $salida .= '<li class="item-programacion"><a href="' . esc_url(get_permalink()) . '">';
            $salida .= '<span class="fecha">' . esc_html($fecha_legible) . '</span>';
            $salida .= '<span class="title-actividad" href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</span>';
            $salida .= '</a></li>';
        }
    }

    $salida .= '</ul>';
    wp_reset_postdata();

    return '<div class="programacion-mensual ' . esc_attr($atts['tipo']) . '">' . $salida . '</div>';
}
add_shortcode('programacion_por_mes', 'mostrar_programacion_por_mes');



// Year Shortcode
function currentYear( $atts ){
    return date('Y');
}
add_shortcode( 'year', 'currentYear' );