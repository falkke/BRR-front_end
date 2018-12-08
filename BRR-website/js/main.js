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

function DeleteAlert(type, id) 
{
	if(confirm("Are you sure ?")) 
	{
		if(type == "race") {
			window.location.href = "index.php?page=race-list&race=" + id + "&remove=1";
		}
		
		else if(type == "runner") {
			window.location.href = "index.php?page=runner-list&runner=" + id + "&remove=1";
		}
		
		else if(type == "team") {
			window.location.href = "index.php?page=team-list&team=" + id + "&remove=1";
		}
	}
}