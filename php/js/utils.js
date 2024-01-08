function ajaxGet(url, callbackSuccess, callbackError) {
	var xhr = new XMLHttpRequest();
	xhr.open('GET', url);
	xhr.send(null);
	xhr.onreadystatechange = function () {
		var DONE = 4; // readyState 4 means the request is done.
		var OK = 200; // status 200 is a successful return.
		if (xhr.readyState === DONE) {
			if (xhr.status === OK) {
//				console.log(xhr.responseText); // 'This is the returned text.'
				callbackSuccess(xhr.responseText);
			} else {
//				console.log('Error: ' + xhr.status); // An error occurred during the request.
				callbackError(xhr.responseText);
			}
		}
	};
}

// "params" expects a key/value array of url post paramaters
function ajaxPost(url, params, callbackSuccess, callbackError) {
	var xhr = new XMLHttpRequest();
	xhr.open('POST', url);
	xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xhr.send(asQueryString(params));
	xhr.onreadystatechange = function () {
		var DONE = 4; // readyState 4 means the request is done.
		var OK = 200; // status 200 is a successful return.
		if (xhr.readyState === DONE) {
			if (xhr.status === OK) {
				callbackSuccess(xhr.responseText);
			} else {
				callbackError(xhr);
			}
		}
	};
	
	function asQueryString(params) {
		let substrings = [];
		for(let key in params) {
			substrings.push(encodeURIComponent(key)+"="+encodeURIComponent(params[key]));
		}
		return substrings.join('&');
	}
}
