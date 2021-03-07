<?php

namespace TC\MTI\XF\Entity;

class User extends XFCP_User
{
    public function canDeleteAvatar()
    {
        return $this->is_moderator && $this->hasPermission('general', 'deleteAvatar');
    }

    public function canDeleteBanner()
    {
        return $this->is_moderator && $this->hasPermission('general', 'deleteBanner');
    }

    public function canDiscourage()
    {
        return $this->is_moderator && $this->hasPermission('general', 'discourageUser');
    }

    public function canDeleteUserReaction()
    {
        return $this->is_moderator && $this->hasPermission('general', 'deleteUserReaction');
    }

    public function canTcInlineModReport()
    {
        return \XF::visitor()->is_moderator && \XF::visitor()->hasPermission('forum', 'tcInlineModReport');
    }
}