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

// Incluir arquivos necessários
require_once THEME_DIR . 'includes/apikey/apikey.php';
require_once THEME_DIR . 'includes/settings/settings.php';

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
        <h1 style="margin-bottom: 30px; font-size: 24px; font-weight: bold;">TrinityKitCMS</h1>

        <!-- Bloco de documentação da API -->
        <div style="margin-bottom: 50px;">
            <h2>Documentação da API</h2>
            <p>Acesse a documentação da API para entender como usar as APIs do TrinityKitCMS.</p>
            <a href="<?php echo esc_url($swagger_url); ?>" target="_blank" class="button button-secondary">
                Abrir Documentação
            </a>
        </div>

        <!-- Bloco de configuração da API Key -->
        <div style="margin-bottom: 50px;">
            <h2>API Key</h2>
            <p>A API Key é necessária para autenticação das requisições à API feitas pelo frontend.</p>
            <p>Use a seguinte API Key para autenticação:</p>
            <input type="text" value="<?php echo esc_attr($api_key); ?>" readonly style="width: 100%; max-width: 400px;" />
            <button onclick="copyApiKey()" class="button button-primary">Copiar API Key</button>

            <h3 style="margin-top: 20px;">URL Base da API:</h3>
            <input type="text" value="<?php echo esc_attr($api_base_url); ?>" readonly style="width: 100%; max-width: 400px;" />
            <button onclick="copyApiUrl()" class="button button-primary">Copiar URL da API</button>
        </div>

        <!-- Novo bloco de configurações -->
        <div style="margin-bottom: 50px;">
            <h2>Configurações do Site</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('trinitykitcms_settings');
                ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">WhatsApp URL</th>
                        <td>
                            <input type="url" name="trinitykitcms_whatsapp_url" value="<?php echo esc_attr(get_option('trinitykitcms_whatsapp_url')); ?>" class="regular-text">
                            <p class="description">Entre com a URL do WhatsApp. Esta URL vai ser inserida no botão "Contato →" no menu do frontend.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Frontend URL</th>
                        <td>
                            <input type="url" name="trinitykitcms_frontend_app_url" value="<?php echo esc_attr(get_option('trinitykitcms_frontend_app_url')); ?>" class="regular-text">
                            <p class="description">URL da aplicação frontend. Esta URL vai ser usada para o SEO do frontend e para permitir as requisições do tipo POST apenas deste dominio.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Github User</th>
                        <td>
                            <input type="text" name="trinitykitcms_github_user" value="<?php echo esc_attr(get_option('trinitykitcms_github_user')); ?>" class="regular-text">
                            <p class="description">Seu usuário do Github. Este campo é utilizado para o CI/CD do projeto.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Github Repo Name</th>
                        <td>
                            <input type="text" name="trinitykitcms_github_repo" value="<?php echo esc_attr(get_option('trinitykitcms_github_repo')); ?>" class="regular-text">
                            <p class="description">Slug do nome do projeto no repositório do GitHub. Este campo é utilizado para o CI/CD do projeto.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Github Token</th>
                        <td>
                            <input type="password" name="trinitykitcms_github_token" value="<?php echo esc_attr(get_option('trinitykitcms_github_token')); ?>" class="regular-text">
                            <p class="description">Token pessoal de acesso ao github. Pode ser gerado em: https://github.com/settings/tokens. Este campo é utilizado para o CI/CD do projeto.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Google Analytics ID</th>
                        <td>
                            <input type="text" name="trinitykitcms_google_analytics_id" value="<?php echo esc_attr(get_option('trinitykitcms_google_analytics_id', 'G-XXXXXXX')); ?>" class="regular-text">
                            <p class="description">Entre com o seu Google Analytics ID. Ex. G-XXXXXXX.</p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button('Salvar Configurações'); ?>
            </form>
        </div>
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
<?php
}

// Registrar as configurações
function trinitykitcms_register_settings() {
    register_setting('trinitykitcms_settings', 'trinitykitcms_whatsapp_url', 'sanitize_url');
    register_setting('trinitykitcms_settings', 'trinitykitcms_frontend_app_url', 'sanitize_url');
    register_setting('trinitykitcms_settings', 'trinitykitcms_github_user', 'sanitize_text_field');
    register_setting('trinitykitcms_settings', 'trinitykitcms_github_repo', 'sanitize_text_field');
    register_setting('trinitykitcms_settings', 'trinitykitcms_github_token', 'sanitize_text_field');
    register_setting('trinitykitcms_settings', 'trinitykitcms_google_analytics_id', 'sanitize_text_field');
}
add_action('admin_init', 'trinitykitcms_register_settings');

