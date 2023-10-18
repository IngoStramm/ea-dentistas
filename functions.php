<?php

function ea_dentistas_get_totvs_api()
{
    $username = "admin";
    $password = "Fender03";
    $remote_url = 'https://estheticalignerortho.protheus.cloudtotvs.com.br:4050/rest/api/v1/ListaClientes/List';
    $auth = base64_encode("$username:$password");

    // Create a stream
    $opts = array(
        // "ssl" => [
        //     "verify_peer" => false,
        //     "verify_peer_name" => false,
        // ],
        'http' => array(
            'method' => "GET",
            'header' => "Authorization: Basic " . $auth,
        )
    );

    $context = stream_context_create($opts);

    // Open the file using the HTTP headers set above
    $json = file_get_contents($remote_url, false, $context);
    // $json = str_replace('""', '"', $json);
    //     $json=str_replace(
    // '},

    // ]',"}

    // ]",$json);

    $data = json_decode($json, true);

    return $data;
}

function ea_dentistas_fetch_listagem()
{
    $json_url = ea_dentistas_get_option('json_url');

    if (!$json_url) {
        return __('Não foi possível acessar os dados dos dentistas', 'ea-dentistas');
    }
    // fix para SSL não reconhecido
    // Usar apenas em ambiente de desenvolvimento
    $stream_opts = [
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ]
    ];
    $json = file_get_contents($json_url, false, stream_context_create($stream_opts));

    if ($json === false)
        return;

    $json_data = json_decode($json, true);

    $listagem = [];

    $geocode_key = ea_dentistas_get_option('geocode_key');

    if (!$geocode_key) {
        return __('Geocode Key não definida', 'ea-dentistas');
    }

    foreach ($json_data as $item) {
        $address = $item['endereco']  .  ' ' . $item['numero'] . ' - ' . $item['bairro'] . ' - ' . $item['cep'] . ' - ' . $item['cidade'] . '/' . $item['uf'];
        $address_encoded = urlencode($address);


        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key=' . $geocode_key . '&address=' . $address_encoded . '&sensor=false');

        $output = json_decode($geocode);
        if (isset($output->error_message)) {
            ea_dentistas_debug($output->error_message);
            ea_dentistas_debug($output->status);
            return;
        }

        $lat = $output->results[0]->geometry->location->lat;
        $long = $output->results[0]->geometry->location->lng;
        $listagem[] = [
            'codigo' => $item['codigo'],
            'nome' => $item['nome'],
            'nome_fantasia' => $item['nome_fantasia'],
            'email' => $item['email'],
            'cpf_cnpj' => $item['cpf_cnpj'],
            'cep' => $item['cep'],
            'endereco' => $address,
            'telefone' => $item['ddd_1'] . $item['telefone_1'],
            'cro' => $item['cro'],
            'lat' => $lat,
            'long' => $long,
        ];
    }

    return $listagem;
}

function ea_dentistas_get_listagem()
{
    if (false === ($listagem = get_transient('listagem'))) {
        $listagem = ea_dentistas_fetch_listagem();
        if (!is_array($listagem)) {
            return $listagem;
        }
        set_transient('listagem', $listagem, HOUR_IN_SECONDS);
        ea_dentistas_debug('definiu transiente');
    }

    $latitude = -23.5904898;
    $longitude = -46.635725;
    $listagem = ea_dentistas_sort_by_nearest_location($listagem, $latitude, $longitude);

    return $listagem;
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
    ea_dentistas_debug(ea_dentistas_get_totvs_api());
}

// add_action('wp_head', 'teste');
