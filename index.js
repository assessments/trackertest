/**
 * Helpers
 */

var entityMap = {
  "&": "&amp;",
  "<": "&lt;",
  ">": "&gt;",
  '"': '&quot;',
  "'": '&#39;',
  "/": '&#x2F;'
};

//the moustache.js solution for escaping HTML
function escapeHtml(string) {
    if (string === null) {
        return '';
    } else {
        return String(string).replace(/[&<>"'\/]/g, function (s) {
            return entityMap[s];
        });
    }
};

/**
 * Tracker class
 */

function Tracker(serverUrl, submitBtn, urlInput) {

    /**
     * Models
     */

    this.requestUrl = '';

    this.serverUrl = serverUrl;

    this.response = {};

    /**
     * Controllers
     */

    //the submit method is an event handler
    this.submit = function () {
        this.requestUrl = document.getElementById(urlInput).value;
        this.load();
    };

    //the load method uses AJAX and POST to invoke a request to the server
    this.load = function () {

        var self = this;
        var request = new XMLHttpRequest();
        request.open('POST', this.serverUrl, true);
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

        request.send('url='+encodeURIComponent(this.requestUrl));
    }

    //add an event handler for the submit button
    document.getElementById(submitBtn).addEventListener('click', this.submit.bind(this), false);

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
                html[++i] = '<p>'+escapeHtml(error)+'</p>';
            }, this);
            html[++i] = '</div>';
        }
        document.getElementById('response').innerHTML = html.join('');
    }
}

document.addEventListener('DOMContentLoaded', function () {

    //instantiate the tracker class
    var tracker = new Tracker('tracker.php', 'submit-url', 'url');

});