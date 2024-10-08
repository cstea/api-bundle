<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Traits;

use Cstea\ApiBundle\Event\Event;
use Cstea\ApiBundle\Repository\ReadWriteRepository;
use Cstea\ApiBundle\Repository\ReadWriteDeleteRepository;

/**
 * Trait PersistAware
 * Provides classes the trait of data saving to repositories.
 * 
 * @package Cstea\ApiBundle\Traits
 */
trait PersistAware
{
    
    use EventAware;
    use ValidateAware;

    /**
     * Wrapper function for saving entities.
     *
     * @param \object    $entity     Entity to save.
     * @param ReadWriteRepository  $repository Repository to use.
     * @param Event|null $onSuccess  Event to trigger on success.
     * @throws \Cstea\ApiBundle\Exception\RecordPersistException Save error.
     * @throws \Cstea\ApiBundle\Exception\RecordValidationException Validation error.
     */
    protected function saveEntity(object $entity, ReadWriteRepository $repository, ?Event $onSuccess = null): void
    {
        if (\method_exists($this, 'getLogger')) {
            $this->getLogger()->debug('Entity to save', ['entity' => $entity]);
        }
        $this->validate($entity);
        try {
            $repository->save($entity);
            if ($onSuccess !== null) {
                $this->triggerEvent($onSuccess);
            }
            if (\method_exists($this, 'getLogger')) {
                $this->getLogger()->info('Entity saved', ['entity' => $entity]);
            }
        } catch (\Throwable $exception) {
            if (\method_exists($this, 'getLogger')) {
                $this->getLogger()->error($exception->getMessage(), ['exception' => $exception, 'entity' => $entity]);
            }
            throw new \Cstea\ApiBundle\Exception\RecordPersistException($exception);
        }
    }

    /**
     * Wrapper function for deleting entities.
     *
     * @param \object         $entity     Entity to delete.
     * @param ReadWriteDeleteRepository $repository Repository to use.
     * @param Event|null      $onSuccess  Event to trigger on success.
     * @throws \Cstea\ApiBundle\Exception\RecordPersistException Save error.
     */
    protected function deleteEntity(object $entity, ReadWriteDeleteRepository $repository, ?Event $onSuccess = null): void
    {
        try {
            $repository->delete($entity);
            if ($onSuccess !== null) {
                $this->triggerEvent($onSuccess);
            }
            if (\method_exists($this, 'getLogger')) {
                $this->getLogger()->info('Entity deleted', ['entity' => $entity]);
            }
        } catch (\Throwable $exception) {
            if (\method_exists($this, 'getLogger')) {
                $this->getLogger()->error($exception->getMessage(), ['exception' => $exception, 'entity' => $entity]);
            }
            throw new \Cstea\ApiBundle\Exception\RecordPersistException($exception);
        }
    }
}
