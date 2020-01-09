<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace Aurora\Modules\Contacts;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @package Modules
 */
class Module extends \Aurora\System\Module\AbstractModule
{
	public $oManager = null;

	protected $aImportExportFormats = ['csv', 'vcf'];

	public function getManager()
	{
		if ($this->oManager === null)
		{
			$this->oManager = new Manager($this);
		}

		return $this->oManager;
	}
	
	/**
	 * Initializes Contacts Module.
	 * 
	 * @ignore
	 */
	public function init() 
	{
		$this->subscribeEvent('Mail::AfterUseEmails', array($this, 'onAfterUseEmails'));
		$this->subscribeEvent('Mail::GetBodyStructureParts', array($this, 'onGetBodyStructureParts'));
		$this->subscribeEvent('Core::DeleteUser::before', array($this, 'onBeforeDeleteUser'));
		$this->subscribeEvent('Core::CreateTables::after', array($this, 'onAfterCreateTables'));
		
		\Aurora\Modules\Core\Classes\User::extend(
			self::GetName(),
			[
				'ContactsPerPage' => array('int', $this->getConfig('ContactsPerPage', 20)),
			]
		);
	}

	/***** public functions *****/
	/**
	 * Returns API contacts manager.
	 * @return \CApiContactsManager
	 */
	public function GetApiContactsManager()
	{
		return $this->getManager();
	}
	/***** public functions *****/
	
	/***** public functions might be called with web API *****/
	/**
	 * @apiDefine Contacts Contacts Module
	 * Main Contacts module. It provides PHP and Web APIs for managing contacts.
	 */
	
