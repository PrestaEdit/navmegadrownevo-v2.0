<style type="text/css">
	{literal}
	.container {
		width: {/literal}{$MenuWidthEvo}{literal}px;
	}
	ul#topnavEvo {
		margin : {/literal}{$MarginTopEvo}{literal}px 0px {/literal}{$MarginBottomEvo}{literal}px 0px;
		height: {/literal}{$MenuHeightEvo}{literal}px;
		font-size: {/literal}{$FontSizeMenuEvo}{literal}px;
		background: #{/literal}{$GeneralColorEvo}{literal} {/literal}{if $bg_menuEvo.exist == 1}{literal}url({/literal}{$bg_menuEvo.path}{literal}) repeat-x{/literal}{/if}{literal};
		padding-left : {/literal}{$PaddingLeftEvo}{literal}px;
	}
	ul#topnavEvo li a {
		height: {/literal}{$HeightCalculateEvo}{literal}px;
		color: #{/literal}{$ColorFontMenuEvo}{literal};
		padding-top: {/literal}{$PaddingTopCalculateEvo}{literal}px;
		font-size: {/literal}{$FontSizeMenuEvo}{literal}px;
		min-width: {/literal}{$MinButtonWidthEvo}{literal}px;
		{/literal}
		{if $MaxButtonWidthEvo > 0}
			{literal}			
				max-width: {/literal}{$MaxButtonWidthEvo}{literal}px;
				word-wrap: break-word;
			{/literal}
		{/if}
		{literal}
		_width: {/literal}{$MinButtonWidthEvo}{literal}px;
	}
	ul#topnavEvo li:hover a, ul#topnavEvo li a:hover {
		color: #{/literal}{$ColorFontMenuHoverEvo}{literal};
	}
	{/literal}{if $bg_boutEvo.exist==1}{literal}
	ul#topnavEvo li:hover a, ul#topnavEvo li a:hover { 
		background-position: 0 -{/literal}{$MenuHeightEvo}{literal}px; 
	}
	{/literal}{/if}{literal}

	ul#topnavEvo a.buttons {
		{/literal}
		{if $bg_boutEvo.exist == 1}{literal}background: url({/literal}{$bg_boutEvo.path}{literal}) repeat-x{/literal}{/if}{literal};
	}

	ul#topnavEvo li .sub {
		top: {/literal}{$MenuHeightEvo}{literal}px;
		background: #{/literal}{$GeneralColorEvo}{literal} {/literal}{if $sub_bgEvo.exist == 1}{literal}url({/literal}{$sub_bgEvo.path}{literal}) repeat-x{/literal}{/if}{literal};
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
		{/literal}{if $navlist_arrowEvo.exist == 1}{literal}
		background: url({/literal}{$navlist_arrowEvo.path}{literal}) no-repeat 4px 10px;
		{/literal}{/if}{literal}
		padding: {/literal}{$VerticalPaddingEvo}{literal}px 5px {/literal}{$VerticalPaddingEvo}{literal}px 15px;
		color: #{/literal}{$ColorFontSubSubMenuEvo}{literal};
		font-size: {/literal}{$FontSizeSubSubMenuEvo}{literal}px;
	}
{/literal}

</style>
