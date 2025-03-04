<?php

/**
 * Plugin Name: TrinityKitCMS
 * Plugin URI: https://lucastrindade.dev
 * Description: O Plugin TrinityKitCMS integra o CrawlerX ao WordPress, permitindo a postagem automática de conteúdos gerados. Com ele, você recebe uma API Key exclusiva, um URL base e documentação completa com Swagger, facilitando o acesso e uso das APIs. Simplifique a gestão de conteúdo no seu site WordPress com esta integração prática e segura.
 * Version: 1.0.0
 * Author: Lucas Trindade
 * Author URI: https://lucastrindade.dev
 */

if (!defined('ABSPATH')) {
    exit; // Evita acesso direto ao arquivo
}

// Definir constante do diretório do plugin
define('TRINITYKITCMS_PLUGIN_DIR', plugin_dir_path(__FILE__));


// Adicionar hook de ativação
register_activation_hook(__FILE__, 'trinitykitcms_generate_api_key');

// Também adicionar uma chamada na inicialização do plugin
add_action('init', 'trinitykitcms_generate_api_key');

// Criar menu no admin
function trinitykitcms_add_admin_menu()
{
    add_menu_page(
        'TrinityKitCMS',
        'TrinityKitCMS',
        'manage_options',
        'trinitykitcms',
        'trinitykitcms_render_admin_page',
        'dashicons-admin-generic',
        20
    );
}
add_action('admin_menu', 'trinitykitcms_add_admin_menu');


// Modificar a função de renderização da página admin
function trinitykitcms_render_admin_page()
{
    $api_key = get_option('trinitykitcms_api_key', 'Não gerado');
    $swagger_url = add_query_arg('trinitykitcms_swagger_ui', '1', site_url());
    $api_base_url = site_url('/wp-json/trinitykitcms-api/v1/');
?>
    <div class="wrap">
        <h1>TrinityKitCMS</h1>
        <div style="margin-bottom: 20px;">
            <h2>API Key</h2>
            <p>Use a seguinte API Key para autenticação:</p>
            <input type="text" value="<?php echo esc_attr($api_key); ?>" readonly style="width: 100%; max-width: 400px;" />
            <button onclick="copyApiKey()" class="button button-primary">Copiar API Key</button>

            <h3 style="margin-top: 20px;">URL Base da API:</h3>
            <input type="text" value="<?php echo esc_attr($api_base_url); ?>" readonly style="width: 100%; max-width: 400px;" />
            <button onclick="copyApiUrl()" class="button button-primary">Copiar URL da API</button>

            <!-- <div style="margin-top: 30px; background: #f9f9f9; padding: 20px; border-radius: 5px; border: 1px solid #ddd;">
                <h3>Configuração das Variáveis de Ambiente</h3>
                <p>Para configurar sua aplicação principal, utilize as seguintes variáveis de ambiente:</p>
                <pre style="background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 3px;">
# API KEY TrinityKitCMS
TRINITYKITCMS_API_KEY=<?php echo esc_html($api_key); ?>

TRINITYKITCMS_API_URL=<?php echo esc_html($api_base_url); ?>
                </pre>

                <h4>Descrição das Variáveis:</h4>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <li><strong>TRINITYKITCMS_API_KEY:</strong> Chave de autenticação necessária para todas as requisições à API</li>
                    <li><strong>TRINITYKITCMS_API_URL:</strong> URL base para todos os endpoints da API</li>
                </ul>
            </div> -->
        </div>

        <div style="margin-bottom: 20px;">
            <h2>Documentação da API</h2>
            <a href="<?php echo esc_url($swagger_url); ?>" target="_blank" class="button button-secondary">
                Abrir Documentação
            </a>
        </div>

        <script>
            function copyApiKey() {
                var copyText = document.querySelectorAll('input')[0];
                copyText.select();
                document.execCommand("copy");
                alert("API Key copiada!");
            }

            function copyApiUrl() {
                var copyText = document.querySelectorAll('input')[1];
                copyText.select();
                document.execCommand("copy");
                alert("URL da API copiada!");
            }
        </script>
    </div>
<?php
}

class TrinityKitCMS_API_Security
{
    private static $instance = null;

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function validate_api_key($request)
    {
        // Verifica múltiplos locais para a API key
        $api_key = $this->get_api_key_from_request($request);

        if (empty($api_key)) {
            return new WP_Error(
                'api_key_missing',
                'API Key não fornecida',
                array('status' => 401)
            );
        }

        $stored_key = get_option('trinitykitcms_api_key');

        if (empty($stored_key)) {
            return new WP_Error(
                'api_key_not_configured',
                'API Key não configurada no sistema',
                array('status' => 500)
            );
        }

        if (!hash_equals($stored_key, $api_key)) {
            return new WP_Error(
                'invalid_api_key',
                'API Key inválida',
                array('status' => 401)
            );
        }

        return true;
    }

    private function get_api_key_from_request($request)
    {
        // Tenta obter do cabeçalho X-API-Key
        $api_key = $request->get_header('X-API-Key');

        // Se não encontrar no cabeçalho, tenta nos parâmetros
        if (empty($api_key)) {
            $api_key = $request->get_param('api_key');
        }

        return $api_key;
    }
}

// Criar a função para gerar a chave API
function trinitykitcms_generate_api_key() {
    $existing_key = get_option('trinitykitcms_api_key', '');
    
    if (empty($existing_key)) {
        $api_key = bin2hex(random_bytes(16));
        update_option('trinitykitcms_api_key', $api_key);
    }
}