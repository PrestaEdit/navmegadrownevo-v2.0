<script type="text/javascript" src="{$pathMDEvo}/views/js/jquery.hoverIntent.minified.js"></script>
<script type="text/javascript" src="{$pathMDEvo}/views/js/navmegadrownEvo.js"></script>
<link href="{$pathMDEvo}/views/css/navmegadrownEvo.css" rel="stylesheet" type="text/css" />
<style type="text/css">
	{literal}
	.container {
		width: {/literal}{$MenuWidthEvo}{literal}px;
	}
	ul#topnavEvo {
		margin : {/literal}{$MarginTopEvo}{literal}px 0px {/literal}{$MarginBottomEvo}{literal}px 0px;
		height: {/literal}{$MenuHeightEvo}{literal}px;
		font-size: {/literal}{$FontSizeMenuEvo}{literal}px;
		background: #{/literal}{$GeneralColorEvo}{literal} {/literal}{if $bg_menuEvo==1}{literal}url({/literal}{$pathMDEvo}{literal}img/bg_menu{/literal}{$extensionMenuEvo}{literal}) repeat-x{/literal}{/if}{literal};
		padding-left : {/literal}{$PaddingLeftEvo}{literal}px;
	}
	ul#topnavEvo li a {
		font-weight:normal;
		height: {/literal}{$HeightCalculateEvo}{literal}px;
		color: #{/literal}{$ColorFontMenuEvo}{literal};
		padding-top: {/literal}{$PaddingTopCalculateEvo}{literal}px;
		font-size: {/literal}{$FontSizeMenuEvo}{literal}px;
		min-width: {/literal}{$MinButtonWidthEvo}{literal}px;
		_width: {/literal}{$MinButtonWidthEvo}{literal}px;
	}
	ul#topnavEvo li:hover a, ul#topnavEvo li a:hover {
		color: #{/literal}{$ColorFontMenuHoverEvo}{literal};
	}
	{/literal}{if $bg_boutEvo==1}{literal}
	ul#topnavEvo li:hover a, ul#topnavEvo li a:hover { 
		background-position: 0 -{/literal}{$MenuHeightEvo}{literal}px; 
	}
	{/literal}{/if}{literal}

	ul#topnavEvo a.buttons {
		{/literal}{if $bg_boutEvo==1}{literal}background: url({/literal}{$pathMDEvo}{literal}img/bg_bout{/literal}{$extensionBoutEvo}{literal}) repeat-x;{/literal}{/if}{literal}
	}

	ul#topnavEvo li .sub {
		top: {/literal}{$MenuHeightEvo}{literal}px;
		background: #{/literal}{$GeneralColorEvo}{literal} {/literal}{if $sub_bgEvo==1}{literal}url({/literal}{$pathMDEvo}{literal}img/sub_bg{/literal}{$extensionBackEvo}{literal}) repeat-x{/literal}{/if}{literal};
	}
	.megaDrownTR1 {
		background-color:#{/literal}{$bgColorTR1Evo}{literal};
	}
	.megaDrownTD1 {
		background-color:#{/literal}{$bgColorTD1Evo}{literal};
	}
	.megaDrownTD2 {
		background-color:#{/literal}{$bgColorTD2Evo}{literal};
	}
	.megaDrownTD3 {
		background-color:#{/literal}{$bgColorTD3Evo}{literal};
	}
	
	ul#topnavEvo li .sub {
		padding-top: 10px;
	}
	
	ul#topnavEvo li .sub ul{
		width: {/literal}{$ColumnWidthEvo}{literal}px;
		margin-left: 10px;

	}

	ul#topnavEvo .sub ul li.stitle a {
		font-size: {/literal}{$FontSizeSubMenuEvo}{literal}px;
		color: #{/literal}{$ColorFontSubMenuEvo}{literal};
	}
	ul#topnavEvo .sub ul li a:hover {
		color: #{/literal}{$ColorFontSubSubMenuHoverEvo}{literal};
	}
	ul#topnavEvo .sub ul li.stitle a:hover {
		color: #{/literal}{$ColorFontSubMenuHoverEvo}{literal};
		
	}
	ul#topnavEvo .sub ul li a {
		{/literal}{if $navlist_arrowEvo==1}{literal}
		background: url({/literal}{$pathMDEvo}{literal}img/navlist_arrow{/literal}{$extensionArroEvo}{literal}) no-repeat 4px 10px;
		{/literal}{/if}{literal}
		padding: {/literal}{$VerticalPaddingEvo}{literal}px 5px {/literal}{$VerticalPaddingEvo}{literal}px 15px;
		color: #{/literal}{$ColorFontSubSubMenuEvo}{literal};
		font-size: {/literal}{$FontSizeSubSubMenuEvo}{literal}px;
		width : 80%;
	}
{/literal}

</style>
