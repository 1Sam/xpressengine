<?php
/**
 *  This file is part of the Xpressengine package.
 *
 * PHP version 5
 *
 * @category    Member
 * @package     Xpressengine\Member
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
namespace Xpressengine\Member\Repositories;

use Closure;
use Illuminate\Support\Collection;
use Xpressengine\Member\Entities\MemberEntityInterface;
use Xpressengine\Member\Entities\VirtualGroupEntity;

/**
 * 가상 그룹 정보를 저장하는 Repository
 *
 * @category    Member
 * @package     Xpressengine\Member\Repositories
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
class VirtualGroupRepository implements VirtualGroupRepositoryInterface
{

    /**
     * @var array 가상그룹 리스트
     */
    protected $entities;

    /**
     * @var MemberRepositoryInterface Member Repository
     */
    private $memberRepo;

    /**
     * @var Closure
     */
    private $getter;

    /**
     * VirtualGroupRepository constructor.
     *
     * @param MemberRepositoryInterface $memberRepo  member repository
     * @param array                     $entityInfos list of virtual group infos
     * @param Closure                   $getter      Closure for retrieve virtual group list by member id
     */
    public function __construct(MemberRepositoryInterface $memberRepo, array $entityInfos, Closure $getter)
    {
        $this->memberRepo = $memberRepo;

        $this->entities = [];
        /** @var array $entityInfo */
        foreach ($entityInfos as $id => $entityInfo) {
            $this->entities[$id] = $this->resolveEntity($id, $entityInfo);
        }

        $this->getter = $getter;
    }

    /**
     * 주어진 id에 해당하는 가상그룹 정보를 반환한다.
     *
     * @param string $id 조회할 가상그룹 id
     *
     * @return VirtualGroupEntity
     */
    public function find($id)
    {
        return $this->entities->get($id);
    }

    /**
     * 가상그룹 이름으로 가상그룹을 조회한다.
     *
     * @param string $title 가상그룹 이름
     *
     * @return VirtualGroupEntity|null
     */
    public function findByTitle($title)
    {
        foreach ($this->entities as $entity) {
            if ($entity->title == $title) {
                return $entity;
            }
        }
    }

    /**
     * 회원이 소속된 가상그룹 목록을 조회한다.
     *
     * @param string $memberId 회원아이디
     *
     * @return array
     */
    public function fetchAllByMember($memberId)
    {
        $getByMember = $this->getter;
        $member = $this->memberRepo->find($memberId, ['groups', 'accounts', 'mails', 'pending_mails']);

        $groupIds = $getByMember($member);

        return array_only($this->entities, $groupIds);
    }
    ///**
    // * 가상 그룹 목록을 조회한다. pagination된 목록을 반환한다.
    // *
    // * @return Collection
    // */
    //public function paginate($page = 1, $perPage = 10)
    //{
    //    return $this->entities->forPage($page, $perPage);

    //}

    /**
     * 모든 가상그룹 목록을 반환한다.
     *
     * @return Collection
     */
    public function all()
    {
        return $this->entities;
    }

    /**
     * 주어진 id를 가진 가상 그룹이 있는지의 여부를 반환한다.
     *
     * @param string $id 조회할 가상그룹 id
     *
     * @return bool
     */
    public function has($id)
    {
        return isset($this->entities[$id]);
    }

    /**
     * 주어진 가상그룹 정보로 가상그룹 Entity를 생성하여 반환한다.
     *
     * @param string $id         생성할  가상 그룹 id
     * @param array  $entityInfo 생성할 가상그룹 정보
     *
     * @return VirtualGroupEntity
     */
    private function resolveEntity($id, $entityInfo)
    {
        //return new VirtualGroupEntity($this->memberRepo, $id, $entityInfo);
        $entityInfo['id'] = $id;
        return new VirtualGroupEntity($entityInfo);
    }
}
