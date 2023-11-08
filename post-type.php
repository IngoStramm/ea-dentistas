<?php

/*
	 * Portfolio - Custom Post Type for portfolio projects
	 */
$portfolio = new EA_DENTISTAS_Post_Type(
    'Dentista', // Nome (Singular) do Post Type.
    'dentista' // Slug do Post Type.;
);

$portfolio->set_labels(
    array(
        'name'               => 'Dentista',
        'singular_name'      => 'Dentista',
        'menu_name'          => 'Dentistas',
        'name_admin_bar'     => 'Dentista',
        'add_new'            => 'Adicionar Dentista',
        'add_new_item'       => 'Adicionar Novo Dentista',
        'new_item'           => 'Novo Dentista',
        'edit_item'          => 'Editar Dentista',
        'view_item'          => 'Visualizar Dentista',
        'all_items'          => 'Todos os Dentistas',
        'search_items'       => 'Pesquisar Dentistas',
        'parent_item_colon'  => 'Dentistas Pai',
        'not_found'          => 'Nenhum Dentista encontrado',
        'not_found_in_trash' => 'Nenhum Dentista encontrado na lixeira.',
    )
);

$portfolio->set_arguments(
    array(
        'supports'             => array('title'),
        'menu_icon'         => 'dashicons-id',
        'show_in_nav_menus' => true
    )
);
