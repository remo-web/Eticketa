'use strict';

module.exports = function (oAppData) {
	var
		App = require('%PathToCoreWebclientModule%/js/App.js'),
				
		CAbstractFileModel = require('%PathToCoreWebclientModule%/js/models/CAbstractFileModel.js')
	;
	
	if (App.isUserNormalOrTenant())
	{
		return {
			start: function () {
				var aExtensionsToView = oAppData['%ModuleName%'] ? oAppData['%ModuleName%']['ExtensionsToView'] : [];
				CAbstractFileModel.addViewExtensions(aExtensionsToView);
			}
		};
	}
	
	return null;
};
