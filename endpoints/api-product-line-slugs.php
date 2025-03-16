<?php

if (!defined('ABSPATH')) {
    exit;
}

function trinitykitcms_get_product_line_slugs($request) {
    // Valida a API key
    $api_validation = trinitykitcms_validate_api_key($request);
    if (is_wp_error($api_validation)) {
        return $api_validation;
    }

    // Obtém apenas as categorias pai da taxonomia product_lines
    $product_lines = get_terms(array(
        'taxonomy' => 'product_lines',
        'parent' => 0, // Apenas categorias pai
        'hide_empty' => false
    ));

    // Cria um array para armazenar os slugs
    $slugs = array();
    foreach ($product_lines as $line) {
        $slugs[] = array('slug' => $line->slug);
    }

    return array(
        'success' => true,
        'data' => $slugs
    );
}

/**
 * Registra o endpoint da API para listar slugs das linhas de produtos
 */
function trinitykitcms_register_product_line_slugs_endpoints() {
    register_rest_route('trinitykitcms-api/v1', '/product-line-slugs', array(
        array(
            'methods' => 'GET',
            'callback' => 'trinitykitcms_get_product_line_slugs',
            'permission_callback' => '__return_true'
        )
    ));
}
add_action('rest_api_init', 'trinitykitcms_register_product_line_slugs_endpoints'); 