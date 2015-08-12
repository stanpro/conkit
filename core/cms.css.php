dfn.cms-anchor,
div#cms-veil,
div#cms-context,
div#cms-popup,
form#cms-form *
{
    animation : none;
    animation : none;
    animation-delay : 0;
    animation-direction : normal;
    animation-duration : 0;
    animation-fill-mode : none;
    animation-iteration-count : 1;
    animation-name : none;
    animation-play-state : running;
    animation-timing-function : ease;
    backface-visibility : visible;
    background : 0;
    background-attachment : scroll;
    background-clip : border-box;
    background-color : transparent;
    background-image : none;
    background-origin : padding-box;
    background-position : 0 0;
    background-position-x : 0;
    background-position-y : 0;
    background-repeat : repeat;
    background-size : auto auto;
    border : 0;
    border-style : none;
    border-width : medium;
    border-color : inherit;
    border-bottom : 0;
    border-bottom-color : inherit;
    border-bottom-left-radius : 0;
    border-bottom-right-radius : 0;
    border-bottom-style : none;
    border-bottom-width : medium;
    border-collapse : separate;
    border-image : none;
    border-left : 0;
    border-left-color : inherit;
    border-left-style : none;
    border-left-width : medium;
    border-radius : 0;
    border-right : 0;
    border-right-color : inherit;
    border-right-style : none;
    border-right-width : medium;
    border-spacing : 0;
    border-top : 0;
    border-top-color : inherit;
    border-top-left-radius : 0;
    border-top-right-radius : 0;
    border-top-style : none;
    border-top-width : medium;
    bottom : auto;
    box-shadow : none;
    box-sizing : content-box;
    caption-side : top;
    clear : none;
    clip : auto;
    color : inherit;
    columns : auto;
    column-count : auto;
    column-fill : balance;
    column-gap : normal;
    column-rule : medium none currentColor;
    column-rule-color : currentColor;
    column-rule-style : none;
    column-rule-width : none;
    column-span : 1;
    column-width : auto;
    content : normal;
    counter-increment : none;
    counter-reset : none;
    cursor : auto;
    direction : ltr;
    /*display : inline;*/
    empty-cells : show;
    float : none;
	/*font-family : Arial,Helv,Helvetica;*/
    font-size : medium;
    font-style : normal;
    font-variant : normal;
    font-weight : normal;
    height : auto;
    hyphens : none;
    left : auto;
    letter-spacing : normal;
    line-height : normal;
    list-style : none;
    list-style-image : none;
    list-style-position : outside;
    list-style-type : disc;
    margin : 0;
    margin-bottom : 0;
    margin-left : 0;
    margin-right : 0;
    margin-top : 0;
    max-height : none;
    max-width : none;
    min-height : 0;
    min-width : 0;
    opacity : 1;
    orphans : 0;
    outline : 0;
    outline-color : invert;
    outline-style : none;
    outline-width : medium;
    overflow : visible;
    overflow-x : visible;
    overflow-y : visible;
    padding : 0;
    padding-bottom : 0;
    padding-left : 0;
    padding-right : 0;
    padding-top : 0;
    page-break-after : auto;
    page-break-before : auto;
    page-break-inside : auto;
    perspective : none;
    perspective-origin : 50% 50%;
    position : static;
    /* May need to alter quotes for different locales (e.g fr) */
    quotes : '\201C' '\201D' '\2018' '\2019';
    right : auto;
    tab-size : 8;
    table-layout : auto;
    text-align : inherit;
    text-align-last : auto;
    text-decoration : none;
    text-decoration-color : inherit;
    text-decoration-line : none;
    text-decoration-style : solid;
    text-indent : 0;
    text-shadow : none;
    text-transform : none;
    top : auto;
    transform : none;
    transform-style : flat;
    transition : none;
    transition-delay : 0s;
    transition-duration : 0s;
    transition-property : none;
    transition-timing-function : ease;
    unicode-bidi : normal;
    vertical-align : baseline;
    visibility : visible;
    white-space : normal;
    widows : 0;
    width : auto;
    word-spacing : normal;
    z-index : auto;
}


