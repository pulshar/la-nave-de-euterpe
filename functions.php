<?php
/**
 * Functions and definitions
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package la-nave-de-euterpe
 * @since 2.0.0
 */

/* --------------------------------------------------------------
 *  LOGIN PAGE CUSTOMIZATION
 * -------------------------------------------------------------- */
function euterpe_login_custom_css() {
	wp_enqueue_style(
		'euterpe-login',
		get_stylesheet_directory_uri() . '/assets/css/login.css',
		array(),
		filemtime( get_stylesheet_directory() . '/assets/css/login.css' )
	);
}
add_action( 'login_enqueue_scripts', 'euterpe_login_custom_css' );

/* --------------------------------------------------------------
 *  THEME SETUP
 * -------------------------------------------------------------- */
function euterpe_setup() {

	// Estilos del editor (usa el CSS principal)
	add_editor_style( array( 'style.css' ) );

	// Quitar patrones por defecto de WordPress
	remove_theme_support( 'core-block-patterns' );

}
add_action( 'after_setup_theme', 'euterpe_setup' );

/* --------------------------------------------------------------
 *  ENQUEUE SCRIPTS & STYLES
 * -------------------------------------------------------------- */
function euterpe_enqueue_scripts() {

	// Swiper
	wp_enqueue_style( 'swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css' );
	wp_enqueue_script( 'swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), null, true );

	// Lenis
	wp_enqueue_script(
		'lenis',
		'https://cdn.jsdelivr.net/npm/@studio-freight/lenis@latest/bundled/lenis.min.js',
		array(),
		null,
		true
	);

	// Estilos principales del tema
	wp_enqueue_style(
		'euterpe-style',
		get_stylesheet_uri(),
		array(),
		filemtime( get_stylesheet_directory() . '/style.css' )
	);

	// Script principal dependiente de Swiper y Lenis
	wp_enqueue_script(
		'euterpe-main',
		get_stylesheet_directory_uri() . '/assets/js/main.js',
		array( 'swiper-js', 'lenis' ),
		filemtime( get_stylesheet_directory() . '/assets/js/main.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'euterpe_enqueue_scripts' );

/* --------------------------------------------------------------
 *  BLOCK STYLES (Buttons, Groups, Gallery)
 * -------------------------------------------------------------- */
if ( function_exists( 'register_block_style' ) ) {
	
register_block_style(
  'core/heading',
  [
    'name'  => 'display',
    'label' => 'Display',
  ]
);

	// Buttons
	register_block_style(
		'core/button',
		array(
			'name'        => 'primary',
			'label'       => __( 'Primary', 'euterpe' ),
			'is_default'  => true,
		)
	);

	register_block_style(
		'core/button',
		array(
			'name'  => 'secondary',
			'label' => __( 'Secondary', 'euterpe' ),
		)
	);

	register_block_style(
		'core/button',
		array(
			'name'  => 'primary-outline',
			'label' => __( 'Primary Outline', 'euterpe' ),
		)
	);

	register_block_style(
		'core/button',
		array(
			'name'  => 'secondary-outline',
			'label' => __( 'Secondary Outline', 'euterpe' ),
		)
	);

	// Group
	register_block_style(
		'core/group',
		array(
			'name'         => 'blank-group',
			'label'        => __( 'margin-top-0', 'euterpe' ),
			'inline_style' => '.is-style-blank-group { margin-block-start: 0 !important; }',
		)
	);
}

/* --------------------------------------------------------------
 *  BLOCK PATTERN CATEGORIES
 * -------------------------------------------------------------- */
function euterpe_register_block_pattern_categories() {

	 register_block_pattern_category('euterpe', [
        'label' => __('Euterpe', 'euterpe'),
    ]);

    register_block_pattern_category('euterpe_hero', [
        'label' => __('Hero', 'euterpe'),
    ]);

     register_block_pattern_category('euterpe_slider', [
        'label' => __('Slider', 'euterpe'),
    ]);
}
add_action( 'init', 'euterpe_register_block_pattern_categories' );

/* --------------------------------------------------------------
 *  Imagen destacada por defecto para el CPT 'actividad'
 * -------------------------------------------------------------- */
function actividad_default_thumbnail( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

    if ( get_post_type( $post_id ) !== 'actividad' ) {
        return $html;
    }
    if ( ! empty( $html ) ) {
        return $html;
    }
    if ( is_singular( 'actividad' ) ) {
        return $html; // deja vacío
    }

    $default_url = get_stylesheet_directory_uri() . '/assets/images/default-actividad.webp';
    $html = '<img src="' . esc_url( $default_url ) . '" class="default-image" alt="Imagen por defecto" />';

    return $html;
}
add_filter( 'post_thumbnail_html', 'actividad_default_thumbnail', 10, 5 );

/* --------------------------------------------------------------
 *  SHORTCODE: PROGRAMACIÓN FUTURA/PASADA
 * -------------------------------------------------------------- */
function euterpe_programacion_completa( $atts ) {
	$atts = shortcode_atts(
		array(
			'limite' => 6,        
			'modo'   => 'futuro',
			'orden'  => 'ASC',
			'paginacion' => 'true',
		),
		$atts,
		'programacion_completa'
	);

	$hoy = current_time( 'Y-m-d' );
	$hoy_num = str_replace( '-', '', $hoy );

	// Comparador según modo
	if ( $atts['modo'] === 'pasado' ) {
		$compare = '<=';
		$order   = 'DESC';
	} else {
		$compare = '>=';
		$order   = 'ASC';
	}

	if ( strtoupper( $atts['orden'] ) === 'ASC' || strtoupper( $atts['orden'] ) === 'DESC' ) {
		$order = strtoupper( $atts['orden'] );
	}

	// Paginación
	$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

	$args = array(
		'post_type'      => 'actividad',
		'posts_per_page' => intval( $atts['limite'] ),
		'paged'          => $paged,
		'meta_key'       => 'fecha',
		'orderby'        => 'meta_value_num',
		'order'          => $order,
		'meta_query'     => array(
			array(
				'key'     => 'fecha',
				'value'   => $hoy_num,
				'compare' => $compare,
				'type'    => 'NUMERIC',
			),
		),
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		$mensaje = ( $atts['modo'] === 'pasado' )
			? __( 'No hay actividades pasadas registradas.', 'euterpe' )
			: __( 'No hay actividades programadas próximamente.', 'euterpe' );
		return '<p>' . $mensaje . '</p>';
	}

	ob_start(); ?>

	<div class="wp-block-query">
		<ul class="wp-block-post-template lista-programacion grid-container post-overlay">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<?php
				$fecha_raw = trim( get_post_meta( get_the_ID(), 'fecha', true ) );
				if ( empty( $fecha_raw ) ) continue;

				$fecha_formateada = substr( $fecha_raw, 0, 4 ) . '-' . substr( $fecha_raw, 4, 2 ) . '-' . substr( $fecha_raw, 6, 2 );
				$timestamp        = strtotime( $fecha_formateada );
				if ( ! $timestamp ) continue;

				$fecha_legible = date_i18n( 'j \d\e F Y', $timestamp );	
				$hora          = get_post_meta( get_the_ID(), 'hora', true );
				?>
				<li class="wp-block-post item-programacion">
					<figure class="wp-block-post-featured-image">
						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'medium' ); ?>
						</a>
					</figure>

					<div class="info wp-block-group">
						<h2 class="wp-block-post-title">
							<?php the_title(); ?>
						</h2>

						<div class="fecha-hora wp-block-group">
							<p class="fecha"><?php echo esc_html( $fecha_legible ); ?></p>
							<p class="hora">
								<?php 
									echo esc_html(
										!empty($hora) && strtotime($hora) 
											? date_i18n( 'g:i a', strtotime($hora) ) 
											: 'Hora pendiente'
									); 
								?>
							</p>
						</div>
					</div>
				</li>
			<?php endwhile; ?>
		</ul>

		<?php
		if ( $atts['paginacion'] === 'true' ) :
			$big = 999999999;
			$total_pages = $query->max_num_pages;
			if ( $total_pages > 1 ) :
				$current_page = max( 1, get_query_var( 'paged' ) );
				?>
				<nav class="pagination wp-block-query-pagination" aria-label="Paginación">
					<?php if ( $current_page > 1 ) : ?>
						<a href="<?php echo get_pagenum_link( $current_page - 1 ); ?>" class="wp-block-query-pagination-previous">
							<span class="wp-block-query-pagination-previous-arrow is-arrow-arrow" aria-hidden="true">←</span>Anteriores
						</a>
					<?php endif; ?>

					<div class="wp-block-query-pagination-numbers">
						<?php
						for ( $i = 1; $i <= $total_pages; $i++ ) {
							if ( $i == $current_page ) {
								echo '<span aria-current="page" class="page-numbers current">' . $i . '</span>';
							} else {
								echo '<a class="page-numbers" href="' . get_pagenum_link( $i ) . '">' . $i . '</a>';
							}
						}
						?>
					</div>

					<?php if ( $current_page < $total_pages ) : ?>
						<a href="<?php echo get_pagenum_link( $current_page + 1 ); ?>" class="wp-block-query-pagination-next">
							Siguientes<span class="wp-block-query-pagination-next-arrow is-arrow-arrow" aria-hidden="true">→</span>
						</a>
					<?php endif; ?>
				</nav>
			<?php endif;
		endif; // fin del if de paginación
		?>
    </div>

    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode( 'programacion_completa', 'euterpe_programacion_completa' );

/* --------------------------------------------------------------
 *  SHORTCODE: PROGRAMACIÓN POR MES
 * -------------------------------------------------------------- */
function mostrar_programacion_por_mes( $atts ) {
	$atts = shortcode_atts(
		array( 'tipo' => 'simple' ),
		$atts,
		'programacion_por_mes'
	);

	$hoy = current_time( 'Y-m-d' );

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
				'value'   => str_replace( '-', '', $hoy ),
				'compare' => '>=',
				'type'    => 'NUMERIC',
			),
		),
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p>' . __( 'No hay actividades programadas próximamente.', 'euterpe' ) . '</p>';
	}

	$salida          = '';
	$mes_actual      = '';
	$meses_mostrados = array();

	while ( $query->have_posts() ) {
		$query->the_post();

		$fecha_raw = trim( get_post_meta( get_the_ID(), 'fecha', true ) );
		if ( empty( $fecha_raw ) ) {
			continue;
		}

		$fecha_formateada = substr( $fecha_raw, 0, 4 ) . '-' . substr( $fecha_raw, 4, 2 ) . '-' . substr( $fecha_raw, 6, 2 );
		$timestamp        = strtotime( $fecha_formateada );
		if ( ! $timestamp ) {
			continue;
		}

		$mes_clave     = date( 'Y-m', $timestamp );
		$mes_titulo    = date_i18n( 'F Y', $timestamp );
		$fecha_legible = date_i18n( 'j \d\e F', $timestamp );

		if ( ! in_array( $mes_clave, $meses_mostrados, true ) ) {
			if ( $mes_actual !== '' ) {
				$salida .= '</ul></div>';
			}

			$clase_ul = ( $atts['tipo'] === 'completa' )
				? 'lista-programacion grid-container post-overlay'
				: 'lista-programacion';

			$salida .= '<div class="mes-wrapper"><h2 class="mes-programacion">' . esc_html( ucfirst( $mes_titulo ) ) . '</h2>';
			$salida .= '<ul class="' . esc_attr( $clase_ul ) . '">';

			$mes_actual        = $mes_clave;
			$meses_mostrados[] = $mes_clave;
		}

		if ( $atts['tipo'] === 'completa' ) {
			$hora = get_post_meta( get_the_ID(), 'hora', true );

			$salida .= '<li class="item-programacion">';
			$salida .= '<figure><a href="' . esc_url( get_permalink() ) . '">' . get_the_post_thumbnail( get_the_ID(), 'medium' ) . '</a></figure>';
			$salida .= '<div class="info">';
			$salida .= '<h3>' . esc_html( get_the_title() ) . '</h3>';
			$salida .= '<div class="fecha-hora"><p class="fecha">' . esc_html( $fecha_legible ) . '</p>';
			if ( ! empty( $hora ) ) {
				$salida .= '<p class="hora">' . esc_html( date_i18n( 'g:i a', strtotime( $hora ) ) ) . '</p>';
			}
			$salida .= '</div></div>';
			$salida .= '</li>';
		} else {
			$salida .= '<li class="item-programacion"><a href="' . esc_url( get_permalink() ) . '">';
			$salida .= '<span class="fecha">' . esc_html( $fecha_legible ) . '</span>';
			$salida .= '<span class="title-actividad">' . esc_html( get_the_title() ) . '</span>';
			$salida .= '</a></li>';
		}
	}

	wp_reset_postdata();

	$salida .= '</ul>';

	return '<div class="programacion-mensual ' . esc_attr( $atts['tipo'] ) . '">' . $salida . '</div>';
}
add_shortcode( 'programacion_por_mes', 'mostrar_programacion_por_mes' );

/* --------------------------------------------------------------
 *  SHORTCODE: COLABORADORES SLIDER
 * -------------------------------------------------------------- */
function mostrar_colaboradores_slider() {
    $args = array(
        'post_type'      => 'colaborador', // CPT de colaboradores
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    );

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return '<p>No hay colaboradores disponibles.</p>';
    }

    $salida = '<div class="swiper"><div class="swiper-wrapper">';

    while ($query->have_posts()) {
        $query->the_post();

        $nombre = get_the_title();
        $enlace = get_permalink();
        $imagen = get_the_post_thumbnail(get_the_ID(), 'original');

        // Cada colaborador como slide
        $salida .= '<div class="swiper-slide colaborador-card"><a href="' . $enlace . '">';
        if ($imagen) {
            $salida .= '<figure class="colaborador-figura">' . $imagen . '</figure>';
        }
        $salida .= '<div class="title-wrapper"><h3>' . esc_html($nombre) . '</h3></div>';
        $salida .= '</div></a>';
    }

    $salida .= '</div>'; // cerrar swiper-wrapper
    $salida .= '<div class="swiper-pagination"></div>'; 
    $salida .= '</div>'; // cerrar swiper container

    wp_reset_postdata();

    return $salida;
}
add_shortcode('colaboradores_slider', 'mostrar_colaboradores_slider');


