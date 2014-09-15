{extends file='index.tpl'}

{block name=content}
    <div class="jumbotron">
        <form method="get" name="searchForm" action="/" class="form-inline">
			<img src="/qr-app.png" height="100" width="100" class="fr-img-rounded" style="position: absolute;top: 50px;" alt="Android App"/>
            <div class="col-lg-8 col-centered">
                <div class="input-group">
                    <input type="text" id="number" name="number" value="{$tpl_data.number}"
                           placeholder="Введите номер запчасти (например, oc90)"
                           class="form-control">

                    <div class="input-group-btn">
                        <button id="submitSearchButton" class="btn btn-primary" type="submit"
                                data-loading-text="Ищем...">
                            Найти
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>
    {block name="searchResult"}{/block}
{/block}