<?php

namespace App\Domain;

use App\HttpCommunication\Ymdy\MemberInterface as MemberHttpCommunication;

class Member implements MemberInterface
{
    const BATCH_SPLIT_COUNT = 50;

    /**
     * @var MemberHttpCommunication
     */
    private $memberHttpCommunication;

    /**
     * @param MemberHttpCommunication $memberHttpCommunication
     */
    public function __construct(MemberHttpCommunication $memberHttpCommunication)
    {
        $this->memberHttpCommunication = $memberHttpCommunication;
    }

    /**
     * 会員検索
     *
     * @param array $params
     * @param array $applyingPertialParams (default: ['tel', 'name', 'email'])
     *
     * @return array
     */
    public function search(array $params, $applyingPertialParams = ['tel', 'name', 'email'])
    {
        foreach ($applyingPertialParams as $name) {
            if (isset($params[$name])) {
                $params["{$name}_partial"] = \App\Enums\Common\Boolean::IsTrue;
            }
        }

        $data = $this->memberHttpCommunication->indexMember($params)->getBody();

        return $data['members'] ?? [];
    }

    /**
     * 複数会員IDを指定してをまとめて取得する（外部サーバー負荷対策）
     *
     * @param array|Collection $memberIds
     * @param array $params
     * @param array $applyingPertialParams (default: ['tel', 'name', 'email'])
     *
     * @return array
     */
    public function fetchBatchMembers($memberIds, array $conditions = [], array $applyingPertialParams = ['tel', 'name', 'email'])
    {
        $memberDict = [];
        $targets = [];

        $loadMemberDict = function (&$memberDict, $targets, $conditions, $applyingPertialParams) {
            $members = $this->search(array_merge(['member_id' => $targets], $conditions), $applyingPertialParams);

            foreach ($members as $member) {
                $memberDict[$member['id']] = $member;
            }
        };

        foreach ($memberIds as $memberId) {
            if (isset($memberDict[$memberId])) {
                continue;
            }

            $targets[] = $memberId;

            if (count($targets) < self::BATCH_SPLIT_COUNT) {
                continue;
            }

            $loadMemberDict($memberDict, $targets, $conditions, $applyingPertialParams);

            $targets = [];
        }

        if ($targets > 0) {
            $loadMemberDict($memberDict, $targets, $conditions, $applyingPertialParams);
        }

        return array_values($memberDict);
    }

    /**
     * メンバートークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setMemberToken(string $token)
    {
        $this->memberHttpCommunication->setMemberTokenHeader($token);

        return $this;
    }

    /**
     * スタッフトークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setStaffToken(string $token)
    {
        $this->memberHttpCommunication->setStaffToken($token);

        return $this;
    }

    /**
     * 会員詳細
     *
     * @param string $memberId
     *
     * @return array
     */
    public function fetchOne($memberId)
    {
        $data = $this->memberHttpCommunication->showMember($memberId)->getBody();

        return $data['member'] ?? null;
    }
}
