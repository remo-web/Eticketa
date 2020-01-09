'use strict';

var
	_ = require('underscore'),

	App = require('%PathToCoreWebclientModule%/js/App.js'),
	TextUtils = require('%PathToCoreWebclientModule%/js/utils/Text.js'),
	Settings = require('modules/%ModuleName%/js/Settings.js')
;

module.exports = function (oAppData) {
	Settings.init(oAppData);

	return {
		/**
		 * Runs before application start. Subscribes to the event before post displaying.
		 * 
		 * @param {Object} ModulesManager
		 */
		start: function (ModulesManager) {
			ModulesManager.run('SettingsWebclient', 'registerSettingsTab', [
				function () { return require('modules/%ModuleName%/js/views/TwoFactorAuthSettingsFormView.js'); },
				Settings.HashModuleName,
				TextUtils.i18n('%MODULENAME%/LABEL_SETTINGS_TAB')
			]);
			App.subscribeEvent('StandardLoginFormWebclient::ConstructView::after', function (oParams) {
				var
					oLoginScreenView = oParams.View,
					Popups = require('%PathToCoreWebclientModule%/js/Popups.js'),
					VerifyTokenPopup = require('modules/%ModuleName%/js/popups/VerifyTokenPopup.js')
				;

				if (oLoginScreenView)
				{
					// Do not completely replace previous onSystemLoginResponse, because it might be already changed by another plugin
					var fOldOnSystemLoginResponse = oLoginScreenView.onSystemLoginResponse.bind(oLoginScreenView);
					if (!_.isFunction(fOldOnSystemLoginResponse))
					{
						fOldOnSystemLoginResponse = oLoginScreenView.onSystemLoginResponseBase.bind(oLoginScreenView);
					}
					if (!_.isFunction(fOldOnSystemLoginResponse))
					{
						fOldOnSystemLoginResponse = function () {};
					}
					oLoginScreenView.onSystemLoginResponse = function (oResponse, oRequest) {
						//if TwoFactorAuth enabled - trying to verify user token
						if (oResponse.Result && oResponse.Result.TwoFactorAuth && oResponse.Result.TwoFactorAuth.UserId)
						{
							Popups.showPopup(VerifyTokenPopup, [
								_.bind(this.onSystemLoginResponseBase, this),
								_.bind(function () { this.loading(false); }, this),
								oResponse.Result.TwoFactorAuth.UserId
							]);
						}
						else
						{
							fOldOnSystemLoginResponse(oResponse, oRequest);
						}
					};
				}
			});
		}
	};
};
