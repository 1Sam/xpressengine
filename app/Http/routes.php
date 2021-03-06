<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/locale/{locale}', 'LangController@setLocale');

Route::settings(
    '/',
    function () {
        Route::get(
            '/',
            [
                'as' => 'settings',
                'uses' => function () {
                    return redirect()->route('settings.dashboard');
                }
            ]
        );
    }
);
Route::settings(
    'dashboard',
    function () {
        Route::get(
            '/',
            ['as' => 'settings.dashboard', 'uses' => 'DashboardController@index', 'settings_menu' => ['dashboard']]

        );
    }
);

Route::settings(
    'lang',
    function () {
        Route::get('lines/{key}', ['as' => 'manage.lang.lines.key', 'uses' => 'LangController@getLinesWithKey']);
        Route::get('search/{locale}', ['as' => 'manage.lang.search', 'uses' => 'LangController@searchKeyword']);
        Route::put('save', ['as' => 'manage.lang.save', 'uses' => 'LangController@save']);
        Route::get(
            '{namespace?}/{keyword?}',
            [
                'as' => 'manage.lang.index',
                'uses' => 'LangController@index',
                'settings_menu' => ['lang.default']
            ]
        );
    }
);

/* member */

/*
 * member/auth
 * */
Route::group(
    ['prefix' => 'auth'],
    function () {
        // login
        Route::get('login', ['as' => 'login', 'uses' => 'Auth\AuthController@getLogin']);
        Route::post('login', ['as' => 'login', 'uses' => 'Auth\AuthController@postLogin']);

        // register
        Route::get('register', ['as' => 'auth.register', 'uses' => 'Auth\AuthController@getRegister']);
        Route::post('register', ['as' => 'auth.register', 'uses' => 'Auth\AuthController@postRegister']);

        // email confirm
        Route::get('confirm', ['as' => 'auth.confirm', 'uses' => 'Auth\AuthController@getConfirm']);

        // logout
        Route::get('logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@getLogout']);

        // password reset request
        Route::get('reset', ['as' => 'auth.reset', 'uses' => 'Auth\PasswordController@getReset']);
        Route::post('reset', ['as' => 'auth.reset', 'uses' => 'Auth\PasswordController@postReset']);

        // password reset
        Route::get('password', ['as' => 'auth.password', 'uses' => 'Auth\PasswordController@getPassword']);
        Route::post('password', ['as' => 'auth.password', 'uses' => 'Auth\PasswordController@postPassword']);
    }
);

/*
 * member/profile
 * */
Route::group(
    ['prefix' => '@{member}'],
    function () {
        // profile
        Route::get('/', ['as' => 'member.profile', 'uses' => 'Member\ProfileController@index']);
        Route::post('/', ['as' => 'member.profile.update', 'uses' => 'Member\ProfileController@update']);
    }
);

/*
 * member settings
 * */
Route::group(
    ['prefix' => 'member'],
    function () {

        Route::get('/{section?}', ['as' => 'member.settings', 'uses' => 'Member\MemberController@show']);

        // settings secton
        Route::group(
            ['prefix' => 'settings'],
            function () {
                Route::group(
                    ['prefix' => 'name'],
                    function () {
                        Route::post(
                        '/',
                            [
                                'as' => 'member.settings.name.update',
                                'uses' => 'Member\MemberController@updateDisplayName'
                            ]
                        );

                        // check name exists
                        Route::post(
                            'check',
                            [
                                'as' => 'member.settings.name.check',
                                'uses' => 'Member\MemberController@validateDisplayName'
                            ]
                        );
                    }
                );

                Route::group(
                    ['prefix' => 'password'],
                    function () {
                        Route::post(
                            '/',
                            [
                                'as' => 'member.settings.password.update',
                                'uses' => 'Member\MemberController@updatePassword'
                            ]
                        );
                        // check password is valid
                        Route::post(
                            'check',
                            [
                                'as' => 'member.settings.password.check',
                                'uses' => 'Member\MemberController@validatePassword'
                            ]
                        );
                    }
                );

                // mail action at edit
                Route::group(
                    ['prefix' => 'mail'],
                    function () {
                        Route::get(
                            'list',
                            ['as' => 'member.settings.mail.list', 'uses' => 'Member\MemberController@getMailList']
                        );
                        Route::post(
                            'add',
                            ['as' => 'member.settings.mail.add', 'uses' => 'Member\MemberController@addMail']
                        );
                        Route::post(
                            'update',
                            ['as' => 'member.settings.mail.update', 'uses' => 'Member\MemberController@updateMainMail']
                        );
                        Route::post(
                            'confirm',
                            ['as' => 'member.settings.mail.confirm', 'uses' => 'Member\MemberController@confirmMail']
                        );
                        Route::post(
                            'delete',
                            ['as' => 'member.settings.mail.delete', 'uses' => 'Member\MemberController@deleteMail']
                        );
                    }
                );

                Route::group(
                    ['prefix' => 'pending_mail'],
                    function () {
                        Route::post(
                            'delete',
                            [
                                'as' => 'member.settings.pending_mail.delete',
                                'uses' => 'Member\MemberController@deletePendingMail'
                            ]
                        );
                        Route::post(
                            'resend',
                            [
                                'as' => 'member.settings.pending_mail.resend',
                                'uses' => 'Member\MemberController@resendPendingMail'
                            ]
                        );
                    }
                );

                Route::group(
                    ['prefix' => 'leave'],
                    function () {
                        Route::post(
                            '/',
                            [
                                'as' => 'member.settings.leave',
                                'uses' => 'Member\MemberController@leave'
                            ]
                        );
                    }
                );
            }
        );

    }
);

