<?php
/** @noinspection SpellCheckingInspection */declare(strict_types=1);

namespace MVQN\UCRM\Plugins;

/**
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 *
 * @method static bool|null getVerboseDebug()
 * @method static string|null getApiKey()
 * @method static string|null getApiUsername()
 * @method static string|null getApiPassword()
 * @method static string|null getDuplicateMode()
 */
final class Settings extends SettingsBase
{
	/** @const string The absolute path to the root path of this project. */
	public const PLUGIN_ROOT_PATH = 'C:\Users\rspaeth\Documents\PhpStorm\Projects\ucrm-plugins\towercoverage\src';

	/** @const string The absolute path to the data path of this project. */
	public const PLUGIN_DATA_PATH = 'C:\Users\rspaeth\Documents\PhpStorm\Projects\ucrm-plugins\towercoverage\src\data';

	/** @const string The absolute path to the source path of this project. */
	public const PLUGIN_SOURCE_PATH = 'C:\Users\rspaeth\Documents\PhpStorm\Projects\ucrm-plugins\towercoverage\src\src';

	/** @const string The publicly accessible URL of this UCRM, null if not configured in UCRM. */
	public const UCRM_PUBLIC_URL = 'http://ucrm.dev.mvqn.net/';

	/** @const string An automatically generated UCRM API 'App Key' with read/write access. */
	public const PLUGIN_APP_KEY = 'DrWL+nvuoqNW/TNSZ4OkXz+7YHxU3CZQjbFOo3JGS94sfVisiZi6rGWLuNYLuxh4';

	/**
	 * Verbose Debugging?
	 * @var bool|null If enabled, will include verbose debug messages in the Webhook Request Body.
	 */
	protected static $verboseDebug;

	/**
	 * API Key
	 * @var string|null The API Key from TowerCoverage.com. If blank, allows submissions from any EUS API.
	 */
	protected static $apiKey;

	/**
	 * API Username
	 * @var string|null The API Username from TowerCoverage.com. If blank, allows submissions from any EUS API.
	 */
	protected static $apiUsername;

	/**
	 * API Password
	 * @var string|null The API Password from TowerCoverage.com. If blank, allows submissions from any EUS API.
	 */
	protected static $apiPassword;

	/**
	 * Duplicate Mode
	 * @var string|null Select the method for determining duplicate entries.
	 */
	protected static $duplicateMode;
}
