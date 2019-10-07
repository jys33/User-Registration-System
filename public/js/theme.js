document.addEventListener('DOMContentLoaded', function (){
    if(localStorage.dark === 'true'){
        darkLightMode();
    }
});

querySelector('.dark-switcher').onclick = darkLightMode;

function darkLightMode() {
    var className, themeIcon, alt;
    if(querySelector('.theme-icon').getAttribute('src') === 'img/moon.svg'){
        localStorage.dark = true;
        className = 'dark';
        themeIcon = 'img/sun.svg';
        alt = 'Light Mode';
        querySelector('.navbar').classList.add('navbar-dark');
    } else{
        localStorage.dark = false;
        className = '';
        themeIcon = 'img/moon.svg';
        alt = 'Dark Mode';
        querySelector('.navbar').classList.remove('navbar-dark');
    }
    querySelector('body').setAttribute('class', className);
    querySelector('.theme-icon').setAttribute('src', themeIcon);
    querySelector('.theme-icon').setAttribute('alt', alt);
}