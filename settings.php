<?php

function ea_dentistas_register_settings_options_submenu_for_page_post_type()
{

    /**
     * Registers options page menu item and form.
     */
    $cmb = new_cmb2_box(array(
        'id'           => 'ea_dentistas_options_submenu_page',
        'title'        => esc_html__('EA Dentistas', 'ea-dentistas'),
        'object_types' => array('options-page'),

        /*
		 * The following parameters are specific to the options-page box
		 * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
		 */

        'option_key'      => 'ea_dentistas_page_options', // The option key and admin menu page slug.
        'icon_url'        => 'dashicons-welcome-widgets-menus', // Menu icon. Only applicable if 'parent_slug' is left empty.
        // 'menu_title'      => esc_html__('Configurações', 'ea-dentistas'), // Falls back to 'title' (above).
        // 'parent_slug'     => 'options-general.php', // Make options page a submenu item of the themes menu.
        // 'capability'      => 'manage_options', // Cap required to view options-page.
        // 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
        // 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
        // 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
        // 'save_button'     => esc_html__( 'Save Theme Options', 'ea-dentistas' ), // The text for the options-page save button. Defaults to 'Save'.
        // 'disable_settings_errors' => true, // On settings pages (not options-general.php sub-pages), allows disabling.
        // 'message_cb'      => 'ea_dentistas_options_page_message_callback',
    ));

    // $cmb->add_field(array(
    //     'name'    => esc_html__('URL da Planilha em formato .csv', 'ea-dentistas'),
    //     'description' => esc_html__('Não usado mais, mantido como referência', 'ea-dentistas'),
    //     'id'      => 'planilha_url',
    //     'type'    => 'text_url',
    // ));

    $cmb->add_field(array(
        'name'    => esc_html__('URL do arquivo em formato .json com os dados dos dentistas', 'ea-dentistas'),
        'id'      => 'json_url',
        'type'    => 'text_url',
    ));

    $cmb->add_field(array(
        'name'    => esc_html__('Nome de usuário para se conectar com a API da Totvs', 'ea-dentistas'),
        'id'      => 'api_user',
        'type'    => 'text',
    ));

    $cmb->add_field(array(
        'name'    => esc_html__('Senha para se conectar com a API da Totvs', 'ea-dentistas'),
        'id'      => 'api_pass',
        'type'    => 'text',
        'attributes' => array(
            'type' => 'password',
        ),
    ));

    $cmb->add_field(array(
        'name'    => esc_html__('Google Maps Key', 'ea-dentistas'),
        'description'    => 'Acesse o <strong>Google Console</strong> para ativar a API e criar a Key: <a href="https://console.cloud.google.com/" target="_blank">clique aqui</a>.',
        'id'      => 'gmaps_key',
        'type'    => 'text',
    ));

    $cmb->add_field(array(
        'name'    => esc_html__('Geocode Key', 'ea-dentistas'),
        'description'    => 'Acesse o <strong>Google Console</strong> para ativar a API e criar a Key: <a href="https://console.cloud.google.com/" target="_blank">clique aqui</a>.',
        'id'      => 'geocode_key',
        'type'    => 'text',
    ));

    $cmb->add_field(array(
        'name'    => esc_html__('Página com a listagem de Dentistas', 'ea-dentistas'),
        'id'      => 'dentistas_page_id',
        'type'    => 'select',
        'show_option_none' => true,
        'options_cb' => function () {
            $pages = get_pages();
            $options = [];
            foreach ($pages as $page) {
                $options[$page->ID] = $page->post_title;
            }
            return $options;
        }
    ));

    $cmb->add_field(array(
        'name'    => esc_html__('Distância máxima em kilômetros para buscar um dentista.', 'ea-dentistas'),
        'id'      => 'ea_dentistas_max_distance',
        'type'    => 'text',
        'default'   => 50,
        'attributes' => array(
            'type' => 'number',
            'min'   => 1
        )
    ));
}

add_action('cmb2_admin_init', 'ea_dentistas_register_settings_options_submenu_for_page_post_type');

function ea_dentistas_register_update_options_submenu_for_page_post_type()
{

    /**
     * Registers options page menu item and form.
     */
    $cmb = new_cmb2_box(array(
        'id'           => 'ea_dentistas_update_submenu_page',
        'title'        => esc_html__('Atualização dos Dentistas', 'ea-dentistas'),
        'object_types' => array('options-page'),

        /*
		 * The following parameters are specific to the options-page box
		 * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
		 */

        'option_key'      => 'ea_dentistas_page_update', // The option key and admin menu page slug.
        // 'icon_url'        => 'dashicons-welcome-widgets-menus', // Menu icon. Only applicable if 'parent_slug' is left empty.
        // 'menu_title'      => esc_html__( 'Options', 'ea-dentistas' ), // Falls back to 'title' (above).
        'parent_slug'     => 'ea_dentistas_page_options', // Make options page a submenu item of the themes menu.
        // 'capability'      => 'manage_options', // Cap required to view options-page.
        // 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
        // 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
        // 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
        // 'save_button'     => esc_html__( 'Save Theme Options', 'ea-dentistas' ), // The text for the options-page save button. Defaults to 'Save'.
        // 'disable_settings_errors' => true, // On settings pages (not options-general.php sub-pages), allows disabling.
        // 'message_cb'      => 'ea_dentistas_options_page_message_callback',
    ));

    $cmb->add_field(array(
        'name'    => esc_html__('Atualização dos Dentistas', 'ea-dentistas'),
        'id'      => 'title_update',
        'type'    => 'title',
        'before'  => '<style>#submit-cmb{display:none;}</style>',
        'after_field'  => 'ea_dentistas_admin_update_page_html',
    ));
}

add_action('cmb2_admin_init', 'ea_dentistas_register_update_options_submenu_for_page_post_type');

function ea_dentistas_get_option($key = '', $default = false)
{
    if (function_exists('cmb2_get_option')) {
        // Use cmb2_get_option as it passes through some key filters.
        return cmb2_get_option('ea_dentistas_page_options', $key, $default);
    }

    // Fallback to get_option if CMB2 is not loaded yet.
    $opts = get_option('ea_dentistas_page_options', $default);

    $val = $default;

    if ('all' == $key) {
        $val = $opts;
    } elseif (is_array($opts) && array_key_exists($key, $opts) && false !== $opts[$key]) {
        $val = $opts[$key];
    }

    return $val;
}

function ea_dentistas_admin_update_page_html()
{
?>
    <p>Para atualizar os dados dos dentistas com a API, clique no botão abaixo.</p>
    <p>Dependendo da quantidade de registros que precisem ser criados/atualizados, este processo pode demorar. Por isso, é importante deixar esta página aberta enquanto a atualização está sendo processada.</p>
    <a id="ea-dentistas-update-btn" class="button button-primary">Atualizar dentistas</a>
    <div id="ea-dentistas-update-html"></div>
<?php
}
