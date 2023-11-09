<?php

function ea_lista_ufs()
{
    $ufs = array(
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        'AP' => 'Amapá',
        'AM' => 'Amazonas',
        'BA' => 'Bahia',
        'CE' => 'Ceará',
        'DF' => 'Distrito Federal',
        'ES' => 'Espírito Santo',
        'GO' => 'Goiás',
        'MA' => 'Maranhão',
        'MT' => 'Mato Grosso',
        'MS' => 'Mato Grosso do Sul',
        'MG' => 'Minas Gerais',
        'PA' => 'Pará',
        'PB' => 'Paraíba',
        'PR' => 'Paraná',
        'PE' => 'Pernambuco',
        'PI' => 'Piauí',
        'RJ' => 'Rio de Janeiro',
        'RN' => 'Rio Grande do Norte',
        'RS' => 'Rio Grande do Sul',
        'RO' => 'Rondônia',
        'RR' => 'Roraima',
        'SC' => 'Santa Catarina',
        'SP' => 'São Paulo',
        'SE' => 'Sergipe',
        'TO' => 'Tocantins'
    );
    return $ufs;
}

function ea_dentistas_endereco_completo($endereco, $numero, $bairro, $cep, $cidade, $estado)
{
    $address = rtrim(trim($endereco), ',')  . ', ' . trim($numero) . ' - ' . trim($bairro) . ' - ' . trim($cep) . ' - ' . trim($cidade) . '/' . trim($estado);
    return $address;
}

function ea_dentistas_telefone_contato($ddd1 = null, $fone1 = null, $ddd2 = null, $fone2 = null)
{
    $telefone = null;
    if (trim($fone1)) {
        $telefone = trim($ddd1) . trim($fone1);
    } elseif ($fone2) {
        $telefone = trim($ddd2) . trim($fone2);
    }
    return $telefone;
}

function ea_dentistas_fetch_listagem_original()
{
    $json_url = ea_dentistas_get_option('json_url');
    $api_user = ea_dentistas_get_option('api_user');
    $api_pass = ea_dentistas_get_option('api_pass');

    if (!$json_url && !$api_user && !$api_pass) {
        return __('Não foi possível acessar os dados dos dentistas', 'ea-dentistas');
    }
    // fix para SSL não reconhecido
    // Usar apenas em ambiente de desenvolvimento
    $auth = base64_encode("$api_user:$api_pass");
    $context = stream_context_create([
        "http" => [
            "header" => "Authorization: Basic $auth"
        ],
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ]
    ]);
    // $stream_opts = [
    //     "ssl" => [
    //         "verify_peer" => false,
    //         "verify_peer_name" => false,
    //     ]
    // ];
    $json = file_get_contents($json_url, false, $context);
    // $json = file_get_contents($json_url, false, stream_context_create($stream_opts));

    if ($json === false)
        return;

    // ea_dentistas_debug(json_last_error());
    $json = mb_convert_encoding($json, 'UTF-8');
    $json_data = json_decode($json, true);

    $listagem = [];

    $geocode_key = ea_dentistas_get_option('geocode_key');

    if (!$geocode_key) {
        return __('Geocode Key não definida', 'ea-dentistas');
    }

    foreach ($json_data['items'] as $item) {
        if (trim($item['EXCLUIDO']) !== 'N' || !trim($item['ENDERECO']) || !trim($item['CEP']) || !trim($item['MUNICIPIO']) || !trim($item['ESTADO'])) {
            continue;
        }
        $telefone = null;
        if (trim($item['TEL'])) {
            $telefone = trim($item['DDD']) . trim($item['TEL']);
        } elseif ($item['TEL2']) {
            $telefone = trim($item['DDD2']) . trim($item['TEL2']);
        }
        if (!$telefone) {
            continue;
        }
        $address = trim($item['ENDERECO'])  . ' - ' . trim($item['BAIRRO']) . ' - ' . trim($item['CEP']) . ' - ' . trim($item['MUNICIPIO']) . '/' . trim($item['ESTADO']);
        $address_encoded = urlencode($address);


        // $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key=' . $geocode_key . '&address=' . $address_encoded . '&sensor=false');

        // $output = json_decode($geocode);
        // if (isset($output->error_message)) {
        //     ea_dentistas_debug($output->error_message);
        //     ea_dentistas_debug($output->status);
        //     return;
        // }

        // $lat = $output->results[0]->geometry->location->lat;
        // $long = $output->results[0]->geometry->location->lng;
        $listagem[] = [
            'codigo' => trim($item['CODIGO']),
            'nome_fantasia' => trim($item['FANTASIA']),
            'cep' => trim($item['CEP']),
            'endereco' => $address,
            'telefone' => $telefone,
            'cro' => trim($item['CRO']),
            // 'lat' => $lat,
            // 'long' => $long,
            'destaque' => trim($item['DESTAQUE']),
            'estado' => trim($item['ESTADO'])
        ];
    }

    return $listagem;
}

