'use strict';

var
	_ = require('underscore'),
	$ = require('jquery'),
	ko = require('knockout'),
	
	AddressUtils = require('%PathToCoreWebclientModule%/js/utils/Address.js'),
	Types = require('%PathToCoreWebclientModule%/js/utils/Types.js'),
	
	App = require('%PathToCoreWebclientModule%/js/App.js'),
	openpgp = require('%PathToCoreWebclientModule%/js/vendors/openpgp.js'),
	Enums = require('modules/%ModuleName%/js/Enums.js'),
	COpenPgpKey = require('modules/%ModuleName%/js/COpenPgpKey.js'),
	COpenPgpResult = require('modules/%ModuleName%/js/COpenPgpResult.js')
;

/**
 * @constructor
 */
function COpenPgp()
{
	var sPrefix = 'user_' + (App.getUserId() || '0') + '_';

	this.oKeyring = new openpgp.Keyring(new openpgp.Keyring.localstore(sPrefix));
	this.keys = ko.observableArray([]);
	this.oKeyring.load()
	.then(() => {
		this.reloadKeysFromStorage();
	});
}

COpenPgp.prototype.oKeyring = null;
COpenPgp.prototype.keys = [];

/**
 * @return {Array}
 */
COpenPgp.prototype.getKeys = function ()
{
	return this.keys();
};

/**
 * @return {mixed}
 */
COpenPgp.prototype.getKeysObservable = function ()
{
	return this.keys;
};

/**
 * @private
 */
COpenPgp.prototype.reloadKeysFromStorage = function ()
{
	var
		aKeys = [],
		oOpenpgpKeys = this.oKeyring.getAllKeys()
	;

	_.each(oOpenpgpKeys, function (oItem) {
		if (oItem && oItem.primaryKey)
		{
			aKeys.push(new COpenPgpKey(oItem));
		}
	});

	this.keys(aKeys);
};

/**
 * @private
 * @param {Array} aKeys
 * @return {Array}
 */
COpenPgp.prototype.convertToNativeKeys = function (aKeys)
{
	return _.map(aKeys, function (oItem) {
		return (oItem && oItem.pgpKey) ? oItem.pgpKey : oItem;
	});
};

/**
 * @private
 * @param {Object} oKey
 */
COpenPgp.prototype.cloneKey = async function (oKey)
{
	var oPrivateKey = null;
	if (oKey)
	{
		oPrivateKey = await openpgp.key.readArmored(oKey.armor());
		if (oPrivateKey && !oPrivateKey.err && oPrivateKey.keys && oPrivateKey.keys[0])
		{
			oPrivateKey = oPrivateKey.keys[0];
			if (!oPrivateKey || !oPrivateKey.primaryKey)
			{
				oPrivateKey = null;
			}
		}
		else
		{
			oPrivateKey = null;
		}
	}

	return oPrivateKey;
};

/**
 * @private
 * @param {Object} oResult
 * @param {Object} oKey
 * @param {string} sPassword
 * @param {string} sKeyEmail
 */
COpenPgp.prototype.decryptKeyHelper = async function (oResult, oKey, sPassword, sKeyEmail)
{
	if (oKey && oKey.primaryKey && oKey.primaryKey.isDecrypted() && sPassword === '')
	{
		//key is encoded with an empty password
	}
	else if(oKey)
	{
		try
		{
			await oKey.decrypt(Types.pString(sPassword));
			if (!oKey || !oKey.primaryKey || !oKey.primaryKey.isDecrypted())
			{
				oResult.addError(Enums.OpenPgpErrors.KeyIsNotDecodedError, sKeyEmail || '');
			}
		}
		catch (e)
		{
			oResult.addExceptionMessage(e, Enums.OpenPgpErrors.KeyIsNotDecodedError, sKeyEmail || '');
		}
	}
	else
	{
		oResult.addError(Enums.OpenPgpErrors.KeyIsNotDecodedError, sKeyEmail || '');
	}
};

/**
 * @private
 * @param {Object} oResult
 * @param {string} sFromEmail
 * @param {Object} oDecryptedMessage
 */
