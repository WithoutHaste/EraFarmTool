window.addEventListener('load', displayAttribution);

function displayAttribution(event) {
	const body = document.getElementsByTagName('body')[0];
	let hr = document.createElement('hr');
	hr.style.marginTop = '3em';
	let div = document.createElement('div');
	div.classList.add('footer');
	div.innerHTML = "Era Farm Tool is an open-source project from <a href='https://github.com/WithoutHaste/EraFarmTool'>GitHub/WithoutHaste</a>.";
	body.appendChild(hr);
	body.appendChild(div);
}
