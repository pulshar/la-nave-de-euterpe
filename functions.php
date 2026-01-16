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
function euterpe_login_custom_css()
{
	wp_enqueue_style(
		'euterpe-login',
		get_stylesheet_directory_uri() . '/assets/css/login.min.css',
		array(),
		file_exists(get_stylesheet_directory() . '/assets/css/login.min.css') ? filemtime(get_stylesheet_directory() . '/assets/css/login.min.css') : '1.0'
	);
}
add_action('login_enqueue_scripts', 'euterpe_login_custom_css');

/* --------------------------------------------------------------
 *  ADMIN PAGE CUSTOMIZATION
 * -------------------------------------------------------------- */
function euterpe_admin_custom_css()
{
	wp_enqueue_style(
		'euterpe-login',
		get_stylesheet_directory_uri() . '/assets/css/admin.css',
		array(),
		filemtime(get_stylesheet_directory() . '/assets/css/admin.css')
	);
}
add_action('admin_enqueue_scripts', 'euterpe_admin_custom_css');

/* --------------------------------------------------------------
 *  THEME SETUP
 * -------------------------------------------------------------- */
function euterpe_setup()
{

	// Estilos del editor (usa el CSS principal)
	add_editor_style(array('style.min.css'));

	// Quitar patrones por defecto de WordPress
	remove_theme_support('core-block-patterns');

}
add_action('after_setup_theme', 'euterpe_setup');

/* --------------------------------------------------------------
 *  ENQUEUE SCRIPTS & STYLES
 * -------------------------------------------------------------- */
