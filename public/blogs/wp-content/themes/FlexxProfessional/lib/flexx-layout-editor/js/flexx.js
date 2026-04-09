(function($) {
	$(document).ready(
		function(){
			$(".flexx-option").click(
				function(event){
					$(this).siblings(".flexx-option").removeClass("flexx-option-selected");
					$(this).siblings(".flexx-option").addClass("flexx-option-not-selected");
					$(this).removeClass("flexx-option-not-selected");
					$(this).addClass("flexx-option-selected");
					
					$(this).siblings(":input").attr("value", $(this).attr("id")); 
					
					if($(this).html() == 'none')
					{
						$("#" + $(this).siblings(":input").attr("name") + "_flexx_preview").html("");
					}
					else
					{
						$("#" + $(this).siblings(":input").attr("name") + "_flexx_preview").html($(this).html());
					}
				}
			);
		}
	);
})(jQuery);