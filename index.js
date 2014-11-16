function Tracker() {

	this.url = '';

	this.submit = function () {
		this.url = document.getElementById('url').value;
		
		this.load();
	};

	this.load = function () {

		var request = new XMLHttpRequest();
		request.open('POST', 'tracker.php', true);
		request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

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

		request.send('url='+this.url);
	}

	document.getElementById('submit-url').addEventListener('click', this.submit.bind(this), false);
}

document.addEventListener('DOMContentLoaded', function(){

	//instantiate the tracker class
	var tracker = new Tracker();

});