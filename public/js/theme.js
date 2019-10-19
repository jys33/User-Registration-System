if(localStorage.dark === 'true'){
    darkLightMode();
}

querySelector('.dark-switcher').onclick = darkLightMode;

function darkLightMode() {
    let themeIcon, alt;
    if(querySelector('.theme-icon').getAttribute('src') === 'img/moon.svg'){
        localStorage.dark = true;
        themeIcon = 'sun.svg';
        alt = 'Light';
    } else{
        localStorage.dark = false;
        themeIcon = 'moon.svg';
        alt = 'Dark';
    }
    document.body.classList.toggle('dark');
    querySelector('.navbar').classList.toggle('navbar-dark');
    querySelector('.navbar').classList.toggle('navbar-light');
    querySelector('.theme-icon').setAttribute('src', 'img/' + themeIcon);
    querySelector('.theme-icon').setAttribute('alt', alt + ' Mode');
}