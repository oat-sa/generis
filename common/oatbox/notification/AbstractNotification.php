<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\oatbox\notification;


abstract class AbstractNotification implements NotificationInterface, \JsonSerializable
{

    protected $id;

    protected $status;

    protected $recipient;

    protected $sender;

    protected $senderName;

    protected $title;

    protected $message;

    protected $createdAt;

    protected $updatedAt;

    public function __construct($userId , $title , $message , $senderId , $senderName  , $id = null, $createdAt = null , $updatedAt = null,  $status = 0)
    {
        $this->id         = $id;
        $this->status     = $status;
        $this->recipient  = $userId;
        $this->sender     = $senderId;
        $this->senderName = $senderName;
        $this->title      = $title;
        $this->message    = $message;
        $this->createdAt  = $createdAt;
        $this->updatedAt  = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @return string
     */
    public function getSenderId()
    {
        return $this->sender;
    }

    /**
     * @return string
     */
    public function getSenderName()
    {
        return $this->sender;
    }

    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null
     */
    public function getCreatedAt()
    {
        return strtotime($this->createdAt);
    }

    /**
     * @return null
     */
    public function getUpdatedAt()
    {
        return strtotime($this->updatedAt);
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->updatedAt = date('Y-m-d H:i:s');
        return $this;
    }

    public function setId($id)
    {
        if(is_null($this->id)) {
            $this->id = $id;
        }
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function jsonSerialize()
    {
        return
            [
                'id'         => $this->getId(),
                'status'     => $this->getStatus(),
                'recipient'  => $this->getRecipient(),
                'sender'     => $this->getSenderId(),
                'senderName' => $this->getSenderId(),
                'title'      => $this->getTitle(),
                'message'    => $this->getMessage(),
                'createdAt'  => $this->getCreatedAt(),
                'updatedAt'  => $this->getUpdatedAt(),
            ];
    }

}