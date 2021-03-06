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
namespace Xpressengine\Member\Repositories\Database;

use Xpressengine\Database\VirtualConnectionInterface;
use Xpressengine\Keygen\Keygen;
use Xpressengine\Member\Entities\Database\GroupEntity;
use Xpressengine\Member\Entities\MemberEntityInterface;
use Xpressengine\Member\Repositories\DatabaseRepositoryTrait;
use Xpressengine\Member\Repositories\GroupRepositoryInterface;

/**
 * 회원 그룹 정보를 저장하는 Repository
 *
 * @category    Member
 * @package     Xpressengine\Member
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
class GroupRepository implements GroupRepositoryInterface
{
    use DatabaseRepositoryTrait {
        delete as traitDelete;
    }

    /**
     * GroupRepository constructor.
     *
     * @param VirtualConnectionInterface $connection db connection
     * @param Keygen                     $generator  key generator
     */
    public function __construct(VirtualConnectionInterface $connection, Keygen $generator)
    {
        $this->connection = $connection;
        $this->generator = $generator;
        $this->isDynamic = false;
        $this->mainTable = 'member_group';
        $this->entityClass = GroupEntity::class;
    }

    /**
     * 주어진 회원이 소속된 그룹 목록을 반환한다.
     *
     * @param MemberEntityInterface $member 조회할 회원
     * @param string[]|null         $with   함께 반환할 relation 정보
     *
     * @return mixed
     */
    public function fetchAllByMember(MemberEntityInterface $member, $with = null)
    {
        $id = $member->id;
        $query = $this->table('member_group')->leftJoin(
            'member_group_member as map',
            'member_group.id',
            '=',
            'map.groupId'
        )->whereIn('map.memberId', (array) $id);
        $groups = $this->getEntities($query, $with);
        return $groups;
    }

    /**
     * 주어진 entity를 database에서 삭제한다. 해당 그룹에 소속된 회원들은 그룹에서 제외시킨다.
     *
     * @param GroupEntity $entity 삭제할 entity 정보
     *
     * @return int
     */
    public function delete($entity)
    {
        $this->table('member_group_member')->where('groupId', $entity->id)->delete();
        $this->traitDelete($entity);
    }

    /**
     * 주어진 그룹에 주어진 회원을 추가한다.
     *
     * @param GroupEntity           $group  대상 그룹
     * @param MemberEntityInterface $member 추가할 회원
     *
     * @return mixed
     */
    public function addMember(GroupEntity $group, MemberEntityInterface $member)
    {
        $this->table('member_group_member')->insert(
            [
                'groupId' => $group->id,
                'memberId' => $member->id,
                'createdAt' => $this->getCurrentTime()
            ]
        );

        $this->table()->where('id', $group->id)->increment('count');
    }


    /**
     * 주어진 회원을 그룹에서 제외시킨다.
     *
     * @param GroupEntity           $group  대상 그룹
     * @param MemberEntityInterface $member 제외시킬 회원
     *
     * @return void
     */
    public function exceptMember(GroupEntity $group, MemberEntityInterface $member)
    {
        $this->table('member_group_member')->where('groupId', $group->id)->where('memberId', $member->id)->delete();
        $this->table()->where('id', $group->id)->decrement('count');
    }
}
