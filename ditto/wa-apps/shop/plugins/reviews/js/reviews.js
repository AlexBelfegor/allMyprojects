$(function() {

	$("#form-add-comment").submit(function () {
	    var f = $(this);
	    var url = $("#product_url").val();

	    $.post("/reviews/senddata/?type=reviewadd", f.serialize(), function (response)
	    {
	       	$('.form-input').css({'border':'1px solid #7FCD5E'});

	        if (response.data.status == 'ok')
	        {
	        	$("#form-add-comment").html("<h3>Спасибо, ваш комментарий был успешно добавлен. <a href=\'"+url+"\'>Обновить страницу.</a></h3>");
	        }
	        else if (response.data.status == 'fail')
	        {
	        	$.each(response.data.error, function( index, value )
	        	{
					if(value != "")
					{
                    	$('#'+index).css({'border':'1px solid #cc0000'});
					}

					if(index == 'capcha')
					{
						$('.wa-captcha-input').css({'border':'1px solid #cc0000'});
					}
				});
	        }
	    }, "json");

	    return false;
	});

	$(".yes").click(function () {
        var id_review = $(this).data("id_review");
        $.post("/reviews/senddata/?type=reviewcount&id_review="+id_review+"&status=yes", function (response)
        {
            if (response.data.status == 'ok')
            {
		        var $cnt = parseInt($("#yes_"+id_review).html());
		        $("#yes_"+id_review).html($cnt + 1);
            }

        }, "json");

        return false;
    });

	$(".no").click(function () {
        var id_review = $(this).data("id_review");

        $.post("/reviews/senddata/?type=reviewcount&id_review="+id_review+"&status=no", function (response)
        {
            if (response.data.status == 'ok')
            {
		        var $cnt = parseInt($("#no_"+id_review).html());
		        $("#no_"+id_review).html($cnt + 1);
            }

        }, "json");

        return false;
    });

});

function votecomment(number)
{
	$('#big-currant-black').attr("style","width:0px;z-index:0;");
	$('#big-currant-black').attr("style","width:"+(number*26)+"px;z-index:0;");

	$('#mark').val(number);

    return false;
}
