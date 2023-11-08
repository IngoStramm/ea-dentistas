let map;
let marker;
let lat, lng;
let autocomplete;

const allInfoWindows = [];
const markers = {};
const itemsLista = Object.values(ajax_object.listagem);
const itemsDestaques = itemsLista.filter(item => item.destaque === 'on');
const itensNaoDestaques = itemsLista.filter(item => item.destaque !== 'on');
const todosDentistas = itemsDestaques.concat(itensNaoDestaques);
console.log('todosDentistas', todosDentistas);

function removeAccents(str) {
    return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
}

lat = ajax_object.lat;
lng = ajax_object.lng;

const estadoUsuario = ajax_object.estado ? removeAccents(ajax_object.estado.toUpperCase()) : '';
const cidadeUsuario = ajax_object.cidade ? removeAccents(ajax_object.cidade.toUpperCase()) : '';
console.log('estadoUsuario', estadoUsuario);
console.log('cidadeUsuario', cidadeUsuario);

function closeAllInfoWindows() {
    for (const item of allInfoWindows) {
        item.close();
    }
}

function btnClick(btn, google) {
    btn.addEventListener('click', e => {
        e.preventDefault();
        closeAllInfoWindows();
        const markerid = btn.dataset.markerid;
        console.log('markerid', markerid);
        google.maps.event.trigger(markers[markerid], 'click');
    });
}

function initGoogleApi() {
    document.addEventListener('DOMContentLoaded', function () {
        eaDentistasListagem();
        initAutocomplete();
    });

}

function initMap() {
    const mapDiv = document.getElementById('map');
    if (typeof (mapDiv) === undefined || !mapDiv) {
        return;
    }
    if ((typeof (lat) !== undefined && !lat) || (typeof (lng) !== undefined && !lng)) {
        lat = '-23.5502909';
        lng = '-46.6341887';
        // eaDentistasGetGeolocation();
        // initMap();
    }

    lat = Number(lat);
    lng = Number(lng);
    const defaultLocation = { lat, lng };
    console.log('2 - lat', lat);
    console.log('2 - lng', lng);
    map = new google.maps.Map(mapDiv, {
        zoom: 15,
        center: defaultLocation,
        mapTypeControl: false,
    });

    // const defaultLocationMarker = new google.maps.Marker({
    //     position: defaultLocation,
    //     map,
    //     title: "Sua localização",
    // });

    todosDentistas.forEach((dentista, i) => {
        const LatLng = { lat: parseFloat(dentista.lat), lng: parseFloat(dentista.lng) };
        const itemLocation = LatLng;
        const itemLocationContentString =
            `<div>
                <h5>${dentista.nome}</h5>
                <address>
                Endereço: ${dentista.endereco_completo}
                Telefone: ${dentista.telefone_contato}
                </address>
            </div>`;
        const itemLocationInfowindow = new google.maps.InfoWindow({
            content: itemLocationContentString,
            ariaLabel: dentista.nome_fantasia,
        });
        allInfoWindows.push(itemLocationInfowindow);
        const itemLocationMarker = new google.maps.Marker({
            position: itemLocation,
            map,
            title: dentista.nome_fantasia,
        });
        // itemLocationMarker.addListener("click", () => {
        //     itemLocationInfowindow.open({
        //         anchor: itemLocationMarker,
        //         map,
        //     });
        // });
        // ref @link: https://jsfiddle.net/upsidown/8gjt0y6p/
        google.maps.event.addListener(itemLocationMarker, 'click', (function (itemLocationMarker, i) {

            return function () {
                closeAllInfoWindows();
                itemLocationInfowindow.setContent(itemLocationContentString);
                itemLocationInfowindow.open(map, itemLocationMarker);
            };

        })(itemLocationMarker, i));
        // markers.push(itemLocationMarker);
        markers[dentista.post_id] = itemLocationMarker;
    });

}

function initAutocomplete() {
    const autocompleteInput = document.getElementById('autocomplete');
    if (typeof autocompleteInput === undefined || !autocompleteInput) {
        return;
    }
    autocomplete = new google.maps.places.Autocomplete(
        autocompleteInput,
        {
            componentRestrictions: { 'country': ['BR'] },
            fields: ['place_id', 'geometry', 'name', 'address_components']
        }
    );
    autocomplete.addListener('place_changed', onPlaceChanged);
}