	/**
	 * @api {post} ?/Api/ GetSettings
	 * @apiName GetSettings
	 * @apiGroup Contacts
	 * @apiDescription Obtains list of module settings for authenticated user.
	 * 
	 * @apiHeader {string} [Authorization] "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=GetSettings} Method Method name
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetSettings'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name.
	 * @apiSuccess {string} Result.Method Method name.
	 * @apiSuccess {mixed} Result.Result List of module settings in case of success, otherwise **false**.
	 * @apiSuccess {int} Result.Result.ContactsPerPage=20 Count of contacts that will be displayed on one page.
	 * @apiSuccess {string} Result.Result.ImportContactsLink=&quot;&quot; Link for learning more about CSV format.
	 * @apiSuccess {array} Result.Result.Storages='[]' List of storages wich will be shown in the interface.
	 * @apiSuccess {array} Result.Result.ImportExportFormats='[]' List of formats that can be used for import and export contacts.
	 * @apiSuccess {array} Result.Result.\Aurora\Modules\Contacts\Enums\PrimaryEmail='[]' Enumeration with primary email values.
	 * @apiSuccess {array} Result.Result.\Aurora\Modules\Contacts\Enums\PrimaryPhone='[]' Enumeration with primary phone values.
	 * @apiSuccess {array} Result.Result.\Aurora\Modules\Contacts\Enums\PrimaryAddress='[]' Enumeration with primary address values.
	 * @apiSuccess {array} Result.Result.\Aurora\Modules\Contacts\Enums\SortField='[]' Enumeration with sort field values.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetSettings',
	 *	Result: { ContactsPerPage: 20, ImportContactsLink: '', Storages: ['personal', 'team'],
	 * ImportExportFormats: ['csv', 'vcf'], \Aurora\Modules\Contacts\Enums\PrimaryEmail: {'Personal': 0, 'Business': 1, 'Other': 2},
	 * \Aurora\Modules\Contacts\Enums\PrimaryPhone: {'Mobile': 0, 'Personal': 1, 'Business': 2},
	 * \Aurora\Modules\Contacts\Enums\PrimaryAddress: {'Personal': 0, 'Business': 1},
	 * \Aurora\Modules\Contacts\Enums\SortField: {'Name': 1, 'Email': 2, 'Frequency': 3} }
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetSettings',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Obtains list of module settings for authenticated user.
	 * @return array
	 */
	public function GetSettings()
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::Anonymous);
		
		$aStorages = self::Decorator()->GetStorages();
		
		$oUser = \Aurora\System\Api::getAuthenticatedUser();
		$ContactsPerPage = $this->getConfig('ContactsPerPage', 20);
		if ($oUser && $oUser->isNormalOrTenant() && isset($oUser->{self::GetName().'::ContactsPerPage'}))
		{
			$ContactsPerPage = $oUser->{self::GetName().'::ContactsPerPage'};
		}
		
		return array(
			'ContactsPerPage' => $ContactsPerPage,
			'ImportContactsLink' => $this->getConfig('ImportContactsLink', ''),
			'Storages' => $aStorages,
			'PrimaryEmail' => (new Enums\PrimaryEmail)->getMap(),
			'PrimaryPhone' => (new Enums\PrimaryPhone)->getMap(),
			'PrimaryAddress' => (new Enums\PrimaryAddress)->getMap(),
			'SortField' => (new Enums\SortField)->getMap(),
			'ImportExportFormats' => $this->aImportExportFormats,
			'SaveVcfServerModuleName' => \Aurora\System\Api::GetModuleManager()->ModuleExists('DavContacts') ? 'DavContacts' : ''
		);
	}

	public function GetStorages()
	{
		$aStorages = [];
		$this->broadcastEvent('GetStorages', $aStorages);
		return $aStorages;
	}
	
	public function IsDisplayedStorage($Storage)
	{
		return true;
	}

	public function GetContactStorages()
	{
		$aStorages = [];
		$aStorageNames = [];
		$this->broadcastEvent('GetStorages', $aStorageNames);
		
		$iUserId = \Aurora\System\Api::getAuthenticatedUserId();
		
		foreach ($aStorageNames as $sStorageName) {
			$aStorages[] = [
				'Id' => $sStorageName,
				'CTag' => $this->Decorator()->GetCTag($iUserId, $sStorageName),
				'Display' => $this->Decorator()->IsDisplayedStorage($sStorageName)
			];
		}
		
		return $aStorages;
	}
	
	/**
	 * @api {post} ?/Api/ UpdateSettings
	 * @apiName UpdateSettings
	 * @apiGroup Contacts
	 * @apiDescription Updates module's settings - saves them to config.json file.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=UpdateSettings} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **ContactsPerPage** *int* Count of contacts per page.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'UpdateSettings',
	 *	Parameters: '{ ContactsPerPage: 10 }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {bool} Result.Result Indicates if settings were updated successfully.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'UpdateSettings',
	 *	Result: true
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'UpdateSettings',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Updates module's settings - saves them to config.json file or to user settings in db.
	 * @param int $ContactsPerPage Count of contacts per page.
	 * @return boolean
	 */
	public function UpdateSettings($ContactsPerPage)
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);
		
		$bResult = false;

		$oUser = \Aurora\System\Api::getAuthenticatedUser();
		if ($oUser)
		{
			if ($oUser->isNormalOrTenant())
			{
				$oCoreDecorator = \Aurora\Modules\Core\Module::Decorator();
				$oUser->{self::GetName().'::ContactsPerPage'} = $ContactsPerPage;
				return $oCoreDecorator->UpdateUserObject($oUser);
			}
			if ($oUser->Role === \Aurora\System\Enums\UserRole::SuperAdmin)
			{
				$this->setConfig('ContactsPerPage', $ContactsPerPage);
				$bResult = $this->saveModuleConfig();
			}
		}
		
		return $bResult;
	}
	
	/**
	 * @api {post} ?/Api/ Export
	 * @apiName Export
	 * @apiGroup Contacts
	 * @apiDescription Exports specified contacts to a file with specified format.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=Export} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **Format** *string* File format that should be used for export.<br>
	 * &emsp; **Filters** *array* Filters for obtaining specified contacts.<br>
	 * &emsp; **GroupUUID** *string* UUID of group that should contain contacts for export.<br>
	 * &emsp; **ContactUUIDs** *array* List of UUIDs of contacts that should be exported.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'Export',
	 *	Parameters: '{ Format: "csv", Filters: [], GroupUUID: "", ContactUUIDs: [] }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {mixed} Result.Result Contents of CSV or VCF file in case of success, otherwise **false**.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * contents of CSV or VCF file 
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'Export',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Exports specified contacts to a file with specified format.
	 * @param string $Format File format that should be used for export.
	 * @param array $Filters Filters for obtaining specified contacts.
	 * @param string $GroupUUID UUID of group that should contain contacts for export.
	 * @param array $ContactUUIDs List of UUIDs of contacts that should be exported.
	 */
	public function Export($UserId, $Format, $Filters = [], $GroupUUID = '', $ContactUUIDs = [])
	{
		$this->CheckAccess($UserId);

		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);
		
		$aFilters = $this->prepareFilters($Filters);
		
		if (empty($ContactUUIDs) && !empty($GroupUUID))
		{
			$aGroupContact = $this->getManager()->getGroupContacts($GroupUUID);
			foreach ($aGroupContact as $oGroupContact)
			{
				$ContactUUIDs[] = $oGroupContact->ContactUUID;
			}
			if (empty($ContactUUIDs))
			{
				$ContactUUIDs = array();
			}
		}
		if (count($ContactUUIDs) > 0)
		{
			foreach ($aFilters as $sKey => $mValue)
			{
				if (stripos($sKey, '$AND'))
				{
					$aFilters[$sKey]['UUID'] = [$ContactUUIDs, 'IN'];
				}
			}
		}
		
		$aFilters = ['$OR' => $aFilters];
		
		$aContacts = $this->getManager()->getContacts(Enums\SortField::Name, \Aurora\System\Enums\SortOrder::ASC, 0, 0, $aFilters);
		
		$sOutput = '';
		
		if (is_array($aContacts))
		{
			switch ($Format)
			{
				case 'csv':
					$oSync = new Classes\Csv\Sync();
					$sOutput = $oSync->Export($aContacts);
					break;
				case 'vcf':
					foreach ($aContacts as $oContact)
					{
						$oContact->GroupsContacts = $this->getManager()->getGroupContacts(null, $oContact->UUID);
					
						$sOutput .= self::Decorator()->GetContactAsVCF($UserId, $oContact);
					}
					break;
			}
		}
		
		if (is_string($sOutput) && !empty($sOutput))
		{
			header('Pragma: public');
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="export.' . $Format . '";');
			header('Content-Transfer-Encoding: binary');
		}
		
		echo $sOutput;
	}

	public function GetContactAsVCF($UserId, $Contact)
	{
		$this->CheckAccess($UserId);

		$oVCard = new \Sabre\VObject\Component\VCard();
		Classes\VCard\Helper::UpdateVCardFromContact($Contact, $oVCard);
		return $oVCard->serialize();
	}
	
	/**
	 * @api {post} ?/Api/ GetGroups
	 * @apiName GetGroups
	 * @apiGroup Contacts
	 * @apiDescription Returns all groups for authenticated user.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=GetGroups} Method Method name
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetGroups'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {mixed} Result.Result List of groups in case of success, otherwise **false**.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetGroups',
	 *	Result: [{ City: '', Company: '', Contacts: [], Country: '', Email: '', Fax: '', IdUser: 3,
	 * IsOrganization: false, Name: 'group_name', Phone: '', State: '', Street: '', UUID: 'uuid_value',
	 * Web: '', Zip: '' }]
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetGroups',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Returns all groups for authenticated user.
	 * @return array
	 */
	public function GetGroups($UserId = null)
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);
		
		$this->CheckAccess($UserId);

		return $this->getManager()->getGroups($UserId);
	}
	
	/**
	 * @api {post} ?/Api/ GetGroup
	 * @apiName GetGroup
	 * @apiGroup Contacts
	 * @apiDescription Returns group with specified UUID.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=GetGroup} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **$UUID** *string* UUID of group to return.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetGroup',
	 *	Parameters: '{ UUID: "group_uuid" }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {mixed} Result.Result Group object in case of success, otherwise **false**.
	 * @apiSuccess {string} Result.Result.City=&quot;&quot;
	 * @apiSuccess {string} Result.Result.Company=&quot;&quot;
	 * @apiSuccess {array} Result.Result.Contacts='[]'
	 * @apiSuccess {string} Result.Result.Country=&quot;&quot;
	 * @apiSuccess {string} Result.Result.Email=&quot;&quot;
	 * @apiSuccess {string} Result.Result.Fax=&quot;&quot;
	 * @apiSuccess {int} Result.Result.IdUser=0
	 * @apiSuccess {bool} Result.Result.IsOrganization=false
	 * @apiSuccess {string} Result.Result.Name=&quot;&quot;
	 * @apiSuccess {string} Result.Result.Phone=&quot;&quot;
	 * @apiSuccess {string} Result.Result.Street=&quot;&quot;
	 * @apiSuccess {string} Result.Result.UUID=&quot;&quot;
	 * @apiSuccess {string} Result.Result.Web=&quot;&quot;
	 * @apiSuccess {string} Result.Result.Zip=&quot;&quot;
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetGroup',
	 *	Result: { City: '', Company: 'group_company', Contacts: [], Country: '', Email: '', Fax: '',
	 * IdUser: 3, IsOrganization: true, Name: 'group_name', Phone:'', State:'', Street:'',
	 * UUID: 'group_uuid', Web:'', Zip: '' }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetGroup',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Returns group with specified UUID.
	 * @param string $UUID UUID of group to return.
	 * @return \Aurora\Modules\Contacts\Classes\Group
	 */
	public function GetGroup($UserId, $UUID)
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);
		
		$this->CheckAccess($UserId);

		return $this->getManager()->getGroup($UUID);
	}

	/**
	 * Returns group item identified by its name.
	 * @param string $sName Group name
	 * @return \Aurora\Modules\Contacts\Classes\Group
	 */
	public function GetGroupByName($Name, $UserId = null)
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);

		if (isset($UserId) && $UserId !==  \Aurora\System\Api::getAuthenticatedUserId())
		{
			\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::SuperAdmin);
		}
		else
		{
			$UserId = \Aurora\System\Api::getAuthenticatedUserId();
		}

		$this->CheckAccess($UserId);

		return $this->getManager()->getGroupByName($Name, $UserId);
	}
	/**
	 * @api {post} ?/Api/ GetContacts
	 * @apiName GetContacts
	 * @apiGroup Contacts
	 * @apiDescription Returns list of contacts for specified parameters.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=GetContacts} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **Offset** *int* Offset of contacts list.<br>
	 * &emsp; **Limit** *int* Limit of result contacts list.<br>
	 * &emsp; **SortField** *int* Name of field order by.<br>
	 * &emsp; **SortOrder** *int* Sorting direction.<br>
	 * &emsp; **Storage** *string* Storage value.<br>
	 * &emsp; **Search** *string* Search string.<br>
	 * &emsp; **GroupUUID** *string* UUID of group that should contain all returned contacts.<br>
	 * &emsp; **Filters** *array* Other conditions for obtaining contacts list.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetContacts',
	 *	Parameters: '{ Offset: 0, Limit: 20, SortField: 1, SortOrder: 0, Storage: "personal",
	 *		Search: "", GroupUUID: "", Filters: [] }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {mixed} Result.Result Object with contacts data in case of success, otherwise **false**.
	 * @apiSuccess {int} Result.Result.ContactCount Count of contacts that are obtained with specified conditions.
	 * @apiSuccess {array} Result.Result.List List of contacts objects.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetContacts',
	 *	Result: '{ "ContactCount": 6, "List": [{ "UUID": "contact_uuid", "IdUser": 3, "Name": "",
	 *		"Email": "contact@email.com", "Storage": "personal" }] }'
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetContacts',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Returns list of contacts for specified parameters.
	 * @param string $Storage Storage type of contacts.
	 * @param int $Offset Offset of contacts list.
	 * @param int $Limit Limit of result contacts list.
	 * @param int $SortField Name of field order by.
	 * @param int $SortOrder Sorting direction.
	 * @param string $Search Search string.
	 * @param string $GroupUUID UUID of group that should contain all returned contacts.
	 * @param array $Filters Other conditions for obtaining contacts list.
	 * @param bool $WithGroups Indicates whether contact groups should be included in the contact list
	 * @param bool $WithoutTeamContactsDuplicates Do not show a contact from the global address book if the contact with the same email address already exists in personal address book
	 * @return array
	 */
	public function GetContacts($UserId, $Storage = '', $Offset = 0, $Limit = 20, $SortField = Enums\SortField::Name, $SortOrder = \Aurora\System\Enums\SortOrder::ASC, $Search = '', $GroupUUID = '', $Filters = array(), $WithGroups = false, $WithoutTeamContactsDuplicates = false)
	{
		// $Storage is used by subscribers to prepare filters.
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);

		$this->CheckAccess($UserId);

		$aFilters = $this->prepareFilters($Filters);

		$aContactUUIDs = array();
		$aGroupUsersList = [];
		if (!empty($GroupUUID))
		{
			$aGroupContact = $this->getManager()->getGroupContacts($GroupUUID);
			foreach ($aGroupContact as $oGroupContact)
			{
				$aContactUUIDs[] = $oGroupContact->ContactUUID;
			}
			if (empty($aContactUUIDs))
			{
				$aContactUUIDs = array();
			}
			if (count($aContactUUIDs) > 0)
			{
				foreach ($aFilters as $sKey => $mValue)
				{
					if (stripos($sKey, '$AND'))
					{
						$aFilters[$sKey]['UUID'] = [$aContactUUIDs, 'IN'];
					}
				}
			}
		}
		
		if (!empty($Search))
		{
			$aSearchFilters = [
				'FullName' => ['%'.$Search.'%', 'LIKE'],
				'PersonalEmail' => ['%'.$Search.'%', 'LIKE'],
				'BusinessEmail' => ['%'.$Search.'%', 'LIKE'],
				'OtherEmail' => ['%'.$Search.'%', 'LIKE'],
				'BusinessCompany' => ['%'.$Search.'%', 'LIKE']
			];
			
			if (count($aFilters) > 0)
			{
				$aFilters = [
					'$AND' => [
						'1$OR' => $aFilters, 
						'2$OR' => $aSearchFilters
					]
				];
			}
			else
			{
				$aFilters = [
					'$OR' => $aSearchFilters
				];
			}
			if ($WithGroups)
			{
				$oUser = \Aurora\System\Api::getAuthenticatedUser();
				if ($oUser instanceof \Aurora\Modules\Core\Classes\User)
				{
					$aGroups = $this->getManager()->getGroups($oUser->EntityId, ['Name' => ['%' . $Search . '%', 'LIKE']]);
					if ($aGroups)
					{
						foreach ($aGroups as $oGroup)
						{
							$aGroupContactsEmails = [];
							$aGroupContacts = $this->getManager()->getGroupContacts($oGroup->UUID);

							foreach ($aGroupContacts as $oGroupContact)
							{
								$oContact = $this->getManager()->getContact($oGroupContact->ContactUUID);
								if ($oContact)
								{
									$aGroupContactsEmails[] = $oContact->FullName ? "\"{$oContact->FullName}\" <{$oContact->ViewEmail}>" : $oContact->ViewEmail;
								}
							}
							$aGroupUsersList[] = [
								'UUID' => $oGroup->UUID,
								'IdUser' => $oGroup->IdUser,
								'FullName' => $oGroup->Name,
								'FirstName' => '',
								'LastName' => '',
								'ViewEmail' => implode(', ', $aGroupContactsEmails),
								'Storage' => '',
								'Frequency' => 0,
								'DateModified' => '',
								'IsGroup' => true,
							];
						}
					}
				}
			}
		}
		elseif (count($aFilters) > 1)
		{
			$aFilters = ['$OR' => $aFilters];
		}
		
		if ($SortField === Enums\SortField::Frequency)
		{
			$aFilters['Frequency'] = ['-1', '!='];
			$aFilters = [
				'$AND' => $aFilters
			];
		}
		
		$aContacts = array();
		$iCount = 0;
		if ((!empty($GroupUUID) && count($aContactUUIDs) > 0) || empty($GroupUUID))
		{
			$iCount = $this->getManager()->getContactsCount($aFilters);
			$aContacts = $this->getManager()->getContacts($SortField, $SortOrder, $Offset, $Limit, $aFilters);
		}
		if ($Storage === 'all' && $WithoutTeamContactsDuplicates)
		{
			$aPersonalContactEmails = array_map(function($oContact) {
				if ($oContact->Storage === 'personal')
				{
					return $oContact->ViewEmail;
				}
			}, $aContacts);
			$aUniquePersonalContactEmails = array_unique(array_diff($aPersonalContactEmails, [null]));
			foreach ($aContacts as $key => $oContact)
			{
				if ($oContact->Storage === 'team' && in_array($oContact->ViewEmail, $aUniquePersonalContactEmails))
				{
					unset($aContacts[$key]);
				}
			}
		}
		$aList = array();
		if (is_array($aContacts))
		{
			foreach ($aContacts as $oContact)
			{
				$aList[] = array(
					'UUID' => $oContact->UUID,
					'IdUser' => $oContact->IdUser,
					'FullName' => $oContact->FullName,
					'FirstName' => $oContact->FirstName,
					'LastName' => $oContact->LastName,
					'ViewEmail' => $oContact->ViewEmail,
					'Storage' => $oContact->Storage,
					'Frequency' => $oContact->Frequency,
					'DateModified' => $oContact->DateModified,
					'ETag' => $oContact->ETag,
				);
			}
		}

		$aList = array_merge($aList, $aGroupUsersList);
		return array(
			'ContactCount' => $iCount,
			'List' => \Aurora\System\Managers\Response::GetResponseObject($aList)
		);		
	}	
	
	/**
	 * @api {post} ?/Api/ GetContact
	 * @apiName GetContact
	 * @apiGroup Contacts
	 * @apiDescription Returns contact with specified UUID.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=GetContact} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **UUID** *string* UUID of contact to return.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetContact',
	 *	Parameters: '{ UUID: "contact_uuid" }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {mixed} Result.Result Object with contact data in case of success, otherwise **false**.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetContact',
	 *	Result: '{ "IdUser": 3, "UUID": "group_uuid", "Storage": "personal", "FullName": "", "PrimaryEmail": 0,
	 * "PrimaryPhone": 1, "PrimaryAddress": 0, "FirstName": "", "LastName": "", "NickName": "", "Skype": "",
	 * "Facebook": "", "PersonalEmail": "contact@email.com", "PersonalAddress": "", "PersonalCity": "",
	 * "PersonalState": "", "PersonalZip": "", "PersonalCountry": "", "PersonalWeb": "", "PersonalFax": "",
	 * "PersonalPhone": "", "PersonalMobile": "123-234-234", "BusinessEmail": "", "BusinessCompany": "",
	 * "BusinessAddress": "", "BusinessCity": "", "BusinessState": "", "BusinessZip": "", "BusinessCountry": "",
	 * "BusinessJobTitle": "", "BusinessDepartment": "", "BusinessOffice": "", "BusinessPhone": "",
	 * "BusinessFax": "", "BusinessWeb": "", "OtherEmail": "", "Notes": "", "BirthDay": 0, "BirthMonth": 0,
	 * "BirthYear": 0, "ETag": "", "GroupUUIDs": ["group1_uuid", "group2_uuid"] }'
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetContact',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */

	 public function CheckAccessToObject($User, $UUID)
	 {
		 return true;
	 }

	 public function CheckAccess(&$UserId)
	 {
		$bAccessDenied = true;
		
		$oAuthenticatedUser = \Aurora\System\Api::getAuthenticatedUser();

		$iUserId = $UserId;
		if ($iUserId === null)
		{
			$iUserId = $oAuthenticatedUser->EntityId;
		}
		else
		{
			$iUserRole = $oAuthenticatedUser instanceof \Aurora\Modules\Core\Classes\User ? $oAuthenticatedUser->Role : \Aurora\System\Enums\UserRole::Anonymous;
			switch ($iUserRole)
			{
				case (\Aurora\System\Enums\UserRole::SuperAdmin):
					// everything is allowed for SuperAdmin
					$UserId = $iUserId;
					$bAccessDenied = false;
					break;
				case (\Aurora\System\Enums\UserRole::TenantAdmin):
					// everything is allowed for TenantAdmin
					$oUser = \Aurora\Modules\Core\Module::getInstance()->GetUser($iUserId);
					if ($oUser instanceof \Aurora\Modules\Core\Classes\User)
					{
						if ($oAuthenticatedUser->IdTenant === $oUser->IdTenant)
						{
							$UserId = $iUserId;
							$bAccessDenied = false;
						}
					}
					break;
				case (\Aurora\System\Enums\UserRole::NormalUser):
					// User identifier shoud be checked
					if ($iUserId === $oAuthenticatedUser->EntityId)
					{
						$UserId = $iUserId;
						$bAccessDenied = false;
					}
					break;
				case (\Aurora\System\Enums\UserRole::Customer):
				case (\Aurora\System\Enums\UserRole::Anonymous):
					// everything is forbidden for Customer and Anonymous users
					break;
			}
			if ($bAccessDenied)
			{
				throw new \Aurora\System\Exceptions\ApiException(\Aurora\System\Notifications::AccessDenied);
			}
		}			 
	 }
	
	/**
	 * Returns contact with specified UUID.
	 * @param string $UUID UUID of contact to return.
	 * @return \Aurora\Modules\Contacts\Classes\Contact
	 */
	public function GetContact($UUID, $UserId = null)
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);

		$this->CheckAccess($UserId);
		$oUser = \Aurora\Modules\Core\Module::getInstance()->GetUserUnchecked($UserId);
		
		$mResult = false;

		if ($oUser instanceof \Aurora\Modules\Core\Classes\User)
		{
			$oContact = $this->getManager()->getContact($UUID);
			if ($this->CheckAccessToObject($UserId, $UUID))
			{
				$mResult = $oContact;
			}						
		}
		
		return $mResult;
	}

	/**
	 * @api {post} ?/Api/ GetContactsByEmails
	 * @apiName GetContactsByEmails
	 * @apiGroup Contacts
	 * @apiDescription Returns list of contacts with specified emails.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=GetContactsByEmails} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **Emails** *array* List of emails of contacts to return.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetContactsByEmails',
	 *	Parameters: '{ Emails: ["contact@email.com"] }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {mixed} Result.Result List of contacts in case of success, otherwise **false**.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetContactsByEmails',
	 *	Result: [{ "IdUser": 3, "UUID": "group_uuid", "Storage": "personal", "FullName": "", "PrimaryEmail": 0,
	 * "PrimaryPhone": 1, "PrimaryAddress": 0, "FirstName": "", "LastName": "", "NickName": "", "Skype": "",
	 * "Facebook": "", "PersonalEmail": "contact@email.com", "PersonalAddress": "", "PersonalCity": "",
	 * "PersonalState": "", "PersonalZip": "", "PersonalCountry": "", "PersonalWeb": "", "PersonalFax": "",
	 * "PersonalPhone": "", "PersonalMobile": "123-234-234", "BusinessEmail": "", "BusinessCompany": "",
	 * "BusinessAddress": "", "BusinessCity": "", "BusinessState": "", "BusinessZip": "", "BusinessCountry": "",
	 * "BusinessJobTitle": "", "BusinessDepartment": "", "BusinessOffice": "", "BusinessPhone": "",
	 * "BusinessFax": "", "BusinessWeb": "", "OtherEmail": "", "Notes": "", "BirthDay": 0, "BirthMonth": 0,
	 * "BirthYear": 0, "ETag": "", "GroupUUIDs": ["group1_uuid", "group2_uuid"] }]
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'GetContactsByEmails',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Returns list of contacts with specified emails.
	 * @param string $Storage storage of contacts.
	 * @param array $Emails List of emails of contacts to return.
	 * @return array
	 */
	public function GetContactsByEmails($UserId, $Storage, $Emails, $Filters = array())
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);

		$this->CheckAccess($UserId);
		
		$aFilters = $this->prepareFilters($Filters);
		
		if (!empty($aFilters))
		{
			$aFilters = [
				'$AND' => [
					'$OR' => $aFilters,
					'ViewEmail' => [$Emails, 'IN']
				]
			];
		}
		else
		{
			$aFilters['ViewEmail'] = [$Emails, 'IN'];
		}
		
		$aContacts = $this->getManager()->getContacts(Enums\SortField::Name, \Aurora\System\Enums\SortOrder::ASC, 0, 0, $aFilters);
		
		return $aContacts;
	}	

	/**
	 * Returns list of contacts with specified uids.
	 * @param string $Storage storage of contacts.
	 * @param array $Uids List of uids of contacts to return.
	 * @return array
	 */
	public function GetContactsByUids($UserId, $Storage, $Uids, $Filters = array())
	{
		$aResult = [];
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);

		$this->CheckAccess($UserId);
		
		$aFilters['UUID'] = [$Uids, 'IN'];
		
		$aContacts = $this->getManager()->getContacts(Enums\SortField::Name, \Aurora\System\Enums\SortOrder::ASC, 0, 0, $aFilters);

		foreach ($aContacts as $oContact)
		{
			if ($oContact instanceof \Aurora\Modules\Contacts\Classes\Contact)
			{
				if ($this->CheckAccessToObject($UserId, $oContact->UUID))
				{
					$oContact->GroupsContacts = $this->getManager()->getGroupContacts(null, $oContact->UUID);
					$oContact->Storage = ($oContact->Auto) ? 'collected' : $oContact->Storage;
					$aResult[] = $oContact;
				}
			}
		}
		
		return $aResult;
	}		

	/**
	 * Returns list of contacts with specified emails.
	 * @param string $Storage storage of contacts.
	 * @param array $Uids List of emails of contacts to return.
	 * @return array
	 */
	public function GetContactsInfo($Storage, $UserId = null, $Filters = [])
	{
		$aResult = [
			'CTag' => $this->GetCTag($UserId, $Storage)
		];
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);

		$this->CheckAccess($UserId);

		$aFilters = $this->prepareFilters($Filters);
		if (count($aFilters) > 1)
		{
			$aFilters = ['$OR' => $aFilters];
		}
		$aContacts = (new \Aurora\System\EAV\Query())
			->select(['UUID', 'ETag', 'Storage', 'Auto'])
			->whereType(Classes\Contact::class)
			->where($aFilters)
			->exec();

		foreach ($aContacts as $oContact)
		{
			$aResult['Info'][] = [
				'UUID' => $oContact->UUID,
				'ETag' => $oContact->ETag,
				'Storage' => $oContact->Auto ? 'collected' : $oContact->Storage
			];
		}
		
		return $aResult;
	}		
	
	/**
	 * @api {post} ?/Api/ CreateContact
	 * @apiName CreateContact
	 * @apiGroup Contacts
	 * @apiDescription Creates contact with specified parameters.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=CreateContact} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **Contact** *object* Parameters of contact to create.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'CreateContact',
	 *	Parameters: '{ "Contact": { "UUID": "", "PrimaryEmail": 0, "PrimaryPhone": 0, "PrimaryAddress": 0,
	 * "FullName": "second", "FirstName": "", "LastName": "", "NickName": "", "Storage": "personal",
	 * "Skype": "", "Facebook": "", "PersonalEmail": "contact2@email.com", "PersonalAddress": "",
	 * "PersonalCity": "", "PersonalState": "", "PersonalZip": "", "PersonalCountry": "", "PersonalWeb": "",
	 * "PersonalFax": "", "PersonalPhone": "", "PersonalMobile": "", "BusinessEmail": "", "BusinessCompany": "",
	 * "BusinessJobTitle": "", "BusinessDepartment": "", "BusinessOffice": "", "BusinessAddress": "",
	 * "BusinessCity": "", "BusinessState": "", "BusinessZip": "", "BusinessCountry": "", "BusinessFax": "",
	 * "BusinessPhone": "", "BusinessWeb": "", "OtherEmail": "", "Notes": "", "ETag": "", "BirthDay": 0,
	 * "BirthMonth": 0, "BirthYear": 0, "GroupUUIDs": [] } }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {mixed} Result.Result New contact UUID in case of success, otherwise **false**.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'CreateContact',
	 *	Result: 'new_contact_uuid'
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'CreateContact',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Creates contact with specified parameters.
	 * @param array $Contact Parameters of contact to create.
	 * @param int $UserId Identifier of user that should own a new contact.
	 * @return bool|string
	 * @throws \Aurora\System\Exceptions\ApiException
	 */
	public function CreateContact($Contact, $UserId = null)
	{
		$this->CheckAccess($UserId);

		$oUser = \Aurora\Modules\Core\Module::getInstance()->GetUserUnchecked($UserId);

		$mResult = false;
		
		if ($oUser instanceof \Aurora\Modules\Core\Classes\User)
		{
			$oContact = \Aurora\Modules\Contacts\Classes\Contact::createInstance(
				Classes\Contact::class,
				self::GetName()
			);
			$oContact->IdUser = $oUser->EntityId;
			$oContact->IdTenant = $oUser->IdTenant;
			$oContact->populate($Contact, true);

			$oContact->Frequency = $this->getAutocreatedContactFrequencyAndDeleteIt($oUser->EntityId, $oContact->ViewEmail);
			if ($this->getManager()->createContact($oContact))
			{
				$mResult = ['UUID' => $oContact->UUID, 'ETag' => $oContact->ETag];
			}
		}
		
		return $mResult;
	}
	
	/**
	 * Obtains autocreated contact frequency if user have already created it.
	 * Removes autocreated contact.
	 * @param int $UserId User identifier.
	 * @param string $sViewEmail View email of contact to create
	 */
	private function getAutocreatedContactFrequencyAndDeleteIt($UserId, $sViewEmail)
	{
		$this->CheckAccess($UserId);

		$iFrequency = 0;
		$sStorage = 'personal';
		$aFilters = [
			'$AND' => [
				'ViewEmail' => [$sViewEmail, '='],
				'IdUser' => [$UserId, '='],
				'Auto' => [true, '='],
				'Storage' => $sStorage
			]
		];
		$oAutocreatedContacts = $this->getManager()->getContacts(
			\Aurora\Modules\Contacts\Enums\SortField::Name,
			\Aurora\System\Enums\SortOrder::ASC,
			0,
			1,
			$aFilters
		);
		if (is_array($oAutocreatedContacts) && isset($oAutocreatedContacts[0]))
		{
			$iFrequency = $oAutocreatedContacts[0]->Frequency;
			$this->getManager()->deleteContacts($UserId, $sStorage, [$oAutocreatedContacts[0]->UUID]);
		}
		return $iFrequency;
	}
	
	/**
	 * @api {post} ?/Api/ UpdateContact
	 * @apiName UpdateContact
	 * @apiGroup Contacts
	 * @apiDescription Updates contact with specified parameters.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=UpdateContact} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **Contact** *array* Parameters of contact to update.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'UpdateContact',
	 *	Parameters: '{ "Contact": { "UUID": "contact2_uuid", "PrimaryEmail": 0, "PrimaryPhone": 0,
	 * "PrimaryAddress": 0, "FullName": "contact2", "FirstName": "", "LastName": "", "NickName": "",
	 * "Storage": "personal", "Skype": "", "Facebook": "", "PersonalEmail": "contact2@email.com",
	 * "PersonalAddress": "", "PersonalCity": "", "PersonalState": "", "PersonalZip": "", "PersonalCountry": "",
	 * "PersonalWeb": "", "PersonalFax": "", "PersonalPhone": "", "PersonalMobile": "", "BusinessEmail": "",
	 * "BusinessCompany": "", "BusinessJobTitle": "", "BusinessDepartment": "", "BusinessOffice": "",
	 * "BusinessAddress": "", "BusinessCity": "", "BusinessState": "", "BusinessZip": "", "BusinessCountry": "",
	 * "BusinessFax": "", "BusinessPhone": "", "BusinessWeb": "", "OtherEmail": "", "Notes": "", "ETag": "",
	 * "BirthDay": 0, "BirthMonth": 0, "BirthYear": 0, "GroupUUIDs": [] } }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {bool} Result.Result Indicates if contact was updated successfully.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'UpdateContact',
	 *	Result: true
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'UpdateContact',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Updates contact with specified parameters.
	 * @param array $Contact Parameters of contact to update.
	 * @return bool
	 */
	public function UpdateContact($UserId, $Contact)
	{
		$this->CheckAccess($UserId);

		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);
		
		$oContact = $this->getManager()->getContact($Contact['UUID']);
		if ($oContact)
		{
			$oContact->populate($Contact, true);
			if ($this->getManager()->updateContact($oContact))
			{
				return [
					'UUID' => $oContact->UUID, 
					'ETag' => $oContact->ETag
				];
			}
			else
			{
				return false;
			}
		}
		
		return false;
	}
	
	/**
	 * @api {post} ?/Api/ DeleteContacts
	 * @apiName DeleteContacts
	 * @apiGroup Contacts
	 * @apiDescription Deletes contacts with specified UUIDs.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=DeleteContacts} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **UUIDs** *array* Array of strings - UUIDs of contacts to delete.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'DeleteContacts',
	 *	Parameters: '{ UUIDs: ["uuid1", "uuid"] }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {bool} Result.Result Indicates if contacts were deleted successfully.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'DeleteContacts',
	 *	Result: true
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'DeleteContacts',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Deletes contacts with specified UUIDs.
	 * @param array $UUIDs Array of strings - UUIDs of contacts to delete.
	 * @return bool
	 */
	public function DeleteContacts($UserId, $Storage, $UUIDs)
	{
		$this->CheckAccess($UserId);

		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);

		return $this->getManager()->deleteContacts($UserId, $Storage, $UUIDs);
	}	
	
	/**
	 * @api {post} ?/Api/ CreateGroup
	 * @apiName CreateGroup
	 * @apiGroup Contacts
	 * @apiDescription Creates group with specified parameters.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=CreateGroup} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **Group** *object* Parameters of group to create.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'CreateGroup',
	 *	Parameters: '{ "Group": { "UUID": "", "Name": "new_group_name", "IsOrganization": "0", "Email": "",
	 * "Country": "", "City": "", "Company": "", "Fax": "", "Phone": "", "State": "", "Street": "",
	 * "Web": "", "Zip": "", "Contacts": [] } }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {mixed} Result.Result New group UUID in case of success, otherwise **false**.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'CreateGroup',
	 *	Result: 'new_group_uuid'
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'CreateGroup',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Creates group with specified parameters.
	 * @param array $Group Parameters of group to create.
	 * @return string|bool
	 */
	public function CreateGroup($Group, $UserId = null)
	{
		$this->CheckAccess($UserId);

		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);
		
		$oGroup = \Aurora\Modules\Contacts\Classes\Group::createInstance(
			Classes\Group::class,
			self::GetName()
		);
		$oGroup->IdUser = (int) $UserId;

		$oGroup->populate($Group);

		$this->getManager()->createGroup($oGroup);
		return $oGroup ? $oGroup->UUID : false;
	}	
	
	/**
	 * @api {post} ?/Api/ UpdateGroup
	 * @apiName UpdateGroup
	 * @apiGroup Contacts
	 * @apiDescription Updates group with specified parameters.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=UpdateGroup} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **Group** *object* Parameters of group to update.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'UpdateGroup',
	 *	Parameters: '{ "Group": { "UUID": "group_uuid", "Name": "group_name", "IsOrganization": "0",
	 * "Email": "", "Country": "", "City": "", "Company": "", "Fax": "", "Phone": "", "State": "",
	 * "Street": "", "Web": "", "Zip": "", "Contacts": [] } }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {bool} Result.Result Indicates if group was updated successfully.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'UpdateGroup',
	 *	Result: true
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'UpdateGroup',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Updates group with specified parameters.
	 * @param array $Group Parameters of group to update.
	 * @return boolean
	 */
	public function UpdateGroup($UserId, $Group)
	{
		$this->CheckAccess($UserId);

		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);
		
		$oGroup = $this->getManager()->getGroup($Group['UUID']);
		if ($oGroup)
		{
			if (isset($Group['Contacts']) && is_array($Group['Contacts']))
			{
				$aGroupContactUUIDs = [];
				foreach ($oGroup->GroupContacts as $oGroupContact)
				{
					$aGroupContactUUIDs[] = $oGroupContact->UUID;
				}
				\Aurora\System\Managers\Eav::getInstance()->deleteEntities($aGroupContactUUIDs);
			}

			$oGroup->populate($Group);
			return $this->getManager()->updateGroup($oGroup);
		}

		return false;
	}	
	
	/**
	 * @api {post} ?/Api/ DeleteGroup
	 * @apiName DeleteGroup
	 * @apiGroup Contacts
	 * @apiDescription Deletes group with specified UUID.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=DeleteGroup} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **UUID** *string* UUID of group to delete.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'DeleteGroup',
	 *	Parameters: '{ UUID: "group_uuid" }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {bool} Result.Result Indicates if group was deleted successfully.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'DeleteGroup',
	 *	Result: true
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'DeleteGroup',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Deletes group with specified UUID.
	 * @param string $UUID UUID of group to delete.
	 * @return bool
	 */
	public function DeleteGroup($UserId, $UUID)
	{
		$this->CheckAccess($UserId);
		
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);
		$this->getManager()->updateCTag($UserId, 'personal');
		return $this->getManager()->deleteGroups([$UUID]);
	}
	
	/**
	 * @api {post} ?/Api/ AddContactsToGroup
	 * @apiName AddContactsToGroup
	 * @apiGroup Contacts
	 * @apiDescription Adds specified contacts to specified group.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=AddContactsToGroup} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **GroupUUID** *string* UUID of group.<br>
	 * &emsp; **ContactUUIDs** *array* Array of strings - UUIDs of contacts to add to group.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'AddContactsToGroup',
	 *	Parameters: '{ GroupUUID: "group_uuid", ContactUUIDs: ["contact1_uuid", "contact2_uuid"] }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {bool} Result.Result Indicates if contacts were successfully added to group.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'AddContactsToGroup',
	 *	Result: true
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'AddContactsToGroup',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Adds specified contacts to specified group.
	 * @param string $GroupUUID UUID of group.
	 * @param array $ContactUUIDs Array of strings - UUIDs of contacts to add to group.
	 * @return boolean
	 */
	public function AddContactsToGroup($UserId, $GroupUUID, $ContactUUIDs)
	{
		$this->CheckAccess($UserId);

		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);
		
		if (is_array($ContactUUIDs) && !empty($ContactUUIDs))
		{
			return $this->getManager()->addContactsToGroup($GroupUUID, $ContactUUIDs);
		}
		
		return true;
	}
	
	/**
	 * @api {post} ?/Api/ RemoveContactsFromGroup
	 * @apiName RemoveContactsFromGroup
	 * @apiGroup Contacts
	 * @apiDescription Removes specified contacts from specified group.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=RemoveContactsFromGroup} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **GroupUUID** *string* UUID of group.<br>
	 * &emsp; **ContactUUIDs** *array* Array of strings - UUIDs of contacts to remove from group.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'RemoveContactsFromGroup',
	 *	Parameters: '{ GroupUUID: "group_uuid", ContactUUIDs: ["contact1_uuid", "contact2_uuid"] }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {bool} Result.Result Indicates if contacts were successfully removed from group.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'RemoveContactsFromGroup',
	 *	Result: true
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'RemoveContactsFromGroup',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Removes specified contacts from specified group.
	 * @param string $GroupUUID UUID of group.
	 * @param array $ContactUUIDs Array of strings - UUIDs of contacts to remove from group.
	 * @return boolean
	 */
	public function RemoveContactsFromGroup($UserId, $GroupUUID, $ContactUUIDs)
	{
		$this->CheckAccess($UserId);

		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);
		
		if (is_array($ContactUUIDs) && !empty($ContactUUIDs))
		{
			return $this->getManager()->removeContactsFromGroup($GroupUUID, $ContactUUIDs);
		}
		
		return true;
	}	
	
	/**
	 * @api {post} ?/Api/ Import
	 * @apiName Import
	 * @apiGroup Contacts
	 * @apiDescription Imports contacts from file with specified format.
	 * 
	 * @apiHeader {string} Authorization "Bearer " + Authentication token which was received as the result of Core.Login method.
	 * @apiHeaderExample {json} Header-Example:
	 *	{
	 *		"Authorization": "Bearer 32b2ecd4a4016fedc4abee880425b6b8"
	 *	}
	 * 
	 * @apiParam {string=Contacts} Module Module name
	 * @apiParam {string=Import} Method Method name
	 * @apiParam {string} Parameters JSON.stringified object <br>
	 * {<br>
	 * &emsp; **UploadData** *array* Array of uploaded file data.<br>
	 * &emsp; **Storage** *string* Storage name.<br>
	 * &emsp; **GroupUUID** *array* Group UUID.<br>
	 * }
	 * 
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'Import',
	 *	Parameters: '{ "UploadData": { "tmp_name": "tmp_name_value", "name": "name_value" },
	 *		"Storage": "personal", "GroupUUID": "" }'
	 * }
	 * 
	 * @apiSuccess {object[]} Result Array of response objects.
	 * @apiSuccess {string} Result.Module Module name
	 * @apiSuccess {string} Result.Method Method name
	 * @apiSuccess {mixed} Result.Result Object with counts of imported and parsed contacts in case of success, otherwise **false**.
	 * @apiSuccess {int} [Result.ErrorCode] Error code
	 * 
	 * @apiSuccessExample {json} Success response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'Import',
	 *	Result: { "ImportedCount" : 2, "ParsedCount": 3}
	 * }
	 * 
	 * @apiSuccessExample {json} Error response example:
	 * {
	 *	Module: 'Contacts',
	 *	Method: 'Import',
	 *	Result: false,
	 *	ErrorCode: 102
	 * }
	 */
	
	/**
	 * Imports contacts from file with specified format.
	 * @param array $UploadData Array of uploaded file data.
	 * @param array $GroupUUID Group UUID.
	 * @return array
	 * @throws \Aurora\System\Exceptions\ApiException
	 */
	public function Import($UserId, $UploadData, $GroupUUID)
	{
		$this->CheckAccess($UserId);
		$oUser = \Aurora\Modules\Core\Module::getInstance()->GetUserUnchecked($UserId);

		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);
		
		$aResponse = array(
			'ImportedCount' => 0,
			'ParsedCount' => 0
		);
		
		if (is_array($UploadData))
		{
			$oApiFileCacheManager = new \Aurora\System\Managers\Filecache();
			$sTempFileName = 'import-post-' . md5($UploadData['name'] . $UploadData['tmp_name']);
			if ($oApiFileCacheManager->moveUploadedFile($oUser->UUID, $sTempFileName, $UploadData['tmp_name'], '', self::GetName()))
			{
				$sTempFilePath = $oApiFileCacheManager->generateFullFilePath($oUser->UUID, $sTempFileName, '', self::GetName());

				$aImportResult = array();
				
				$sFileExtension = strtolower(\Aurora\System\Utils::GetFileExtension($UploadData['name']));
				switch ($sFileExtension)
				{
					case 'csv':
						$oSync = new Classes\Csv\Sync();
						$aImportResult = $oSync->Import($oUser->EntityId, $sTempFilePath, $GroupUUID);
						break;
					case 'vcf':
						$aImportResult = $this->importVcf($oUser->EntityId, $sTempFilePath);
						break;
				}

				if (is_array($aImportResult) && isset($aImportResult['ImportedCount']) && isset($aImportResult['ParsedCount']))
				{
					$aResponse['ImportedCount'] = $aImportResult['ImportedCount'];
					$aResponse['ParsedCount'] = $aImportResult['ParsedCount'];
				}
				else
				{
					throw new \Aurora\System\Exceptions\ApiException(\Aurora\System\Notifications::IncorrectFileExtension);
				}

				$oApiFileCacheManager->clear($oUser->UUID, $sTempFileName, '', self::GetName());
			}
			else
			{
				throw new \Aurora\System\Exceptions\ApiException(\Aurora\System\Notifications::UnknownError);
			}
		}
		else
		{
			throw new \Aurora\System\Exceptions\ApiException(\Aurora\System\Notifications::UnknownError);
		}

		return $aResponse;
	}
	
