<?php

add_action('acf/init', 'register_contact_fields');

function register_contact_fields() {
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_contact_form',
            'title' => 'Campos do Formulário de Contato',
            'fields' => array(
                array(
                    'key' => 'field_phone',
                    'label' => 'Telefone',
                    'name' => 'phone',
                    'type' => 'text',
                    'instructions' => 'Digite o número de telefone',
                    'placeholder' => '(00) 00000-0000',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'contact_form',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
        ));
    }
}
?>