COpenPgp.prototype.verifyMessageHelper = async function (oResult, sFromEmail, oDecryptedMessage)
{
	var
		bResult = false,
		oValidKey = null,
		aVerifyResult = [],
		aVerifyKeysId = [],
		aPublicKeys = []
	;

	if (oDecryptedMessage && oDecryptedMessage.getSigningKeyIds)
	{
		aVerifyKeysId = oDecryptedMessage.getSigningKeyIds();
		if (aVerifyKeysId && 0 < aVerifyKeysId.length)
		{
			aPublicKeys = this.findKeysByEmails([sFromEmail], true);
			if (!aPublicKeys || 0 === aPublicKeys.length)
			{
				oResult.addNotice(Enums.OpenPgpErrors.PublicKeyNotFoundNotice, sFromEmail);
			}
			else
			{
				aVerifyResult = [];
				try
				{
					aVerifyResult = await oDecryptedMessage.verify(this.convertToNativeKeys(aPublicKeys));
				}
				catch (e)
				{
					oResult.addNotice(Enums.OpenPgpErrors.VerifyErrorNotice, sFromEmail);
				}

				if (aVerifyResult && 0 < aVerifyResult.length)
				{
					let aValidityPromises = [];
					for (let oKey of aVerifyResult)
					{
						aValidityPromises.push(
							oKey.verified
							.then(validity => {
								return oKey && oKey.keyid && validity ? oKey : null
							})
						);
					}
					await Promise.all(aValidityPromises)
					.then(aKeys => {
						oValidKey = _.find(aKeys, function (oKey) {
							return oKey !== null;
						});
						if (oValidKey && oValidKey.keyid && 
							aPublicKeys && aPublicKeys[0] &&
							oValidKey.keyid.toHex().toLowerCase() === aPublicKeys[0].getId())
						{
							bResult = true;
						}
						else
						{
							oResult.addNotice(Enums.OpenPgpErrors.VerifyErrorNotice, sFromEmail);
						}
					});
				}
			}
		}
		else
		{
			oResult.addNotice(Enums.OpenPgpErrors.NoSignDataNotice);
		}
	}
	else
	{
		oResult.addError(Enums.OpenPgpErrors.UnknownError);
	}

	if (!bResult && !oResult.hasNotices())
	{
		oResult.addNotice(Enums.OpenPgpErrors.VerifyErrorNotice);
	}

	return bResult;
};

/**
 * @param {string} sUserID
 * @param {string} sPassword
 * @param {number} nKeyLength
 * @param {Function} fOkHandler
 * @param {Function} fErrorHandler
 *
 * @return {COpenPgpResult}
 */
COpenPgp.prototype.generateKey = function (sUserID, sPassword, nKeyLength, fOkHandler, fErrorHandler)
{
	var
		oEmailParts = AddressUtils.getEmailParts(sUserID),
		oOptions = {
			userIds: [{ name: oEmailParts.name, email: oEmailParts.email }],
			numBits: nKeyLength,
			passphrase: sPassword
		}
	;

	openpgp.generateKey(oOptions).then(_.bind(async function(oKeyPair) {
		await this.oKeyring.privateKeys.importKey(oKeyPair.privateKeyArmored);
		await this.oKeyring.publicKeys.importKey(oKeyPair.publicKeyArmored);
		await this.oKeyring.store();
		if (_.isFunction(fOkHandler))
		{
			fOkHandler();
		}
		this.reloadKeysFromStorage();
		}, this),
		function (err) {
			if (_.isFunction(fErrorHandler))
			{
				fErrorHandler();
			}
		}
	);
};

/**
 * @private
 * @param {string} sArmor
 * @return {Array}
 */
COpenPgp.prototype.splitKeys = function (sArmor)
{
	var
		aResult = [],
		iCount = 0,
		iLimit = 30,
		aMatch = null,
		sKey = $.trim(sArmor),
		oReg = /[\-]{3,6}BEGIN[\s]PGP[\s](PRIVATE|PUBLIC)[\s]KEY[\s]BLOCK[\-]{3,6}[\s\S]+?[\-]{3,6}END[\s]PGP[\s](PRIVATE|PUBLIC)[\s]KEY[\s]BLOCK[\-]{3,6}/gi
	;

//	If the key doesn't have any additional fields (for example "Version: 1.1"), this transformation corrupts the key.
//	Seems like it is unnecessary transformation. Everything works fine without it.
//	sKey = sKey.replace(/[\r\n]([a-zA-Z0-9]{2,}:[^\r\n]+)[\r\n]+([a-zA-Z0-9\/\\+=]{10,})/g, '\n$1---xyx---$2')
//		.replace(/[\n\r]+/g, '\n').replace(/---xyx---/g, '\n\n');

	do
	{
		aMatch = oReg.exec(sKey);
		if (!aMatch || 0 > iLimit)
		{
			break;
		}

		if (aMatch[0] && aMatch[1] && aMatch[2] && aMatch[1] === aMatch[2])
		{
			if ('PRIVATE' === aMatch[1] || 'PUBLIC' === aMatch[1])
			{
				aResult.push([aMatch[1], aMatch[0]]);
				iCount++;
			}
		}

		iLimit--;
	}
	while (true);

	return aResult;
};

