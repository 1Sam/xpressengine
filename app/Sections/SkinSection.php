<?php
namespace App\Sections;

use View;
use Xpressengine\Skin\SkinHandler;

class SkinSection
{

    /**
     * setting
     *
     * @param string $target
     * @param string $instanceId
     *
     * @return \Illuminate\View\View
     */
    public function setting($target, $instanceId = null, $mode = null)
    {
        if ($mode === null) {
            $view = $this->makeView($target, $instanceId, 'desktop')->render();
            $view .= $this->makeView($target, $instanceId, 'mobile')->render();
            return $view;
        } else {
            return $this->makeView($target, $instanceId, $mode);
        }

    }

    /**
     * makeView
     *
     * @param $target
     * @param $instanceId
     * @param $mode
     *
     * @return \Illuminate\Contracts\View\View
     */
    protected function makeView($target, $instanceId, $mode)
    {
        /** @var SkinHandler $skinHandler */
        $skinHandler = app('xe.skin');

        $skinInstanceId = $skinHandler->mergeKey($target, $instanceId);

        $selectedSkin = $skinHandler->getAssigned([$target, $instanceId], $mode);

        if ($selectedSkin !== null) {
            $settingView = $selectedSkin->getSettingView();
        } else {
            $settingView = null;
        }

        // get skin list
        $skinList = $skinHandler->getList($target);

        $skins = function ($skinList, $selectedSkin) {
            yield [
                'text' => '선택하세요',
                'selected' => false
            ];

            foreach ($skinList as $id => $skin) {
                $support = [];
                $support[] = $skin->supportDesktop() ? '데스크탑' : '';
                $support[] = $skin->supportMobile() ? '모바일' : '';
                $support = '['.implode('|', $support).']';

                yield [
                    'value' => $id,
                    'text' => $skin->getTitle().$support,
                    'selected' => $selectedSkin === null ? false : $id === $selectedSkin->getId(),
                ];
            }
        };

        $skins = $skins($skinList, $selectedSkin);

        \Frontend::js('assets/skin/section.js')->load();

        $url = route('settings.skin.section.setting');
        \Frontend::html('skin.loadSkinSetting')->content(
            "<script>
            XE.$(function($){
                $('.__xe_skinSetting').xeSkinSetting({
                    'loadUrl': '$url',
                    'saveUrl': '$url'
                });
            });
        </script>"
        )->load();

        return View::make(
            'skin.setting',
            compact('skinInstanceId', 'settingView', 'skins', 'mode', 'selectedSkin')
        );
    }
}
