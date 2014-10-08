function Zo(){

   // Add object properties like this
   this._baseURL = '/zo';
   this._paths = {};
   this._about = {};

}

Zo.prototype.paths = function(callback) {
	me = this;
	$.getJSON( me._baseURL+"/api/path", function(data) {
		me._paths = data;
		if (callback) { callback(); }
	});
}

Zo.prototype.about = function(callback) {
	me = this;
	$.getJSON( me._baseURL+"/api/about", function(data) {
		me._about = data;
		if (callback) { callback(); }
	});
}

Zo.prototype.configure = function(callback) {
	me = this;
	$.getJSON( me._baseURL+"/api/configuration", function(data) {
		me._about = data.about;
		me._paths = data.path;
		if (callback) { callback(); }
	});

};

var zo = new Zo();

$(function () {

	zo.configure(function () {
		console.log(zo);
	});

	//console.log(zo._paths);
});
/*

// Add methods like this.  All Person objects will be able to invoke this
Person.prototype.speak = function(){
    alert("Howdy, my name is" + this.name);
}

// Instantiate new objects with 'new'
var person = new Person("Bob", "M");

// Invoke methods like this
person.speak(); // alerts "Howdy, my name is Bob"

*/