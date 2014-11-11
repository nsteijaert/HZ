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
				$('.result').html(result);
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
