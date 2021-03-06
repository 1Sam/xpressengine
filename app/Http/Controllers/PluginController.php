<?php namespace App\Http\Controllers;

use Plugin;
use Presenter;
use Redirect;
use Symfony\Component\HttpFoundation\Response;
use Xpressengine\Http\Request;
use Xpressengine\Plugin\PluginHandler;
use Xpressengine\Support\Exceptions\HttpXpressengineException;
use Xpressengine\Support\Exceptions\XpressengineException;

class PluginController extends Controller
{

    /**
     * PluginController constructor.
     */
    public function __construct()
    {
        Presenter::setSettingsSkin('plugins');
    }

    public function index(Request $request)
    {
        // filter input
        $field = [];
        $field['component'] = $request->get('component');
        $field['status'] = $request->get('status');
        $field['keyword'] = $request->get('query');

        if ($field['keyword'] === '') {
            $field['keyword'] = null;
        }

        $collection = Plugin::getAllPlugins(true);
        $plugins = $collection->fetch($field);

        $componentTypes = $this->getComponentTypes();

        return Presenter::make(
            'index',
            [
                'plugins' => $plugins,
                'componentTypes' => $componentTypes,
            ]
        );
    }

    public function show($pluginId, PluginHandler $handler)
    {
        $componentTypes = $this->getComponentTypes();

        $plugin = $handler->getPlugin($pluginId);
        return Presenter::make('show', compact('plugin', 'componentTypes'));
    }

    public function postActivatePlugin($pluginId)
    {
        try {
            Plugin::activatePlugin($pluginId);
        } catch (XpressengineException $e) {
            $exception = new HttpXpressengineException('403');
            $exception->setMessage($e->getMessage());
            throw $exception;
        } catch (\Exception $e) {
            throw $e;
        }

        return Redirect::route('settings.plugins')->withAlert(['type' => 'success', 'message' => '플러그인을 켰습니다.']);
    }

    public function postDeactivatePlugin($pluginId)
    {
        try {
            Plugin::deactivatePlugin($pluginId);
        } catch (XpressengineException $e) {
            $exception = new HttpXpressengineException('403');
            $exception->setMessage($e->getMessage());
            throw $exception;
        } catch (\Exception $e) {
            throw $e;
        }

        return Redirect::route('settings.plugins')->withAlert(['type' => 'success', 'message' => '플러그인을 껐습니다.']);
    }

    public function postUpdatePlugin($pluginId)
    {
        try {
            Plugin::updatePlugin($pluginId);
        } catch (XpressengineException $e) {
            $exception = new HttpXpressengineException(Response::HTTP_FORBIDDEN);
            $exception->setMessage($e->getMessage());
            throw $exception;
        } catch (\Exception $e) {
            throw $e;
        }

        return Redirect::route('settings.plugins')->withAlert(['type' => 'success', 'message' => '플러그인을 업데이트했습니다.']);
    }

    /**
     * getComponentTypes
     *
     * @return array
     */
    protected function getComponentTypes()
    {
        $componentTypes = [
            'theme' => '테마',
            'skin' => '스킨',
            'settingsSkin' => '설정스킨',
            'settingsTheme' => '관리페이지테마',
            'widget' => '위젯',
            'module' => '모듈',
            'uiobject' => 'UI오브젝트',
            'FieldType' => '다이나믹필드',
            'FieldSkin' => '다이나믹필드스킨',
        ];
        return $componentTypes;
    }
}