/*
 * member/manage
 * */
Route::settings(
    'member',
    function () {

        Route::get(
            'searchMember/{keyword?}',
            ['as' => 'settings.member.search', 'uses' => 'Member\Settings\MemberController@searchMember']
        );
        // index
        Route::get(
            '/',
            [
                'as' => 'settings.member.index',
                'uses' => 'Member\Settings\MemberController@index',
                'settings_menu' => 'member.list',
                'permission' => 'member.list'
            ]
        );

        // create
        Route::get('create',
                   [
                       'as' => 'settings.member.create',
                       'uses' => 'Member\Settings\MemberController@create',
                       'settings_menu' => 'member.create'
                   ]
        );
        Route::post(
            'store',
            ['as' => 'settings.member.store', 'uses' => 'Member\Settings\MemberController@store']
        );

        Route::get(
            '{id}/edit',
            [
                'as' => 'settings.member.edit',
                'uses' => 'Member\Settings\MemberController@edit',
                'settings_menu' => 'member.edit',
                'permission' => 'member.edit',

            ]
        )->where('id', '[0-9a-z\-]+');

        Route::post('{id}/edit', ['as' => 'settings.member.edit', 'uses' => 'Member\Settings\MemberController@update'])
            ->where('id', '[0-9a-z\-]+');

        // mail action at edit
        Route::get(
            'mail/list',
            ['as' => 'settings.member.mail.list', 'uses' => 'Member\Settings\MemberController@getMailList']
        );
        Route::post(
            'mail/add',
            ['as' => 'settings.member.mail.add', 'uses' => 'Member\Settings\MemberController@postAddMail']
        );
        Route::post(
            'mail/delete',
            ['as' => 'settings.member.mail.delete', 'uses' => 'Member\Settings\MemberController@postDeleteMail']
        );
        Route::post(
            'mail/confirm',
            ['as' => 'settings.member.mail.confirm', 'uses' => 'Member\Settings\MemberController@postConfirmMail']
        );

        // delete
        Route::delete(
            'destroy',
            ['as' => 'settings.member.destroy', 'uses' => 'Member\Settings\MemberController@deleteMember']
        );

        // setting
        Route::group(
            ['prefix' => 'setting'],
            function () {

                Route::get(
                    '/',
                    [
                        'as' => 'settings.member.setting',
                        'uses' => 'Member\Settings\SettingController@getCommonSetting',
                        'settings_menu' => 'member.setting.default',
                        'permission' => 'member.setting'
                    ]
                );
                Route::post(
                    '/',
                    ['as' => 'settings.member.setting', 'uses' => 'Member\Settings\SettingController@postCommonSetting']
                );

                Route::get(
                    'join',
                    [
                        'as' => 'settings.member.setting.join',
                        'uses' => 'Member\Settings\SettingController@getJoinSetting',
                        'settings_menu' => 'member.setting.join',
                        'permission' => 'member.setting'
                    ]
                );

                Route::post(
                    'join',
                    [
                        'as' => 'settings.member.setting.join',
                        'uses' => 'Member\Settings\SettingController@postJoinSetting'
                    ]
                );

                Route::get(
                    'skin',
                    [
                        'as' => 'settings.member.setting.skin',
                        'uses' => 'Member\Settings\SettingController@getSkinSetting',
                        'settings_menu' => 'member.setting.skin',
                        'permission' => 'member.setting'
                    ]
                );

                Route::get(
                    'field',
                    [
                        'as' => 'settings.member.setting.field',
                        'uses' => 'Member\Settings\SettingController@getFieldSetting',
                        'settings_menu' => 'member.setting.field',
                        'permission' => 'member.setting'
                    ]
                );

                Route::get(
                    'togglemenu',
                    [
                        'as' => 'settings.member.setting.togglemenu',
                        'uses' => 'Member\Settings\SettingController@getToggleMenuSetting',
                        'permission' => 'member.setting'
                    ]
                );

                Route::post(
                    'togglemenu',
                    [
                        'as' => 'settings.member.setting.togglemenu',
                        'uses' => 'Member\Settings\SettingController@postToggleMenuSetting'
                    ]
                );
            }
        );
    }
);

