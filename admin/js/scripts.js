//--------(new window)-------------------------------------------------------------------------------------------------

	function openWindow(url,w,h)
	{
		var optionz = "width="+w+",height="+h+",menubar=no,location=no,directories=no,status=no,resizable=yes,scrollbars=yes";
		msgWindow = window.open(url,'WinOpen',optionz);
	}

//--------(check all checkboxes)---------------------------------------------------------------------------------------

	var checked = false;
	function checkAll()
	{
		var myform = document.getElementById("form2");
		
		if (checked == false) { checked = true }else{ checked = false }
		for (var i=0; i<myform.elements.length; i++) 
		{
			myform.elements[i].checked = checked;
		}
	}

///---------(tabs)--------------------------------------------------------------------------------------------------------

$(document).ready(function(){

	$(".tab_content").hide(); // Hide all content
	$("#tabs li:first").addClass("active").show(); // Activate first tab
	$(".tab_content:first").show(); // Show first tab content

	$("#tabs li").click(function() {
		//	First remove class "active" from currently active tab
		$("#tabs li").removeClass('active');

		//	Now add class "active" to the selected/clicked tab
		$(this).addClass("active");

		//	Hide all tab content
		$(".tab_content").hide();

		//	Here we get the href value of the selected tab
		var selected_tab = $(this).find("a").attr("href");

		//	Show the selected tab content
		$(selected_tab).fadeIn();
		return false;
	});

	if(window.location.hash) {
		var hash = window.location.hash;
		$('#tabs li').each(function() {
			if($(this).find('a').attr('href') == hash) {
				$("#tabs li").removeClass("active");
				$(this).addClass("active");
				$(".tab_content").hide();
				var activeTab =  $(this).find('a[href=' + hash + ']').attr('href');
				$(activeTab).fadeIn();
				return false;
			}
		});
	}

});