<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Traits;

use Cstea\ApiBundle\Security\OutputScope;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Trait SerializerAware
 * Provides a class the capability of serializing and de-serializing entities.
 * @package Cstea\ApiBundle\Traits
 */
trait SerializerAware
{
    /** @var SerializerInterface */
    private $serializer;
    
    /**
     * Setter injection method for the SerializerInterface.
     *
     * @required
     * @param SerializerInterface $serializer Serializer.
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    /**
     * Allows for making changes to the json prior to serialization.
     *
     * @param string $json JSON string to serialize.
     * @return string
     */
    protected function beforeDeserialize(string $json): string
    {
        return $json;
    }

    /**
     * Load json data into an Entity.
     *
     * @param string  $json   String to deserialize.
     * @param \object $entity Entity object to inject data into.
     */
    public function deserialize(string $json, object $entity): void
    {
        $converted = \json_decode($json, true);

        if (!$json || \json_last_error() !== \JSON_ERROR_NONE || !$converted) {
            throw new \InvalidArgumentException('Invalid object');
        }
        
        $json = $this->beforeDeserialize($json);
        try {
            $this->serializer->deserialize(
                $json,
                \get_class($entity),
                'json',
                ['object_to_populate' => $entity]
            );
        } catch (\Symfony\Component\Serializer\Exception\NotNormalizableValueException $exception) {
            if (\method_exists($this, 'getLogger')) {
                $this->getLogger()->error('Serialization error', ['exception' => $exception]);
            }
            throw new \InvalidArgumentException('Cannot serialize', 0, $exception);
        }
    }

    /**
     * Allows for making changes to the entity before it gets serialized.
     *
     * @param object $entity Entity to serialize.
     * @return object Modified object.
     */
    protected function beforeSerialize(object $entity): object
    {
        return $entity;
    }

    /**
     * Finds all of the timestamps in an entity and converts them to a specific timezone.
     *
     * @param \object       $entity   Entity to normalize.
     * @param \DateTimeZone $timeZone Timezone to convert to.
     * @return \object
     */
    protected function normalizeTimestamps(object $entity, \DateTimeZone $timeZone): object
    {
        try {
            $reflectionClass = new \ReflectionClass(\get_class($entity));

            foreach ($reflectionClass->getProperties() as $property) {
                $property->setAccessible(true);
                $value = $property->getValue($entity);
                if ($value instanceof \DateTime || $value instanceof \DateTimeImmutable) {
                    $property->setValue($entity, $value->setTimezone($timeZone));
                }
                $property->setAccessible(false);
            }
        } catch (\ReflectionException $e) {
        }
        
        return $entity;
    }

    /**
     * Serializes an entity object into a json string.
     *
     * @param \object     $entity Entity.
     * @param OutputScope $scope  Output scope.
     * @return string
     */
    public function toJson(object $entity, OutputScope $scope): string
    {
        $entity = $this->beforeSerialize($entity);

        return $this->serializer->serialize(
            $entity,
            'json',
            [
                'groups' => $scope->getScopes(),
                'json_encode_options' => \JSON_PRETTY_PRINT,
            ]
        );
    }

    /**
     * Normalizes an entity object into an associative array.
     *
     * @param \object     $entity Entity.
     * @param OutputScope $scope  Output scope.
     * @return mixed[]
     */
    public function normalize(object $entity, OutputScope $scope): array
    {
        $entity = $this->beforeSerialize($entity);
        /** @var Serializer $serializer */
        $serializer = $this->serializer;

        return $serializer->normalize($entity, null, ['groups' => $scope->getScopes()]);
    }

    /**
     * Normalizes an array of entities into an array of associative arrays.
     *
     * @param object[]    $entityCollection Array of entities.
     * @param OutputScope $scope            Output scope.
     * @return mixed[]
     */
    public function normalizeCollection(array $entityCollection, OutputScope $scope): array
    {
        $output = [];
        foreach ($entityCollection as $entity) {
            $output[] = $this->normalize($entity, $scope);
        }
        
        return $output;
    }
}