/*
 * member group
 * */
Route::settings(
    'group',
    function () {

        Route::get(
            'searchGroup/{keyword?}',
            ['as' => 'manage.group.search', 'uses' => 'Member\Settings\GroupController@searchGroup']
        );

        // list
        Route::get(
            '/',
            [
                'as' => 'manage.group.index',
                'uses' => 'Member\Settings\GroupController@index',
                'settings_menu' => ['member.group']
            ]
        );

        // create
        Route::get('create', ['as' => 'manage.group.create', 'uses' => 'Member\Settings\GroupController@getCreate']);
        Route::post('create', ['as' => 'manage.group.create', 'uses' => 'Member\Settings\GroupController@postCreate']);

        // edit
        Route::get(
            '{id}/edit',
            [
                'as' => 'manage.group.edit',
                'uses' => 'Member\Settings\GroupController@getEdit',
            ]
        )->where('id', '[0-9a-z\-]+');
        Route::post('{id}/edit', ['as' => 'manage.group.edit', 'uses' => 'Member\Settings\GroupController@postEdit'])
            ->where('id', '[0-9a-z\-]+');

        // delete
        Route::delete(
            'destroy',
            ['as' => 'manage.group.destroy', 'uses' => 'Member\Settings\GroupController@deleteGroup']
        );
    }
);

/* setting */
Route::settings(
    'setting',
    function () {
        Route::get(
            '/',
            [
                'as' => 'settings.setting.edit',
                'uses' => 'SettingsController@editSetting',
                'settings_menu' => ['setting.default']
            ]
        );
        Route::post('store', ['as' => 'settings.setting.update', 'uses' => 'SettingsController@updateSetting']);

        Route::get(
            'permissions',
            [
                'as' => 'settings.setting.permissions',
                'uses' => 'SettingsController@editPermissions',
                'settings_menu' => ['setting.permission']
            ]
        );

        Route::post(
            'permissions/{permissionId}',
            [
                'as' => 'settings.setting.update.permission',
                'uses' => 'SettingsController@updatePermission'
            ]
        );
    }
);

