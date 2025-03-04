<?php

// Verifica se o arquivo está sendo acessado diretamente
if (!defined('ABSPATH')) {
    exit;
}

function trinitykitcms_get_products($request) {
    // Valida a API key
    $api_validation = trinitykitcms_validate_api_key($request);
    if (is_wp_error($api_validation)) {
        return $api_validation;
    }

    $args = array(
        'post_type' => 'products',
        'posts_per_page' => -1,
        'post_status' => 'publish'
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
        'items' => count($formatted_products)
    );
}

/**
 * Registra os endpoints da API para produtos
 */
function trinitykitcms_register_products_endpoints() {
    register_rest_route('trinitykitcms-api/v1', '/products', array(
        array(
            'methods' => 'GET',
            'callback' => 'trinitykitcms_get_products',
            'permission_callback' => '__return_true'
        )
    ));
}
add_action('rest_api_init', 'trinitykitcms_register_products_endpoints');