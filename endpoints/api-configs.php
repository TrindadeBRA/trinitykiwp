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
        return $api_validation; // Retorna o erro se a validação falhar
    }

    // Obtém as configurações
    $settings = trinitykitcms_get_settings();
    
    


    // Remove campos sensíveis/desnecessários
    unset($settings['github_token'], $settings['frontend_app_url']);

    // Obtém informações do site
    $site_configs = array(
        'site_name' => get_bloginfo('name'),
        'site_description' => get_bloginfo('description'),
        'whatsapp_url' => $settings['whatsapp_url'],
        'google_analytics_id' => $settings['google_analytics_id']
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