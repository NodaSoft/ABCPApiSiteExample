{extends file='search/form.tpl'}

{block name="searchResult"}
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Бренд</th>
            <th>Номер</th>
            <th>Описание</th>
            <th>Поставщик</th>
            <th>Наличие</th>
            <th>Ожидаемый срок</th>
            <th>Цена</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$tpl_data.searchArticles item='row' }
            <tr>
                <td>{$row.brand}</td>
                <td>{$row.number}</td>
                <td>{$row.description}</td>
                <td>{$row.supplierCode}</td>
                <td>{$row.availability}</td>
                <td>{$row.deliveryPeriod}</td>
                <td>{$row.price}</td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="7">По вашему запросу ничего не найдено.</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/block}