function euterpe_enqueue_scripts()
{

	// Lenis
	if (!is_admin()) {
		wp_enqueue_script(
			'lenis',
			'https://cdn.jsdelivr.net/npm/@studio-freight/lenis@latest/bundled/lenis.min.js',
			array(),
			null,
			true
		);
	}

	// Swiper
	global $post;
	$has_swiper = false;
	if (is_a($post, 'WP_Post')) {
		$has_swiper = has_block('core/gallery', $post) || has_shortcode($post->post_content, 'colaboradores_slider');
	}

	if ($has_swiper || is_front_page() || is_singular('actividad')) {
		wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
		wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), null, true);
	}

	// Fancybox (solo en páginas que lo requieran)
	if (is_singular() && (has_block('core/image') || has_block('core/gallery') || is_singular('tribe_events'))) {
		wp_enqueue_style('fancybox-css', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css');
		wp_enqueue_script('fancybox-js', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js', array(), null, true);
	}

	// Estilos principales del tema
	wp_enqueue_style(
		'euterpe-style',
		get_stylesheet_directory_uri() . '/style.css',
		array(),
		filemtime(get_stylesheet_directory() . '/style.css')
	);

	// Script principal
	wp_enqueue_script(
		'euterpe-main',
		get_stylesheet_directory_uri() . '/assets/js/main.js',
		array(),
		filemtime(get_stylesheet_directory() . '/assets/js/main.js'),
		true
	);
}
add_action('wp_enqueue_scripts', 'euterpe_enqueue_scripts');

/**
 * Añadir defer a los scripts para mejorar carga paralela
 */
function euterpe_add_defer_attribute($tag, $handle)
{
	$scripts_to_defer = array('lenis', 'swiper-js', 'fancybox-js', 'euterpe-main');
	if (in_array($handle, $scripts_to_defer)) {
		return str_replace(' src', ' defer src', $tag);
	}
	return $tag;
}
add_filter('script_loader_tag', 'euterpe_add_defer_attribute', 10, 2);

/**
 * Limpieza de WordPress Bloat
 */
function euterpe_cleanup_wp()
{
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'euterpe_cleanup_wp');


/* --------------------------------------------------------------
 *  BLOCK STYLES (Buttons, Groups, Gallery)
 * -------------------------------------------------------------- */
if (function_exists('register_block_style')) {

	register_block_style(
		'core/heading',
		[
			'name' => 'display',
			'label' => 'Display',
		]
	);

	// Buttons
	register_block_style(
		'core/button',
		array(
			'name' => 'primary',
			'label' => __('Primary', 'euterpe'),
			'is_default' => true,
		)
	);

	register_block_style(
		'core/button',
		array(
			'name' => 'secondary',
			'label' => __('Secondary', 'euterpe'),
		)
	);

	register_block_style(
		'core/button',
		array(
			'name' => 'primary-outline',
			'label' => __('Primary Outline', 'euterpe'),
		)
	);

	register_block_style(
		'core/button',
		array(
			'name' => 'secondary-outline',
			'label' => __('Secondary Outline', 'euterpe'),
		)
	);

	// Group
	register_block_style(
		'core/group',
		array(
			'name' => 'blank-group',
			'label' => __('margin-top-0', 'euterpe'),
			'inline_style' => '.is-style-blank-group { margin-block-start: 0 !important; }',
		)
	);
}

/* --------------------------------------------------------------
 *  BLOCK PATTERN CATEGORIES
 * -------------------------------------------------------------- */
function euterpe_register_block_pattern_categories()
{

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
add_action('init', 'euterpe_register_block_pattern_categories');

/* --------------------------------------------------------------
 *  Imagen destacada por defecto para el CPT 'actividad'
 * -------------------------------------------------------------- */
function actividad_default_thumbnail($html, $post_id, $post_thumbnail_id, $size, $attr)
{

	if (get_post_type($post_id) !== 'actividad') {
		return $html;
	}
	if (!empty($html)) {
		return $html;
	}
	if (is_singular('actividad')) {
		return $html; // deja vacío
	}

	$default_url = get_stylesheet_directory_uri() . '/assets/images/default-actividad.webp';
	$html = '<img src="' . esc_url($default_url) . '" class="default-image" alt="Imagen por defecto" />';

	return $html;
}
add_filter('post_thumbnail_html', 'actividad_default_thumbnail', 10, 5);

/* --------------------------------------------------------------
 *  SHORTCODE: PROGRAMACIÓN FUTURA/PASADA
 * -------------------------------------------------------------- */
function euterpe_programacion_completa($atts)
{
	$atts = shortcode_atts(
		array(
			'limite' => 6,
			'modo' => 'futuro',
			'orden' => 'ASC',
			'paginacion' => 'true',
		),
		$atts,
		'programacion_completa'
	);

	$hoy = current_time('Y-m-d');
	$hoy_num = str_replace('-', '', $hoy);

	// Comparador según modo
	if ($atts['modo'] === 'pasado') {
		$compare = '<=';
		$order = 'ASC';
	} else {
		$compare = '>=';
		$order = 'ASC';
	}

	if (strtoupper($atts['orden']) === 'ASC' || strtoupper($atts['orden']) === 'DESC') {
		$order = strtoupper($atts['orden']);
	}

	// Paginación
	$paged = get_query_var('paged') ? get_query_var('paged') : 1;

	$args = array(
		'post_type' => 'actividad',
		'posts_per_page' => intval($atts['limite']),
		'paged' => $paged,
		'meta_key' => 'fecha',
		'orderby' => 'meta_value_num',
		'order' => $order,
		'meta_query' => array(
			array(
				'key' => 'fecha',
				'value' => $hoy_num,
				'compare' => $compare,
				'type' => 'NUMERIC',
			),
		),
	);

	$query = new WP_Query($args);

	if (!$query->have_posts()) {
		$mensaje = ($atts['modo'] === 'pasado')
			? __('No hay actividades pasadas registradas.', 'euterpe')
			: __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-frown-icon lucide-frown"><circle cx="12" cy="12" r="10"/><path d="M16 16s-1.5-2-4-2-4 2-4 2"/><line x1="9" x2="9.01" y1="9" y2="9"/><line x1="15" x2="15.01" y1="9" y2="9"/></svg>No hay actividades programadas próximamente.', 'euterpe');
		return '<div class="no-actividades"><p>' . $mensaje . '</p></div>';
	}

	ob_start(); ?>

	<div class="wp-block-query">
		<ul class="wp-block-post-template lista-programacion grid-container post-overlay">
			<?php while ($query->have_posts()):
				$query->the_post(); ?>
				<?php
				$fecha_raw = trim(get_post_meta(get_the_ID(), 'fecha', true));
				if (empty($fecha_raw))
					continue;

				$fecha_formateada = substr($fecha_raw, 0, 4) . '-' . substr($fecha_raw, 4, 2) . '-' . substr($fecha_raw, 6, 2);
				$timestamp = strtotime($fecha_formateada);
				if (!$timestamp)
					continue;

				$fecha_legible = date_i18n('j \d\e F Y', $timestamp);
				$hora = get_post_meta(get_the_ID(), 'hora', true);
				?>
				<li class="wp-block-post item-programacion">
					<figure class="wp-block-post-featured-image">
						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail('medium_large'); ?>
						</a>
					</figure>

					<div class="info wp-block-group">
						<h2 class="wp-block-post-title">
							<?php the_title(); ?>
						</h2>

						<div class="fecha-hora wp-block-group">
							<p class="fecha"><?php echo esc_html($fecha_legible); ?></p>
							<p class="hora">
								<?php
								echo esc_html(
									!empty($hora) && strtotime($hora)
									? date_i18n('g:i a', strtotime($hora))
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
		if ($atts['paginacion'] === 'true'):
			$big = 999999999;
			$total_pages = $query->max_num_pages;
			if ($total_pages > 1):
				$current_page = max(1, get_query_var('paged'));
				?>
				<nav class="pagination wp-block-query-pagination" aria-label="Paginación">
					<?php if ($current_page > 1): ?>
						<a href="<?php echo get_pagenum_link($current_page - 1); ?>" class="wp-block-query-pagination-previous">
							<span class="wp-block-query-pagination-previous-arrow is-arrow-arrow" aria-hidden="true">←</span>Anteriores
						</a>
					<?php endif; ?>

					<div class="wp-block-query-pagination-numbers">
						<?php
						for ($i = 1; $i <= $total_pages; $i++) {
							if ($i == $current_page) {
								echo '<span aria-current="page" class="page-numbers current">' . $i . '</span>';
							} else {
								echo '<a class="page-numbers" href="' . get_pagenum_link($i) . '">' . $i . '</a>';
							}
						}
						?>
					</div>

					<?php if ($current_page < $total_pages): ?>
						<a href="<?php echo get_pagenum_link($current_page + 1); ?>" class="wp-block-query-pagination-next">
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
add_shortcode('programacion_completa', 'euterpe_programacion_completa');

// Forzar siempre que los eventos sean "featured"
add_action('save_post', function ($post_id, $post, $update) {

	// Sólo para eventos
	if (!isset($post->post_type) || 'tribe_events' !== $post->post_type) {
		return;
	}

	// Evitar autosaves o revisiones
	if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
		return;
	}

	// Evitar bucles infinitos: si ya está marcado, no hacer nada pesado
	$already = get_post_meta($post_id, '_tribe_featured', true);
	if (empty($already)) {
		update_post_meta($post_id, '_tribe_featured', 1);
	} else {
		// Aun así forzamos la otra key por si acaso
		update_post_meta($post_id, '_tribe_featured', 1);
	}

	// También escribir la key que usa la clase de TEC si existe la constante
	if (class_exists('Tribe__Events__Featured_Events') && defined('Tribe__Events__Featured_Events::FEATURED_EVENT_KEY')) {
		// la constante se refiere normalmente a algo como '_tribe_featured' pero por si cambia:
		$const_key = Tribe__Events__Featured_Events::FEATURED_EVENT_KEY;
		update_post_meta($post_id, $const_key, 1);
	} else {
		// Por compatibilidad adicional: escribir también '_tribe_featured' otra vez (no hace daño)
		update_post_meta($post_id, '_tribe_featured', 1);
	}

	// Opcional: log para depuración (quita en producción)
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log("[TEC] Forzado featured en evento #{$post_id}");
	}

}, 10, 3);

/* --------------------------------------------------------------
 *  SHORTCODE: PROGRAMACIÓN POR MES
 * -------------------------------------------------------------- */
function mostrar_programacion_por_mes($atts)
{
	$atts = shortcode_atts(
		array('tipo' => 'simple'),
		$atts,
		'programacion_por_mes'
	);

	$hoy = current_time('Y-m-d');

	$args = array(
		'post_type' => 'actividad',
		'posts_per_page' => -1,
		'meta_key' => 'fecha',
		'orderby' => 'meta_value',
		'order' => 'ASC',
		'meta_type' => 'NUMERIC',
		'meta_query' => array(
			array(
				'key' => 'fecha',
				'value' => str_replace('-', '', $hoy),
				'compare' => '>=',
				'type' => 'NUMERIC',
			),
		),
	);

	$query = new WP_Query($args);

	if (!$query->have_posts()) {
		return '<div class="no-actividades"><p><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-frown-icon lucide-frown"><circle cx="12" cy="12" r="10"/><path d="M16 16s-1.5-2-4-2-4 2-4 2"/><line x1="9" x2="9.01" y1="9" y2="9"/><line x1="15" x2="15.01" y1="9" y2="9"/></svg>' . __('No hay actividades programadas próximamente.', 'euterpe') . '</p></div>';
	}

	$salida = '';
	$mes_actual = '';
	$meses_mostrados = array();

	while ($query->have_posts()) {
		$query->the_post();

		$fecha_raw = trim(get_post_meta(get_the_ID(), 'fecha', true));
		if (empty($fecha_raw)) {
			continue;
		}

		$fecha_formateada = substr($fecha_raw, 0, 4) . '-' . substr($fecha_raw, 4, 2) . '-' . substr($fecha_raw, 6, 2);
		$timestamp = strtotime($fecha_formateada);
		if (!$timestamp) {
			continue;
		}

		$mes_clave = date('Y-m', $timestamp);
		$mes_titulo = date_i18n('F Y', $timestamp);
		$fecha_legible = date_i18n('j \d\e F', $timestamp);

		if (!in_array($mes_clave, $meses_mostrados, true)) {
			if ($mes_actual !== '') {
				$salida .= '</ul></div>';
			}

			$clase_ul = ($atts['tipo'] === 'completa')
				? 'lista-programacion grid-container post-overlay'
				: 'lista-programacion';

			$salida .= '<div class="mes-wrapper"><h2 class="mes-programacion">' . esc_html(ucfirst($mes_titulo)) . '</h2>';
			$salida .= '<ul class="' . esc_attr($clase_ul) . '">';

			$mes_actual = $mes_clave;
			$meses_mostrados[] = $mes_clave;
		}

		if ($atts['tipo'] === 'completa') {
			$hora = get_post_meta(get_the_ID(), 'hora', true);

			$salida .= '<li class="item-programacion">';
			$salida .= '<figure><a href="' . esc_url(get_permalink()) . '">' . get_the_post_thumbnail(get_the_ID(), 'medium_large') . '</a></figure>';
			$salida .= '<div class="info">';
			$salida .= '<h2>' . esc_html(get_the_title()) . '</h2>';
			$salida .= '<div class="fecha-hora"><p class="fecha">' . esc_html($fecha_legible) . '</p>';
			if (!empty($hora)) {
				$salida .= '<p class="hora">' . esc_html(date_i18n('g:i a', strtotime($hora))) . '</p>';
			}
			$salida .= '</div></div>';
			$salida .= '</li>';
		} else {
			$salida .= '<li class="item-programacion"><a href="' . esc_url(get_permalink()) . '">';
			$salida .= '<span class="fecha">' . esc_html($fecha_legible) . '</span>';
			$salida .= '<span class="title-actividad">' . esc_html(get_the_title()) . '</span>';
			$salida .= '</a></li>';
		}
	}

	wp_reset_postdata();

	$salida .= '</ul></div>';  // cierra el último mes-wrapper correctamente

	return '<div class="programacion-mensual ' . esc_attr($atts['tipo']) . '">' . $salida . '</div>';
}
add_shortcode('programacion_por_mes', 'mostrar_programacion_por_mes');

/* --------------------------------------------------------------
 *  SHORTCODE: PROGRAMACIÓN POR MES SELECT MONTH
 * -------------------------------------------------------------- */
function mostrar_programacion_por_mes_con_select($atts)
{
	$atts = shortcode_atts(
		array(
			'tipo' => 'simple',
		),
		$atts,
		'programacion_por_mes'
	);

	$hoy = current_time('Y-m-d');
	$hoy_num = str_replace('-', '', $hoy);

	// -----------------------------
	// 1. Obtener todos los meses disponibles
	// -----------------------------
	$args_todos = array(
		'post_type' => 'actividad',
		'posts_per_page' => -1,
		'meta_key' => 'fecha',
		'orderby' => 'meta_value_num',
		'order' => 'ASC',
	);

	$q_meses = new WP_Query($args_todos);
	$meses_disponibles = array();

	while ($q_meses->have_posts()) {
		$q_meses->the_post();
		$fecha_raw = get_post_meta(get_the_ID(), 'fecha', true);
		if (!$fecha_raw)
			continue;

		$fecha_formateada = substr($fecha_raw, 0, 4) . '-' . substr($fecha_raw, 4, 2) . '-' . substr($fecha_raw, 6, 2);
		$timestamp = strtotime($fecha_formateada);
		if (!$timestamp)
			continue;

		$clave = date('Y-m', $timestamp);
		$meses_disponibles[$clave] = date_i18n('F Y', $timestamp);
	}
	wp_reset_postdata();

	if (empty($meses_disponibles)) {
		return '<p>No hay actividades registradas.</p>';
	}

	// -----------------------------
	// 2. Mes elegido
	// -----------------------------
	$mes_elegido = isset($_GET['mes']) ? sanitize_text_field($_GET['mes']) : '';

	// -----------------------------
	// 3. Consultar actividades según selección
	// -----------------------------
	if ($mes_elegido) {
		// Filtrar por mes
		list($anio, $mes) = explode('-', $mes_elegido);
		$primer_dia = $anio . $mes . '01';
		$ultimo_dia = date('Ymt', strtotime($anio . '-' . $mes . '-01'));

		$args = array(
			'post_type' => 'actividad',
			'posts_per_page' => -1,
			'meta_key' => 'fecha',
			'orderby' => 'meta_value_num',
			'order' => 'ASC',
			'meta_query' => array(
				array(
					'key' => 'fecha',
					'value' => array($primer_dia, $ultimo_dia),
					'compare' => 'BETWEEN',
					'type' => 'NUMERIC',
				),
			),
		);
	} else {
		// Mostrar todas futuras
		$args = array(
			'post_type' => 'actividad',
			'posts_per_page' => -1,
			'meta_key' => 'fecha',
			'orderby' => 'meta_value_num',
			'order' => 'ASC',
			'meta_query' => array(
				array(
					'key' => 'fecha',
					'value' => $hoy_num,
					'compare' => '>=',
					'type' => 'NUMERIC',
				),
			),
		);
	}

	$query = new WP_Query($args);

	// -----------------------------
	// 4. Salida HTML
	// -----------------------------
	ob_start();
	?>
	<div class="menu-mes-programacion">
		<form method="get" class="selector-mes-programacion">
			<label for="selector-mes">Selecciona:</label>
			<select id="selector-mes" name="mes" onchange="this.form.submit()">
				<option value="" <?php selected('', $mes_elegido); ?>>Próximas actividades</option>
				<?php foreach ($meses_disponibles as $valor => $texto): ?>
					<option value="<?php echo esc_attr($valor); ?>" <?php selected($mes_elegido, $valor); ?>>
						<?php echo esc_html(ucfirst($texto)); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</form>
		<div class="view-controls" role="group" aria-label="Cambiar modo de visualización de entradas">
			<button class="view-btn active" data-view="grid" aria-label="Vista en cuadrícula" aria-pressed="true"></button>

			<button class="view-btn" data-view="list" aria-label="Vista en lista" aria-pressed="false"></button>
		</div>
	</div>

	<div class="programacion-mensual <?php echo esc_attr($atts['tipo']); ?>">
		<?php
		if (!$query->have_posts()) {
			echo '<div class="no-actividades"><p><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-frown-icon lucide-frown"><circle cx="12" cy="12" r="10"></circle><path d="M16 16s-1.5-2-4-2-4 2-4 2"></path><line x1="9" x2="9.01" y1="9" y2="9"></line><line x1="15" x2="15.01" y1="9" y2="9"></line></svg>No hay actividades programadas próximamente.</p></div>';
		} else {
			$mes_actual = '';

			while ($query->have_posts()) {
				$query->the_post();
				$fecha_raw = get_post_meta(get_the_ID(), 'fecha', true);
				$fecha_formateada = substr($fecha_raw, 0, 4) . '-' . substr($fecha_raw, 4, 2) . '-' . substr($fecha_raw, 6, 2);
				$timestamp = strtotime($fecha_formateada);
				$fecha_legible = date_i18n('j \d\e F', $timestamp);
				$hora = get_post_meta(get_the_ID(), 'hora', true);

				$mes_clave = date('Y-m', $timestamp);
				$mes_titulo = date_i18n('F Y', $timestamp);

				// Nuevo mes → cerrar ul previo y mostrar título
				if ($mes_actual !== $mes_clave) {
					if ($mes_actual !== '') {
						echo '</div>';
					}
					echo '<div class="mes-wrapper">';
					echo '<h2 class="mes-programacion">' . esc_html(ucfirst($mes_titulo)) . '</h2>';
					echo '<ul class="' . esc_attr($atts['tipo'] === 'completa' ? 'lista-programacion grid-container post-overlay' : 'lista-programacion') . '">';
					$mes_actual = $mes_clave;
				}

				// Mostrar actividad
				if ($atts['tipo'] === 'completa') {
					echo '<li class="item-programacion">';
					echo '<figure class="wp-block-post-featured-image"><a href="' . esc_url(get_permalink()) . '">' . get_the_post_thumbnail(get_the_ID(), 'medium_large') . '</a></figure>';
					echo '<div class="info">';
					echo '<h2>' . esc_html(get_the_title()) . '</h2>';
					echo '<div class="fecha-hora">';
					echo '<p class="fecha">' . esc_html($fecha_legible) . '</p>';
					if (!empty($hora))
						echo '<p class="hora">' . esc_html(date_i18n('g:i a', strtotime($hora))) . '</p>';
					echo '</div></div></li>';
				} else {
					echo '<li class="item-programacion"><a href="' . esc_url(get_permalink()) . '">';
					echo '<span class="fecha">' . esc_html($fecha_legible) . '</span>';
					echo '<span class="title-actividad">' . esc_html(get_the_title()) . '</span>';
					echo '</a></li>';
				}
			}

			echo '</ul></div>';
		}
		wp_reset_postdata();

		// -----------------------------
		// 5. Últimos 3 eventos pasados
		// -----------------------------
		if (!$mes_elegido) {
			$args_pasadas = array(
				'post_type' => 'actividad',
				'posts_per_page' => 3,
				'meta_key' => 'fecha',
				'orderby' => 'meta_value_num',
				'order' => 'DESC',
				'meta_query' => array(
					array(
						'key' => 'fecha',
						'value' => $hoy_num,
						'compare' => '<',
						'type' => 'NUMERIC',
					),
				),
			);

			$query_pasadas = new WP_Query($args_pasadas);

			if ($query_pasadas->have_posts()) {
				echo '<div class="ultimos-eventos"><h2 class="mes-programacion">Actividades recientes</h2>';
				echo '<ul class="' . esc_attr($atts['tipo'] === 'completa' ? 'lista-programacion grid-container post-overlay' : 'lista-programacion') . '">';
				while ($query_pasadas->have_posts()) {
					$query_pasadas->the_post();
					$fecha_raw = get_post_meta(get_the_ID(), 'fecha', true);
					$fecha_formateada = substr($fecha_raw, 0, 4) . '-' . substr($fecha_raw, 4, 2) . '-' . substr($fecha_raw, 6, 2);
					$timestamp = strtotime($fecha_formateada);
					$fecha_legible = date_i18n('j \d\e F', $timestamp);
					$hora = get_post_meta(get_the_ID(), 'hora', true);

					if ($atts['tipo'] === 'completa') {
						echo '<li class="item-programacion">';
						echo '<figure class="wp-block-post-featured-image"><a href="' . esc_url(get_permalink()) . '">' . get_the_post_thumbnail(get_the_ID(), 'medium') . '</a></figure>';
						echo '<div class="info">';
						echo '<h2>' . esc_html(get_the_title()) . '</h2>';
						echo '<div class="fecha-hora">';
						echo '<p class="fecha">' . esc_html($fecha_legible) . '</p>';
						if (!empty($hora))
							echo '<p class="hora">' . esc_html(date_i18n('g:i a', strtotime($hora))) . '</p>';
						echo '</div></div></li>';
					} else {
						echo '<li class="item-programacion"><a href="' . esc_url(get_permalink()) . '">';
						echo '<span class="fecha">' . esc_html($fecha_legible) . '</span>';
						echo '<span class="title-actividad">' . esc_html(get_the_title()) . '</span>';
						echo '</a></li>';
					}
				}
				echo '</ul></div>';
				wp_reset_postdata();
			}
		}

		echo '</div>'; // fin programacion-mensual
	
		return ob_get_clean();
}
add_shortcode('programacion_por_mes_con_select', 'mostrar_programacion_por_mes_con_select');

/* --------------------------------------------------------------
 *  SHORTCODE: PRÓXIMOS EVENTOS THE EVENT CALENDAR
 * -------------------------------------------------------------- */
function euterpe_proximos_eventos()
{

	$hoy = current_time('Y-m-d H:i:s');

	$args = array(
		'post_type' => 'tribe_events',
		'posts_per_page' => 2,
		'post_status' => 'publish',
		'meta_key' => '_EventStartDate',
		'orderby' => 'meta_value',
		'order' => 'ASC',
		'meta_query' => array(
			array(
				'key' => '_EventStartDate',
				'value' => $hoy,
				'compare' => '>=',
				'type' => 'DATETIME',
			),
		),
		'fields' => 'ids',
	);

	$query = new WP_Query($args);

	if (!$query->have_posts()) {
		return '<div class="no-actividades"><p><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-x-icon lucide-calendar-x"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="m14 14-4 4"/><path d="m10 14 4 4"/></svg>No hay actividades programadas próximamente.</p></div>';
	}

	ob_start(); ?>

		<div class="wp-block-query">
			<ul class="wp-block-post-template lista-programacion grid-container post-overlay">

				<?php
				foreach ($query->posts as $event_id):
					setup_postdata(get_post($event_id));

					$fecha_legible = tribe_get_start_date($event_id, false, 'j \d\e F Y');
					$hora = tribe_get_start_date($event_id, false, 'g:i a');
					?>

					<li class="wp-block-post item-programacion">
						<figure class="wp-block-post-featured-image">
							<a href="<?php echo get_permalink($event_id); ?>">
								<?php echo get_the_post_thumbnail($event_id, 'medium_large'); ?>
							</a>
						</figure>

						<div class="info wp-block-group">
							<h2 class="wp-block-post-title">
								<?php echo get_the_title($event_id); ?>
							</h2>

							<div class="fecha-hora wp-block-group">
								<p class="fecha"><?php echo esc_html($fecha_legible); ?></p>
								<p class="hora"><?php echo esc_html($hora); ?></p>
							</div>
						</div>
					</li>

				<?php endforeach;
				wp_reset_postdata(); ?>

			</ul>
		</div>

		<?php
		return ob_get_clean();
}
add_shortcode('proximos_eventos', 'euterpe_proximos_eventos');

add_filter('tribe_the_notices', function ($html, $notices) {

	// Convertimos a array y quitamos el aviso no deseado
	$filtered_notices = array_filter($notices, function ($notice) {
		return strpos($notice, 'No se ha encontrado ningún resultado') === false;
	});

	if (empty($filtered_notices)) {
		return ''; // ningún aviso queda
	}

	// Reconstruimos el HTML con los avisos restantes
	$html = '<div class="tribe-events-notices"><ul><li>' . implode('</li><li>', $filtered_notices) . '</li></ul></div>';

	return $html;

}, 10, 2);
/* --------------------------------------------------------------
 *  SHORTCODE: PRODUCCIONES 
 * -------------------------------------------------------------- */
function euterpe_producciones($atts)
{
	$atts = shortcode_atts(
		array(
			'limite' => 6,
			'paginacion' => 'true',
		),
		$atts,
		'producciones'
	);

	// Paginación estándar de WP
	$paged = get_query_var('paged') ? get_query_var('paged') : 1;

	$args = array(
		'post_type' => 'produccion',
		'posts_per_page' => intval($atts['limite']),
		'paged' => $paged,
		'orderby' => 'date',
		'order' => 'DESC',
	);

	$query = new WP_Query($args);

	if (!$query->have_posts()) {
		return '<p>No hay producciones disponibles.</p>';
	}

	ob_start(); ?>

		<div class="wp-block-query">
			<ul class="wp-block-post-template lista-programacion grid-container producciones post-overlay">

				<?php while ($query->have_posts()):
					$query->the_post(); ?>

					<li class="wp-block-post item-programacion">

						<figure class="wp-block-post-featured-image">
							<a href="<?php the_permalink(); ?>">
								<?php
								$i = $query->current_post;
								the_post_thumbnail(
									'medium_large',
									array(
										'loading' => $i < 2 ? 'eager' : 'lazy',
										'fetchpriority' => $i < 2 ? 'high' : 'auto',
										'decoding' => 'async',
										'sizes' => '(max-width: 768px) 100vw, 33vw',
									)
								);
								?>
							</a>
						</figure>

						<div class="info wp-block-group">

							<h2 class="wp-block-post-title"><?php the_title(); ?></h2>

						</div>

					</li>

				<?php endwhile; ?>

			</ul>

			<?php
			if ($atts['paginacion'] === 'true'):
				$total_pages = $query->max_num_pages;
				if ($total_pages > 1):
					$current_page = max(1, get_query_var('paged'));
					?>
					<nav class="pagination wp-block-query-pagination" aria-label="Paginación">
						<?php if ($current_page > 1): ?>
							<a href="<?php echo get_pagenum_link($current_page - 1); ?>" class="wp-block-query-pagination-previous">
								<span class="wp-block-query-pagination-previous-arrow is-arrow-arrow"
									aria-hidden="true">←</span>Anteriores
							</a>
						<?php endif; ?>

						<div class="wp-block-query-pagination-numbers">
							<?php
							for ($i = 1; $i <= $total_pages; $i++) {
								if ($i == $current_page) {
									echo '<span aria-current="page" class="page-numbers current">' . $i . '</span>';
								} else {
									echo '<a class="page-numbers" href="' . get_pagenum_link($i) . '">' . $i . '</a>';
								}
							}
							?>
						</div>

						<?php if ($current_page < $total_pages): ?>
							<a href="<?php echo get_pagenum_link($current_page + 1); ?>" class="wp-block-query-pagination-next">
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
add_shortcode('producciones', 'euterpe_producciones');

/* --------------------------------------------------------------
 *  SHORTCODE: COLABORADORES SLIDER
 * -------------------------------------------------------------- */
function mostrar_colaboradores_slider()
{
	$args = array(
		'post_type' => 'colaborador', // CPT de colaboradores
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
	);

	$query = new WP_Query($args);

	if (!$query->have_posts()) {
		return '<p>No hay colaboradores disponibles.</p>';
	}

	$salida = '<div class="swiper colaboradores-swiper"><div class="swiper-wrapper">';

	while ($query->have_posts()) {
		$query->the_post();

		$nombre = get_the_title();
		$enlace = get_permalink();
		$imagen = get_the_post_thumbnail(get_the_ID(), 'medium', array(
			'loading' => 'lazy',
			'sizes' => '(max-width: 600px) 100vw, 200px',
		));

		// Cada colaborador como slide
		$salida .= '<div class="swiper-slide colaborador-card"><a href="' . $enlace . '">';
		if ($imagen) {
			$salida .= '<figure class="colaborador-figura">' . $imagen . '</figure><div class="swiper-lazy-preloader"></div>';
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
function euterpe_limit_editor_admin_menu()
{
	if (current_user_can('editor') && !current_user_can('administrator')) {
		remove_menu_page('index.php');                  // Escritorio
		remove_menu_page('edit.php?post_type=page');    // Páginas
		remove_menu_page('edit-comments.php');          // Comentarios
		remove_menu_page('themes.php');                 // Apariencia
		remove_menu_page('plugins.php');                // Plugins
		remove_menu_page('users.php');                  // Usuarios
		remove_menu_page('tools.php');                  // Herramientas
		remove_menu_page('options-general.php');        // Ajustes
		remove_menu_page('edit.php?post_type=wp_block'); // Bloques reutilizables
		remove_menu_page('edit.php?post_type=wp_template'); // Plantillas
		remove_menu_page('edit.php?post_type=wp_template_part'); // Partes de plantilla
	}
}
add_action('admin_menu', 'euterpe_limit_editor_admin_menu', 999);


/* --------------------------------------------------------------
 *  PRELOAD FONTS FROM THEME.JSON
 * -------------------------------------------------------------- */
function mytheme_preload_fonts()
{
	$theme_json = wp_get_global_settings(['typography', 'fontFamilies']);

	if (empty($theme_json)) {
		return;
	}

	foreach ($theme_json as $font_family) {
		if (empty($font_family['fontFace'])) {
			continue;
		}

		foreach ($font_family['fontFace'] as $face) {
			if (empty($face['src'])) {
				continue;
			}

			foreach ($face['src'] as $src) {
				if (str_starts_with($src, 'file:')) {
					$src_path = str_replace('file:./', '/', $src);

					printf(
						'<link rel="preload" href="%s%s" as="font" type="font/woff2" crossorigin>' . "\n",
						esc_url(get_template_directory_uri()),
						esc_attr($src_path)
					);
				}
			}
		}
	}
}
add_action('wp_head', 'mytheme_preload_fonts', 5);

/* --------------------------------------------------------------
 *  SHORTCODE: CURRENT YEAR
 * -------------------------------------------------------------- */
function euterpe_current_year_shortcode()
{
	return date('Y');
}
add_shortcode('year', 'euterpe_current_year_shortcode');
