/*
 * This code is listen for triggers when loading is complete.
 */
$(document).ready(function() {

	if (window.location.hash != "") {
		if (window.location.hash == "#runQuery") {
			$('#visualisation').fadeOut(function() {
				$(this).html('');
				$('#runQuery').fadeIn();
			});
		} else {
			var str = window.location.hash.replace("#", "");
			$('#visualisation').load(str).hide();
			$('#runQuery').fadeOut(function() {
				$('#visualisation').fadeIn();
			});
		}
	} else {
		$('#visualisation').fadeOut(function() {
			$(this).html('');
			$('#runQuery').fadeIn();
		});
	}

	$('form').submit(function() {
		var query = $('#query').val();
		var selection = $('#selection').val();
		var depth = $('#depth').val();

		if (query != "") {
			$('.result').html('<div style="text-align:center"><i class="fa fa-spinner fa-spin fa-3x"></i><br/><span>Loading data...</span></div>');
			if (depth != "") {
				getQuery(query, depth, true, selection);
			} else {
				runQuery(query, selection);
			}
		} else {
			console.log("No given query");
		}
	});

	$('nav').on('click', 'a', function() {
		if ($(this).attr('href') == "#runQuery") {
			$('#visualisation').fadeOut(function() {
				$(this).html('');
				$('#runQuery').fadeIn();
			});
		} else {
			var str = $(this).attr('href').replace("#", "");
			$('#visualisation').load(str).hide();
			$('#runQuery').fadeOut(function() {
				$('#visualisation').fadeIn();
			});
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

		if (selection == "json") {
			$('.result').html("<span>" + new Date() + "</span><pre><code class='json'>" + JSON.stringify(result, undefined, 4) + "</code></pre>");
		} else if (selection == "xml") {
			var xmlText = new XMLSerializer().serializeToString(result);
			var xmlTextNode = document.createTextNode(xmlText);
			$('.result').html("<span>" + new Date() + "</span><pre><code class='xml'></code></pre>");
			$('.result pre code').append(xmlTextNode);
		} else {
			$('.result').html("<span>" + new Date() + "</span><pre><code class='text'></code></pre>");
			$('.result pre code').text(result).append();
		}

		$('pre code').each(function(i, e) {
			//hljs.highlightBlock(e);
		});

		$.post("php/VisualisationScript.php", {
			do : "parse",
			data : result
		}, function(data) {
			return data;
		});
	}).fail(function(result) {
		$('.result').html("<p><b style='color:red'>Error retrieving data...</b></p><pre>" + result.responseText + "</pre>");
	});
}

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