Route::settings(
    'menu',
    function () {

        Route::get(
            '/',
            [
                'as' => 'settings.menu.index',
                'uses' => 'MenuController@index',
                'settings_menu' => ['sitemap.default'],
            ]
        );

        // ajax 로 전체 menu list 가져오기
        Route::get('list', ['as' => 'settings.menu.list', 'uses' => 'MenuController@menuList']);

        // ajax 로 move Item
        Route::put('moveItem', ['as' => 'settings.menu.move.item', 'uses' => 'MenuController@moveItem']);

        // ajax 로 home 으로 지정
        Route::put('setHome', ['as' => 'settings.menu.setHome.item', 'uses' => 'MenuController@setHome']);


        Route::get('menus', ['as' => 'settings.menu.create.menu', 'uses' => 'MenuController@create']);
        Route::post('menus', ['as' => 'settings.menu.store.menu', 'uses' => 'MenuController@store']);

        Route::get('menus/{menuId}', ['as' => 'settings.menu.edit.menu', 'uses' => 'MenuController@edit']);
        Route::put('menus/{menuId}', ['as' => 'settings.menu.update.menu', 'uses' => 'MenuController@update']);

        Route::get('menus/{menuId}/permit', ['as' => 'settings.menu.permit.menu', 'uses' => 'MenuController@permit']);
        Route::delete('menus/{menuId}', ['as' => 'settings.menu.delete.menu', 'uses' => 'MenuController@destroy']);

        Route::get(
            'menus/{menuId}/permission',
            ['as' => 'settings.menu.edit.permission.menu', 'uses' => 'MenuController@editMenuPermission']
        );
        Route::put(
            'menus/{menuId}/permission',
            ['as' => 'settings.menu.update.permission.menu', 'uses' => 'MenuController@updateMenuPermission']
        );

        Route::get(
            'menus/{menuId}/types',
            ['as' => 'settings.menu.select.types', 'uses' => 'MenuController@selectType']
        );
        Route::get(
            'menus/{menuId}/items',
            ['as' => 'settings.menu.create.item', 'uses' => 'MenuController@createItem']
        );
        Route::post(
            'menus/{menuId}/items',
            ['as' => 'settings.menu.store.item', 'uses' => 'MenuController@storeItem']
        );

        Route::get(
            'menus/{menuId}/items/{itemId}',
            ['as' => 'settings.menu.edit.item', 'uses' => 'MenuController@editItem']
        );
        Route::put(
            'menus/{menuId}/items/{itemId}',
            ['as' => 'settings.menu.update.item', 'uses' => 'MenuController@updateItem']
        );

        Route::get(
            'menus/{menuId}/items/{itemId}/permit',
            ['as' => 'settings.menu.permit.item', 'uses' => 'MenuController@permitItem']
        );
        Route::delete(
            'menus/{menuId}/items/{itemId}',
            ['as' => 'settings.menu.delete.item', 'uses' => 'MenuController@destroyItem']
        );

        Route::get(
            'menus/{menuId}/items/{itemId}/permission',
            ['as' => 'settings.menu.edit.permission.item', 'uses' => 'MenuController@editItemPermission']
        );
        Route::put(
            'menus/{menuId}/items/{itemId}/permission',
            ['as' => 'settings.menu.update.permission.item', 'uses' => 'MenuController@updateItemPermission']
        );
    }
);

/* theme package */
Route::settings(
    'theme',
    function () {
        Route::get('edit', ['as' => 'settings.theme.edit', 'uses' => 'ThemeController@getEdit']);
        Route::post('edit', ['as' => 'settings.theme.edit', 'uses' => 'ThemeController@postEdit']);
    }
);

/* plugin package */
Route::settings(
    'plugins',
    function () {
        Route::get(
            '/',
            [
                'as' => 'settings.plugins',
                'uses' => 'PluginController@index',
                'settings_menu' => ['plugin.list']
            ]
        );

        Route::get(
            '{pluginId?}',
            [
                'as' => 'settings.plugins.show',
                'uses' => 'PluginController@show',
                'settings_menu' => ['plugin.list.detail']
            ]
        );

        Route::post(
            '{pluginId}/activate',
            [
                'as' => 'settings.plugins.activate',
                'uses' => 'PluginController@postActivatePlugin'
            ]
        );
        Route::post(
            '{pluginId}/deactivate',
            [
                'as' => 'settings.plugins.deactivate',
                'uses' => 'PluginController@postDeactivatePlugin'
            ]
        );
    }
);

Route::settings('category', function () {

    // 이하 신규
    Route::group(['prefix' => '{id}', 'where' => ['id' => '[0-9]+']], function () {
        Route::get('/', ['as' => 'manage.category.show', 'uses' => 'CategoryController@show']);
        Route::post('item/store', [
            'as' => 'manage.category.edit.item.store',
            'uses' => 'CategoryController@storeItem'
        ]);
        Route::post('item/update', [
            'as' => 'manage.category.edit.item.update',
            'uses' => 'CategoryController@updateItem'
        ]);
        Route::post('item/destroy', [
            'as' => 'manage.category.edit.item.destroy',
            'uses' => 'CategoryController@destroyItem'
        ]);
        Route::post('item/move', [
            'as' => 'manage.category.edit.item.move',
            'uses' => 'CategoryController@moveItem'
        ]);
        Route::get('item/roots', [
            'as' => 'manage.category.edit.item.roots',
            'uses' => 'CategoryController@roots'
        ]);
        Route::get('item/children', [
            'as' => 'manage.category.edit.item.children',
            'uses' => 'CategoryController@children'
        ]);
    });

});

Route::group(['prefix' => 'tag'], function () {
    Route::get('autoComplete', ['as' => 'tag.autoComplete', 'uses' => 'TagController@autoComplete']);
});

