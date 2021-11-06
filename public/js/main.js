'use strict';

function querySelector(el){
    return document.querySelector(el);
}

function getElementById(el){
    return document.getElementById(el);
}

function disabledBtn() {
    querySelector('.btn').setAttribute('disabled', true);
}
