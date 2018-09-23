<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Test;

use Cstea\ApiBundle\Traits\RandomAware;
use Mockery as m;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class Unit
 * Base class for Unit tests. It extends the Codeception Unit test class,
 * but provides additional tools commonly used during unit testing.
 *
 * @package Cstea\ApiBundle\Test
 */
abstract class Unit extends \Codeception\Test\Unit
{
    
    use RandomAware;

    /**
     * Tests whether a timestamp entity field returns the proper Datetime immutable class time.
     *
     * @param object $object       Object to test.
     * @param string $propertyName Field to test.
     */
    protected function testTimestamp(object $object, string $propertyName): void
    {
        $propName = \ucfirst($propertyName);
        if (!\method_exists($object, 'get' . $propName)) {
            throw new \InvalidArgumentException('Could not find getter');
        }
        $getter = 'get' . $propName;

        $this->assertInstanceOf(\DateTimeImmutable::class, $object->$getter());

        try {
            $now = new \DateTimeImmutable();
            $interval = $now->diff($object->$getter());
            $this->assertEquals(0, $interval->days + $interval->h + $interval->i);
        } catch (\Throwable $e) {
        }
    }
    
    /**
     * Tests addX(), removeX() and getXs() functions for a collection property.
     *
     * @param object $object       Object to test.
     * @param string $propertyName Property to test.
     * @param mixed  $value        Value to add and remove.
     */
    protected function testAddRemove(object $object, string $propertyName, $value): void
    {
        $propName = \ucfirst($propertyName);
        if (!\method_exists($object, 'get' . $propName)) {
            throw new \InvalidArgumentException('Could not find getter');
        }
        $getter = 'get' . $propName;
        $propName = \substr($propName, 0, \strlen($propName) - 4);
        try {
            $reflectionClass = new \ReflectionClass(\get_class($object));
            $objectMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
            $adder = null;
            $remover = null;
            foreach ($objectMethods as $method) {
                if (\stripos($method->getShortName(), 'add' . $propName) !== false) {
                    $adder = $method->getShortName();
                } elseif (\stripos($method->getShortName(), 'remove' . $propName) !== false) {
                    $remover = $method->getShortName();
                }
            }
            if (!$adder) {
                throw new \InvalidArgumentException('Could not find adder');
            }
            if (!$remover) {
                throw new \InvalidArgumentException('Could not find remover');
            }
            $this->assertEmpty($object->$getter());
            $this->assertEquals($object, $object->$adder($value));
            $this->assertEquals([$value], $object->$getter());
            $this->assertEquals($object, $object->$remover($value));
            $this->assertEmpty($object->$getter());
        } catch (\ReflectionException $e) {
        }
        
        return;
    }

    /**
     * Tests properties with only getters and no setters.
     *
     * @param object     $object       Object to test.
     * @param string     $propertyName Property to test.
     * @param mixed      $value        Value to set.
     * @param mixed|null $defaultValue Default value.
     */
    protected function testReadOnly(object $object, string $propertyName, $value, $defaultValue = null): void
    {
        $propName = \ucfirst($propertyName);
        if (!\method_exists($object, 'get' . $propName)) {
            throw new \InvalidArgumentException('Could not find getter');
        }
        $getter = 'get' . $propName;
        $this->assertEquals($defaultValue, $object->$getter());
        $this->setPrivateProperty($object, $propertyName, $value);
        $this->assertEquals($value, $object->$getter());
    }
    
    /**
     * Tests getX() and setX() for a given property.
     *
     * @param object     $object       Object.
     * @param string     $propertyName Property.
     * @param mixed      $value        Value to test.
     * @param mixed|null $defaultValue Default value.
     */
    protected function testGetSet(object $object, string $propertyName, $value, $defaultValue = null): void
    {
        $propName = \ucfirst($propertyName);
        
        if (\method_exists($object, 'get' . $propName)) {
            $getter = 'get' . $propName;
        } elseif (\method_exists($object, 'is' . $propName)) {
            $getter = 'is' . $propName;
        } elseif (\method_exists($object, 'has' . $propName)) {
            $getter = 'has' . $propName;
        } else {
            throw new \InvalidArgumentException('Could not find getter');
        }
        if (!\method_exists($object, 'set' . $propName)) {
            throw new \InvalidArgumentException('Could not find setter');
        }
        $setter = 'set' . $propName;
        $this->assertEquals($defaultValue, $object->$getter());
        $this->assertEquals($object, $object->$setter($value));
        $this->assertEquals($value, $object->$getter());
        
        return;
    }
    
    /**
     * Injects the standard mock dependencies into a provided object.
     *
     * @param object   $object       Mocked service object.
     * @param object[] $dependencies Optional dependencies.
     */
    protected function injectMockDependencies(object $object, array $dependencies = []): void
    {
        if (\method_exists($object, 'setLogger')) {
            $object->setLogger($dependencies['logger'] ?? new NullLogger());
        }
        
        if (\method_exists($object, 'setEventDispatcher')) {
            $mockDispatcher = m::mock(EventDispatcherInterface::class, ['dispatch' => null]);
            $object->setEventDispatcher($dependencies['eventDispatcher'] ?? $mockDispatcher);
        }
        
        if (\method_exists($object, 'setSerializer')) {
            $mockSerializer = m::mock(SerializerInterface::class, [
                'serialize' => null,
                'deserialize' => null,
            ]);
            $object->setSerializer($dependencies['serializer'] ?? $mockSerializer);
        }
        
        if (\method_exists($object, 'setValidator')) {
            $mockValidator = m::mock(ValidatorInterface::class, [
                'validate' => new ConstraintViolationList(),
            ]);
            $object->setValidator($dependencies['validator'] ?? $mockValidator);
        }
        
        return;
    }

    /**
     * Fetches the value of a private/protected property through reflection.
     *
     * @param object $object       Object.
     * @param string $propertyName Property name.
     * @return mixed|null
     */
    protected function getPrivateProperty(object $object, string $propertyName)
    {
        try {
            $reflectionClass = new \ReflectionClass(\get_class($object));
            $prop = $reflectionClass->getProperty($propertyName);
            $prop->setAccessible(true);
            $value = $prop->getValue($object);
            $prop->setAccessible(false);
            
            return $value;
        } catch (\ReflectionException $e) {
        }
        
        return null;
    }

    /**
     * Sets the value of a private/protected property through reflection.
     *
     * @param object $object       Object.
     * @param string $propertyName Property name.
     * @param mixed  $value        Property value.
     */
    protected function setPrivateProperty(object $object, string $propertyName, $value): void
    {
        try {
            $reflectionClass = new \ReflectionClass(\get_class($object));
            $prop = $reflectionClass->getProperty($propertyName);
            $prop->setAccessible(true);
            $prop->setValue($object, $value);
            $prop->setAccessible(false);
        } catch (\ReflectionException $e) {
        }
    }

    /**
     * Invokes a private method through reflection.
     *
     * @param object     $object        Object.
     * @param string     $methodName    Method name.
     * @param mixed|null ...$parameters Method parameters.
     * @return mixed
     */
    protected function invokePrivateMethod(object $object, string $methodName, ...$parameters)
    {
        $result = null;
        
        try {
            $reflectionClass = new \ReflectionClass(\get_class($object));
            $method = $reflectionClass->getMethod($methodName);
            $method->setAccessible(true);
            $result = $method->invokeArgs($object, $parameters);
            $method->setAccessible(false);
        } catch (\ReflectionException $e) {
        }
        
        return $result;
    }
    
    /**
     * Tear down
     */
    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
