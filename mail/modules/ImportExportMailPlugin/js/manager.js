'use strict';

var
	Settings = require('modules/%ModuleName%/js/Settings.js')
;

module.exports = function (oAppData) {
	Settings.init(oAppData);

	return {
		getImportExportPopup: function () {
			return require('modules/%ModuleName%/js/popups/ImportExportPopup.js');
		}
	};
};
