$(window).ready(function() 
{
	$('dfn.cms-anchor').each(function()
	{
		if ($(this).id=='cms-anchor-global') var Element= $('body');
		else var Element= $(this).next();
		var Offset= Element.offset();
		$(this).css("top",Offset.top);
		$(this).css("left",Offset.left);
	});

	$('dfn.cms-anchor').hover(function() 
	{
		cms_veil_show(this);
	});

	$('dfn.cms-anchor').click(function(event) 
	{
 		cms_context_show(this);
	});
});

function cms_veil_show(Anchor)
{
	$('body').append('<div id="cms-veil"></div>');
	var Veil= $('#cms-veil');
	if (Anchor.id=='cms-anchor-global') var Element= $('body');
	else var Element= $(Anchor).next();
	var Offset= Element.offset();
	var Width= Element.outerWidth();
	var Height= Element.outerHeight();
	Veil.css("top",Offset.top);
	Veil.css("left",Offset.left);
	Veil.width(Width);
	Veil.height(Height);
	Veil.stop(true,true).fadeIn();
	$(Anchor).css('z-index', Veil.css('z-index')+1);
	Veil.click(function() {
  		cms_veil_hide();
	});
	Veil.mouseout(function() {
  		cms_veil_hide();
	});
	return false;
}

function cms_veil_hide()
{
	cms_context_hide();
	$('#cms-veil').remove();
}

function cms_context_show(Anchor)
{
	cms_context_hide();
	$('body').append('<div id="cms-context"></div>');
	var Context= $('#cms-context');
	if (Anchor.id=='cms-anchor-global') var Element= $('body');
	else var Element= $(Anchor).next();
	
	var Title= $(Anchor).attr('title');
	if (Title==undefined) Title= 'Control options:';
	Context.append('<h9>'+Title+'</h9>');

	var Data= $(Anchor).children().text();
	Data= JSON.parse(Data);
	for (i in Data) 
	{
		var Html= '';
		if (Data[i].url==undefined)
		{
			var Tag= 'div';
			var Title= (Data[i].popuptitle==undefined ? Data[i].title : Data[i].popuptitle);
			var Attr= ' onclick="return cms_popup_show(\''+Title+'\',false,\''+Data[i].popup+'\')"';
		}
		else
		{
			var Tag= 'a';
			var Attr= ' href="'+Data[i].url+'"';
			if (Data[i].confirm!=undefined) Attr+= ' onclick="return confirm(\''+Data[i].confirm+'\')"';
		}
		Html+= '<'+Tag+Attr+'>';
		if (Data[i].icon[0]<'a' && Data[i].icon[0]<'z') var Class= '';
		else var Class= ' class="material-icons md-18"';
		Html+= '<i'+Class+'>'+Data[i].icon+'</i>';
		//Html+= '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="64" height="64" viewBox="0 0 64 64"><path d="M0 64h32v-64h-32v64zM20 8h8v8h-8v-8zM20 24h8v8h-8v-8zM20 40h8v8h-8v-8zM4 8h8v8h-8v-8zM4 24h8v8h-8v-8zM4 40h8v8h-8v-8zM36 20h28v4h-28zM36 64h8v-16h12v16h8v-36h-28z" fill="white"></path></svg>';
		Html+= '<span>'+Data[i].title+'</span>';
		Html+= '</'+Tag+'>';
		Context.append(Html);
	}	
	var Offset= Element.offset();
	if (Offset.left+Context.width()>$(window).width()) Offset.left= $(window).width()-Context.width(); 
	Context.css("top",Offset.top);
	Context.css("left",Offset.left);
	Context.css('z-index', $(Anchor).css('z-index')+1);
	Context.show();
	return false;
}

function cms_context_hide()
{
	$('#cms-context').remove();
}

function cms_popup_show(Title,Content,Source)
{
	cms_popup_hide()
	$('body').append(' \
		<div id="cms-popup"> \
			<i id="cms-popup-close" class="material-icons md-18" onclick="cms_popup_hide()">close</i> \
			<h3>'+Title+'</h3> \
			<div id="cms-popup-content"> \
				<marquee direction="right">&FilledSmallSquare;&FilledSmallSquare;&FilledSmallSquare;</marquee> \
			</div> \
		</div> \
	');
	var Popup= $('#cms-popup');
	cms_popup_position();
	Popup.css('z-index', $('#cms-context').css('z-index')+1);

	if (Content) Popup.find('#cms-popup-content').html(Content);	
	else if (Source!=undefined) Popup.find('#cms-popup-content').load(Source,'',cms_popup_shown);	
	return Popup;
}

function cms_popup_shown(response, status, xhr)
{
	if (status=="error")
	{
		$('#cms-popup').find('h3').text('Error');
		$('#cms-popup').find('#cms-popup-content').text(response);
	}
	else
	{
		cms_popup_position();
		if (typeof on_cms_popup=='function') on_cms_popup();
	}
}

function cms_popup_position()
{
	var Popup= $('#cms-popup');
	var Left= ($(window).width() - Popup.outerWidth()) / 2 + $(window).scrollLeft(); 
	var Top= $(window).scrollTop() + 50;
	if (Left<0) Left= 0;
	Popup.css("top",Top);
	Popup.css("left",Left);
}

function cms_popup_hide()
{
	$('#cms-popup').remove();
}

function cms_file_preview(Input,Target)
{
	if (Input.files && Input.files[0]) 
	{
		var Reader= new FileReader();
		Reader.onload= function (e) 
		{
			$('#'+Target).replaceWith('<img src="'+e.target.result+'" style="width:70px;" id="'+Target+'">');
		}
		Reader.readAsDataURL(Input.files[0]);
	}
}

function cms_checkbox_adapt(Element,Check,Uncheck)
{
	var Value= $(Element).prop('checked');
	Value= (Value?Check:Uncheck);
	$(Element).prev().val(Value);
}
