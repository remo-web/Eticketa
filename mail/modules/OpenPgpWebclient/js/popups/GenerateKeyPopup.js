'use strict';

var
	_ = require('underscore'),
	$ = require('jquery'),
	ko = require('knockout'),
	
	TextUtils = require('%PathToCoreWebclientModule%/js/utils/Text.js'),
	
	ModulesManager = require('%PathToCoreWebclientModule%/js/ModulesManager.js'),
	Screens = require('%PathToCoreWebclientModule%/js/Screens.js'),
	
	CAbstractPopup = require('%PathToCoreWebclientModule%/js/popups/CAbstractPopup.js'),
	
	ErrorsUtils = require('modules/%ModuleName%/js/utils/Errors.js'),
	
	Enums = require('modules/%ModuleName%/js/Enums.js'),
	OpenPgp = require('modules/%ModuleName%/js/OpenPgp.js')
;

/**
 * @constructor
 */
function CGenerateKeyPopup()
{
	CAbstractPopup.call(this);
	
	this.emails = ko.observableArray([]);
	this.selectedEmail = ko.observable('');
	this.password = ko.observable('');
	this.keyLengthOptions = [2048, 4096];
	this.selectedKeyLength = ko.observable(2048);
	this.process = ko.observable(false);
	this.keysExistText = ko.observable('');
}

_.extendOwn(CGenerateKeyPopup.prototype, CAbstractPopup.prototype);

CGenerateKeyPopup.prototype.PopupTemplate = '%ModuleName%_GenerateKeyPopup';

CGenerateKeyPopup.prototype.onOpen = function ()
{
	var
		aEmails = ModulesManager.run('MailWebclient', 'getAllAccountsFullEmails') || [],
		aEmailsToUse = []
	;
	
	_.each(aEmails, function (sEmail) {
		var
			aPubKeys = OpenPgp.findKeysByEmails([sEmail], true),
			aPrivKeys = OpenPgp.findKeysByEmails([sEmail], false)
		;
		if (aPubKeys.length === 0 && aPrivKeys.length === 0)
		{
			aEmailsToUse.push(sEmail);
		}
	});
	
	if (aEmailsToUse.length === 0)
	{
		this.keysExistText(TextUtils.i18n('%MODULENAME%/INFO_KEYS_EXIST_PLURAL', null, null, aEmails.length));
	}
	
	this.emails(aEmailsToUse);
	this.selectedEmail('');
	this.password('');
	this.selectedKeyLength(2048);
	this.process(false);
};

CGenerateKeyPopup.prototype.generate = function ()
{
	if (this.emails().length === 0)
	{
		return;
	}
	
	var
		fKeysGenerated = _.bind(function () {
			Screens.showReport(TextUtils.i18n('%MODULENAME%/REPORT_KEY_SUCCESSFULLY_GENERATED'));
			this.process(false);
			this.closePopup();
		}, this),
		fKeysGenerateError = _.bind(function () {
			ErrorsUtils.showPgpErrorByCode({}, Enums.PgpAction.Generate);
			this.process(false);
			this.closePopup();
		}, this)
	;
	
	this.process(true);
	_.delay(_.bind(function () {
		OpenPgp.generateKey(this.selectedEmail(), $.trim(this.password()), this.selectedKeyLength(), fKeysGenerated, fKeysGenerateError);
	}, this));
};

module.exports = new CGenerateKeyPopup();
