/**
 * Tracker class
 */

function Tracker() {

    /**
     * Models
     */

    this.url = '';

    this.response = {};

    /**
     * Controllers
     */

    //the submit method is an event handler
    this.submit = function () {
        this.url = document.getElementById('url').value;
        this.load();
    };

    //the load method uses AJAX and POST to invoke a request to the server
    this.load = function () {

        var self = this;
        var request = new XMLHttpRequest();
        request.open('POST', 'tracker.php', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

        request.onload = function () {
            if (request.status >= 200 && request.status < 400) {
                self.response = JSON.parse(request.responseText); //success
            } else {
                self.response = {count: null, errors: [request.responseText]}; //server error
            }
            self.draw();
        };

        request.onerror = function () {
            self.response = {count: null, errors: ['Connection error']}; //connection error
            self.draw();
        };

        request.send('url='+encodeURIComponent(this.url));
    }

    //add an event handler for the submit button
    document.getElementById('submit-url').addEventListener('click', this.submit.bind(this), false);

    /**
     * Views
     *
     * In a larger app, consider a template solution (e.g Handlebars.js) or view framework (e.g. React)
     */

    //the draw method renders the response to the page
    this.draw = function () {
        var html = [], i = -1;
        if (this.response.count) {
            html[++i] = '<div class="success">Success: '+this.response.count+' products tracked</div>';
        } else if (this.response.errors.length > 0) {
            html[++i] = '<div class="error">';
            this.response.errors.forEach(function (error) {
                html[++i] = '<p>'+error+'</p>';
            }, this);
            html[++i] = '</div>';
        }
        document.getElementById('response').innerHTML = html.join('');
    }
}

document.addEventListener('DOMContentLoaded', function (){

    //instantiate the tracker class
    var tracker = new Tracker();

});