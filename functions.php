<?php

// Definir constante do diretório do plugin
define('THEME_DIR', plugin_dir_path(__FILE__));

// // Incluir arquivos necessários
require_once THEME_DIR . 'includes/configs.php';
require_once THEME_DIR . 'includes/swagger/swagger-page.php';
// require_once THEME_DIR . 'includes/api.php';
// require_once THEME_DIR . 'includes/helpers.php';
// require_once THEME_DIR . 'includes/swagger-page.php';


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



ini_set('error_log', get_template_directory() . '/debug.log');

// Adicionar a página do Swagger
function trinitykitcms_add_swagger_page()
{
    if (isset($_GET['trinitykitcms_swagger_ui'])) {
        require_once THEME_DIR . 'includes/swagger/swagger-page.php';
        trinitykitcms_render_swagger_page();
    }
}
add_action('init', 'trinitykitcms_add_swagger_page');

// Adicionar rota para servir o arquivo swagger.json
function trinitykitcms_serve_swagger_json()
{
    if (isset($_GET['trinitykitcms_swagger'])) {
        header('Content-Type: application/json');
        echo file_get_contents(THEME_DIR . 'includes/swagger/swagger.json');
        exit;
    }
}
add_action('init', 'trinitykitcms_serve_swagger_json');

