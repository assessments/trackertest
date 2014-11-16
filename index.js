function Tracker() {

	this.url = '';

	this.submit = function () {
		this.url = document.getElementById('url').value;
		console.log(this.url);
	};

	this.load = function () {
		request = new XMLHttpRequest();
		request.open('GET', 'tracker.php', true);

		request.onload = function() {
			if (request.status >= 200 && request.status < 400){
		    	console.log(request.responseText); //success
		  	} else {
		  		console.log(request.responseText); //error
			}
		};

		request.onerror = function() {
			console.log('connection error');
		};

		request.send();
	}

	document.getElementById('submit-url').addEventListener('click', this.submit.bind(this), false);
}

document.addEventListener('DOMContentLoaded', function(){

	//instantiate the tracker class
	var tracker = new Tracker();

});