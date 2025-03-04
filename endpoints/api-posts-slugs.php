<?php

// Verifica se o arquivo está sendo acessado diretamente
if (!defined('ABSPATH')) {
    exit; // Sai se acessado diretamente
}

function trinitykitcms_get_post_slugs($request) {
    // Valida a API key
    $api_validation = trinitykitcms_validate_api_key($request);
    if (is_wp_error($api_validation)) {
        return $api_validation;
    }

    // Obtém todos os posts publicados
    $posts = get_posts(array(
        'numberposts' => -1, // Pega todos os posts
        'post_status' => 'publish', // Apenas posts publicados
        'fields' => 'ids' // Retorna apenas os IDs dos posts
    ));

    // Cria um array para armazenar os slugs
    $slugs = array();
    foreach ($posts as $post_id) {
        $slugs[] = array('slug' => get_post_field('post_name', $post_id));
    }

    return array(
        'success' => true,
        'data' => $slugs
    );
}

/**
 * Registra os endpoints da API para listar slugs dos posts.
 */
function trinitykitcms_register_post_slug_endpoints() {
    register_rest_route('trinitykitcms-api/v1', '/post-slugs', array(
        array(
            'methods' => 'GET',
            'callback' => 'trinitykitcms_get_post_slugs',
            'permission_callback' => '__return_true'
        )
    ));
}
add_action('rest_api_init', 'trinitykitcms_register_post_slug_endpoints', 10);