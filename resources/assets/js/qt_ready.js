var _ = require("lodash");

function executeReadyFunction(f) {
  f();
}

function initQtReady() {
  var functions = global.qtReady || [];
  global.qtReady = {
    push: executeReadyFunction
  };
  _.each(functions, executeReadyFunction);
}

module.exports = initQtReady;
