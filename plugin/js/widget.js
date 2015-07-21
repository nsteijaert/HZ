/*
 * This code is listen for triggers when loading is complete.
 */
$(document).ready(function() {

	$('form').submit(function() {
		var query = $('#query').val();
		var selection = $('#selection').val();
		var depth = $('#depth').val();

		if (query != "") {
			if (depth != "") {
				getQuery(query, depth, true, selection);
			} else {
				runQuery(query, selection);
			}
		} else {
			console.log("No given query");
		}
	});
});

function getQuery(concept, depth, run, selection) {
	$.ajax({
		type : "POST",
		cache : false,
		url : "php/VisualisationScript.php",
		async : true,
		data : {
			do : "generate",
			concept : concept,
			depth : depth
		}
	}).done(function(result) {
		if ( typeof run === 'undefined')
			return result;
		else
			console.log(runQuery(result, selection));
	});
}

/*
 * This function runs a query on your local fuseki server
 */
function runQuery(query, selection) {
	$.ajax({
		type : "GET",
		cache : false,
		url : "http://localhost:3030/ds/query",
		async : true,
		data : {
			query : query,
			output : selection
		}
	}).done(function(result) {

		$.post("php/VisualisationScript.php", {
			do : "parse",
			data : result
		}, function(data) {
			return data;
		});
	}).fail(function(result) {
	});
}
