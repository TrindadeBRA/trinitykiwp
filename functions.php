<?php

// Definir constante do diretório do plugin
define('THEME_DIR', plugin_dir_path(__FILE__));

// Incluir arquivos necessários
require_once THEME_DIR . 'includes/configs.php';
require_once THEME_DIR . 'includes/apikey/apikey.php';
require_once THEME_DIR . 'includes/swagger/swagger-page.php';
require_once THEME_DIR . 'includes/settings/settings.php';
require_once THEME_DIR . 'endpoints/api-configs.php';

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

