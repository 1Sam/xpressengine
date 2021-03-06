<?php
/**
 *  This file is part of the Xpressengine package.
 *
 * PHP version 5
 *
 * @category    Theme
 * @package     Xpressengine\Theme
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
namespace Xpressengine\Theme;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * ThemeEntity는 하나의 테마에 대한 정보를 가지고 있는 클래스이다.
 * XpressEngine에 등록된 테마들의 정보를 ThemeEntity로 생성하여 처리한다.
 *
 * @category    Theme
 * @package     Xpressengine\Theme
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
class ThemeEntity implements Arrayable, Jsonable
{

    /**
     * @var string theme id
     */
    protected $id;

    /**
     * @var AbstractTheme class name of theme
     */
    protected $class;

    /**
     * @var AbstractTheme object of theme
     */
    protected $object;

    /**
     * ThemeEntity constructor.
     *
     * @param string $id    theme id
     * @param string $class theme class name
     */
    public function __construct($id, $class)
    {
        $this->id = $id;
        $this->class = $class;
    }

    /**
     * get theme id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * get class name of theme
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * get theme title
     *
     * @return mixed
     */
    public function getTitle()
    {
        $class = $this->class;
        return $class::getTitle();
    }

    /**
     * get theme's description
     *
     * @return string
     */
    public function getDescription()
    {
        $class = $this->class;
        return $class::getDescription();
    }

    /**
     * 각 테마는 편집 페이지에서 편집할 수 있는 템플릿파일(blade)이나 css 파일 목록을 지정한다.
     * 이 메소드는 그 파일 목록을 조회한다.
     *
     * @return array
     */
    public function getEditFiles()
    {
        $class = $this->class;
        return $class::getEditFiles();
    }

    /**
     * get screenshot of theme
     *
     * @return mixed
     */
    public function getScreenshot()
    {
        $class = $this->class;
        return $class::getScreenshot();
    }

    /**
     * get theme setting page url
     *
     * @return null|string
     */
    public function getSettingsURI()
    {
        $class = $this->class;
        return $class::getSettingsURI();
    }

    /**
     * 테마가 desktop 버전을 지원하는지 조사한다.
     *
     * @return bool desktop 버전을 지원할 경우 true
     */
    public function supportDesktop()
    {
        $class = $this->class;
        return $class::supportDesktop();
    }

    /**
     * 테마가 desktop 버전만을 지원하는지 조사한다.
     *
     * @return bool desktop 버전만을 지원할 경우 true
     */
    public function supportDesktopOnly()
    {
        $class = $this->class;
        return $class::supportDesktopOnly();
    }

    /**
     * 테마가 mobile 버전을 지원하는지 조사한다.
     *
     * @return bool mobile 버전을 지원할 경우 true
     */
    public function supportMobile()
    {
        $class = $this->class;
        return $class::supportMobile();
    }

    /**
     * 테마가 mobile 버전만을 지원하는지 조사한다.
     *
     * @return bool mobile 버전만을 지원할 경우 true
     */
    public function supportMobileOnly()
    {
        $class = $this->class;
        return $class::supportMobileOnly();
    }


    /**
     * get object of theme
     *
     * @return AbstractTheme
     */
    public function getObject()
    {
        if (isset($this->object) && is_a($this->object, 'Xpressengine\Theme\AbstractTheme')) {
            return $this->object;
        } else {
            $this->object = new $this->class();
            return $this->object;
        }
    }

    /**
     * ThemeEntity에서 제공하지 않는 메소드일 경우 이 entity가 저장하고 있는 theme의 method를 호출한다.
     *
     * @param string $method    method name
     * @param array  $arguments argument list
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->getObject(), $method), $arguments);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options json_encode option
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->getTitle(),
            'class' => $this->class,
            'description' => $this->getDescription(),
            'screenshot' => $this->getScreenshot()
        ];
    }
}
