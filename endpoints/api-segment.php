<?php

if (!defined('ABSPATH')) {
    exit;
}

function trinitykitcms_get_products_by_segment($request) {
    // Valida a API key
    $api_validation = trinitykitcms_validate_api_key($request);
    if (is_wp_error($api_validation)) {
        return $api_validation;
    }

    // Obtém o slug do segmento da URL
    $segment_slug = $request['slug'];

    // Verifica se o segmento existe
    $segment = get_term_by('slug', $segment_slug, 'segments');
    if (!$segment) {
        return new WP_Error(
            'segment_not_found',
            'Segmento não encontrado',
            array('status' => 404)
        );
    }

    // Argumentos para buscar produtos do segmento específico
    $args = array(
        'post_type' => 'products',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'segments',
                'field' => 'slug',
                'terms' => $segment_slug
            )
        )
    );

    $products = get_posts($args);
    $formatted_products = array();

    foreach ($products as $product) {
        // Obtém as taxonomias
        $segments = wp_get_post_terms($product->ID, 'segments', array('fields' => 'all'));
        $product_lines = wp_get_post_terms($product->ID, 'product_lines', array('fields' => 'all'));

        // Obtém os campos ACF
        $cas_number = get_field('cas_number', $product->ID);

        $formatted_products[] = array(
            'id' => $product->ID,
            'title' => $product->post_title,
            'cas_number' => $cas_number,
            'segments' => array_map(function($segment) {
                return array(
                    'id' => $segment->term_id,
                    'name' => $segment->name,
                    'slug' => $segment->slug
                );
            }, $segments),
            'product_lines' => array_map(function($line) {
                return array(
                    'id' => $line->term_id,
                    'name' => $line->name,
                    'slug' => $line->slug
                );
            }, $product_lines)
        );
    }

    return array(
        'success' => true,
        'data' => $formatted_products,
        'items' => count($formatted_products),
        'segment' => array(
            'id' => $segment->term_id,
            'name' => $segment->name,
            'slug' => $segment->slug
        )
    );
}

/**
 * Registra o endpoint da API para produtos por segmento
 */
function trinitykitcms_register_segment_endpoints() {
    register_rest_route('trinitykitcms-api/v1', '/segment/(?P<slug>[a-zA-Z0-9-]+)', array(
        array(
            'methods' => 'GET',
            'callback' => 'trinitykitcms_get_products_by_segment',
            'permission_callback' => '__return_true'
        )
    ));
}
add_action('rest_api_init', 'trinitykitcms_register_segment_endpoints');