function onPlaceChanged() {
    let place = autocomplete.getPlace();
    const eaAutocompleteForm = document.querySelector('.ea-autocomplete-form');
    const latInput = eaAutocompleteForm.querySelector('input[name="lat"]');
    const lngInput = eaAutocompleteForm.querySelector('input[name="lng"]');
    const stateInput = eaAutocompleteForm.querySelector('input[name="estado"]');
    const cidadeInput = eaAutocompleteForm.querySelector('input[name="cidade"]');

    if (typeof eaAutocompleteForm === undefined || !eaAutocompleteForm) {
        console.error('Não foi possível encontrar o formulário do autocomplete');
        return;
    }

    if (typeof latInput === undefined || !latInput) {
        console.error('Não foi possível encontrar o input de latitude');
        return;
    }

    if (typeof lngInput === undefined || !lngInput) {
        console.error('Não foi possível encontrar o input de longitude');
        return;
    }

    if (typeof stateInput === undefined || !stateInput) {
        console.error('Não foi possível encontrar o input de Estado');
        return;
    }

    if (typeof cidadeInput === undefined || !cidadeInput) {
        console.error('Não foi possível encontrar o input de Estado');
        return;
    }

    if (!place.geometry) {
        document.getElementById('autocomplete').placeholder = 'Digite um endereço';
        latInput.value = '';
        lngInput.value = '';
        stateInput.value = '';
        cidadeInput.value = '';
    } else {
        lat = place.geometry.location.lat();
        lng = place.geometry.location.lng();
        console.log('lat', lat);
        console.log('lng', lng);
        const estado = place.address_components.filter(item => item.types.includes('administrative_area_level_1'));
        const cidade = place.address_components.filter(item => item.types.includes('administrative_area_level_2'));
        console.log('estado', estado[0].short_name);
        console.log('cidade', cidade[0].short_name);
        document.getElementById('autocomplete').innerHTML = place.name;
        latInput.value = lat;
        lngInput.value = lng;
        stateInput.value = estado[0].short_name;
        cidadeInput.value = cidade[0].short_name;
    }
}

function addPageNumbers(paginationNumbers, paginationContainer, totalPages, items, itemsPerPage) {
    const currPage = Number(paginationContainer.querySelector('input[name="current-page"]').value);
    for (let index = 1; index <= totalPages; index++) {
        const pageNumber = document.createElement('li');
        pageNumber.classList.add('page-number');
        if (currPage === index) {
            pageNumber.classList.add('current');
        }

        const pageNumberBtn = document.createElement('button');
        pageNumberBtn.innerText = index;
        pageNumberBtn.dataset.pageIndex = index;

        pageNumber.append(pageNumberBtn);
        paginationNumbers.append(pageNumber);

        changeCurrentPage(pageNumberBtn, items, itemsPerPage, paginationNumbers, paginationContainer);
    }
}

function setCurrentPage(items, paginationContainer, itemsPerPage) {
    const currPage = Number(paginationContainer.querySelector('input[name="current-page"]').value);
    const lastItemToShow = currPage * itemsPerPage;
    const firstItemToShow = lastItemToShow - itemsPerPage;
    let index = 1;
    for (const item of items) {
        if (index <= firstItemToShow || index > lastItemToShow) {
            item.style.display = 'none';
        } else {
            item.style.display = '';
        }
        index++;
    }
}

function changeCurrentPage(btn, items, itemsPerPage, paginationNumbers, paginationContainer) {
    btn.addEventListener('click', e => {
        e.preventDefault();
        const currPageInput = paginationContainer.querySelector('input[name="current-page"]');
        const selectedPage = Number(btn.dataset.pageIndex);
        const li = btn.parentElement;
        if (typeof li === undefined || !li) {
            return;
        }
        if (li.classList.contains('current')) {
            return;
        }
        currPageInput.value = selectedPage;

        const paginationNumbersItems = paginationNumbers.querySelectorAll('.page-number');
        let index = 1;
        for (const paginationNumbersItem of paginationNumbersItems) {
            paginationNumbersItem.classList.remove('current');
            if (index === selectedPage) {
                paginationNumbersItem.classList.add('current');
            }
            index++;
        }
        setCurrentPage(items, paginationContainer, itemsPerPage);
    });
}

function eaPaginationPrevNext(prevBtn, nextBtn, items, paginationNumbers, paginationContainer) {
    prevBtn.addEventListener('click', e => {
        e.preventDefault();
        const currPage = Number(paginationContainer.querySelector('input[name="current-page"]').value);
        const paginationNumbersItems = paginationNumbers.querySelectorAll('.page-number');
        if (currPage <= 1) {
            return;
        }
        paginationNumbersItems[currPage - 2].querySelector('button').click();
    });
    nextBtn.addEventListener('click', e => {
        e.preventDefault();
        const currPage = Number(paginationContainer.querySelector('input[name="current-page"]').value);
        const paginationNumbersItems = paginationNumbers.querySelectorAll('.page-number');
        const totalPages = paginationNumbersItems.length;
        if (currPage >= totalPages) {
            return;
        }
        paginationNumbersItems[currPage].querySelector('button').click();
    });
}