function ea_dentistas_get_listagem_api()
{
    $json_url = ea_dentistas_get_option('json_url');
    $api_user = ea_dentistas_get_option('api_user');
    $api_pass = ea_dentistas_get_option('api_pass');

    if (!$json_url && !$api_user && !$api_pass) {
        return __('A configuração de conexão com a API está incompleta ou ausente.', 'ea-dentistas');
    }
    // fix para SSL não reconhecido
    // Usar apenas em ambiente de desenvolvimento
    $auth = base64_encode("$api_user:$api_pass");
    $context = stream_context_create([
        "http" => [
            "header" => "Authorization: Basic $auth"
        ],
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ]
    ]);
    $stream_opts = [
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ]
    ];
    // $json = file_get_contents($json_url); // api-sample.json
    $json = file_get_contents($json_url, false, $context);
    // $json = file_get_contents($json_url, false, stream_context_create($stream_opts));

    if ($json === false)
        return;

    // ea_dentistas_debug(json_last_error());
    $json = mb_convert_encoding($json, 'UTF-8');
    $json_data = json_decode($json, true);

    $listagem = [];

    $geocode_key = ea_dentistas_get_option('geocode_key');

    if (!$geocode_key) {
        return __('Geocode Key não definida', 'ea-dentistas');
    }


    foreach ($json_data['items'] as $item) {
        // ea_dentistas_debug($item['CODIGO']);
        // ea_dentistas_debug($item['ENDERECO']);
        if (
            trim($item['EXCLUIDO']) !== 'N' ||
            trim($item['BLOQUEADO']) !== 'N' ||
            !rtrim(trim($item['ENDERECO'])) ||
            !trim($item['NUMERO']) ||
            !trim($item['CEP']) ||
            !trim($item['MUNICIPIO']) ||
            !trim($item['ESTADO']) ||
            trim($item['ESTADO'] === 'EX') ||
            trim($item['BAIRRO'] === 'EX') ||
            trim($item['MUNICIPIO'] === 'ESTRANGEIRO')
        ) {
            // ea_dentistas_debug(trim($item['ESTADO']));
            // ea_dentistas_debug(trim($item['BAIRRO']));
            // ea_dentistas_debug(trim($item['MUNICIPIO']));
            // ea_dentistas_debug(trim($item['EXCLUIDO']));
            // ea_dentistas_debug(trim($item['BLOQUEADO']));
            // ea_dentistas_debug(trim($item['ENDERECO']));
            // ea_dentistas_debug(trim($item['NUMERO']));
            // ea_dentistas_debug(trim($item['CEP']));
            // ea_dentistas_debug(trim($item['MUNICIPIO']));
            // ea_dentistas_debug(trim($item['ESTADO']));
            continue;
        }

        $telefone = ea_dentistas_telefone_contato($item['DDD'], $item['TEL'], $item['DDD2'], $item['TEL2']);
        if (!$telefone) {
            // ea_dentistas_debug($telefone);
            continue;
        }

        $address = ea_dentistas_endereco_completo($item['ENDERECO'], $item['NUMERO'], $item['BAIRRO'], $item['CEP'], $item['MUNICIPIO'], $item['ESTADO']);

        // $address_encoded = urlencode($address);
        // $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key=' . $geocode_key . '&address=' . $address_encoded . '&sensor=false');

        // $output = json_decode($geocode);
        // if (isset($output->error_message)) {
        //     ea_dentistas_debug($output->error_message);
        //     ea_dentistas_debug($output->status);
        //     return;
        // }

        // $lat = $output->results[0]->geometry->location->lat;
        // $long = $output->results[0]->geometry->location->lng;
        $listagem[trim($item['CODIGO'])] = [
            'nome' => trim($item['FANTASIA']),
            'destaque' => trim($item['DESTAQUE']),
            'cro' => trim($item['CRO']),
            'codigo' => trim($item['CODIGO']),
            'endereco' => rtrim(trim($item['ENDERECO']), ','),
            'numero' => trim($item['NUMERO']),
            'bairro' => trim($item['BAIRRO']),
            'cidade' => trim($item['MUNICIPIO']),
            'estado' => trim($item['ESTADO']),
            'cep' => trim($item['CEP']),
            'ddd1' => trim($item['DDD']),
            'telefone1' => trim($item['TEL']),
            'ddd2' => trim($item['DDD2']),
            'telefone2' => trim($item['TEL2']),
            'endereco_completo' => $address,
            'telefone_contato' => $telefone,
            // 'lat' => $lat,
            // 'long' => $long,
        ];
    }

    return $listagem;
}

