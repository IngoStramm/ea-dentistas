let map;
let marker;
let lat, lng;
let autocomplete;

lat = ajax_object.lat;
lng = ajax_object.lng;


function initGoogleApi() {
    initMap();
    initAutocomplete();
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
        zoom: 12,
        center: defaultLocation,
        mapTypeControl: false,
    });

    // const defaultLocationMarker = new google.maps.Marker({
    //     position: defaultLocation,
    //     map,
    //     title: "Sua localização",
    // });

    const listagem = ajax_object.listagem;
    const markers = [];
    const allInfoWindows = [];
    listagem.map((item, i) => {
        const LatLng = { lat: item.lat, lng: item.long };
        const itemLocation = LatLng;
        const itemLocationContentString =
            `<div>
                <h5>${item.nome_fantasia}</h5>
                <address>
                Endereço: ${item.endereco}
                Telefone: ${item.telefone}
                </address>
            </div>`;
        const itemLocationInfowindow = new google.maps.InfoWindow({
            content: itemLocationContentString,
            ariaLabel: item.nome_fantasia,
        });
        allInfoWindows.push(itemLocationInfowindow);
        const itemLocationMarker = new google.maps.Marker({
            position: itemLocation,
            map,
            title: item.nome_fantasia,
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
                itemLocationInfowindow.setContent(itemLocationContentString);
                itemLocationInfowindow.open(map, itemLocationMarker);
            };

        })(itemLocationMarker, i));
        markers.push(itemLocationMarker);
        return item;
    });

    const btns = document.querySelectorAll('.listagem-item-btn');
    for (const btn of btns) {
        btnClick(btn, google);
    }

    function btnClick(btn, google) {
        btn.addEventListener('click', e => {
            e.preventDefault();
            closeAllInfoWindows();
            const markerid = btn.dataset.markerid;
            google.maps.event.trigger(markers[markerid], 'click');
        });
    }

    function closeAllInfoWindows() {
        for (const item of allInfoWindows) {
            item.close();
        }
    }
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
            fields: ['place_id', 'geometry', 'name']
        }
    );
    autocomplete.addListener('place_changed', onPlaceChanged);
}

function onPlaceChanged() {
    let place = autocomplete.getPlace();
    const eaAutocompleteForm = document.querySelector('.ea-autocomplete-form');
    const latInput = eaAutocompleteForm.querySelector('input[name="lat"]');
    const lngInput = eaAutocompleteForm.querySelector('input[name="lng"]');

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

    if (!place.geometry) {
        document.getElementById('autocomplete').placeholder = 'Digite um endereço';
        latInput.value = '';
        lngInput.value = '';
    } else {
        lat = place.geometry.location.lat();
        lng = place.geometry.location.lng();
        console.log('lat', lat);
        console.log('lng', lng);
        document.getElementById('autocomplete').innerHTML = place.name;
        latInput.value = lat;
        lngInput.value = lng;
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

function eaDentistasPagination() {
    const paginationLists = document.querySelectorAll('.pagination-list');
    for (const lista of paginationLists) {
        const items = lista.querySelectorAll('.listagem-item');
        const totalItems = items.length;
        const itemsPerPage = 3;
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

document.addEventListener('DOMContentLoaded', function () {
    eaDentistasPagination();
});
