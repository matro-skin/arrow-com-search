# parts-search
* `<div id="vue-parts-search"></div>` добавить на страницу, где должна быть форма поиска
* `<script>const partsSearchConfig = { url: '/api', drivers: [ 'ArrowCom', 'Element14' ] }</script><script src="dist/js/partsSearch.js"></script>` добавить перед `</body>`, указать путь до файлов `/api`
* `api/index.php:ENV_PATH` - указать место хранения токена (файлы)
* переименовать `api/.env.example` в `api/.env` и заполнить его