/**
 * @param {string} sArmor
 * @return {COpenPgpResult}
 */
COpenPgp.prototype.importKeys = async function (sArmor)
{
	sArmor = $.trim(sArmor);

	var
		iIndex = 0,
		iCount = 0,
		oResult = new COpenPgpResult(),
		aData = null,
		aKeys = []
	;

	if (!sArmor)
	{
		return oResult.addError(Enums.OpenPgpErrors.InvalidArgumentErrors);
	}

	aKeys = this.splitKeys(sArmor);

	for (iIndex = 0; iIndex < aKeys.length; iIndex++)
	{
		aData = aKeys[iIndex];
		if ('PRIVATE' === aData[0])
		{
			try
			{
				await this.oKeyring.privateKeys.importKey(aData[1]);
				iCount++;
			}
			catch (e)
			{
				oResult.addExceptionMessage(e, Enums.OpenPgpErrors.ImportKeyError, 'private');
			}
		}
		else if ('PUBLIC' === aData[0])
		{
			try
			{
				await this.oKeyring.publicKeys.importKey(aData[1]);
				iCount++;
			}
			catch (e)
			{
				oResult.addExceptionMessage(e, Enums.OpenPgpErrors.ImportKeyError, 'public');
			}
		}
	}

	if (0 < iCount)
	{
		await this.oKeyring.store();
	}
	else
	{
		oResult.addError(Enums.OpenPgpErrors.ImportNoKeysFoundError);
	}

	this.reloadKeysFromStorage();

	return oResult;
};

/**
 * @param {string} sArmor
 * @return {Array|boolean}
 */
COpenPgp.prototype.getArmorInfo = async function (sArmor)
{
	sArmor = $.trim(sArmor);

	var
		iIndex = 0,
		iCount = 0,
		oKey = null,
		aResult = [],
		aData = null,
		aKeys = []
	;

	if (!sArmor)
	{
		return false;
	}

	aKeys = this.splitKeys(sArmor);

	for (iIndex = 0; iIndex < aKeys.length; iIndex++)
	{
		aData = aKeys[iIndex];
		if ('PRIVATE' === aData[0])
		{
			try
			{
				oKey = await openpgp.key.readArmored(aData[1]);
				if (oKey && !oKey.err && oKey.keys && oKey.keys[0])
				{
					aResult.push(new COpenPgpKey(oKey.keys[0]));
				}
				
				iCount++;
			}
			catch (e)
			{
				aResult.push(null);
			}
		}
		else if ('PUBLIC' === aData[0])
		{
			try
			{
				oKey = await openpgp.key.readArmored(aData[1]);
				if (oKey && !oKey.err && oKey.keys && oKey.keys[0])
				{
					aResult.push(new COpenPgpKey(oKey.keys[0]));
				}

				iCount++;
			}
			catch (e)
			{
				aResult.push(null);
			}
		}
	}

	return aResult;
};

/**
 * @param {string} sID
 * @param {boolean} bPublic
 * @return {COpenPgpKey|null}
 */
COpenPgp.prototype.findKeyByID = function (sID, bPublic)
{
	bPublic = !!bPublic;
	sID = sID.toLowerCase();
	
	var oKey = _.find(this.keys(), function (oKey) {
		
		var
			oResult = false,
			aKeys = null
		;
		
		if (oKey && bPublic === oKey.isPublic())
		{
			aKeys = oKey.pgpKey.getKeyIds();
			if (aKeys)
			{
				oResult = _.find(aKeys, function (oKey) {
					return oKey && oKey.toHex && sID === oKey.toHex().toLowerCase();
				});
			}
		}
		
		return !!oResult;
	});

	return oKey ? oKey : null;
};

