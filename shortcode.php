<?php

function ea_dentistas_get_xlxs()
{
    // https://docs.google.com/spreadsheets/d/1BznEiNSlsB5DvGHuKWIqVgHmuXio1CRU/edit?usp=sharing&ouid=114578232595558687838&rtpof=true&sd=true

    // https://docs.google.com/spreadsheets/d/e/2PACX-1vR2hsiX_AvMsTQfCA0pdSTQAOvb7RBIg1lcbla-e-I6b7fvNe6qPC82PQqf3yeS2w/pub?gid=1744666743&single=true&output=csv

    // https://encontre-um-dentista.local/wp-content/uploads/2023/08/ea-exemplo-cadastro.xlsx

    $planilha_url = ea_dentistas_get_option('planilha_url');

    if (!$planilha_url) {
        return __('Arquivo não encontrado', 'ea_dentistas');
    }

    $row = 1;
    $output = '';
    $headers = [];
    $rows = [];
    if (($handle = fopen($planilha_url, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            $new_line = [];
            $num = count($data);
            $output .= "<p> $num fields in line $row: <br /></p>\n";
            for ($c = 0; $c < $num; $c++) {
                $output .= $data[$c] . "<br />\n";
                if ($row === 4) {
                    $headers[] = $data[$c];
                } else if ($row > 4) {
                    $new_line[$headers[$c]] = $data[$c];
                }
            }
            $rows[] = $new_line;
            $row++;
        }
        fclose($handle);
    }
    // ea_dentistas_debug($headers);
    // ea_dentistas_debug($rows);
    return '<div id="map" style="height: 500px;"></div>';
}
add_shortcode('ea_xlxs', 'ea_dentistas_get_xlxs');

function ea_dentistas_listagem()
{
    $estado = isset($_POST['estado']) ? $_POST['estado'] : null;
    $cidade = isset($_POST['cidade']) ? $_POST['cidade'] : null;
    $output = '';
    // $listagem = ea_dentistas_get_listagem_original($estado);
    // if (!is_array($listagem)) {
    //     return $listagem;
    // }
    $output .= '<section class="listagem-wrapper">';
    $output .=      '<div class="listagem-col">';
    $output .=      '<div id="lista-dentistas">';

    $output .=      '<input type="text" id="pesquisar-por-cidade" placeholder="' . __('Pesquisar por cidade', 'ea-dentista') . '" />';
    $output .=      '<input type="text" id="pesquisar-por-estado" placeholder="' . __('Pesquisar por Estado', 'ea-dentista') . '" />';

    $output .=          '<ul id="listagem-items" class="list listagem-items pagination-list" aria-live="polite">';
    $i = 0;
    // ea_dentistas_debug($listagem);
    // foreach ($listagem as $item) {
    //     $output .=              '<li class="listagem-item">';
    //     $output .=                  '<h4>' . $item['nome_fantasia'] . '</h4>';
    //     $output .=                  '<address>';
    //     $output .=                      '<p>' . $item['endereco'] . '</p>';
    //     $output .=                      '<ul>';
    //     $output .=                          '<li>' . __('Contato', 'ea-dentistas') . ' ' . $item['telefone'] . '</li>';
    //     // $output .=                          '<li>' . __('E-mail', 'ea-dentistas') . ' ' . $item['email'] . '</li>';
    //     $output .=                      '</ul>';
    //     $output .=                  '</address>';
    //     $output .=                  '<button class="listagem-item-btn" data-markerid="' . $i . '">' . __('Visualizar', 'ea-dentistas') . '</button>';
    //     $output .=              '</li>';
    //     $i++;
    // }
    $output .=          '</ul>';

    // $output .=      '<nav class="pagination-container">
    //                     <input type="hidden" name="current-page" value="1" />
    //                     <button class="pagination-prev pagination-arrow" aria-label="' . __('Voltar', 'ea-dentistas') . '" title="' . __('Voltar', 'ea-dentistas') . '">&lt;</button>
    //                     <ul class="pagination-numbers"></ul>
    //                     <button class="pagination-next pagination-arrow" aria-label="' . __('Avançar', 'ea-dentistas') . '" title="' . __('Avançar', 'ea-dentistas') . '">&gt;</button>
    //                 </nav>';

    $output .= '<ul class="pagination"></ul>';

    $output .=      '</div>';
    $output .=      '</div>';
    $output .=      '<div class="listagem-col">';
    $output .=          '<div id="map"></div>';
    $output .=      '</div>';
    $output .= '</section>';
    return $output;
}

add_shortcode('ea_listagem', 'ea_dentistas_listagem');

function ea_dentistas_autocomplete()
{
    $dentistas_page_id = ea_dentistas_get_option('dentistas_page_id');
    if (!$dentistas_page_id) {
        return __('Página com a listagem de Dentistas não definida.', 'ea-dentistas');
    }
    $action_url = get_the_permalink($dentistas_page_id);
    $output = '';
    $output .= '<form class="ea-autocomplete-form" action="' . $action_url . '" method="post">';
    $output .=      '<input id="autocomplete" name="autocomplete" placeholder="Digite um endereço" type="text" />';
    $output .=      '<input name="lat" type="text" />';
    $output .=      '<input name="lng" type="text" />';
    $output .=      '<input name="estado" type="text" />';
    $output .=      '<input name="cidade" type="text" />';
    $output .=      '<button>' . __('Enviar', 'ea-dentistas') . '</button>';
    $output .= '</form>';
    return $output;
}

add_shortcode('ea_autocomplete', 'ea_dentistas_autocomplete');
