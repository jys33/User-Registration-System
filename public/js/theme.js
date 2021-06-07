if(localStorage.dark === 'true'){
    darkLightMode();
}

querySelector('.dark-switcher').onclick = darkLightMode;

function darkLightMode() {
    let themeIcon;
    if(querySelector('.theme-icon').textContent === '🌙'){
        localStorage.dark = true;
        themeIcon = '☀️';
    } else{
        localStorage.dark = false;
        themeIcon = '🌙';
    }
    document.body.classList.toggle('dark');
    querySelector('.navbar').classList.toggle('navbar-dark');
    querySelector('.navbar').classList.toggle('navbar-light');
    querySelector('.theme-icon').textContent = themeIcon;
}