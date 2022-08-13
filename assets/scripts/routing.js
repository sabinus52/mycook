const routes = require('../config/fos_js_routes.json'); // sf fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json
const Routing = require("../../public/bundles/fosjsrouting/js/router"); // do not forget to dump your assets `symfony console assets:install --symlink public`

Routing.setRoutingData(routes);

module.exports = Routing;
