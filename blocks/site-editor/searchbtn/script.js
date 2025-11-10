
let isOpen = false;

const searchBtn = document.querySelector('.header__searchbtn');
const searchInput = document.getElementById('wp-block-search__input-1');

if (searchBtn) {
    // Convert $('.header__searchbtn').on('click', function(e) {...})
    document.querySelector('.header__searchbtn').addEventListener('click', function(e) {
        e.stopPropagation();
        isOpen = !isOpen;

        if (isOpen) {
            this.classList.add('active');
            document.querySelector('.header__search').classList.add('active');
        } else {
            this.classList.remove('active');
            document.querySelector('.header__search').classList.remove('active');
        }
    });
}


if (searchInput) {
    // Convert $('#wp-block-search__input-1').on('input', function () {...})
    document.getElementById('wp-block-search__input-1').addEventListener('input', function() {
        if (this.value.trim() !== '') {
            document.querySelector('.main-header__searchform .wp-block-search__button').classList.add('hide');
        } else {
            document.querySelector('.main-header__searchform .wp-block-search__button').classList.remove('hide');
        }
    });
}

