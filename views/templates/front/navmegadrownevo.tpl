<!-- MeGa DrOwN mEnU Evolution v2.0 -->
	</div>
	<div style="padding-bottom: {$MarginBottomEvo}px; padding-top: {$MarginTopEvo}px ">
		<div class="container">
			<ul id="topnavEvo">
				{$menuMDEvo}
				{if $MENUEVO_SEARCH == "on"}
				<li class="sf-search noBack" style="float:right">
					<form id="searchbox" action="{$link->getPageLink('search')}" method="get">
						<p>
							<input type="hidden" name="controller" value="search" />
							<input type="hidden" value="position" name="orderby"/>
							<input type="hidden" value="desc" name="orderway"/>
							<input type="text" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|escape:'htmlall':'UTF-8'}{/if}" />
						</p>
					</form>
				</li>
				{/if}
			</ul>
		</div>
	</div>
	<div>
<!-- /MeGa DrOwN mEnU Evolution v2.0 -->

