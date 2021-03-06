<?php
/**
 * This file is a grant value object.
 *
 * PHP version 5
 *
 * @category    Permission
 * @package     Xpressengine\Permission
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
namespace Xpressengine\Permission;

use Xpressengine\Support\EntityTrait;
use Xpressengine\Permission\Exceptions\InvalidArgumentException;

/**
 * register 되어진 권한의 실제 값들을 보관하는 클래스.
 *
 * @category    Permission
 * @package     Xpressengine\Permission
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
class Grant
{
    use EntityTrait;

    const RATING_TYPE = 'rating';

    const GROUP_TYPE = 'group';

    const USER_TYPE = 'user';

    const EXCEPT_TYPE = 'except';

    const VGROUP_TYPE = 'vgroup';

    /**
     * Set grant information
     *
     * @param string       $action action type
     * @param string|array $type   value type
     * @param mixed        $value  values
     * @return $this
     * @throws InvalidArgumentException
     */
    public function set($action, $type, $value = null)
    {
        if (array_key_exists($action, Action::all()) === false) {
            throw new InvalidArgumentException(['arg' => $action]);
        }

        $this->attributes[$action] = $this->makeValue($type, $value);

        return $this;
    }

    /**
     * Add grant information
     *
     * @param string       $action action type
     * @param string|array $type   value type
     * @param mixed        $value  values
     * @return $this
     */
    public function add($action, $type, $value = null)
    {
        if (isset($this->attributes[$action]) === false) {
            return $this->set($action, $type, $value);
        }

        $this->attributes[$action] = array_merge($this->attributes[$action], $this->makeValue($type, $value));
        return $this;
    }

    /**
     * Add except user identifier
     *
     * @param string $action  action type
     * @param mixed  $userIds user id or ids
     * @return $this
     */
    public function except($action, $userIds = null)
    {
        $this->add($action, static::EXCEPT_TYPE, $userIds);

        return $this;
    }

    /**
     * Make value array from arguments
     *
     * @param string|array $type  value type
     * @param mixed        $value values
     * @return array
     */
    protected function makeValue($type, $value = null)
    {
        if ($value === null) {
            $value = $type;

            if (is_array($value) === true) {
                $type = null;
            } else {
                $type = static::RATING_TYPE;
            }
        }
        if ($type !== null) {
            if ($type !== static::RATING_TYPE && is_array($value) !== true) {
                $value = [$value];
            }
            $value = [$type => $value];
        }

        return $value;
    }
}
