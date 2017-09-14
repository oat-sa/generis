<?php

namespace oat\oatbox\TaskQueue;

/**
 * Basic Message class
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
class Message implements MessageInterface
{
    private $body;
    private $metadata = [];

    /**
     * Message constructor.
     *
     * @param null|string $body
     */
    public function __construct($body = null)
    {
        $this->setMetadata('_id_', \common_Utils::getNewUri());
        $this->setBody($body);
        $this->setOwner(\common_session_SessionManager::getSession()->getUser()->getIdentifier());
        $this->setCreatedAt(new \DateTime());
    }

    public function __toString()
    {
        return 'MID: '. $this->getId() .'; MBODY: '. $this->getBody();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->getMetadata('_id_');
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return Message
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param \DateTime $dateTime
     * @return Message
     */
    public function setCreatedAt(\DateTime $dateTime)
    {
        $this->setMetadata('_created_at_', $dateTime);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->getMetadata('_created_at_');
    }

    /**
     * @param string $owner
     * @return Message
     */
    public function setOwner($owner)
    {
        $this->setMetadata('_owner_', (string) $owner);

        return $this;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->getMetadata('_owner_');
    }

    /**
     * Set message metadata
     *
     * Non-destructive setting of message metadata; always adds to the metadata, never overwrites
     * the entire metadata container.
     *
     * @param  string|int|array|\Traversable $spec
     * @param  mixed $value
     * @throws \InvalidArgumentException
     * @return Message
     */
    public function setMetadata($spec, $value = null)
    {
        if (is_scalar($spec)) {
            $this->metadata[$spec] = $value;
            return $this;
        }

        if (!is_array($spec) && !$spec instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                'Expected a string, array, or Traversable argument in first position; received "%s"',
                (is_object($spec) ? get_class($spec) : gettype($spec))
            ));
        }

        foreach ($spec as $key => $value) {
            $this->metadata[$key] = $value;
        }

        return $this;
    }

    /**
     * Retrieve all metadata or a single metadatum as specified by key
     *
     * @param  null|string|int $key
     * @param  null|mixed $default
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function getMetadata($key = null, $default = null)
    {
        if (null === $key) {
            return $this->metadata;
        }

        if (!is_scalar($key)) {
            throw new \InvalidArgumentException('Non-scalar argument provided for key');
        }

        if (array_key_exists($key, $this->metadata)) {
            return $this->metadata[$key];
        }

        return $default;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            self::JSON_BODY_KEY => $this->getBody(),
            self::JSON_METADATA_KEY => $this->getMetadata()
        ];
    }
}