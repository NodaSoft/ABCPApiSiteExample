{extends file='search/form.tpl'}

{block name="searchResult"}
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Бренд</th>
            <th>Номер</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$tpl_data.searchBrands item='row' }
            <tr class="firstStepRow" data-link="/?number={$row.number|escape:'url'}&brand={$row.brand|escape:'url'}">
                <td>{$row.brand}</td>
                <td>{$row.number}</td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="2">По вашему запросу ничего не найдено.</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/block}