function ea_dentistas_get_listagem_wp()
{
    $args = array(
        'post_type'         => 'dentista',
        'post_status'       => 'publish',
        'numberposts'       => -1
    );
    $posts = get_posts($args);
    $listagem = [];
    if ($posts) {
        foreach ($posts as $post) {
            $post_id = $post->ID;
            $nome = get_the_title($post_id);
            $codigo = get_post_meta($post_id, 'ea_dentista_codigo', true);
            $destaque = get_post_meta($post_id, 'ea_dentista_destaque', true);
            $cro = get_post_meta($post_id, 'ea_dentista_cro', true);
            $endereco = get_post_meta($post_id, 'ea_dentista_endereco', true);
            $numero = get_post_meta($post_id, 'ea_dentista_numero', true);
            $bairro = get_post_meta($post_id, 'ea_dentista_bairro', true);
            $cidade = get_post_meta($post_id, 'ea_dentista_cidade', true);
            $estado = get_post_meta($post_id, 'ea_dentista_estado', true);
            $cep = get_post_meta($post_id, 'ea_dentista_cep', true);
            $ddd1 = get_post_meta($post_id, 'ea_dentista_ddd', true);
            $telefone1 = get_post_meta($post_id, 'ea_dentista_telefone', true);
            $ddd2 = get_post_meta($post_id, 'ea_dentista_ddd2', true);
            $telefone2 = get_post_meta($post_id, 'ea_dentista_telefone2', true);
            $endereco_completo = get_post_meta($post_id, 'ea_dentista_endereco_completo', true);
            $telefone_contato = get_post_meta($post_id, 'ea_dentista_telefone_contato', true);
            $lat = get_post_meta($post_id, 'ea_dentista_lat', true);
            $lng = get_post_meta($post_id, 'ea_dentista_lng', true);
            $listagem[$codigo] = array(
                'nome'                  => $nome,
                'codigo'                => $codigo,
                'post_id'               => $post_id,
                'destaque'              => $destaque,
                'cro'                   => $cro,
                'endereco'              => $endereco,
                'numero'                => $numero,
                'bairro'                => $bairro,
                'cidade'                => $cidade,
                'estado'                => $estado,
                'cep'                   => $cep,
                'ddd1'                  => $ddd1,
                'telefone1'             => $telefone1,
                'ddd2'                  => $ddd2,
                'telefone2'             => $telefone2,
                'endereco_completo'     => $endereco_completo,
                'telefone_contato'      => $telefone_contato,
                'lat'                   => $lat,
                'lng'                   => $lng,
            );
        }
    }
    wp_reset_postdata();
    return $listagem;
}

function ea_dentistas_get_listagem_original($state = null)
{
    if (false === ($listagem = get_transient('listagem'))) {
        $listagem = ea_dentistas_fetch_listagem_original();
        if (!is_array($listagem)) {
            return $listagem;
        }
        set_transient('listagem', $listagem, HOUR_IN_SECONDS);
        ea_dentistas_debug('definiu transiente');
    }

    if ($state) {
        $listagem = ea_dentistas_filter_listagem_by_state($listagem, $state);
    }

    // $latitude = -23.5904898;
    // $longitude = -46.635725;
    // $listagem = ea_dentistas_sort_by_nearest_location($listagem, $latitude, $longitude);

    return $listagem;
}

add_action('wp_ajax_ea_dentistas_start_update', 'ea_dentistas_start_update');

function ea_dentistas_start_update()
{
    $listagem_wp = ea_dentistas_get_listagem_wp();
    // $listagem_api = ea_dentistas_get_listagem_api();
    // $posts_por_lotes = (int)ea_dentistas_get_option('ea_dentistas_posts_por_lote');
    // $lotes_wp = count($listagem_wp) / $posts_por_lotes;
    $msg = 'Iniciando processo de atualização...';
    $response = array('success' => true, 'msg' => $msg);
    wp_send_json($response);
}

add_action('wp_ajax_ea_dentistas_delete_posts', 'ea_dentistas_delete_posts');

