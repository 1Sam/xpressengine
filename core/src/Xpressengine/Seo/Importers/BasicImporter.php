<?php
/**
 * This file is basic importer.
 *
 * PHP version 5
 *
 * @category    Seo
 * @package     Xpressengine\Seo
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
namespace Xpressengine\Seo\Importers;

/**
 * BasicImporter class
 *
 * @category    Seo
 * @package     Xpressengine\Seo
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
class BasicImporter extends AbstractImporter
{
    /**
     * meta items
     *
     * @var array
     */
    protected $metaItems = [
        'keywords' => 'keywords',
        'description' => 'description',
    ];

    /**
     * will be cut items name and length
     *
     * @var array
     */
    protected $cuts = [
        'description' => 100
    ];

    /**
     * Execute import job
     *
     * @param array $data data array
     * @return void
     */
    public function exec(array $data)
    {
        $url = $this->extractUrl($data);

        $this->frontend->html('canonical')->content($this->makeCanonical($url))->appendTo('head')->load();

        parent::exec($data);
    }

    /**
     * Make canonical tag
     *
     * @param string $url url path
     * @return string
     */
    protected function makeCanonical($url)
    {
        return '<link rel="canonical" href="' . $this->prependHost($url) . '" />';
    }
}
