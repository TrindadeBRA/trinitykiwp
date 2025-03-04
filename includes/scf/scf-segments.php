<?php
// Verifica se a função do ACF existe antes de prosseguir.
if ( function_exists('acf_add_local_field_group') ) {
    // Registra o grupo de campos para a taxonomia "product_lines".
    acf_add_local_field_group(array(
        'key' => 'group_product_lines_parent',
        'title' => 'Campos personalizados para segmentos',
        'fields' => array(
            array(
                'key'         => 'field_imagens_obrigatorias',
                'label'       => 'Imagens Obrigatórias',
                'name'        => 'imagens_obrigatorias',
                'type'        => 'gallery',
                'instructions'=> 'Para os itens "Linhas de Produtos", cadastre 5 imagens obrigatórias.',
            ),
            // Você pode adicionar outros campos conforme necessário.
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'taxonomy',
                    'operator' => '==',
                    'value'    => 'product_lines',
                ),
            ),
        ),
    ));
}