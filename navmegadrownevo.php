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
<div style="padding-bottom: {$MarginBottomEvo}px; padding-top: {$MarginTopEvo}px ">
    <div class="container">
        <ul id="topnavEvo">
            {foreach from=$buttons item=button}
            <li class="liBouton liBouton{$button.id_button}" style="background-color: {$button.background_color};">
                <div>
                    <a class="buttons" {if $button.link}href="{$button.link}"{else}onClick="return false;" href="#"{/if}>{$button.name}</a>
                </div>
                <div style="width: {$parameters.MenuWidth}px; background-color: {$button.background_color};" class="sub">
                    <table width="100%" cellspacing="0" cellpadding="0" class="megaDrownTable">
                        <tbody>
                        <tr style="height:{$parameters.heightTR1}px">
                            <td valign="top" colspan="2" class="megaDrownTR1">
                                {$button.top}
                            </td>
                            <td valign="top" style="width:150px" class="megaDrownTD3" rowspan="2">
                                {$button.right}
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" style="width:150px" class="megaDrownTD1">
                                <br />
                                {$button.left}
                            </td>
                            <td valign="top" class="megaDrownTD2">
                                <table style="border:0px" class="MegaEvoLinks">
                                    <tbody>
                                    <tr>
                                        <td valign="top" class="col1"></td>
                                        <td valign="top" class="col2"></td>
                                        <td valign="top" class="col3"></td>
                                        <td valign="top" class="col4"></td>
                                        <td valign="top" class="col5"></td>
                                        <td valign="top" class="col6"></td>
                                        <td valign="top" class="col7"></td>
                                        <td valign="top" class="col8"></td>
                                        <td valign="top" class="col9"></td>
                                        <td valign="top" class="col10">
                                            <table border="0" style="width:150px">
                                                <tbody>
                                                <tr>
                                                    <td class="ligne10" style="width:150px">
                                                        <ul style="list-style: none outside none;">
                                                            <li class="stitle"><a style="text-align:left" onclick="return false" href="">Test01</a></li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </li>
            {/foreach}
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