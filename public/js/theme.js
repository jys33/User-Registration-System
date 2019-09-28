document.addEventListener('DOMContentLoaded', function () {
	onLoad();
})

querySelector('.switch-theme').onclick = function (){
	var themeIcon = querySelector('.theme-icon');
	var srcElt;
	if (themeIcon.src == moonIcon) {
	    localStorage.setItem("theme", "dark");
	    srcElt = sunIcon;
	    showDarkItems();
	} else {
	    localStorage.setItem("theme", "light");
	    srcElt = moonIcon;
	    showLightItems();
	}
	themeIcon.setAttribute('src', srcElt);
}

function showDarkItems(){
	querySelector('.theme-icon').alt = "Light Mode";
	document.body.setAttribute('class', 'dark');
	querySelector('.navbar').classList.remove('navbar-light');
	querySelector('.navbar').classList.add('navbar-dark');
}

function showLightItems(){
	querySelector('.theme-icon').alt = "Dark Mode";
	document.body.setAttribute('class', '');
	querySelector('.navbar').classList.remove('navbar-dark');
	querySelector('.navbar').classList.add('navbar-light');

}

function onLoad(){
	var themeIcon = querySelector('.theme-icon');
	var srcElt;
	if (localStorage.getItem("theme") === "dark") {
	    showDarkItems();
	    srcElt = sunIcon;
	}
	else {
	    showLightItems();
	    srcElt = moonIcon;
	}
	themeIcon.setAttribute('src', srcElt);
}