/* --------------------------------------------------------------
 *  ADMIN LIMITS FOR EDITORS
 * -------------------------------------------------------------- */
function euterpe_limit_editor_admin_menu() {
	if ( current_user_can( 'editor' ) && ! current_user_can( 'administrator' ) ) {
		remove_menu_page( 'index.php' );                  // Escritorio
		remove_menu_page( 'edit.php?post_type=page' );    // Páginas
		remove_menu_page( 'edit-comments.php' );          // Comentarios
		remove_menu_page( 'themes.php' );                 // Apariencia
		remove_menu_page( 'plugins.php' );                // Plugins
		remove_menu_page( 'users.php' );                  // Usuarios
		remove_menu_page( 'tools.php' );                  // Herramientas
		remove_menu_page( 'options-general.php' );        // Ajustes
		remove_menu_page( 'edit.php?post_type=wp_block' ); // Bloques reutilizables
		remove_menu_page( 'edit.php?post_type=wp_template' ); // Plantillas
		remove_menu_page( 'edit.php?post_type=wp_template_part' ); // Partes de plantilla
	}
}
add_action( 'admin_menu', 'euterpe_limit_editor_admin_menu', 999 );


/* --------------------------------------------------------------
 *  PRELOAD FONTS FROM THEME.JSON
 * -------------------------------------------------------------- */
function mytheme_preload_fonts() {
    $theme_json = wp_get_global_settings( [ 'typography', 'fontFamilies' ] );

    if ( empty( $theme_json ) ) {
        return;
    }

    foreach ( $theme_json as $font_family ) {
        if ( empty( $font_family['fontFace'] ) ) {
            continue;
        }

        foreach ( $font_family['fontFace'] as $face ) {
            if ( empty( $face['src'] ) ) {
                continue;
            }

            foreach ( $face['src'] as $src ) {
                if ( str_starts_with( $src, 'file:' ) ) {
                    $src_path = str_replace( 'file:./', '/', $src );

                    printf(
                        '<link rel="preload" href="%s%s" as="font" type="font/woff2" crossorigin>' . "\n",
                        esc_url( get_template_directory_uri() ),
                        esc_attr( $src_path )
                    );
                }
            }
        }
    }
}
add_action( 'wp_head', 'mytheme_preload_fonts', 5 );

/* --------------------------------------------------------------
 *  SHORTCODE: CURRENT YEAR
 * -------------------------------------------------------------- */
function euterpe_current_year_shortcode() {
	return date( 'Y' );
}
add_shortcode( 'year', 'euterpe_current_year_shortcode' );
