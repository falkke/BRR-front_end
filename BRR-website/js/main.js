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

function DeleteRaceAlert(race_id) 
{
	if(confirm("Are you sure ?")) 
	{
		window.location.href = "index.php?page=race-list&race=" + race_id + "&remove=1";
	}
}
