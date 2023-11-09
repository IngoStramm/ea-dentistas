document.addEventListener('DOMContentLoaded', function () {

    function ea_delete_dentistas(btn) {
        listaEtapa1 = document.getElementById('ea-dentista-lista-etapa-1');
        if (typeof listaEtapa1 === undefined || !listaEtapa1) {
            return;
        }
        const listaItemProcessing = document.createElement('li');
        listaItemProcessing.innerText = 'Etapa 1: apagar os dentistas que não existem mais na API.';
        listaEtapa1.append(listaItemProcessing);

        const listaExecutandoEtapa = document.createElement('li');
        listaExecutandoEtapa.innerText = 'Executando Etapa 1...';
        listaEtapa1.append(listaExecutandoEtapa);

        const listaItemDeletedPosts = document.createElement('li');
        listaItemDeletedPosts.id = 'ea-dentistas-deleted-posts';
        listaEtapa1.append(listaItemDeletedPosts);

        const listaItemEnd = document.createElement('li');
        listaEtapa1.append(listaItemEnd);

        const action = 'ea_dentistas_delete_posts';
        const xhr = new XMLHttpRequest();
        xhr.responseType = 'json';
        xhr.open('POST', ajax_object.ajax_url + '?action=' + action);
        xhr.onload = function () {
            const response = xhr.response;
            if (xhr.status === 200) {
                if (!response.success) {
                    console.log('success', response.success);
                    console.log('msg', response.msg);
                    listaItemDeletedPosts.innerText = response.msg;
                    delete btn.dataset.disabled;
                    btn.innerText = 'Atualizar dentistas';

                    return;
                }
                // if (index_wp < lotes_wp) {
                //     console.log('success', response.success);
                //     console.log('index_wp', response.index_wp);
                //     console.log('posts already deleted', postsAlreadyDeleted);
                //     console.log('deleted posts', response.deleted_posts);
                //     // console.log('posts count deleted via ajax', response.deleted_posts.length);
                //     // console.log('posts that should be deleted', response.posts_that_should_be_deleted);
                //     // console.log('posts not deleted', response.posts_not_deleted);
                console.log('response.deleted_posts', response.deleted_posts);
                const count = typeof response.deleted_posts === undefined || !response.deleted_posts ? 0 : response.deleted_posts.length;
                console.log('count', count);
                listaItemDeletedPosts.innerText = `Dentistas apagados (${count}).`;
                //     return ea_delete_dentistas();
                // } else {
                listaItemEnd.innerText = 'Etapa 1 concluída.';
                // }
                ea_novos_dentistas(btn);
            } else {
                listaItemDeletedPosts.innerText = 'Ocorreu um erro de conexão, tente novamente.';
                listaItemUpdatedPosts.style.color = '#ff0000';
                delete btn.dataset.disabled;
                btn.innerText = 'Atualizar dentistas';
            }
        };
        xhr.send();

    }

    function ea_novos_dentistas(btn) {
        listaEtapa2 = document.getElementById('ea-dentista-lista-etapa-2');
        if (typeof listaEtapa2 === undefined || !listaEtapa2) {
            return;
        }
        const listaItemProcessing = document.createElement('li');
        listaItemProcessing.innerText = 'Etapa 2: cadastrar os novos dentistas.';
        listaEtapa2.append(listaItemProcessing);

        const listaExecutandoEtapa = document.createElement('li');
        listaExecutandoEtapa.innerText = 'Executando Etapa 2...';
        listaEtapa2.append(listaExecutandoEtapa);

        const listaItemDeletedPosts = document.createElement('li');
        listaItemDeletedPosts.id = 'ea-dentistas-new-posts';
        listaEtapa2.append(listaItemDeletedPosts);

        const listaItemEnd = document.createElement('li');
        listaEtapa2.append(listaItemEnd);

        const action = 'ea_dentistas_register_new_posts';
        const xhr = new XMLHttpRequest();
        xhr.responseType = 'json';
        xhr.open('POST', ajax_object.ajax_url + '?action=' + action);
        xhr.onload = function () {
            const response = xhr.response;
            if (xhr.status === 200) {
                if (!response.success) {
                    console.log('success', response.success);
                    console.log('msg', response.msg);
                    listaItemDeletedPosts.innerText = response.msg;
                    return;
                }
                console.log('response.new_posts', response.new_posts);
                const count = typeof response.new_posts === undefined || !response.new_posts ? 0 : response.new_posts.length;
                console.log('count', count);
                listaItemDeletedPosts.innerText = `Novos dentistas cadastrados (${count}).`;
                //     return ea_delete_dentistas();
                // } else {
                listaItemEnd.innerText = 'Etapa 2 concluída.';
                // }
                ea_atualiza_dentistas(btn);
            } else {
                listaItemDeletedPosts.innerText = 'Ocorreu um erro de conexão, tente novamente.';
                listaItemUpdatedPosts.style.color = '#ff0000';
                delete btn.dataset.disabled;
                btn.innerText = 'Atualizar dentistas';
            }
        };
        xhr.send();
    }

    function ea_atualiza_dentistas(btn) {
        listaEtapa3 = document.getElementById('ea-dentista-lista-etapa-3');
        if (typeof listaEtapa3 === undefined || !listaEtapa3) {
            return;
        }
        const listaItemProcessing = document.createElement('li');
        listaItemProcessing.innerText = 'Etapa 3: atualizar os dentistas existentes.';
        listaEtapa3.append(listaItemProcessing);

        const listaExecutandoEtapa = document.createElement('li');
        listaExecutandoEtapa.innerText = 'Executando Etapa 3...';
        listaEtapa3.append(listaExecutandoEtapa);

        const listaItemDeletedPosts = document.createElement('li');
        listaItemDeletedPosts.id = 'ea-dentistas-existing-posts';
        listaEtapa3.append(listaItemDeletedPosts);

        const listaItemEnd = document.createElement('li');
        listaEtapa3.append(listaItemEnd);

        const action = 'ea_dentistas_update_existing_posts';
        const xhr = new XMLHttpRequest();
        xhr.responseType = 'json';
        xhr.open('POST', ajax_object.ajax_url + '?action=' + action);
        xhr.onload = function () {
            const response = xhr.response;
            if (xhr.status === 200) {
                if (!response.success) {
                    console.log('success', response.success);
                    console.log('msg', response.msg);
                    listaItemDeletedPosts.innerText = response.msg;
                    return;
                }
                console.log('response.updated_posts', response.updated_posts);
                const count = typeof response.updated_posts === undefined || !response.updated_posts ? 0 : response.updated_posts.length;
                console.log('count', count);
                listaItemDeletedPosts.innerText = `Dentistas atualizados (${count}).`;
                //     return ea_delete_dentistas();
                // } else {
                listaItemEnd.innerText = 'Etapa 3 concluída.';
                // }
                ea_atualiza_coordenadas(btn);
            } else {
                listaItemDeletedPosts.innerText = 'Ocorreu um erro de conexão, tente novamente.';
                listaItemUpdatedPosts.style.color = '#ff0000';
                delete btn.dataset.disabled;
                btn.innerText = 'Atualizar dentistas';
            }
        };
        xhr.send();
    }

    function ea_atualiza_coordenadas(btn) {
        listaEtapa4 = document.getElementById('ea-dentista-lista-etapa-4');
        if (typeof listaEtapa4 === undefined || !listaEtapa4) {
            return;
        }
        const listaItemProcessing = document.createElement('li');
        listaItemProcessing.innerText = 'Etapa 4: atualizar a latitide e longitude dos dentistas.';
        listaEtapa4.append(listaItemProcessing);

        const listaExecutandoEtapa = document.createElement('li');
        listaExecutandoEtapa.innerText = 'Executando Etapa 4...';
        listaEtapa4.append(listaExecutandoEtapa);

        const listaItemUpdatedPosts = document.createElement('li');
        listaItemUpdatedPosts.id = 'ea-dentistas-coordinates';
        listaEtapa4.append(listaItemUpdatedPosts);

        const listaItemStepFinished = document.createElement('li');
        listaEtapa4.append(listaItemStepFinished);

        const listaItemEnd = document.createElement('li');
        listaEtapa4.append(listaItemEnd);

        const action = 'ea_dentistas_update_addresses';
        const xhr = new XMLHttpRequest();
        xhr.responseType = 'json';
        xhr.open('POST', ajax_object.ajax_url + '?action=' + action);
        xhr.onload = function () {
            const response = xhr.response;
            if (xhr.status === 200) {
                if (!response.success) {
                    console.log('success', response.success);
                    console.log('msg', response.msg);
                    listaItemUpdatedPosts.innerText = response.msg;
                    delete btn.dataset.disabled;
                    btn.innerText = 'Atualizar dentistas';
                    return;
                }
                console.log('response.updated_posts', response.updated_posts);
                const count = typeof response.updated_posts === undefined || !response.updated_posts ? 0 : response.updated_posts.length;
                console.log('count', count);
                listaItemUpdatedPosts.innerText = `Dentistas atualizados (${count}).`;
                //     return ea_delete_dentistas();
                // } else {
                listaItemStepFinished.innerText = 'Etapa 4 concluída.';
                listaItemEnd.innerText = 'Atualização concluída';
                // }
            } else {
                listaItemUpdatedPosts.innerText = 'Ocorreu um erro de conexão, tente novamente.';
                listaItemUpdatedPosts.style.color = '#ff0000';
            }
            delete btn.dataset.disabled;
            btn.innerText = 'Atualizar dentistas';
        };
        xhr.send();
    }

    function ea_dentistas_start_update() {
        const btn = document.getElementById('ea-dentistas-update-btn');

        if (!btn) {
            return;
        }

        const div = document.getElementById('ea-dentistas-update-html');

        if (!div) {
            return;
        }

        btn.addEventListener('click', function (e) {

            e.preventDefault();
            if (btn.dataset.disabled) {
                console.log('Processando, aguarde');
                return;
            }
            btn.dataset.disabled = true;
            btn.innerText = 'Processando...';
            console.log('is_disabled 2', btn.dataset.disabled);

            div.innerHTML = '';
            const listaEtapa1 = document.createElement('ul');
            listaEtapa1.id = 'ea-dentista-lista-etapa-1';
            div.append(listaEtapa1);

            const listaEtapa2 = document.createElement('ul');
            listaEtapa2.id = 'ea-dentista-lista-etapa-2';
            div.append(listaEtapa2);

            const listaEtapa3 = document.createElement('ul');
            listaEtapa3.id = 'ea-dentista-lista-etapa-3';
            div.append(listaEtapa3);

            const listaEtapa4 = document.createElement('ul');
            listaEtapa4.id = 'ea-dentista-lista-etapa-4';
            div.append(listaEtapa4);

            const listaItemStart = document.createElement('li');
            listaItemStart.innerText = 'Preparando atualização...';
            listaEtapa1.append(listaItemStart);
            const action = 'ea_dentistas_start_update';

            const xhr = new XMLHttpRequest();
            xhr.responseType = 'json';
            xhr.open('POST', ajax_object.ajax_url + '?action=' + action);
            xhr.onload = function () {
                const response = xhr.response;
                if (xhr.status === 200) {
                    if (!response.success) {
                        const listaItemError = document.createElement('li');
                        listaItemError.innerText = response.msg;
                        listaEtapa1.append(listaItemError);
                        return;
                    }

                    ea_delete_dentistas(btn);

                } else {
                    const listaItemFailConnection = document.createElement('li');
                    listaItemFailConnection.innerText = 'Ocorreu um erro de conexão.';
                    listaItemFailConnection.style.color = '#ff0000';
                    listaEtapa1.append(listaItemFailConnection);
                    delete btn.dataset.disabled;
                    btn.innerText = 'Atualizar dentistas';
                }
            };

            xhr.send();
            console.log('click');
        });

    }
    ea_dentistas_start_update();
});