/**
 * @param {Array} aEmail
 * @param {boolean} bIsPublic
 * @param {COpenPgpResult=} oResult
 * @return {Array}
 */
COpenPgp.prototype.findKeysByEmails = function (aEmail, bIsPublic, oResult)
{
	bIsPublic = !!bIsPublic;
	
	var
		aResult = [],
		aKeys = this.keys()
	;
	_.each(aEmail, function (sEmail) {

		var oKey = _.find(aKeys, function (oKey) {
			return oKey && bIsPublic === oKey.isPublic() && sEmail === oKey.getEmail();
		});

		if (oKey)
		{
			aResult.push(oKey);
		}
		else
		{
			if (oResult)
			{
				oResult.addError(bIsPublic ?
					Enums.OpenPgpErrors.PublicKeyNotFoundError : Enums.OpenPgpErrors.PrivateKeyNotFoundError, sEmail);
			}
		}
	});

	return aResult;
};

/**
 * @param {type} aEmail
 * @returns {Array}
 */
COpenPgp.prototype.getPublicKeysIfExistsByEmail = function (sEmail)
{
	var
		aResult = [],
		aKeys = this.keys(),
		oKey = _.find(aKeys, function (oKey) {
			return oKey && oKey.isPublic() === true && sEmail === oKey.getEmail();
		})
	;

	if (oKey)
	{
		aResult.push(oKey);
	}

	return aResult;
};

/**
 * @param {object} oKey
 * @param {string} sPrivateKeyPassword
 * @returns {object}
 */
COpenPgp.prototype.verifyKeyPassword = async function (oKey, sPrivateKeyPassword)
{
	var
		oResult = new COpenPgpResult(),
		oPrivateKey = this.convertToNativeKeys([oKey])[0],
		oPrivateKeyClone = await this.cloneKey(oPrivateKey)
	;

	await this.decryptKeyHelper(oResult, oPrivateKeyClone, sPrivateKeyPassword, '');

	return oResult;
};

/**
 * @param {string} sData
 * @param {string} sAccountEmail
 * @param {string} sFromEmail
 * @param {string} sPrivateKeyPassword = ''
 * @param {Function} fOkHandler
 * @param {Function} fErrorHandler
 * @return {string}
 */
COpenPgp.prototype.decryptAndVerify = async function (sData, sAccountEmail, sFromEmail, sPrivateKeyPassword, fOkHandler, fErrorHandler)
{
	var
		oResult = new COpenPgpResult(),
		aPrivateKeys = this.findKeysByEmails([sAccountEmail], false, oResult),
		oPrivateKey = this.convertToNativeKeys(aPrivateKeys)[0],
		oPrivateKeyClone = await this.cloneKey(oPrivateKey),
		aPublicKeys = this.getPublicKeysIfExistsByEmail(sFromEmail),
		oOptions = {
			message: await openpgp.message.readArmored(sData), // parse armored message
			publicKeys: this.convertToNativeKeys(aPublicKeys) // for verification (optional)
		},
		aInvalidSignatures = []
	;

	if (oPrivateKeyClone)
	{
		await this.decryptKeyHelper(oResult, oPrivateKeyClone, sPrivateKeyPassword, sAccountEmail);
		if (oResult.errors)
		{
			if (_.isFunction(fErrorHandler))
			{
				fErrorHandler(oResult);
			}
		}
		else
		{
			oOptions.privateKeys = oPrivateKeyClone; // for decryption

			openpgp.decrypt(oOptions).then(async function(oPgpResult) {
				oResult.result = await openpgp.stream.readToEnd(oPgpResult.data);
				//if result contains invalid signatures 
				let aValidityPromises = [];
				for (let oSignature of oPgpResult.signatures)
				{
					aValidityPromises.push(
						oSignature.verified
						.then(validity => {
							return oSignature && validity !== true ? oSignature : null
						})
					);
				}
				Promise.all(aValidityPromises)
				.then(aSignatures => {
					aInvalidSignatures = _.filter(aSignatures, function (oSignature) {
						return oSignature !== null;
					});
					if (oPgpResult.signatures.length && aInvalidSignatures.length > 0)
					{
						oResult.addNotice(Enums.OpenPgpErrors.VerifyErrorNotice, sFromEmail);
					}

					if (_.isFunction(fOkHandler))
					{
						fOkHandler(oResult);
					}
				});
			}, function (e) {
				oResult.addExceptionMessage(e, Enums.OpenPgpErrors.VerifyAndDecryptError);
				if (_.isFunction(fErrorHandler))
				{
					fErrorHandler(oResult);
				}
			});
		}
	}
	else
	{
		oResult.addError(Enums.OpenPgpErrors.PrivateKeyNotFoundError);
		if (_.isFunction(fErrorHandler))
		{
			fErrorHandler(oResult);
		}
	}
};

