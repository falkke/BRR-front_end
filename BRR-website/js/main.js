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