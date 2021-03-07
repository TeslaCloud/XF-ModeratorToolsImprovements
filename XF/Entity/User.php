<?php

namespace TC\MTI\XF\Entity;

class User extends XFCP_User
{
    public function canDeleteAvatar(): bool
    {
        return $this->is_moderator && $this->hasPermission('general', 'deleteAvatar');
    }

    public function canDeleteBanner(): bool
    {
        return $this->is_moderator && $this->hasPermission('general', 'deleteBanner');
    }

    public function canDiscourage(): bool
    {
        return $this->is_moderator && $this->hasPermission('general', 'discourageUser');
    }

    public function canDeleteUserReaction(): bool
    {
        return $this->is_moderator && $this->hasPermission('general', 'deleteUserReaction');
    }

    public function canTcInlineModReport() : bool
    {
        return \XF::visitor()->is_moderator && \XF::visitor()->hasPermission('forum', 'tcInlineModReport');
    }
}