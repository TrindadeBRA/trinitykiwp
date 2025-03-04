<?php

// Definir constante do diretório do plugin
define('THEME_DIR', plugin_dir_path(__FILE__));

// Configurações
require_once THEME_DIR . 'configs.php';

// Includes
require_once THEME_DIR . 'includes/apikey.php';
require_once THEME_DIR . 'includes/settings.php';
require_once THEME_DIR . 'includes/cpt/cpt-products.php';
require_once THEME_DIR . 'includes/cpt/cpt-contacts.php';
require_once THEME_DIR . 'includes/scf/scf-segments.php';
require_once THEME_DIR . 'includes/scf/scf-products.php';
require_once THEME_DIR . 'includes/scf/scf-contacts.php';
require_once THEME_DIR . 'includes/swagger/swagger-page.php';

// Endpoints
require_once THEME_DIR . 'endpoints/api-configs.php';
require_once THEME_DIR . 'endpoints/api-products.php';
require_once THEME_DIR . 'endpoints/api-segment.php';
require_once THEME_DIR . 'endpoints/api-contacts.php';
require_once THEME_DIR . 'endpoints/api-posts-slugs.php';

// Impede acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Configuração básica do tema
function trinitykitcms_setup() {
    add_theme_support('title-tag'); // Permite que o WP gerencie o título da página
    add_theme_support('post-thumbnails'); // Ativa suporte a imagens destacadas
    add_theme_support('custom-logo'); // Permite upload de logo personalizado
}

// Hook para executar a configuração do tema
add_action('after_setup_theme', 'trinitykitcms_setup');

// Configuração de logs para debug do tema
ini_set('error_log', get_template_directory() . '/debug.log');


// Verifica se o plugin Secure Custom Fields está ativo.
add_action('admin_init', 'check_secure_custom_fields');
function check_secure_custom_fields() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
    
    // Verifica se o plugin Secure Custom Fields está ativo.
    // Altere o caminho para refletir a estrutura do plugin, se necessário.
    if ( ! is_plugin_active( 'secure-custom-fields/secure-custom-fields.php' ) ) {
        add_action( 'admin_notices', 'secure_custom_fields_warning' );
    }
}

function secure_custom_fields_warning() {
    echo '<div class="notice notice-error">
            <p><strong>Warning:</strong> The <em>Secure Custom Fields</em> plugin is required for the full functionality of this theme. Please <a href="/wp-admin/plugin-install.php?s=Secure%2520Custom%2520Fields&tab=search&type=term" target="_blank">install and activate the plugin</a>.</p>
          </div>';
}

// Desabilita o Gutenberg e força o uso do editor clássico
function disable_gutenberg_editor() {
    return false;
}
add_filter('use_block_editor_for_post', 'disable_gutenberg_editor', 10);
add_filter('use_block_editor_for_post_type', 'disable_gutenberg_editor', 10);