dfn.cms-anchor
{
	position: absolute;
	width: 12px;
	height: 12px;
	border-radius: 6px;
	background-color: <?=core::config('cms-bcolor')?>;
	box-shadow: 0 0 10px <?=core::config('cms-fcolor')?>;
	cursor: pointer;
}
dfn.cms-anchor code
{
	display: none;
}
div#cms-veil
{
	position: absolute;
	display: none;
	background-color: <?=core::config('cms-bcolor')?>;
	opacity: 0.5;
	cursor: pointer;
}
div#cms-context
{
	position: absolute;
	display: none;
	background-color: <?=core::config('cms-bcolor')?>;
	color: <?=core::config('cms-fcolor')?>;
	box-shadow: 0 0 20px <?=core::config('cms-fcolor')?>;
}
div#cms-context h9
{
	display: block;
	font-size: 14px;
	font-weight: bold;
	font-style: normal;
	margin: 0;
	text-align: center;
	background-color: <?=core::config('cms-bcolor2')?>;
	padding: 3px 7px;
}	
div#cms-context a, div#cms-context div
{
	display: block;
	height: 25px;
	font-size: 13px;
	line-height: 25px;
	color: <?=core::config('cms-fcolor')?>;
	text-decoration: none;
	border-top: solid 1px <?=core::config('cms-fcolor')?>;
	padding-right: 7px;
	cursor: pointer;
}
div#cms-context i
{
	display: block;
	position: absolute;
	width: 25px;
	height: 25px;
	line-height: 25px;
	font-size: 18px;
	text-align: center;
	background-color: <?=core::config('cms-bcolor2')?>;
	font-weight: normal;
}
div#cms-context svg
{
	width: 17px;
	height: 17px;
	padding: 4px;
	background-color: <?=core::config('cms-bcolor2')?>;
	vertical-align: middle;
}
div#cms-context span
{
	display: inline-block;
	height: 25px;
	line-height: 25px;
	padding: 0 7px 0 32px;
	vertical-align: middle;
}
div#cms-popup
{
	position: absolute;
	color: <?=core::config('cms-fcolor')?>;
	background-color: <?=core::config('cms-bcolor')?>;
	box-shadow: 0 0 20px <?=core::config('cms-fcolor')?>;
	min-width: 300px;
}
div#cms-popup marquee
{
	display: block;
	margin: 10px 100px;
	border: solid thin <?=core::config('cms-fcolor')?>;
}
div#cms-popup h3
{
	font-size: 16px;
	line-height: 20px;
	background-color: <?=core::config('cms-bcolor2')?>;
	color: <?=core::config('cms-fcolor')?>;
	text-align: center;
	padding: 5px 0;
	margin: 0 0 10px 0;
}
div#cms-popup i#cms-popup-close
{
	display: block;
	float: right;
	text-align: center;
	cursor: pointer;
	font-size: 18px;
	line-height: 20px;
	padding-right: 10px;
	padding-top: 4px;
	vertical-align: center;
}
div#cms-popup div#cms-popup-content
{
	margin: 10px;
}
form#cms-form div
{
	margin-top: 3px;
	text-align: right;
	white-space: nowrap;
}
form#cms-form label
{
	line-height: 25px;
	text-align: right;
	margin-right: 10px;
	vertical-align: top;
}
form#cms-form fieldset label
{
	text-align: left;
	vertical-align: middle;
	cursor: default;
}
form#cms-form input[type="text"], 
form#cms-form select, 
form#cms-form textarea,
form#cms-form input[type="password"],
form#cms-form input[type="file"]
{
	width: 300px;
	height: 23px;
	border: solid thin <?=core::config('cms-bcolor2')?>;
	color: black;
	background-color: <?=core::config('cms-fcolor')?>;
	text-align: left;
	padding: 0 3px;
	box-sizing: border-box;
}
form#cms-form textarea 
{
	height: auto;
}
form#cms-form input[type="submit"], 
div#cms-popup .cms-button
{
	width: auto;
	height: 23px;
	line-height: 23px;
	padding: 3px 15px;
	border: solid thin <?=core::config('cms-fcolor')?>;
	color: <?=core::config('cms-fcolor')?>;
	background-color: <?=core::config('cms-bcolor2')?>;
	cursor: pointer;
	text-decoration: none;
}
form#cms-form input[type="radio"], 
form#cms-form input[type="checkbox"]
{
	display: inline-block;
	width: 15px;
	height: 15px;
	background-color: transparent;
	border: 0;
	vertical-align: middle;	
}
form#cms-form select option 
{
	color: <?=core::config('cms-fcolor')?>;
	background-color: <?=core::config('cms-bcolor2')?>;
}
form#cms-form fieldset 
{
	width: 300px;
	min-height: 23px;
    display: inline-block;
    margin: 0;
    padding: 0;
    border: none;
    text-align: left;
}
form#cms-form div.cms-form-static 
{
	text-align: center;
}
div#cms-popup h4
{
	text-align: center; 
	margin: 10px 0 0 0;
}
div#cms-popup hr
{
    border: 0;
    height: 1px;
    background-color: <?=core::config('cms-fcolor')?>;
}

.material-icons.md-18 { font-size: 18px; }