function ea_dentistas_delete_posts()
{
    // Log
    $log = [];

    // Dentistas da API
    $listagem_api = ea_dentistas_get_listagem_api();
    $error_msg = null;

    // Error caso não encontre dentistas na API
    if (!$listagem_api) {
        $error_msg = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Não foi possível se conectar com a API.', 'ea-dentistas');
    } elseif (!is_array($listagem_api)) {
        $error_msg = current_datetime()->format('d/m/Y H:i:s') . ': ' . $listagem_api;
    }

    if ($error_msg) {
        $response = array('success' => false, 'msg' => $error_msg);
        wp_send_json($response);
        return;
    }

    // Dentistas do WP
    $listagem_wp = ea_dentistas_get_listagem_wp();
    if (!$listagem_wp) {
        $log[] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Cadastro inicial', 'ea-dentistas');
    } else {
        $log[] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Iniciando atualização', 'ea-dentistas');
    }

    $deleted_post = [];
    foreach ($listagem_wp as $codigo => $dentista) {
        if (!isset($listagem_api[$codigo])) {
            $deleted_post = wp_delete_post($dentista['post_id']);
            $deleted_posts[] = $deleted_post->ID;
        }
    }

    // $index_wp++;
    $response = array('success' => true, 'deleted_posts' => $deleted_posts);
    wp_send_json($response);
    return;
}

add_action('wp_ajax_ea_dentistas_register_new_posts', 'ea_dentistas_register_new_posts');

function ea_dentistas_register_new_posts()
{
    $log = [];

    $listagem_api = ea_dentistas_get_listagem_api();
    $listagem_wp = ea_dentistas_get_listagem_wp();

    // Salva quantos novos posts foram criados
    $new_posts = [];

    // Salva posts que não conseguiram ser criados/atualizados por causa de erro
    $failed_posts = [];

    // Verifica se os dentistas na lista da api existem no WP
    foreach ($listagem_api as $codigo => $dentista) {

        # Segundo passo, cria os novos posts

        if (isset($listagem_wp[$codigo])) {
            continue;
        }
        // Se não existe no WP
        // Cria o post no WP
        $destaque_meta = $dentista['destaque'] === 'S' ? 'on' : '';
        $args = array(
            'post_title'            => $dentista['nome'],
            'post_type'             => 'dentista',
            'post_status'           => 'publish',
            'meta_input' => array(
                'ea_dentista_destaque' => $destaque_meta,
                'ea_dentista_codigo' => $codigo,
                'ea_dentista_cro' => $dentista['cro'],
                'ea_dentista_endereco' => $dentista['endereco'],
                'ea_dentista_numero' => $dentista['numero'],
                'ea_dentista_bairro' => $dentista['bairro'],
                'ea_dentista_cidade' => $dentista['cidade'],
                'ea_dentista_estado' => $dentista['estado'],
                'ea_dentista_cep' => $dentista['cep'],
                'ea_dentista_endereco_completo' => $dentista['endereco_completo'],
                'ea_dentista_ddd' => $dentista['ddd1'],
                'ea_dentista_telefone' => $dentista['telefone1'],
                'ea_dentista_ddd2' => $dentista['ddd2'],
                'ea_dentista_telefone2' => $dentista['telefone2'],
                'ea_dentista_telefone_contato' => $dentista['telefone_contato'],
            )
        );
        $novo_dentista = wp_insert_post($args, true);
        // Se ocorreu um erro ao criar o post no WP
        if (is_wp_error($novo_dentista)) {

            $failed_posts[] = $dentista['nome'];
            $log[] = current_datetime()->format('d/m/Y H:i:s') . ': ' . $novo_dentista->get_error_message();
        } else { // Se o novo dentista foi criado com sucesso no WP
            $log[] = current_datetime()->format('d/m/Y H:i:s') . ': Novo dentista cadastrado - ' . $novo_dentista;
            $new_posts[] = $novo_dentista;
        }
    }
    $response = array('success' => true, 'new_posts' => $new_posts, 'failed_posts' => $failed_posts);
    wp_send_json($response);
    return;
}

add_action('wp_ajax_ea_dentistas_update_existing_posts', 'ea_dentistas_update_existing_posts');

