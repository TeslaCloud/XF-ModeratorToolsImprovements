<?php

namespace TC\MTI\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Member extends XFCP_Member
{
    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\Redirect|\XF\Mvc\Reply\View
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionAvatarDelete(ParameterBag $params)
    {
        $user = $this->assertViewableUser($params->user_id);

        if ($this->isPost())
        {
            /** @var \XF\Service\User\Avatar $avatarService */
            $avatarService = $this->service('XF:User\Avatar', $user);
            $avatarService->deleteAvatar();

            $this->app->logger()->logModeratorAction('user', $user, 'tc_mit_delete_avatar');

            return $this->redirect($this->buildLink('members', $user));
        }

        return $this->view('XF:Member\Avatar\Delete', 'tc_mti_member_avatar_delete', [
            'user' => $user
        ]);
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\Message|\XF\Mvc\Reply\Redirect|\XF\Mvc\Reply\View
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionDiscourage(ParameterBag $params)
    {
        $user = $this->assertViewableUser($params->user_id);

        if (\XF::visitor()->user_id == $user->user_id)
        {
            return $this->message(\XF::phrase('tc_mti_you_cannot_discourage_yourself'));
        }

        if ($this->isPost())
        {
            $newState = !$user->Option->is_discouraged;

            $user->Option->fastUpdate('is_discouraged', $newState);

            $this->app->logger()->logModeratorAction(
                'user', $user,
                $newState ? 'tc_mit_user_discourage' : 'tc_mit_user_undiscourage'
            );

            return $this->redirect($this->buildLink('members', $user));
        }

        return $this->view('XF:Member\Discourage', 'tc_mti_member_discourage', [
            'user' => $user
        ]);
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\Redirect|\XF\Mvc\Reply\View
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionBannerDelete(ParameterBag $params)
    {
        $user = $this->assertViewableUser($params->user_id);

        if ($this->isPost())
        {
            /** @var \XF\Service\User\ProfileBanner $bannerService */
            $bannerService = $this->service('XF:User\ProfileBanner', $user);
            $bannerService->deleteBanner();

            $this->app->logger()->logModeratorAction('user', $user, 'tc_mit_delete_banner');

            return $this->redirect($this->buildLink('members', $user));
        }

        return $this->view('XF:Member\Banner\Delete', 'tc_mti_member_banner_delete', [
            'user' => $user
        ]);
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\Redirect|\XF\Mvc\Reply\View
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionReactionDelete(ParameterBag $params)
    {
        $user = $this->assertViewableUser($params->user_id);

        if ($this->isPost())
        {
            $input = $this->filter([
                'start_date' => 'str',
                'start_time' => 'str',
                'end_date' => 'str',
                'end_time' => 'str'
            ]);

            $startDate = date_parse($input['start_date']);
            $startTime = date_parse($input['start_time']);
            $endDate = date_parse($input['end_date']);
            $endTime = date_parse($input['end_time']);

            $betweenDate = (new \DateTime())->setTimezone(new \DateTimeZone(\XF::visitor()->timezone))
                ->setDate($startDate['year'], $startDate['month'], $startDate['day'])
                ->setTime($startTime['hour'], $startTime['minute'])
                ->getTimestamp();

            $andTime = (new \DateTime())->setTimezone(new \DateTimeZone(\XF::visitor()->timezone))
                ->setDate($endDate['year'], $endDate['month'], $endDate['day'])
                ->setTime($endTime['hour'], $endTime['minute'])
                ->getTimestamp();

            $reactionContentFinder = $this->finder('XF:ReactionContent');
            $reactions = $reactionContentFinder->where('reaction_date', 'BETWEEN', [$betweenDate, $andTime])->fetch();

            foreach ($reactions as $reaction)
            {
                $reaction->delete();
            }

            $this->app->logger()->logModeratorAction('user', $user, 'tc_mit_reactions_delete', [
                'reactions' => implode(', ', $reactions->keys())
            ]);

            return $this->redirect($this->buildLink('members', $user));
        }

        return $this->view('XF:Members\Reaction\Delete', 'tc_mti_member_reaction_delete', [
            'user' => $user
        ]);
    }
}