function eaDentistasPagination(items) {
    const paginationLists = document.querySelectorAll('.pagination-list');
    for (const lista of paginationLists) {
        // const items = lista.querySelectorAll('.listagem-item');
        const totalItems = items.length;
        console.log('totalItems', totalItems);
        const itemsPerPage = 10;
        const totalPages = Math.ceil(totalItems / itemsPerPage);

        const paginationContainer = lista.parentElement.querySelector('.pagination-container');
        if (typeof paginationContainer === undefined || !paginationContainer) {
            return;
        }
        const paginationNumbers = paginationContainer.querySelector('.pagination-numbers');
        if (typeof paginationNumbers === undefined || !paginationNumbers) {
            return;
        }

        const prevBtn = paginationContainer.querySelector('.pagination-prev');
        const nextBtn = paginationContainer.querySelector('.pagination-next');

        addPageNumbers(paginationNumbers, paginationContainer, totalPages, items, itemsPerPage);
        setCurrentPage(items, paginationContainer, itemsPerPage);
        eaPaginationPrevNext(prevBtn, nextBtn, items, paginationNumbers, paginationContainer);
    }
}

function eaDentistasGetGeolocation({ lat, lng }) {
    navigator.geolocation.getCurrentPosition(
        function (position) {
            console.log(`Deu certo!`);
            lat = position.coords.latitude;
            lng = position.coords.longitude;
            console.log('1 - lat', lat);
            console.log('1 - lng', lng);
            return { lat, lng };
        },
        function errorCallback(error) {
            console.log(`Deu erro`, error);
            // lat = '-23.5502909';
            // lng = '-46.6341887';
            // initMap();
        }
    );

}

function eaDentistasListagem() {
    const ul = document.getElementById('listagem-items');
    if (typeof ul === undefined || !ul) {
        return;
    }

    // const itemPorEstado = items.filter(item => removeAccents(item.estado.toUpperCase()) === estadoUsuario);
    // const itemPorCidade = itemPorEstado.filter(item => removeAccents(item.cidade.toUpperCase()) === cidadeUsuario);

    // items.forEach((item, i) => {
    //     const dentista = item;
    //     const li = document.createElement('li');
    //     li.classList.add('listagem-item');
    //     li.innerHTML = `
    //     <h4 class="nome-dentista">${dentista.nome}</h4>
    //     <address>
    //         <p class="endereco-dentista">${dentista.endereco_completo}</p>
    //         <ul>
    //             <li class="telefone-dentista">${dentista.telefone_contato}</li>
    //         </ul>
    //     </address>
    //     `;
    //     const button = document.createElement('button');
    //     button.classList.add('listagem-item-btn');
    //     button.setAttribute('data-markerid', i);
    //     button.innerText = 'Visualizar';
    //     li.append(button);
    //     ul.append(li);
    // });
    // eaDentistasPagination(itemPorCidade);

    const options = {
        valueNames: [
            'nome',
            'endereco_completo',
            'telefone_contato',
            'destaque',
            { name: 'post_id', attr: 'data-markerid' },
        ],
        page: 5,
        pagination: true,
        item: `
        <li>
            <input class="destaque" type="hidden" />
            <input class="cidade" type="hidden" />
            <input class="estado" type="hidden" />
            <h3 class="nome"></h3>
            <p class="endereco_completo"></p>
            <ul>
                <li class="telefone_contato"></li>
                <button class="listagem-item-btn post_id">Visualizar</button>
            </ul>
        </li>`
    };

    const listaDentistas = new List('lista-dentistas', options, todosDentistas);

    listaDentistas.sort('nome', { alphabet: "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvXxYyZzÀàÁáÃãÂâÉéÈèÍíÓóÚúÇç" });

    listaDentistas.sort(['destaque'], { order: 'desc' }, { alphabet: "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvXxYyZzÀàÁáÃãÂâÉéÈèÍíÓóÚúÇç" });

    if (cidadeUsuario) {
        listaDentistas.search(cidadeUsuario, ['cidade']);
        const pesquisarPorCidadeInput = document.getElementById('pesquisar-por-cidade');
        if (typeof pesquisarPorCidadeInput !== undefined && pesquisarPorCidadeInput) {
            pesquisarPorCidadeInput.value = cidadeUsuario;
        }
    }

    const searchCidade = document.getElementById('pesquisar-por-cidade');
    const searchEstado = document.getElementById('pesquisar-por-estado');

    searchCidade.addEventListener('keyup', e => {
        const s = removeAccents(e.target.value);
        listaDentistas.search(s, ['cidade']);
        searchEstado.value = '';
    });

    searchEstado.addEventListener('keyup', e => {
        const s = removeAccents(e.target.value);
        listaDentistas.search(s, ['estado']);
        searchCidade.value = '';
    });

    const btns = document.querySelectorAll('.listagem-item-btn');
    for (const btn of btns) {
        btnClick(btn, google);
    }

    listaDentistas.on('updated', e => {
        const btns = document.querySelectorAll('.listagem-item-btn');
        for (const btn of btns) {
            btnClick(btn, google);
        }

    });

    initMap();
}