function ea_dentistas_update_existing_posts()
{

    $log = [];

    $listagem_api = ea_dentistas_get_listagem_api();
    $listagem_wp = ea_dentistas_get_listagem_wp();

    // Salva quantos posts que foram atualizados
    $updated_posts = [];

    // Salva posts que não conseguiram ser criados/atualizados por causa de erro
    $failed_posts = [];

    // Verifica se os dentistas na lista da api existem no WP
    foreach ($listagem_api as $codigo => $dentista) {

        if (!isset($listagem_wp[$codigo])) {
            continue;
        }

        // Se já existe o dentista no WP
        # Terceiro passo, atualiza os posts que já existem

        $updated = [];
        $post_id = $listagem_wp[$codigo]['post_id'];

        // Verifica se o título do post precisa ser atualizado
        if ($dentista['nome'] !== $listagem_wp[$codigo]['nome']) {

            $post_update = array(
                'ID'         => $post_id,
                'post_title' => $dentista['nome']
            );
            $updated_post = wp_update_post($post_update);

            if (is_wp_error($updated_post)) {
                // Se não conseguiu atualizar o nome
                $failed_posts[] = $post_id;
                $log[] = current_datetime()->format('d/m/Y H:i:s') . ': ' . $updated_post->get_error_message();
            } else {
                // Se conseguiu atualizar o nome
                $updated['nome'] = $updated_post->post_tite;
            }
        }
        $destaque_meta = $dentista['destaque'] === 'S' ? 'on' : '';

        // Verifica se os campos customizados do posts precisam ser atualizados

        $updated['destaque'] = update_post_meta($post_id, 'ea_dentista_destaque', $destaque_meta);
        $updated['cro'] = update_post_meta($post_id, 'ea_dentista_cro', $dentista['cro']);
        $updated['endereco'] = update_post_meta($post_id, 'ea_dentista_endereco', $dentista['endereco']);
        $updated['numero'] = update_post_meta($post_id, 'ea_dentista_numero', $dentista['numero']);
        $updated['bairro'] = update_post_meta($post_id, 'ea_dentista_bairro', $dentista['bairro']);
        $updated['cidade'] = update_post_meta($post_id, 'ea_dentista_cidade', $dentista['cidade']);
        $updated['estado'] = update_post_meta($post_id, 'ea_dentista_estado', $dentista['estado']);
        $updated['cep'] = update_post_meta($post_id, 'ea_dentista_cep', $dentista['cep']);
        $updated['endereco_completo'] = update_post_meta($post_id, 'ea_dentista_endereco_completo', $dentista['endereco_completo']);
        $updated['ddd1'] = update_post_meta($post_id, 'ea_dentista_ddd', $dentista['ddd1']);
        $updated['telefone1'] = update_post_meta($post_id, 'ea_dentista_telefone', $dentista['telefone1']);
        $updated['ddd2'] = update_post_meta($post_id, 'ea_dentista_ddd2', $dentista['ddd2']);
        $updated['telefone2'] = update_post_meta($post_id, 'ea_dentista_telefone2', $dentista['telefone2']);
        $updated['telefone_contato'] = update_post_meta($post_id, 'ea_dentista_telefone_contato', $dentista['telefone_contato']);

        foreach ($updated as $k => $v) {
            if ($k !== 'post_id' && $v) {
                $log[$post_id][$k] = current_datetime()->format('d/m/Y H:i:s') . ': ' . "Campo \"$k\" do post $post_id atualizado.";
            }
        }
        if (empty($log[$post_id])) {
            // Se o post não foi atualizado
            $log[$post_id] = current_datetime()->format('d/m/Y H:i:s') . ': ' . "Post $post_id não precisou ser atualizado.";
        } else {
            // Se o post foi atualizado
            $updated_posts[] = $updated['nome'];
        }
    }

    $response = array('success' => true, 'updated_posts' => $updated_posts, 'failed_posts' => $failed_posts);
    wp_send_json($response);
    return;
}

add_action('wp_ajax_ea_dentistas_update_addresses', 'ea_dentistas_update_addresses');