Route::get('file/{id}', ['as' => 'file.path', 'uses' => 'StorageController@file'])->where('id', '[0-9a-z\-]+');
Route::settings('storage', function () {
    Route::get('/', ['as' => 'manage.storage.index', 'uses' => 'StorageController@index']);
    Route::post('destroy', ['as' => 'manage.storage.destroy', 'uses' => 'StorageController@destroy']);
});

Route::settings('dynamicField', function () {
    Route::get('/', ['as' => 'manage.dynamicField.index', 'uses' => 'DynamicFieldController@index']);
    Route::get('getSkinOption', ['as' => 'manage.dynamicField.getSkinOption', 'uses' => 'DynamicFieldController@getSkinOption']);
    Route::get('getAdditionalConfigure', ['as' => 'manage.dynamicField.getAdditionalConfigure', 'uses' => 'DynamicFieldController@getAdditionalConfigure']);
    Route::post('store', ['as' => 'manage.dynamicField.store', 'uses' => 'DynamicFieldController@store']);
    Route::get('getEditInfo', ['as' => 'manage.dynamicField.getEditInfo', 'uses' => 'DynamicFieldController@getEditInfo']);
    Route::post('update', ['as' => 'manage.dynamicField.update', 'uses' => 'DynamicFieldController@update']);
    Route::post('destroy', ['as' => 'manage.dynamicField.destroy', 'uses' => 'DynamicFieldController@destroy']);
});

Route::group(['prefix' => 'fieldType'], function () {
    Route::post('/storeCategory', ['as' => 'fieldType.storeCategory', 'uses' => 'FieldTypeController@storeCategory']);
    Route::get('/storeCategory', ['as' => 'fieldType.storeCategory', 'uses' => 'FieldTypeController@storeCategory']);
});

Route::group(['prefix' => 'temporary'], function () {
    Route::get('/', ['as' => 'temporary.index', 'uses' => 'TemporaryController@index']);
    Route::post('store', ['as' => 'temporary.store', 'uses' => 'TemporaryController@store']);
    Route::post('update/{temporaryId}', ['as' => 'temporary.update', 'uses' => 'TemporaryController@update'])
        ->where('temporaryId', '[0-9a-z\-]+');
    Route::post('destroy/{temporaryId}', ['as' => 'temporary.destroy', 'uses' => 'TemporaryController@destroy'])
        ->where('temporaryId', '[0-9a-z\-]+');

    Route::post('setAuto', ['as' => 'temporary.setAuto', 'uses' => 'TemporaryController@setAuto']);
    Route::post('destroyAuto', ['as' => 'temporary.destroyAuto', 'uses' => 'TemporaryController@destroyAuto']);
});

Route::settings('widget', function () {
    Route::get('list', ['as' => 'manage.widget.list', 'uses' => 'WidgetController@index']);
    Route::get('setup', ['as' => 'manage.widget.setup', 'uses' => 'WidgetController@setup']);
    Route::get('render', ['as' => 'manage.widget.render', 'uses' => 'WidgetController@render']);
    Route::get('generate', ['as' => 'manage.widget.generate', 'uses' => 'WidgetController@generate']);

});

Route::fixed('toggleMenu', function () {
    Route::get('/', ['as' => 'fixed.toggleMenu', 'uses' => 'ToggleMenuController@get']);
});

Route::settings('toggleMenu', function () {
    Route::post('setting', ['as' => 'manage.toggleMenu.setting', 'uses' => 'ToggleMenuController@postSetting']);
});

Route::settings('trash', function () {
    Route::get('/', ['as' => 'manage.trash.index', 'uses' => 'TrashController@index']);
    Route::post('/clean', ['as' => 'manage.trash.clean', 'uses' => 'TrashController@clean']);
});

/* skin package */
Route::settings(
    'skin',
    function () {
        Route::get('/section', ['as' => 'settings.skin.section.setting', 'uses' => 'SkinController@getSetting']);
        Route::post('/section', ['as' => 'settings.skin.section.setting', 'uses' => 'SkinController@postSetting']);
    }
);

Route::settings(
    'seo',
    function () {
        Route::get(
            'setting',
            [
                'as' => 'manage.seo.edit',
                'uses' => 'SeoController@getSetting',
                'settings_menu' => ['setting.seo'],
            ]
        );
        Route::post(
            'setting',
            [
                'as' => 'manage.seo.update',
                'uses' => 'SeoController@postSetting'
            ]
        );
    }
);
