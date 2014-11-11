$(function() {
	$('form').submit(function() {
		var query = $('#query').val();
		var result = $('#selection').val();

		if (query != "") {
			$.ajax({
				type : "POST",
				cache : false,
				dataType : "json",
				url : "php/SPARQLClient.php",
				async : false,
				data : {
					query : query
				}
			}).done(function(result) {
				console.log("Data successfully retrieved...");
				$('.result').html("<pre>" + syntaxHighlight(JSON.stringify(result, undefined, 4))+ "</pre>");
			}).fail(function() {
				console.log("Error retrieving data...");
			});

			console.log("Asked query: " + query);
		} else {
			console.log("No given query");
		}
		return false;
	});
});

/*
 * Show the json string with nice syntax.
 */
function syntaxHighlight(json) {
	json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
	return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
		var cls = 'number';
		if (/^"/.test(match)) {
			if (/:$/.test(match)) {
				cls = 'key';
			} else {
				cls = 'string';
			}
		} else if (/true|false/.test(match)) {
			cls = 'boolean';
		} else if (/null/.test(match)) {
			cls = 'null';
		}
		return '<span class="' + cls + '">' + match + '</span>';
	});
}