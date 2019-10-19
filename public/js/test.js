if(localStorage.dark === 'true'){
	darkMode();
} else {
    lightMode();
}
querySelector('.dark-mode').onclick = function () {
	localStorage.dark = true;
	darkMode();
};
querySelector('.light-mode').onclick = function () {
	localStorage.dark = false;
	lightMode();
};

function darkMode(){
	document.body.setAttribute('class', 'dark');
	querySelector('.navbar').classList.add('navbar-dark');

	querySelector(".dark-mode").style.display = 'none';
	querySelector(".light-mode").style.display = 'initial';
}

function lightMode(){
	document.body.setAttribute('class', '');
	querySelector('.navbar').classList.remove('navbar-dark');

	querySelector(".dark-mode").style.display = 'initial';
	querySelector(".light-mode").style.display = 'none';
}