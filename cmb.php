<?php

add_action('cmb2_admin_init', 'ea_register_dentista_metabox');

function ea_register_dentista_metabox()
{
    $dentista = new_cmb2_box(array(
        'id'            => 'ea_dentista_metabox',
        'title'         => esc_html__('Opções', 'ea'),
        'object_types'  => array('dentista'), // Post type
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('Destaque', 'ea'),
        'id'         => 'ea_dentista_destaque',
        'type'       => 'checkbox',
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('Código', 'ea'),
        'id'         => 'ea_dentista_codigo',
        'type'       => 'text_medium',
        'attributes' => array(
            'required' => true
        )
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('CRO', 'ea'),
        'id'         => 'ea_dentista_cro',
        'type'       => 'text_small', 
        'attributes' => array(
            'required' => true
        )
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('Endereço', 'ea'),
        'id'         => 'ea_dentista_endereco',
        'type'       => 'text',
        'attributes' => array(
            'required' => true
        )
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('Número', 'ea'),
        'id'         => 'ea_dentista_numero',
        'type'       => 'text_small',
        'attributes' => array(
            'required' => true
        )
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('Complemento', 'ea'),
        'id'         => 'ea_dentista_complemento',
        'type'       => 'text',
        'attributes' => array(
            'required' => true
        )
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('Bairro', 'ea'),
        'id'         => 'ea_dentista_bairro',
        'type'       => 'text_medium',
        'attributes' => array(
            'required' => true
        )
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('Cidade', 'ea'),
        'id'         => 'ea_dentista_cidade',
        'type'       => 'text',
        'attributes' => array(
            'required' => true
        )
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('Estado', 'ea'),
        'id'         => 'ea_dentista_estado',
        'type'       => 'select',
        'options'   => function () {
            $placeholder = array('' => 'Selecione um Estado');
            $ufs = ea_lista_ufs();
            $return = $placeholder + $ufs;
            return $return;
        },'attributes' => array(
            'required' => true
        )
    ));


    $dentista->add_field(array(
        'name'       => esc_html__('CEP', 'ea'),
        'id'         => 'ea_dentista_cep',
        'type'       => 'text_medium', 
        'attributes' => array(
            'required' => true
        )
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('Endereço completo', 'ea'),
        'id'         => 'ea_dentista_endereco_completo',
        'type'       => 'text', 
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('DDD', 'ea'),
        'id'         => 'ea_dentista_ddd',
        'type'       => 'text_small', 
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('Telefone 1', 'ea'),
        'id'         => 'ea_dentista_telefone',
        'type'       => 'text_medium',
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('DDD2', 'ea'),
        'id'         => 'ea_dentista_ddd2',
        'type'       => 'text_small',
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('Telefone 2', 'ea'),
        'id'         => 'ea_dentista_telefone2',
        'type'       => 'text_medium',
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('Telefone de contato', 'ea'),
        'id'         => 'ea_dentista_telefone_contato',
        'type'       => 'text_medium',
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('Latitude', 'ea'),
        'id'         => 'ea_dentista_lat',
        'type'       => 'text_small',
    ));

    $dentista->add_field(array(
        'name'       => esc_html__('Longitude', 'ea'),
        'id'         => 'ea_dentista_lng',
        'type'       => 'text_small',
    ));

}
