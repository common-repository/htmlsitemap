//<![CDATA[
jQuery(document).ready(function() {
	jQuery('#excludepages').click(function() {
		jQuery('#excludepages-inside').is(":hidden")?jQuery('#excludepages-inside').slideDown("slow"):jQuery('#excludepages-inside').slideUp("slow");
	});
});
//]]>
