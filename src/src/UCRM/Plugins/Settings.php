<?php
/** @noinspection SpellCheckingInspection */declare(strict_types=1);

namespace UCRM\Plugins;

use MVQN\UCRM\Plugins\SettingsBase;

/**
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 *
 * @method static bool|null getVerboseDebug()
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
	public const PLUGIN_APP_KEY = '4LOxNWuUXlvk26C3puUrVcZU/wPK3jtmrytqY84JKN/Al7XmFAlN3nJ86Gp2wNU2';

	/**
	 * Verbose Debugging?
	 * @var bool|null If enabled, will include verbose debug messages in the Webhook Request Body.
	 */
	protected static $verboseDebug;
}