/**
 * @param {string} sData
 * @param {string} sFromEmail
 * @param {Function} fOkHandler
 * @param {Function} fErrorHandler
 * @return {string}
 */
COpenPgp.prototype.verify = async function (sData, sFromEmail, fOkHandler, fErrorHandler)
{
	var
		oMessage = await openpgp.cleartext.readArmored(sData),
		oResult = new COpenPgpResult(),
		aPublicKeys = this.findKeysByEmails([sFromEmail], true, oResult),
		oOptions = {
			message: oMessage,
			publicKeys: this.convertToNativeKeys(aPublicKeys) // for verification
		}
	;

	openpgp.verify(oOptions).then(_.bind(async function(oPgpResult) {
		let aValidityPromises = [];
		let aValidSignatures = [];
		for (let oSignature of oPgpResult.signatures)
		{
			aValidityPromises.push(
				oSignature.verified
				.then(validity => {
					return oSignature && validity === true ? oSignature : null
				})
			);
		}
		await Promise.all(aValidityPromises)
		.then(aSignatures => {
			aValidSignatures = _.filter(aSignatures, function (oSignature) {
				return oSignature !== null;
			});
		});
		if (aValidSignatures.length)
		{
			await this.verifyMessageHelper(oResult, sFromEmail, oMessage);
			oResult.result = oMessage.getText();
			if (oResult.notices && _.isFunction(fErrorHandler))
			{
				fErrorHandler(oResult);
			}
			else if (_.isFunction(fOkHandler))
			{
				fOkHandler(oResult);
			}
		}
		else
		{
			oResult.addError(Enums.OpenPgpErrors.CanNotReadMessage);
			if (_.isFunction(fErrorHandler))
			{
				fErrorHandler(oResult);
			}
		}
	}, this), function (e) {
		oResult.addExceptionMessage(e, Enums.OpenPgpErrors.CanNotReadMessage);
		if (_.isFunction(fErrorHandler))
		{
			fErrorHandler(oResult);
		}
	});
};

/**
 * @param {string} sData
 * @param {Array} aPrincipalsEmail
 * @param {Function} fOkHandler
 * @param {Function} fErrorHandler
 * @return {string}
 */
COpenPgp.prototype.encrypt = function (sData, aPrincipalsEmail, fOkHandler, fErrorHandler)
{
	var
		oResult = new COpenPgpResult(),
		aPublicKeys = this.findKeysByEmails(aPrincipalsEmail, true, oResult)
	;

	if (!oResult.hasErrors())
	{
		var oOptions = {
			message: openpgp.message.fromText(sData),
			publicKeys: this.convertToNativeKeys(aPublicKeys) // for encryption
		};

		openpgp.encrypt(oOptions).then(function(oPgpResult) {
			oResult.result = oPgpResult.data;
			if (_.isFunction(fOkHandler))
			{
				fOkHandler(oResult);
			}
		}, function (e) {
			oResult.addExceptionMessage(e, Enums.OpenPgpErrors.SignAndEncryptError);
			if (_.isFunction(fErrorHandler))
			{
				fErrorHandler(oResult);
			}
		});
	}
	else if (_.isFunction(fErrorHandler))
	{
		fErrorHandler(oResult);
	}
};

/**
 * @param {string} sData
 * @param {string} sFromEmail
 * @param {string} sPrivateKeyPassword
 * @param {Function} fOkHandler
 * @param {Function} fErrorHandler
 * @return {string}
 */