function ea_dentistas_update_addresses()
{
    $log = [];

    $listagem_wp = ea_dentistas_get_listagem_wp();

    // Salva quantos posts que foram atualizados
    $updated_posts = [];

    // Salva posts que não conseguiram ser criados/atualizados por causa de erro
    $failed_posts = [];

    // Verifica se os dentistas na lista da api existem no WP
    foreach ($listagem_wp as $dentista) {
        $post_id = $dentista['post_id'];
        # Quarto passo, atualiza a lat e lng

        $address = get_post_meta($post_id, 'ea_dentista_endereco_completo', true);
        if (!$address) {
            $log[$post_id] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Endereço ausente para o post:', 'ea-dentistas') . ' ' . $post_id;
            $failed_posts[] = __('Endereço ausente para o post:', 'ea-dentistas') . ' ' . $post_id;
            continue;
        }

        $codigo = get_post_meta($post_id, 'ea_dentista_codigo', true);
        if (!$codigo) {
            $log[$post_id] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Código ausente para o post:', 'ea-dentistas') . ' ' . $post_id;
            $failed_posts[] = __('Código ausente para o post:', 'ea-dentistas') . ' ' . $post_id;
            continue;
        }

        $lat = get_post_meta($post_id, 'ea_dentista_lat', true);
        $lng = get_post_meta($post_id, 'ea_dentista_lng', true);

        if ($lat && $lng) {
                continue;
        }
        
        $coordinates = ea_dentistas_get_lat_lng_from_google_by_address($address);

        $updated_metas = [];

        $updated_metas['lat'] = isset($coordinates['lat']) ? update_post_meta($post_id, 'ea_dentista_lat', $coordinates['lat']) : null;

        $updated_metas['lng'] = isset($coordinates['lng']) ? update_post_meta($post_id, 'ea_dentista_lng', $coordinates['lng']) : null;

        if ($updated_metas['lat'] || $updated_metas['lng']) {
            $updated_posts[] = $post_id;
        }

        foreach ($updated_metas as $k => $v) {
            if ($v) {
                $log[$post_id][$k] = current_datetime()->format('d/m/Y H:i:s') . ': ' . "Campo \"$k\" do post $post_id atualizado.";
            }
        }
        if (empty($log[$post_id])) {
            // Se o post não foi atualizado
            $log[$post_id] = current_datetime()->format('d/m/Y H:i:s') . ': ' . "Post $post_id não precisou ser atualizado.";
        }
    }

    $response = array('success' => true, 'updated_posts' => $updated_posts, 'failed_posts' => $failed_posts);
    wp_send_json($response);
    return;
}

function ea_dentistas_delete_lote($index_wp, $lotes, $listagem_api, $deleted_posts = [], $index_count = 0)
{
    $lote = $lotes[$index_wp];
    // if ($index_wp >= count($lote)) {
    //     return $deleted_posts;
    // }
    // ea_dentistas_debug(count($lote));
    // return ea_dentistas_debug($index_wp);
    foreach ($lote as $codigo => $dentista) {
        if (!isset($listagem_api[$codigo])) {
            $deleted_post = wp_trash_post($dentista['post_id']);
            $deleted_posts[] = $deleted_post->ID;
            // $deleted_posts[] = get_post($dentista['post_id']);
        }
    }
    return $deleted_posts;
    // $index_wp++;
    // return ea_dentistas_delete_lote($index_wp, $lotes, $listagem_api, $deleted_posts);
}