//	public function GetGroupEvents($UUID)
//	{
//		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);
//		
//		return [];
//	}	
	
	public function UpdateSharedContacts($UserId, $UUIDs)
	{
		$this->CheckAccess($UserId);

		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);
		return true;
	}	
	
	public function AddContactsFromFile($UserId, $File)
	{
		$this->CheckAccess($UserId);
		$oUser = \Aurora\Modules\Core\Module::getInstance()->GetUserUnchecked($UserId);

		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);

		if (empty($File))
		{
			throw new \Aurora\System\Exceptions\ApiException(\Aurora\System\Notifications::InvalidInputParameter);
		}

		$oApiFileCache = new \Aurora\System\Managers\Filecache();
		
		$sTempFilePath = $oApiFileCache->generateFullFilePath($oUser->UUID, $File); // Temp files with access from another module should be stored in System folder
		$aImportResult = $this->importVcf($oUser->EntityId, $sTempFilePath);

		return is_array($aImportResult) && isset($aImportResult['ImportedCount']) && $aImportResult['ImportedCount'] > 0;
	}	
	
	public function GetCTag($UserId, $Storage)
	{
		$this->CheckAccess($UserId);
		$oUser = \Aurora\Modules\Core\Module::getInstance()->GetUserUnchecked($UserId);

		$iResult = 0;
		if ($oUser instanceof \Aurora\Modules\Core\Classes\User)
		{
			$iUserId = $Storage === 'personal' || $Storage === 'collected' ? $oUser->EntityId : $oUser->IdTenant;

			$oCTag = $this->getManager()->getCTag($iUserId, $Storage);
			if ($oCTag instanceof Classes\CTag)
			{
				$iResult = $oCTag->CTag;
			}
		}


		return $iResult;
	}	
	
	/**
	 * 
	 * @param type $UserId
	 * @param type $UUID
	 * @param type $Storage
	 * @param type $FileName
	 */
	public function SaveContactAsTempFile($UserId, $UUID, $FileName)
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\Aurora\System\Enums\UserRole::NormalUser);

		$this->CheckAccess($UserId);

		$mResult = false;

		$oContact = self::Decorator()->GetContact($UUID);
		$oVCard = new \Sabre\VObject\Component\VCard();
		\Aurora\Modules\Contacts\Classes\VCard\Helper::UpdateVCardFromContact($oContact, $oVCard);
		$sVCardData = $oVCard->serialize();
		if ($sVCardData)
		{
			$sUUID = \Aurora\System\Api::getUserUUIDById($UserId);
			$sTempName = md5($sUUID.$UUID);
			$oApiFileCache = new \Aurora\System\Managers\Filecache();

			$oApiFileCache->put($sUUID, $sTempName, $sVCardData);
			if ($oApiFileCache->isFileExists($sUUID, $sTempName))
			{
				$mResult = \Aurora\System\Utils::GetClientFileResponse(
					null, $UserId, $FileName, $sTempName, $oApiFileCache->fileSize($sUUID, $sTempName)
				);
			}
		}
		
		return $mResult;		
	}	
	/***** public functions might be called with web API *****/
	
	/***** private functions *****/
	private function importVcf($iUserId, $sTempFilePath)
	{
		$aImportResult = array(
			'ParsedCount' => 0,
			'ImportedCount' => 0,
		);
		// You can either pass a readable stream, or a string.
		$oHandler = fopen($sTempFilePath, 'r');
		$oSplitter = new \Sabre\VObject\Splitter\VCard($oHandler);
		$oContactsDecorator = Module::Decorator();
		$oApiContactsManager = $oContactsDecorator ? $oContactsDecorator->GetApiContactsManager() : null;
		if ($oApiContactsManager)
		{
			while ($oVCard = $oSplitter->getNext())
			{
				set_time_limit(30);
				
				$aContactData = Classes\VCard\Helper::GetContactDataFromVcard($oVCard);
				$oContact = isset($aContactData['UUID']) ? $oApiContactsManager->getContact($aContactData['UUID']) : null;
				$aImportResult['ParsedCount']++;
				if (!isset($oContact) || empty($oContact))
				{
					if ($oContactsDecorator->CreateContact($aContactData, $iUserId))
					{
						$aImportResult['ImportedCount']++;
					}
				}
			}
		}
		return $aImportResult;
	}
	
	private function prepareFilters($aRawFilters)
	{
		$aFilters = [];
		
		if (is_array($aRawFilters) && count($aRawFilters) > 0)
		{
			$iAndIndex = 1;
			$iOrIndex = 1;
			foreach ($aRawFilters as $aSubFilters)
			{
				if (is_array($aSubFilters))
				{
					foreach ($aSubFilters as $sKey => $a2ndSubFilters)
					{
						if (is_array($a2ndSubFilters))
						{
							$sNewKey = $sKey;
							if ($sKey === '$AND')
							{
								$sNewKey = $iAndIndex.'$AND';
								$iAndIndex++;
							}
							if ($sKey === '$OR')
							{
								$sNewKey = $iOrIndex.'$OR';
								$iOrIndex++;
							}
							$aFilters[$sNewKey] = $a2ndSubFilters;
						}
					}
				}
			}
		}
		else
		{
			// It's forbidden to request contacts without any filters because in that case all contacts of all users will be returned.
			// If filters are empty, there is no subscribers from modules that describe behaviour of contacts storages.
			throw new \Aurora\System\Exceptions\ApiException(\Aurora\System\Notifications::InvalidInputParameter);
		}
		
		return $aFilters;
	}
	
	public function onAfterUseEmails($Args, &$Result)
	{
		$aAddresses = $Args['Emails'];
		$iUserId = \Aurora\System\Api::getAuthenticatedUserId();
		foreach ($aAddresses as $sEmail => $sName)
		{
			$oContact = $this->getManager()->getContactByEmail($iUserId, $sEmail);
			if ($oContact)
			{
				if ($oContact->Frequency !== -1)
				{
					$oContact->Frequency = $oContact->Frequency + 1;
					$this->getManager()->updateContact($oContact);
				}
			}
			else
			{
				$oContactsDecorator = Module::Decorator();
				if ($oContactsDecorator)
				{
					$oContactsDecorator->CreateContact([
						'FullName' => $sName,
						'PersonalEmail' => $sEmail,
						'Auto' => true,
					], $iUserId);
					$this->getManager()->updateCTag($iUserId, 'collected');
				}
			}
		}
	}
	
	public function onGetBodyStructureParts($aParts, &$aResultParts)
	{
		foreach ($aParts as $oPart)
		{
			if ($oPart instanceof \MailSo\Imap\BodyStructure && 
					($oPart->ContentType() === 'text/vcard' || $oPart->ContentType() === 'text/x-vcard'))
			{
				$aResultParts[] = $oPart;
				break;				
			}
		}
	}
	
	public function onBeforeDeleteUser(&$aArgs, &$mResult)
	{
		$aGroups = $this->getManager()->getGroups($aArgs['UserId']);
		if (count($aGroups) > 0)
		{
			$aGroupUUIDs = [];
			foreach ($aGroups as $oGroup)
			{
				$aGroupUUIDs[] = $oGroup->UUID;
			}
			$this->getManager()->deleteGroups($aGroupUUIDs);
		}
		$this->getManager()->deleteCTagsByUserId($aArgs['UserId'], 'personal');
		$this->getManager()->deleteCTagsByUserId($aArgs['UserId'], 'collected');
	}

	/**
	 * 
	 */
	public function onAfterCreateTables(&$aData, &$mResult)
	{
		\Aurora\System\Managers\Db::getInstance()->executeSqlFile(
			dirname(__FILE__) . '/Sql/update_contact_notes_field_type.sql'
		);
	}	
	/***** private functions *****/
}