COpenPgp.prototype.sign = async function (sData, sFromEmail, sPrivateKeyPassword, fOkHandler, fErrorHandler)
{
	var
		oResult = new COpenPgpResult(),
		oPrivateKey = null,
		oPrivateKeyClone = null,
		aPrivateKeys = this.findKeysByEmails([sFromEmail], false, oResult)
	;

	if (!oResult.hasErrors())
	{
		oPrivateKey = this.convertToNativeKeys(aPrivateKeys)[0];
		oPrivateKeyClone = await this.cloneKey(oPrivateKey);
		
		await this.decryptKeyHelper(oResult, oPrivateKeyClone, sPrivateKeyPassword, sFromEmail);
		
		if (oPrivateKeyClone && !oResult.hasErrors())
		{
			var oOptions = {
				message: openpgp.cleartext.fromText(sData),
				privateKeys: oPrivateKeyClone
			};

			openpgp.sign(oOptions).then(function(oPgpResult) {
				oResult.result = oPgpResult.data;
				if (_.isFunction(fOkHandler))
				{
					fOkHandler(oResult);
				}
			}, function (e) {
				oResult.addExceptionMessage(e, Enums.OpenPgpErrors.SignError, sFromEmail);
				if (_.isFunction(fErrorHandler))
				{
					fErrorHandler(oResult);
				}
			});
		}
		else if (_.isFunction(fErrorHandler))
		{
			fErrorHandler(oResult);
		}
	}
	else if (_.isFunction(fErrorHandler))
	{
		fErrorHandler(oResult);
	}
};

/**
 * @param {string} sData
 * @param {string} sFromEmail
 * @param {Array} aPrincipalsEmail
 * @param {string} sPrivateKeyPassword
 * @param {Function} fOkHandler
 * @param {Function} fErrorHandler
 * @return {string}
 */
COpenPgp.prototype.signAndEncrypt = async function (sData, sFromEmail, aPrincipalsEmail, sPrivateKeyPassword, fOkHandler, fErrorHandler)
{
	var
		oPrivateKey = null,
		oPrivateKeyClone = null,
		oResult = new COpenPgpResult(),
		aPrivateKeys = this.findKeysByEmails([sFromEmail], false, oResult),
		aPublicKeys = this.findKeysByEmails(aPrincipalsEmail, true, oResult)
	;

	if (!oResult.hasErrors())
	{
		oPrivateKey = this.convertToNativeKeys(aPrivateKeys)[0];
		oPrivateKeyClone = await this.cloneKey(oPrivateKey);

		await this.decryptKeyHelper(oResult, oPrivateKeyClone, sPrivateKeyPassword, sFromEmail);
		
		if (oPrivateKeyClone && !oResult.hasErrors())
		{
			var oOptions = {
				message: openpgp.message.fromText(sData),
				publicKeys: this.convertToNativeKeys(aPublicKeys), // for encryption
				privateKeys: oPrivateKeyClone // for signing (optional)
			};

			openpgp.encrypt(oOptions).then(function(oPgpResult) {
				oResult.result = oPgpResult.data;
				if (_.isFunction(fOkHandler))
				{
					fOkHandler(oResult);
				}
			}, function (e) {
				oResult.addExceptionMessage(e, Enums.OpenPgpErrors.SignAndEncryptError);
				if (_.isFunction(fErrorHandler))
				{
					fErrorHandler(oResult);
				}
			});
		}
		else if (_.isFunction(fErrorHandler))
		{
			fErrorHandler(oResult);
		}
	}
	else if (_.isFunction(fErrorHandler))
	{
		fErrorHandler(oResult);
	}
};

/**
 * @param {COpenPgpKey} oKey
 */
COpenPgp.prototype.deleteKey = async function (oKey)
{
	var oResult = new COpenPgpResult();
	if (oKey)
	{
		try
		{
			this.oKeyring[oKey.isPrivate() ? 'privateKeys' : 'publicKeys'].removeForId(oKey.getFingerprint());
			await this.oKeyring.store();
		}
		catch (e)
		{
			oResult.addExceptionMessage(e, Enums.OpenPgpErrors.DeleteError);
		}
	}
	else
	{
		oResult.addError(oKey ? Enums.OpenPgpErrors.UnknownError : Enums.OpenPgpErrors.InvalidArgumentError);
	}

	this.reloadKeysFromStorage();

	return oResult;
};

module.exports = new COpenPgp();
