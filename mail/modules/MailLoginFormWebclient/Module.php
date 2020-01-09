<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace Aurora\Modules\MailLoginFormWebclient;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @package Modules
 */
class Module extends \Aurora\System\Module\AbstractWebclientModule
{
	/***** public functions might be called with web API *****/
	/**
	 * Obtains list of module settings for authenticated user.
	 * 
	 * @return array
	 */
	public function GetSettings()
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::Anonymous);

		return array(
			'ServerModuleName' => $this->getConfig('ServerModuleName', ''),
			'HashModuleName' => $this->getConfig('HashModuleName', ''),
			'CustomLoginUrl' => $this->getConfig('CustomLoginUrl', ''),
			'DemoLogin' => $this->getConfig('DemoLogin', ''),
			'DemoPassword' => $this->getConfig('DemoPassword', ''),
			'InfoText' => $this->getConfig('InfoText', ''),
			'BottomInfoHtmlText' => $this->getConfig('BottomInfoHtmlText', ''),
			'LoginSignMeType' => $this->getConfig('LoginSignMeType', 0),
			'AllowChangeLanguage' => $this->getConfig('AllowChangeLanguage', true),
			'UseDropdownLanguagesView' => $this->getConfig('UseDropdownLanguagesView', false),
		);
	}

	public function Login($Login, $Password, $Domain, $Language = '', $SignMe = false)
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::Anonymous);
		$mResult = false;
		$bResult = false;
		$oServer = null;
		$iUserId = 0;
		$sLogin = \trim($Login);
		$sDomain = \trim($Domain);
		$sPassword = $Password;
		if (empty($sLogin) || empty($sPassword) || empty($sDomain))
		{
			throw new \Aurora\System\Exceptions\ApiException(\Aurora\System\Notifications::InvalidInputParameter);
		}
		$sEmail = $sLogin . '@' . $sDomain;
		$aGetMailServerResult = \Aurora\System\Api::getModule('Mail')->GetMailServerByDomain(strtolower($sDomain), /*AllowWildcardDomain*/true);
		if (!empty($aGetMailServerResult) && isset($aGetMailServerResult['Server']) && $aGetMailServerResult['Server'] instanceof \Aurora\Modules\Mail\Classes\Server)
		{
			$oServer = $aGetMailServerResult['Server'];
		}
		if ($oServer)
		{
			$bAutocreateMailAccountOnNewUserFirstLogin = \Aurora\Modules\Mail\Module::Decorator()->getConfig('AutocreateMailAccountOnNewUserFirstLogin', false);
			$sIncomingLogin = $oServer->UseFullEmailAddressAsLogin ? $sEmail : $sLogin;

			$bNewAccount = false;
			$oAccount = \Aurora\System\Api::getModule('Mail')->getAccountsManager()->getAccountUsedToAuthorize($sEmail);
			if (!$bAutocreateMailAccountOnNewUserFirstLogin && !$oAccount)
			{
				$oUser = \Aurora\System\Api::GetModuleDecorator('Core')->GetUserByPublicId($sEmail);
				if ($oUser instanceof \Aurora\Modules\Core\Classes\User)
				{
					$bAutocreateMailAccountOnNewUserFirstLogin = true;
				}
			}

			if ($bAutocreateMailAccountOnNewUserFirstLogin && !$oAccount)
			{
				if ($oServer)
				{
					$oAccount = new \Aurora\Modules\Mail\Classes\Account(self::GetName());
					$oAccount->Email = $sEmail;
					$oAccount->IncomingLogin = $sIncomingLogin;
					$oAccount->setPassword($sPassword);
					$oAccount->ServerId = $oServer->EntityId;
					$bNewAccount = true;
				}
			}

			if ($oAccount instanceof \Aurora\Modules\Mail\Classes\Account)
			{
				try
				{
					if ($bAutocreateMailAccountOnNewUserFirstLogin || !$bNewAccount)
					{
						$bNeedToUpdatePasswordOrLogin = $sPassword !== $oAccount->getPassword() || $sIncomingLogin !== $oAccount->IncomingLogin;
						$oAccount->IncomingLogin = $sIncomingLogin;
						$oAccount->setPassword($sPassword);

						\Aurora\System\Api::getModule('Mail')->getMailManager()->validateAccountConnection($oAccount);

						if ($bNeedToUpdatePasswordOrLogin)
						{
							\Aurora\System\Api::getModule('Mail')->getAccountsManager()->updateAccount($oAccount);
						}

						$bResult =  true;
					}

					if ($bAutocreateMailAccountOnNewUserFirstLogin && $bNewAccount)
					{
						$oUser = null;
						$aSubArgs = array(
							'UserName' => $sEmail,
							'Email' => $sEmail,
							'UserId' => $iUserId
						);
						$this->broadcastEvent(
							'CreateAccount',
							$aSubArgs,
							$oUser
						);
						if ($oUser instanceof \Aurora\Modules\Core\Classes\User)
						{
							$iUserId = $oUser->EntityId;
							$bPrevState = \Aurora\System\Api::skipCheckUserRole(true);
							$oAccount = \Aurora\Modules\Mail\Module::Decorator()->CreateAccount(
								$iUserId,
								$sEmail,
								$sEmail,
								$sIncomingLogin,
								$sPassword,
								array('ServerId' => $oServer->EntityId)
							);
							\Aurora\System\Api::skipCheckUserRole($bPrevState);
							if ($oAccount)
							{
								$oAccount->UseToAuthorize = true;
								$oAccount->UseThreading = $oServer->EnableThreading;
								$bResult = \Aurora\System\Api::getModule('Mail')->getAccountsManager()->updateAccount($oAccount);
							}
							else
							{
								$bResult = false;
							}
						}
					}

					if ($bResult)
					{
						$mResult = array(
							'token' => 'auth',
							'id' => $oAccount->IdUser,
							'account' => $oAccount->EntityId,
							'account_type' => $oAccount->getName()
						);
					}
				}
				catch (\Aurora\System\Exceptions\ApiException $oException)
				{
					throw $oException;
				}
				catch (\Exception $oException) {}
			}
		}

		if (is_array($mResult))
		{
			$iTime = $SignMe ? 0 : time();
			$sAuthToken = \Aurora\System\Api::UserSession()->Set($mResult, $iTime);

			//this will store user data in static variable of Api class for later usage
			$oUser = \Aurora\System\Api::getAuthenticatedUser($sAuthToken);

			if ($oUser->Role !== \Aurora\System\Enums\UserRole::SuperAdmin)
			{
				// If User is super admin don't try to detect tenant. It will try to connect to DB.
				// Super admin should be able to log in without connecting to DB.
				$oTenant = \Aurora\System\Api::getTenantByWebDomain();
				if ($oTenant && $oUser->IdTenant !== $oTenant->EntityId)
				{
					throw new \Aurora\System\Exceptions\ApiException(\Aurora\System\Notifications::AuthError);
				}
			}

			if ($Language !== '' && $oUser && $oUser->Language !== $Language)
			{
				$oUser->Language = $Language;
				\Aurora\System\Api::getModule('Core')->getUsersManager()->updateUser($oUser);
			}

			\Aurora\System\Api::LogEvent('login-success: ' . $sIncomingLogin, self::GetName());
			$mResult = [
				'AuthToken' => $sAuthToken
			];
		}
		else
		{
			\Aurora\System\Api::LogEvent('login-failed: ' . $sLogin, self::GetName());
			\Aurora\System\Api::GetModuleManager()->SetLastException(
				new \Aurora\System\Exceptions\ApiException(\Aurora\System\Notifications::AuthError)
			);
		}

		return $mResult;
	}

	public function GetMailDomains()
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::Anonymous);

		$bPrevState = \Aurora\System\Api::skipCheckUserRole(true);
		$aAllDomains = [];
		$oTenant = \Aurora\System\Api::getTenantByWebDomain();
		$aArgs = [];
		if ($oTenant instanceof \Aurora\Modules\Core\Classes\Tenant)
		{
			$aArgs = [
				'TenantId' => $oTenant->EntityId
			];
		}
		$mResult = [];
		$this->broadcastEvent(
			'GetMailDomains',
			$aArgs,
			$mResult
		);
		if (is_array($mResult) && !empty($mResult))
		{
			$aAllDomains = $mResult;
		}
		else
		{
			if ($oTenant instanceof \Aurora\Modules\Core\Classes\Tenant)
			{
				$aFilters = ['$OR' => [
					'OwnerType' => [\Aurora\Modules\Mail\Enums\ServerOwnerType::SuperAdmin, '='],
					'$AND' => [
						'TenantId' => [$oTenant->EntityId, '='],
						'OwnerType' => [\Aurora\Modules\Mail\Enums\ServerOwnerType::Tenant, '='],
					],
				]];
			}
			else
			{
				//get all servers for all tenants
				$aFilters = ['$OR' => [
					'1@OwnerType' => [\Aurora\Modules\Mail\Enums\ServerOwnerType::Tenant, '='],
					'2@OwnerType' => [\Aurora\Modules\Mail\Enums\ServerOwnerType::SuperAdmin, '=']
				]];
			}
			$aServers = \Aurora\System\Api::getModule('Mail')->getServersManager()->getServerListByFilter($aFilters);
			if ($aServers)
			{
				foreach ($aServers as $oServer)
				{
					$aDomains = explode("\n", $oServer->Domains);
					$aDomains = array_filter($aDomains, function($sDomain) {
						return $sDomain !== '*';
					});
					$aAllDomains = array_merge($aAllDomains, $aDomains);
				}
			}
		}
		\Aurora\System\Api::skipCheckUserRole($bPrevState);

		return $aAllDomains;
	}
	/***** public functions might be called with web API *****/
}
