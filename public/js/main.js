'use strict';

function querySelector(el){
    return document.querySelector(el);
}

function getElementById(el){
    return document.getElementById(el);
}

function createInput(name, value) {
    let newInput = document.createElement('input');
    newInput.type = 'hidden';
    newInput.name = name;
    newInput.value = value;
    return newInput;
}