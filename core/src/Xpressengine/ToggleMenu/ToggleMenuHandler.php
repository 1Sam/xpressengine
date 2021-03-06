<?php
/**
 * This file is toggle menu handler.
 *
 * PHP version 5
 *
 * @category    ToggleMenu
 * @package     Xpressengine\ToggleMenu
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
namespace Xpressengine\ToggleMenu;

use Xpressengine\Config\ConfigManager;
use Xpressengine\Plugin\PluginRegister;
use Xpressengine\ToggleMenu\Exceptions\WrongInstanceException;

/**
 * # ToggleMenuHandler
 * toggle 형태의 메뉴에 나타날 아이템들을 관리합니다.
 *
 * ### app binding : xe.toggleMenu 로 바인딩 되어 있음
 * ToggleMenu Facade 로 접근 가능
 *
 * ### Usage
 * toggle menu 는 'PluginRegister' 를 통해 추가 됩니다.
 *
 * ```php
 *  app('xe.pluginRegister')->add(SampleItem::class);
 * ```
 * 위 예에서 `SampleItem` 은 `AbstractToggleMenu` 상속받아 구현되어야 합니다.
 *
 * 등록되어진 후 설정을 통해 활성화된 아이템들을 반환 받아 사용할 수 있게 됩니다.
 * ```php
 *  $menuItems = ToggleMenu::getItems('pluginId');
 *  // 인스턴스로 구분되어지는 대상은 해당 인스턴스 아이디가 전달되어야 합니다.
 *  $menuItems = ToggleMenu::getItems('pluginId', 'instanceId');
 *  // action 을 실행할 대상의 고유 아이디가 넘겨지면
 *  // 각 아이템이 사용 가능하도록 객체 생성시 다시 전달 됩니다.
 *  $menuItems = ToggleMenu::getItems('pluginId', 'instanceId', 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx');
 * ```
 *
 * @category    ToggleMenu
 * @package     Xpressengine\ToggleMenu
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
class ToggleMenuHandler
{
    /**
     * Register instance
     *
     * @var PluginRegister
     */
    protected $register;

    /**
     * Xe config instance
     *
     * @var ConfigManager
     */
    protected $cfg;

    /**
     * Type suffix for register
     *
     * @var string
     */
    protected static $suffix = 'menu';

    /**
     * Constructor
     *
     * @param PluginRegister $register Register instance
     * @param ConfigManager  $cfg      Xe config instance
     */
    public function __construct(PluginRegister $register, ConfigManager $cfg)
    {
        $this->register = $register;
        $this->cfg = $cfg;
    }

    /**
     * 사용할 메뉴 아이템들을 반환
     *
     * @param string $id         target plugin id
     * @param string $instanceId instance id
     * @param string $identifier target identifier
     * @return ItemInterface[]
     * @throws WrongInstanceException
     */
    public function getItems($id, $instanceId = null, $identifier = null)
    {
        $items = $this->getActivated($id, $instanceId);

        foreach ($items as &$item) {
            $item = new $item($id, $identifier);

            if (!$item instanceof ItemInterface) {
                throw new WrongInstanceException();
            }
        }

        return $items;
    }

    /**
     * 사용할 아이템들을 설정에 저장
     *
     * @param string      $id         target plugin id
     * @param string|null $instanceId instance id
     * @param array       $keys       menu item keys
     * @return \Xpressengine\Config\ConfigEntity
     */
    public function setActivates($id, $instanceId = null, array $keys = [])
    {
        return $this->cfg->set($this->getConfigKey($id, $instanceId), ['activate' => $keys]);
    }

    /**
     * 활성화된 아이템 목록을 반환
     *
     * @param string      $id         target plugin id
     * @param string|null $instanceId instance id
     * @return array
     */
    public function getActivated($id, $instanceId = null)
    {
        // todo: 임시? seed 로 추가되면 제거?
        if ($this->cfg->get($this->getConfigKey($id, null)) === null) {
            $this->cfg->set($this->getConfigKey($id, null), []);
        }


        if (($config = $this->cfg->get($this->getConfigKey($id, $instanceId))) === null) {
            $config = $this->setActivates($id, $instanceId);
        }

        $keys = $config->get('activate', []);


        $activated = array_intersect_key($this->all($id), array_flip($keys));

        // sort
        $activated = array_merge(array_flip($keys), $activated);

        return array_filter($activated, function ($val) {
            return !empty($val);
        });
    }

    /**
     * 활성화 되지 않은 아이템 목록을 반환
     *
     * @param string      $id         target plugin id
     * @param string|null $instanceId instance id
     * @return array
     */
    public function getDeactivated($id, $instanceId = null)
    {
        return array_diff_key($this->all($id), $this->getActivated($id, $instanceId));
    }

    /**
     * config 에서 사용할 key 반환
     *
     * @param string      $id         target plugin id
     * @param string|null $instanceId instance id
     * @return string
     */
    protected function getConfigKey($id, $instanceId)
    {
        return 'toggleMenu@' . $id . ($instanceId !== null ? '.' . $instanceId : '');
    }

    /**
     * type 에 해당하는 모든 메뉴 아이템목록을 반환
     *
     * @param string $id target plugin id
     * @return array
     */
    public function all($id)
    {
        return $this->register->get($this->getTypeKey($id)) ?: [];
    }

    /**
     * register 에서 구분할 수 있는 type key 반환
     *
     * @param string $id target plugin id
     * @return string
     */
    private function getTypeKey($id)
    {
        return $id . PluginRegister::KEY_DELIMITER . 'toggleMenu';
    }
}
