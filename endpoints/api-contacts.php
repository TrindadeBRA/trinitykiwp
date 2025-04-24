<?php

/**
 * Registra as rotas da API para o formulário de contato
 */
add_action('rest_api_init', function () {
    register_rest_route('trinitykitcms-api/v1', '/contact-form/submit', array(
        'methods'  => 'POST',
        'callback' => 'contact_form_submit',
        'permission_callback' => '__return_true'
    ));
});

/**
 * Processa o envio do formulário de contato
 * 
 * @param WP_REST_Request $request
 * @return WP_REST_Response|WP_Error
 */
function contact_form_submit($request) {
    
    $params = $request->get_params();

    // Extrai e sanitiza os parâmetros
    $name = isset($params['name']) ? sanitize_text_field($params['name']) : 'N/A';
    $email = isset($params['email']) ? sanitize_email($params['email']) : '';
    $phone = isset($params['phone']) ? sanitize_text_field($params['phone']) : 'N/A';
    $message = isset($params['message']) ? sanitize_textarea_field($params['message']) : 'N/A';
    $tag = isset($params['tag']) ? sanitize_text_field($params['tag']) : '';

    // Validação dos campos obrigatórios
    if (empty($email)) {
        return new WP_Error('invalid_email_data', __('Email inválido'), array('status' => 400));
    }
    
    if (empty($tag)) {
        return new WP_Error('invalid_tag_data', __('Tag não pode estar vazia'), array('status' => 400));
    }

    // Handle file upload
    $attachment_id = null;
    if (!empty($_FILES['attachment'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $file = $_FILES['attachment'];
        $allowed_types = array('pdf', 'xls', 'xlsx', 'csv', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp');
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_types)) {
            return new WP_Error('invalid_file_type', __('Tipo de arquivo não permitido'), array('status' => 400));
        }

        if ($file['size'] > 10 * 1024 * 1024) { // 10MB limit
            return new WP_Error('file_too_large', __('Arquivo muito grande. Tamanho máximo permitido: 10MB'), array('status' => 400));
        }

        $attachment_id = media_handle_upload('attachment', 0);
        if (is_wp_error($attachment_id)) {
            return new WP_Error('file_upload_failed', __('Falha ao fazer upload do arquivo'), array('status' => 500));
        }
    }

    // Define o título do post
    if (!empty($name) && !empty($email)) {
        $post_title = $name . ' - ' . $email;
    } else {
        $post_title = $email;
    }
    

    // Cria o post
    $post_id = wp_insert_post(array(
        'post_title'   => $post_title,
        'post_content' => $message,
        'post_status'  => 'publish',
        'post_type'    => 'contact_form',
    ));

    // Atualiza os campos personalizados
    update_field('email', $email, $post_id);
    update_field('name', $name, $post_id);
    update_field('phone', $phone, $post_id);

    // Update attachment field if file was uploaded
    if ($attachment_id) {
        update_field('attachment', $attachment_id, $post_id);
    }

    // Adiciona a tag ao post
    if (!empty($tag)) {
        wp_set_post_tags($post_id, $tag, true);
    }

    // Caso a tag seja "Solicitar Amostra" ou "Literatura Técnica" enviar email
    if ($tag == 'Solicitar Amostra' || $tag == 'Literatura Técnica') {
        $to = array(
            'trindadebra@gmail.com',
            'marketing@tiken.com.br',
            'laboratorio@tiken.com.br'
        );
        $subject = $tag . ' - ' . $name;
        $body = $message;

        wp_mail($to, $subject, $body);
    }
    

    // Retorna a resposta
    if ($post_id) {
        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'Formulário enviado com sucesso'
        ), 200);
    } else {
        return new WP_Error(
            'submission_failed', 
            __('Falha ao enviar formulário'), 
            array('status' => 500)
        );
    }
}