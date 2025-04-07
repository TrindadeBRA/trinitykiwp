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

    // Obtém o parâmetro quantity da requisição
    $quantity = $request->get_param('quantity');
    
    // Configura os argumentos da query
    $args = array(
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
        'fields' => 'ids'
    );

    // Se quantity for especificado, limita o número de posts
    if ($quantity) {
        $args['numberposts'] = intval($quantity);
    } else {
        $args['numberposts'] = -1; // Retorna todos os posts
    }

    // Obtém os posts
    $posts = get_posts($args);

    // Cria um array para armazenar os dados dos posts
    $posts_data = array();
    foreach ($posts as $post_id) {
        $post = get_post($post_id);
        $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');
        
        $posts_data[] = array(
            'slug' => $post->post_name,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'created_at' => $post->post_date,
            'featured_image_url' => $featured_image_url ? $featured_image_url : ''
        );
    }

    return array(
        'success' => true,
        'data' => $posts_data,
        'total' => count($posts_data)
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