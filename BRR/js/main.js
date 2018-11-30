jQuery(document).ready(function($) 
{
	$(".clickable-row").click(function() 
	{
		window.location = $(this).data("href");
	});
});

function DeleteRaceAlert(id) 
{
	if(confirm("Are you sure ?")) 
	{
		window.location.href = "index.php?page=home&race=" + id + "&remove=1";
	}
}