function ea_dentistas_update_listagem()
{
    $log = [];

    // Dentistas da API
    $listagem_api = ea_dentistas_get_listagem_api();

    if (!$listagem_api)
        return new WP_Error('failed-api-connect', current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Não foi possível acessar a lista de dentistas da API', 'ea-dentistas'));

    // Dentistas do WP
    $listagem_wp = ea_dentistas_get_listagem_wp();
    if (!$listagem_wp) {
        $log[] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Cadastro inicial', 'ea-dentistas');
    } else {
        $log[] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Iniciando atualização', 'ea-dentistas');
    }

    // verifica se os posts existem na lista da api
    foreach ($listagem_wp as $codigo => $dentista) {
        // Não existe na API
        if (!isset($listagem_api[$codigo])) {
            $force_delete = false;
            // apaga o post do WP
            $deleted_post = wp_delete_post($dentista['post_id'], $force_delete);
            $log[] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Dentista não consta na API, post excluído.', 'ea-dentistas') . ' - ID: ' . $dentista['post_id'] . ' - Nome: ' . $dentista['nome'] . ' Código: ' . $codigo;
            continue;
        }
    }

    // Verifica se os dentistas na lista da api existem no WP
    foreach ($listagem_api as $codigo => $dentista) {
        // Se não existe no WP
        if (!isset($listagem_wp[$codigo])) {
            // Cria o post no WP
            $destaque_meta = $dentista['destaque'] === 'S' ? 'on' : '';
            $args = array(
                'post_title'            => $dentista['nome'],
                'post_type'             => 'dentista',
                'post_status'           => 'publish',
                'meta_input' => array(
                    'ea_dentista_destaque' => $destaque_meta,
                    'ea_dentista_codigo' => $codigo,
                    'ea_dentista_cro' => $dentista['cro'],
                    'ea_dentista_endereco' => $dentista['endereco'],
                    'ea_dentista_numero' => $dentista['numero'],
                    'ea_dentista_bairro' => $dentista['bairro'],
                    'ea_dentista_cidade' => $dentista['cidade'],
                    'ea_dentista_estado' => $dentista['estado'],
                    'ea_dentista_cep' => $dentista['cep'],
                    'ea_dentista_endereco_completo' => $dentista['endereco_completo'],
                    'ea_dentista_ddd' => $dentista['ddd1'],
                    'ea_dentista_telefone' => $dentista['telefone1'],
                    'ea_dentista_ddd2' => $dentista['ddd2'],
                    'ea_dentista_telefone2' => $dentista['telefone2'],
                    'ea_dentista_telefone_contato' => $dentista['telefone_contato'],
                )
            );
            $novo_dentista = wp_insert_post($args, true);
            // Se ocorreu um erro ao criar o post no WP
            if (is_wp_error($novo_dentista)) {
                $log[] = current_datetime()->format('d/m/Y H:i:s') . ': ' . $novo_dentista->get_error_message();
            } else { // Se o novo dentista foi criado com sucesso no WP

                // Acessa a API do Google para bsucar a latitude e longitude do Dentista
                $location = ea_dentistas_get_lat_lng($dentista['endereco_completo']);
                // ea_dentistas_debug($location);

                if (isset($location['lat'])) {
                    $updated_meta = update_post_meta($novo_dentista, 'ea_dentista_lat', $location['lat']);
                    if (!$updated_meta) {
                        $log[$novo_dentista][] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Não foi possível cadastrar o campo latitude.');
                    }
                } else {
                    $log[$novo_dentista][] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Latitude não encontrada.');
                }

                if (isset($location['lng'])) {
                    $updated_meta = update_post_meta($novo_dentista, 'ea_dentista_lng', $location['lng']);
                    if (!$updated_meta) {
                        $log[$novo_dentista][] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Não foi possível cadastrar o campo longitude.');
                    }
                } else {
                    $log[$novo_dentista][] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Longitude não encontrada.');
                }
                $log[$novo_dentista][] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Novo dentista casdastrado.', 'ea-dentistas') . ', ID: ' . $novo_dentista;
            }
        } else { // Se já existe o dentista no WP
            $updated = [];
            $post_id = $listagem_wp[$codigo]['post_id'];

            if ($dentista['nome'] !== $listagem_wp[$codigo]['nome']) {
                $post_update = array(
                    'ID'         => $post_id,
                    'post_title' => $dentista['nome']
                );
                $updated_post = wp_update_post($post_update);
                if (is_wp_error($updated_post)) {
                    $log[] = current_datetime()->format('d/m/Y H:i:s') . ': ' . $updated_post->get_error_message();
                } else {
                    $log[] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Nome do dentista atualizado', 'ea-dentistas') . ', ID: ' . $updated_post;
                }
            }
            $destaque_meta = $dentista['destaque'] === 'S' ? 'on' : '';
            $updated['post_id'] = $post_id;
            $updated['destaque'] = update_post_meta($post_id, 'ea_dentista_destaque', $destaque_meta);
            $updated['cro'] = update_post_meta($post_id, 'ea_dentista_cro', $dentista['cro']);
            $updated['endereco'] = update_post_meta($post_id, 'ea_dentista_endereco', $dentista['endereco']);
            $updated['numero'] = update_post_meta($post_id, 'ea_dentista_numero', $dentista['numero']);
            $updated['bairro'] = update_post_meta($post_id, 'ea_dentista_bairro', $dentista['bairro']);
            $updated['cidade'] = update_post_meta($post_id, 'ea_dentista_cidade', $dentista['cidade']);
            $updated['estado'] = update_post_meta($post_id, 'ea_dentista_estado', $dentista['estado']);
            $updated['cep'] = update_post_meta($post_id, 'ea_dentista_cep', $dentista['cep']);
            $updated['endereco_completo'] = update_post_meta($post_id, 'ea_dentista_endereco_completo', $dentista['endereco_completo']);
            $updated['ddd1'] = update_post_meta($post_id, 'ea_dentista_ddd', $dentista['ddd1']);
            $updated['telefone1'] = update_post_meta($post_id, 'ea_dentista_telefone', $dentista['telefone1']);
            $updated['ddd2'] = update_post_meta($post_id, 'ea_dentista_ddd2', $dentista['ddd2']);
            $updated['telefone2'] = update_post_meta($post_id, 'ea_dentista_telefone2', $dentista['telefone2']);
            $updated['telefone_contato'] = update_post_meta($post_id, 'ea_dentista_telefone_contato', $dentista['telefone_contato']);

            $lat_meta = get_post_meta($post_id, 'ea_dentista_lat', true);
            $lng_meta = get_post_meta($post_id, 'ea_dentista_lng', true);

            if ($updated['endereco_completo'] || !$lat_meta || !$lng_meta) {
                $location = ea_dentistas_get_lat_lng($dentista['endereco_completo']);
                // ea_dentistas_debug($location);

                if (isset($location['lat'])) {
                    $updated['lat'] = update_post_meta($post_id, 'ea_dentista_lat', $location['lat']);
                } else {
                    $log[$post_id]['lat'] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Latitude não encontrada.', 'ea-dentistas');
                }

                if (isset($location['lng'])) {
                    $updated['lng'] = update_post_meta($post_id, 'ea_dentista_lng', $location['lng']);
                } else {
                    $log[$post_id]['lng'] = current_datetime()->format('d/m/Y H:i:s') . ': ' . __('Longitude não encontrada.', 'ea-dentistas');
                }
            }

            foreach ($updated as $k => $v) {
                if ($k !== 'post_id' && $v) {
                    $log[$post_id][$k] = current_datetime()->format('d/m/Y H:i:s') . ': ' . "Campo \"$k\" do post $post_id atualizado.";
                }
            }
            if (empty($log[$post_id])) {
                $log[$post_id] = current_datetime()->format('d/m/Y H:i:s') . ': ' . "Post $post_id não precisou ser atualizado.";
            }
        }
    }

    // if ($state) {
    //     $listagem = ea_dentistas_filter_listagem_by_state($listagem, $state);
    // }

    // $latitude = -23.5904898;
    // $longitude = -46.635725;
    // $listagem = ea_dentistas_sort_by_nearest_location($listagem, $latitude, $longitude);
    return $log;
}

function ea_dentistas_get_lat_lng_from_google_by_address($endereco_completo)
{
    $geocode_key = ea_dentistas_get_option('geocode_key');
    if (!$geocode_key) {
        return new WP_Error('incomplete_settings', __('Geocode Key não definida.', 'ea-dentistas'));
    }

    $address_encoded = urlencode($endereco_completo);
    $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key=' . $geocode_key . '&address=' . $address_encoded . '&sensor=false');

    $output = json_decode($geocode);
    if (isset($output->error_message)) {
        ea_dentistas_debug($output->error_message);
        ea_dentistas_debug($output->status);
        return;
    }

    $lat = $output->results[0]->geometry->location->lat;
    $lng = $output->results[0]->geometry->location->lng;
    return array(
        'lat' => $lat,
        'lng' => $lng
    );
}

function ea_dentistas_filter_listagem_by_state($listagem, $state)
{
    if (!$state) {
        return $listagem;
    }
    $filtered_listagem = [];
    foreach ($listagem as $item) {
        if ($item['estado'] === $state) {
            $filtered_listagem[] = $item;
        }
    }
    return $filtered_listagem;
}

// Ref @link: https://stackoverflow.com/questions/38536014/search-or-sort-multi-dimensional-array-for-closest-lat-and-lon-values

function ea_denstistas_get_distance($fromLatitude, $fromLongitude, $toLatitude, $toLongitude, $radius = 6371000)
{ // Vincenty's formula
    $fromLatitude = deg2rad($fromLatitude);
    $fromLongitude = deg2rad($fromLongitude);
    $toLatitude = deg2rad($toLatitude);
    $toLongitude = deg2rad($toLongitude);

    $deltaLongitude = abs($fromLongitude - $toLongitude);

    $centralAngle = atan2(sqrt(pow(cos($toLatitude) * sin($deltaLongitude), 2) + pow(cos($fromLatitude) * sin($toLatitude) - sin($fromLatitude) * cos($toLatitude) * cos($deltaLongitude), 2)), sin($fromLatitude) * sin($toLatitude) + cos($fromLatitude) * cos($toLatitude) * cos($deltaLongitude));

    return $radius * $centralAngle;
}

function ea_dentistas_sort_by_nearest_location($listagem, $latitude, $longitude)
{
    if ($latitude && $longitude) { // $latitude && $longitude are user's coordinates
        foreach ($listagem as &$item) {
            $item['distance'] = ea_denstistas_get_distance($latitude, $longitude, $item['lat'], $item['long']);
        }

        usort($listagem, function ($a, $b) {
            return $a['distance'] - $b['distance'];
        });
    }
    return $listagem;
}

function teste()
{
    $gmaps_key = ea_dentistas_get_option('gmaps_key');
    $geocode_key = ea_dentistas_get_option('geocode_key');
    $json_url = ea_dentistas_get_option('json_url');
    $dentistas_page_id = ea_dentistas_get_option('dentistas_page_id');
    // ea_dentistas_update_listagem();
    // ea_dentistas_debug(count(ea_dentistas_get_listagem_wp()));
    // ea_dentistas_delete_posts();
    // ea_dentistas_register_new_posts();
    // ea_dentistas_update_existing_posts();
    ea_dentistas_debug(ea_dentistas_update_addresses());
}

// add_action('wp_head', 'teste');
