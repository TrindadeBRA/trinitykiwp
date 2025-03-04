<?php

function trinitykitcms_save_settings() {
    // Verifica se o usuário tem permissão
    if (!current_user_can('manage_options')) {
        return new WP_Error('forbidden', 'Você não tem permissão para realizar esta ação.', array('status' => 403));
    }

    // Lista de campos permitidos
    $allowed_fields = array(
        'whatsapp_url',
        'frontend_app_url',
        'github_user',
        'github_repo',
        'github_token',
        'google_analytics_id'
    );

    // Processa cada campo
    foreach ($allowed_fields as $field) {
        if (isset($_POST[$field])) {
            $value = sanitize_text_field($_POST[$field]);
            
            // Validação específica para URLs
            if ($field === 'whatsapp_url' || $field === 'frontend_app_url') {
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                    return new WP_Error(
                        'invalid_url',
                        'URL inválida para o campo ' . $field,
                        array('status' => 400)
                    );
                }
            }
            
            // Salva a configuração
            set_theme_mod($field, $value);
        }
    }

    return array(
        'success' => true,
        'message' => 'Configurações salvas com sucesso!'
    );
}

function trinitykitcms_get_settings() {
    // Verifica se o usuário tem permissão
    if (!current_user_can('manage_options')) {
        return new WP_Error('forbidden', 'Você não tem permissão para realizar esta ação.', array('status' => 403));
    }

    // Retorna todas as configurações
    return array(
        'whatsapp_url' => get_theme_mod('whatsapp_url', ''),
        'frontend_app_url' => get_theme_mod('frontend_app_url', ''),
        'github_user' => get_theme_mod('github_user', ''),
        'github_repo' => get_theme_mod('github_repo', ''),
        'github_token' => get_theme_mod('github_token', ''),
        'google_analytics_id' => get_theme_mod('google_analytics_id', 'G-XXXXXXX')
    );
}

// Registrar endpoints da API
function trinitykitcms_register_settings_endpoints() {
    register_rest_route('trinitykitcms-api/v1', '/settings', array(
        array(
            'methods' => 'POST',
            'callback' => 'trinitykitcms_save_settings',
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ),
        array(
            'methods' => 'GET',
            'callback' => 'trinitykitcms_get_settings',
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        )
    ));
}
add_action('rest_api_init', 'trinitykitcms_register_settings_endpoints');
