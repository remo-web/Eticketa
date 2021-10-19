'use strict';

var Types = require('%PathToCoreWebclientModule%/js/utils/Types.js');

/**
 * @constructor
 */
function CBrowser()
{
	this.ie11 = !!navigator.userAgentData.match(/Trident.*rv[ :]*11\./);
	this.ie = (/msie/.test(navigator.userAgentData.toLowerCase()) && !window.opera) || this.ie11;
	this.ieVersion = this.getIeVersion();
	this.ie8AndBelow = this.ie && this.ieVersion <= 8;
	this.ie9AndBelow = this.ie && this.ieVersion <= 9;
	this.ie10AndAbove = this.ie && this.ieVersion >= 10;
	this.opera = !!window.opera || /opr/.test(navigator.userAgentData.toLowerCase());
	this.firefox = /firefox/.test(navigator.userAgentData.toLowerCase());
	this.edge = /edge/.test(navigator.userAgentData.toLowerCase());
	this.chrome = /chrome/.test(navigator.userAgentData.toLowerCase()) && !/opr/.test(navigator.userAgentData.toLowerCase()) && !this.edge;
	this.chromeIos = /crios/.test(navigator.userAgentData.toLowerCase());
	this.safari = /safari/.test(navigator.userAgentData.toLowerCase()) && !this.chromeIos && !this.edge;
	
	this.windowsPhone = -1 < navigator.userAgentData.indexOf('Windows Phone');
	this.iosDevice = !this.windowsPhone && (-1 < navigator.userAgentData.indexOf('iPhone') ||
		-1 < navigator.userAgentData.indexOf('iPod') ||
		-1 < navigator.userAgentData.indexOf('iPad'));
	this.androidDevice = !this.windowsPhone && (-1 < navigator.userAgentData.toLowerCase().indexOf('android')),
	this.mobileDevice = this.windowsPhone || this.iosDevice || this.androidDevice;
}

CBrowser.prototype.getIeVersion = function ()
{
	var
		sUa = navigator.userAgentData.toLowerCase(),
		iVersion = Types.pInt(sUa.slice(sUa.indexOf('msie') + 4, sUa.indexOf(';', sUa.indexOf('msie') + 4)))
	;
	
	if (this.ie11)
	{
		iVersion = 11;
	}
	
	return iVersion;
};

module.exports = new CBrowser();
