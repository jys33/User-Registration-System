document.addEventListener('DOMContentLoaded', function () {
	if(!localStorage.theme){
		localStorage.theme = '';
	}

	querySelector('body').setAttribute('class', localStorage.theme);
	if(querySelector('body').classList.contains('dark')){
		querySelector('.theme-icon').setAttribute('src', sunIcon);
		querySelector('.theme-icon').setAttribute('alt', 'Light Mode');

		querySelector('.navbar').classList.add('navbar-dark');
	}

	// opcion button
	querySelector('.dark-switcher').onclick = darkLight;

});

function darkLight() {
	var icon, alt;
	if(localStorage.theme != 'dark'){
		localStorage.theme = 'dark';
		icon = sunIcon;
		alt = 'Light Mode';
		querySelector('.navbar').classList.add('navbar-dark');
	} else {
		icon = moonIcon;
		alt = 'Dark Mode';
		localStorage.theme = '';
		querySelector('.navbar').classList.remove('navbar-dark');
	}
	querySelector('.theme-icon').setAttribute('alt', alt);
	querySelector('.theme-icon').setAttribute('src', icon);
	querySelector('body').setAttribute('class', localStorage.theme);
}