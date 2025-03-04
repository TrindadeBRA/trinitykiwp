<?php

// Verifica se o arquivo está sendo acessado diretamente
if (!defined('ABSPATH')) {
    exit; // Sai se acessado diretamente
}

/**
 * Obtém as configurações da página.
 *
 * @param WP_REST_Request $request A requisição da API.
 * @return array|WP_Error As configurações do site ou um erro.
 */
function trinitykitcms_get_page_configs($request) {
    // Valida a API key
    $api_validation = trinitykitcms_validate_api_key($request);
    if (is_wp_error($api_validation)) {
        return $api_validation;
    }

    // Obtém as configurações diretamente
    $settings = array(
        'whatsapp_url' => get_theme_mod('whatsapp_url', ''),
        'frontend_app_url' => get_theme_mod('frontend_app_url', ''),
        'github_user' => get_theme_mod('github_user', ''),
        'github_repo' => get_theme_mod('github_repo', ''),
        'github_token' => get_theme_mod('github_token', ''),
        'google_analytics_id' => get_theme_mod('google_analytics_id', 'G-XXXXXXX'),
    );

    // Obtém informações do site
    $site_configs = array(
        'site_name' => get_bloginfo('name'),
        'site_description' => get_bloginfo('description'),
        'whatsapp_url' => $settings['whatsapp_url'],
        'google_analytics_id' => $settings['google_analytics_id'],
        'github_user' => $settings['github_user'],
        'github_repo' => $settings['github_repo'],
        'github_token' => $settings['github_token'],
        'frontend_app_url' => $settings['frontend_app_url'],
    );

    return array(
        'success' => true,
        'data' => $site_configs
    );
}

/**
 * Registra os endpoints da API para configurações da página.
 */
function trinitykitcms_register_page_config_endpoints() {
    register_rest_route('trinitykitcms-api/v1', '/configs', array(
        array(
            'methods' => 'GET',
            'callback' => 'trinitykitcms_get_page_configs',
            'permission_callback' => '__return_true' // Permite acesso público, mas requer API key
        )
    ));
}
add_action('rest_api_init', 'trinitykitcms_register_page_config_endpoints', 10);