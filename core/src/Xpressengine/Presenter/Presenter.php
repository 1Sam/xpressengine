<?php
/**
 * Presenter
 *
 * PHP version 5
 *
 * @category  Presenter
 * @package   Xpressengine\Presenter
 * @author    XE Team (developers) <developers@xpressengine.com>
 * @copyright 2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license   http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link      http://www.xpressengine.com
 */
namespace Xpressengine\Presenter;

use Illuminate\Http\Request;
use Xpressengine\Theme\ThemeHandler;
use Xpressengine\Skin\SkinHandler;
use Xpressengine\Settings\SettingsHandler;
use Xpressengine\Routing\InstanceConfig;
use Illuminate\View\Factory as ViewFactory;
use Closure;

/**
 * # Presenter
 * * Response 할 때 View 를 대신해서 사용
 * * request 에 따라 response 를 컨트롤
 * * 스킨, 테마, 메뉴 등을 이용해서 출력
 *
 * ## App binding
 * * xe.presenter 로 바인딩 되어 있음
 * * Presenter facade 제공
 *
 * ## Interception
 *
 * ## 사용법
 *
 * ### 스킨 사용 등록
 * ```php
 * $presenter = app('xe.presenter');
 *
 * // 사용자 스킨 사용
 * $presenter->setSkin('parent-key');
 *
 * // 관리자 스킨 사용
 * $presenter->setSettingsSkin('parent-key');
 * ```
 *
 * ### Html 형식만 지원
 * ```php
 * public controllerMethodName()
 * {
 *      ... 생략 ...
 *      return Presenter::make('skin.view.name');
 * }
 *
 * ```
 *
 * ### Api(json) 형식만 지원 할 경우
 * ```php
 * public controllerMethodName()
 * {
 *      ... 생략 ...
 *      return Presenter::makeApi(['data']);
 * }
 *
 * ```
 *
 * ### 모든 형식을 지원 할 경우
 * ```php
 * public controllerMethodName()
 * {
 *      ... 생략 ...
 *      return Presenter::makeAll('skin.view.name');
 * }
 *
 * ```
 *
 * @category    Presenter
 * @package     Xpressengine\Presenter
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
class Presenter
{
    /**
     * render type
     */
    const RENDER_ALL = 'all';

    /**
     * render type
     */
    const RENDER_POPUP = 'popup';

    /**
     * render type
     */
    const RENDER_CONTENT = 'content';

    /**
     * output id
     *
     * @var string
     */
    protected $id;

    /**
     * Data that should be available to all templates.
     *
     * @var array
     */
    protected $shared = [];

    /**
     * is settings present
     *
     * @var bool
     */
    protected $isSettings = false;

    /**
     * skin class name
     *
     * @var string
     */
    protected $skinName;

    /**
     * @var string
     */
    protected $type = self::RENDER_ALL;

    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ThemeHandler
     */
    protected $themeHandler;

    /**
     * @var SkinHandler
     */
    protected $skinHandler;

    /**
     * @var SettingsHandler
     */
    protected $settingsHandler;

    /**
     * @var InstanceConfig
     */
    protected $instanceConfig;

    /**
     * is support api
     *
     * @var bool
     */
    protected $api = false;

    /**
     * is support html
     *
     * @var bool
     */
    protected $html = true;

    /**
     * registered renderer class names
     *
     * @var array
     */
    protected $renderes = [];

    /**
     * Create a new RendererManager instance.
     *
     * @param ViewFactory     $viewFactory     view factory
     * @param Request         $request         Request instance
     * @param ThemeHandler    $themeHandler    theme handler
     * @param SkinHandler     $skinHandler     skin handler
     * @param SettingsHandler $settingsHandler manage handler
     * @param InstanceConfig  $instanceConfig  menu config
     */
    public function __construct(
        ViewFactory $viewFactory,
        Request $request,
        $themeHandler,
        $skinHandler,
        SettingsHandler $settingsHandler,
        InstanceConfig $instanceConfig
    ) {
        $this->viewFactory = $viewFactory;
        $this->request = $request;
        $this->themeHandler = $themeHandler;
        $this->skinHandler = $skinHandler;
        $this->settingsHandler = $settingsHandler;
        $this->instanceConfig = $instanceConfig;
    }

    /**
     * get ViewFactory
     *
     * @return ViewFactory
     */
    public function getViewFactory()
    {
        return $this->viewFactory;
    }

    /**
     * get request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
    /**
     * get menu config
     *
     * @return InstanceConfig
     */
    public function getInstanceConfig()
    {
        return $this->instanceConfig;
    }

    /**
     * get skin handler
     *
     * @return SkinHandler
     */
    public function getSkinHandler()
    {
        return $this->skinHandler;
    }

    /**
     * get theme handler
     *
     * @return ThemeHandler
     */
    public function getThemeHandler()
    {
        return $this->themeHandler;
    }

    /**
     * get settings handler
     *
     * @return SettingsHandler
     * @deprecated
     */
    public function getManageHandler()
    {
        return $this->getSettingsHandler();
    }


    /**
     * get settings handler
     *
     * @return SettingsHandler
     */
    public function getSettingsHandler()
    {
        return $this->settingsHandler;
    }

    /**
     * register renderer
     *
     * @param string  $format   format
     * @param Closure $callback closure for get instance
     * @return void
     */
    public function register($format, Closure $callback)
    {
        $this->renderes[$format] = $callback;
    }

    /**
     * set skin class name
     *
     * @param string $skinName skin class name
     * @return void
     */
    public function setSkin($skinName)
    {
        $this->skinName = $skinName;
    }

    /**
     * set settings skin class name
     *
     * @param string $skinName skin class name
     * @return void
     */
    public function setSettingsSkin($skinName)
    {
        $this->skinName = $skinName;
        $this->isSettings = true;
    }

    /**
     * render 방식 설정
     * $type [
     *  'all' => theme, skin 처리
     *  'content' => content 만 render
     * ]
     *
     * @param string $type render type
     * @return void
     * @deprecated
     */
    public function renderType($type = self::RENDER_ALL)
    {
        $this->type = $type;
    }

    /**
     * render 방식을 content 로 설정
     *
     * @param bool $partial render to content
     * @return void
     */
    public function htmlRenderPartial($partial = true)
    {
        if ($partial === true) {
            $this->type = self::RENDER_CONTENT;
        } else {
            $this->type = self::RENDER_ALL;
        }
    }

    /**
     * render 방식을 content 로 설정
     *
     * @param bool $popup render to popup
     * @return void
     */
    public function htmlRenderPopup($popup = true)
    {
        if ($popup === true) {
            $this->type = self::RENDER_POPUP;
        } else {
            $this->type = self::RENDER_ALL;
        }
    }

    /**
     * Add a piece of shared data to the environment.
     *
     * @param mixed $key   key(string|array)
     * @param mixed $value value
     * @return null|array
     */
    public function share($key, $value = null)
    {
        if (is_array($key) === false) {
            return $this->shared[$key] = $value;
        }

        foreach ($key as $innerKey => $innerValue) {
            $this->share($innerKey, $innerValue);
        }
        return null;
    }

    /**
     * get shared
     *
     * @return array
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * 출력 처리할 renderer 반환
     * api 지원 안함
     *
     * @param string $id        skin output id
     * @param array  $data      data
     * @param array  $mergeData merge data
     * @param bool   $html      use html
     * @param bool   $api       use api
     * @return RendererInterface
     */
    public function make($id, array $data = [], array $mergeData = [], $html = true, $api = false)
    {
        $this->setUse($html, $api);
        $this->shared = array_merge($this->shared, $data, $mergeData);
        $this->id = $id;

        /** @var RendererInterface $renderer */
        return $this->get();
    }

    /**
     * API 지원하는 renderer 반환
     * html 지원 안하지 않고 api만 처리 할 경우 사용
     *
     * @param array $data      data
     * @param array $mergeData merge data
     * @return RendererInterface
     */
    public function makeApi(array $data = [], array $mergeData = [])
    {
        // api 는 현재 controller 의 데이터 만 출력
        $this->shared = [];

        return $this->make(null, $data, $mergeData, false, true);
    }

    /**
     * api, html 모두 지원하는 renderer 반환
     *
     * @param string $id        skin output id
     * @param array  $data      data
     * @param array  $mergeData merge data
     * @return RendererInterface
     */
    public function makeAll($id, array $data = [], array $mergeData = [])
    {
        return $this->make($id, $data, $mergeData, true, true);
    }

    /**
     * get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * get shared data
     * @return array
     */
    public function getData()
    {
        return $this->shared;
    }

    /**
     * get skin name
     *
     * @return string
     */
    public function getSkin()
    {
        return $this->skinName;
    }

    /**
     * get is settings support
     *
     * @return bool
     */
    public function getIsSettings()
    {
        return $this->isSettings;
    }

    /**
     * get render type
     *
     * @return string
     */
    public function getRenderType()
    {
        return $this->type;
    }

    /**
     * Presenter Package 는 JsonRenderer, HtmlRenderer 를 지원한다.
     * Xpressengine 은 Register Container 로 등록된 Renderer 를 사용한다.
     *
     * @return RendererInterface
     */
    protected function get()
    {
        // is ajax call ? remove theme, skin html tags
        // $this->request->ajax();

        $format = $this->request->format();
        if ($this->isApproveFormat($format) === false) {
            throw new Exceptions\NotApprovedFormatException;
        }

        if (isset($this->renderes[$format]) === false) {
            throw new Exceptions\NotFoundFormatException;
        }

        $callback = $this->renderes[$format];
        $renderer = call_user_func_array($callback, [$this]);

        if (is_subclass_of($renderer, 'Xpressengine\Presenter\RendererInterface') === false) {
            throw new Exceptions\InvalidRendererException;
        }

        return $renderer;
    }

    /**
     * html, api 사용 유무 설정
     *
     * @param bool $html use or disuse
     * @param bool $api  use or disuse
     * @return void
     */
    private function setUse($html, $api)
    {
        $this->setHtml($html);
        $this->setApi($api);
    }

    /**
     * API로 출력 사용 유무 설정
     *
     * @param bool $use use or disuse
     * @return void
     */
    private function setApi($use = true)
    {
        $this->api = $use;
    }

    /**
     * HTML로 출력 사용 유무 설정
     *
     * @param bool $use use or disuse
     * @return void
     */
    private function setHtml($use = true)
    {
        $this->html = $use;
    }

    /**
     * render 할 수 있도록 허용된 요청 fotmat인가?
     *
     * @param string $format request format
     * @return bool
     */
    private function isApproveFormat($format)
    {
        if ($this->html !== true && $format == 'html') {
            return false;
        } elseif ($this->api !== true && $format != 'html') {
            return false;
        }

        return true;
    }
}
