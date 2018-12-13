jQuery(document).ready(function($) 
{
	$(".no-change").click(function(event)
	{
		event.stopPropagation();
	});
	
	$(".clickable-row").click(function()
	{
		window.location = $(this).data("href");
	});
});

function DeleteAlert_timestamp(timestamp, runner_id, race_id) 
{
	if(confirm("Are you sure ?")) 
	{
		window.location.href = "index.php?page=runner&runner=" + runner_id + "&race=" + race_id + "&timestamp=" + timestamp + "&remove=1";
	}
}

function DeleteAlert(from_dashboard, type, id) 
{
	if(confirm("Are you sure ?")) 
	{
		if(from_dashboard == 1) {
			if(type == "race") {
				window.location.href = "index.php?page=dashboard&list=races&race=" + id + "&remove=1";
			}
			
			else if(type == "runner") {
				window.location.href = "index.php?page=dashboard&list=runners&runner=" + id + "&remove=1";
			}
			
			else if(type == "team") {
				window.location.href = "index.php?page=dashboard&list=teams&team=" + id + "&remove=1";
			}
			
			else if(type == "station") {
				window.location.href = "index.php?page=dashboard&list=stations&station=" + id + "&remove=1";
			}
			
			else if(type == "si-unit") {
				window.location.href = "index.php?page=dashboard&list=si-units&si-unit=" + id + "&remove=1";
			}
			
			else if(type == "category") {
				window.location.href = "index.php?page=dashboard&list=categories&category=" + id + "&remove=1";
			}
		}		
		
		else {
			if(type == "race") {
				window.location.href = "index.php?page=races&race=" + id + "&remove=1";
			}
			
			else if(type == "runner") {
				window.location.href = "index.php?page=runners&runner=" + id + "&remove=1";
			}
			
			else if(type == "team") {
				window.location.href = "index.php?page=teams&team=" + id + "&remove=1";
			}
		}
	}
}