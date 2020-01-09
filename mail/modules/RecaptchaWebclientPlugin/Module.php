<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace Aurora\Modules\RecaptchaWebclientPlugin;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @package Modules
 */
class Module extends \Aurora\System\Module\AbstractModule
{
	public function init()
	{
		$this->aErrors = [
			Enums\ErrorCodes::RecaptchaVerificationError	=> $this->i18N('ERROR_RECAPTCHA_VERIFICATION_DID_NOT_COMPLETE'),
			Enums\ErrorCodes::RecaptchaUnknownError		=> $this->i18N('ERROR_UNKNOWN_RECAPTCHA_ERROR'),
		];

		\Aurora\System\EventEmitter::getInstance()->onArray(
			[
				['StandardLoginFormWebclient::Login::before', [$this, 'onLogin'], 90],
				['MailLoginFormWebclient::Login::before', [$this, 'onLogin'], 90],
				['MailSignup::Signup::before', [$this, 'onSignup'], 90],
				['Core::Login::after', [$this, 'onAfterLogin']],
			]
		);
	}

	/**
	 * Obtains list of module settings for authenticated user.
	 * @return array
	 */
	public function GetSettings()
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::Anonymous);

		return [
			'PublicKey' => $this->getConfig('PublicKey', ''),
			'LimitCount' => $this->getConfig('LimitCount', 0)
		];
	}

	public function onLogin($aArgs, &$mResult, &$mSubscriptionResult)
	{
		$iAuthError = isset($_COOKIE['auth-error']) ? (int) $_COOKIE['auth-error'] : 0;
		//If the user has exceeded the number of authentication attempts
		if ($iAuthError >= $this->getConfig('LimitCount', 0))
		{
			if (!isset($aArgs['RecaptchaWebclientPluginToken']) || empty($aArgs['RecaptchaWebclientPluginToken']))
			{
				$mSubscriptionResult = [
					'Error' => [
						'Code'		=> Enums\ErrorCodes::RecaptchaVerificationError,
						'ModuleName'	=> $this->GetName(),
						'Override'		=> true
					]
				];
				return true;
			}
			$sPrivateKey = $this->getConfig('PrivateKey', '');
			$oRecaptcha = new \ReCaptcha\ReCaptcha($sPrivateKey, new \ReCaptcha\RequestMethod\SocketPost());
			$oResponse = $oRecaptcha->verify($aArgs['RecaptchaWebclientPluginToken']);
			if (!$oResponse->isSuccess())
			{
				\Aurora\System\Api::Log("RECAPTCHA error: " . implode(', ', $oResponse->getErrorCodes()));
				$mSubscriptionResult = [
					'Error' => [
						'Code'		=> Enums\ErrorCodes::RecaptchaUnknownError,
						'ModuleName'	=> $this->GetName(),
						'Override'		=> true
					]
				];
				return true;
			}
			//If the user is authenticated, reset the counter for unsuccessful attempts.
			if (isset($_COOKIE['auth-error']))
			{
				@\setcookie('auth-error');
			}
		}
	}

	public function onSignup($aArgs, &$mResult, &$mSubscriptionResult)
	{
		if (!isset($aArgs['RecaptchaWebclientPluginToken']) || empty($aArgs['RecaptchaWebclientPluginToken']))
		{
			$mSubscriptionResult = [
				'Error' => [
					'Code'		=> Enums\ErrorCodes::RecaptchaVerificationError,
					'ModuleName'	=> $this->GetName(),
					'Override'		=> true
				]
			];
			return true;
		}
		$sPrivateKey = $this->getConfig('PrivateKey', '');
		$oRecaptcha = new \ReCaptcha\ReCaptcha($sPrivateKey, new \ReCaptcha\RequestMethod\SocketPost());
		$oResponse = $oRecaptcha->verify($aArgs['RecaptchaWebclientPluginToken']);
		if (!$oResponse->isSuccess())
		{
			\Aurora\System\Api::Log("RECAPTCHA error: " . implode(', ', $oResponse->getErrorCodes()));
			$mSubscriptionResult = [
				'Error' => [
					'Code'		=> Enums\ErrorCodes::RecaptchaUnknownError,
					'ModuleName'	=> $this->GetName(),
					'Override'		=> true
				]
			];
			return true;
		}
	}

	public function onAfterLogin($aArgs, &$mResult)
	{
		//if auth was failed - incrementing auth-error counter
		if (!(is_array($mResult) && isset($mResult['AuthToken'])))
		{
			$iAuthErrorCount = isset($_COOKIE['auth-error']) ? ((int) $_COOKIE['auth-error'] + 1) : 1;
			@\setcookie('auth-error', $iAuthErrorCount, \strtotime('+1 hour'), \Aurora\System\Api::getCookiePath(), 
					null, \Aurora\System\Api::getCookieSecure());
		}
	}
}
