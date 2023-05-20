function showDeviceMenu() {
	$('.navigationDevice').toggle();
}


document.querySelectorAll('.navigationDevice a').forEach(item => {
	item.addEventListener('click', handleToggleUL)
})

function handleToggleUL(event) {
	var li = event.target.parentNode;

	// If parent node is <a>, take its parent which is the <li>
	if (li.nodeName === 'A') {
		li = li.parentNode;
	}

	if (li.className.indexOf('submenuDevice') !== -1) {
		li.classList.toggle("active");
		//event.preventDefault();
	}
}
