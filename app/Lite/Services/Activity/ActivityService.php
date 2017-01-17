<?php namespace App\Lite\Services\Activity;

use App\Lite\Contracts\DocumentLinkRepositoryInterface;
use App\Lite\Services\Data\V202\Activity\Activity;
use App\Lite\Services\Data\Traits\TransformsData;
use App\Lite\Services\Traits\ProvidesLoggerContext;
use Exception;
use Psr\Log\LoggerInterface;
use App\Lite\Contracts\ActivityRepositoryInterface;

/**
 * Class ActivityService
 * @package app\Lite\Services\Activity
 */
class ActivityService
{
    use ProvidesLoggerContext, TransformsData;

    /**
     * @var ActivityRepositoryInterface
     */
    protected $activityRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DocumentLinkRepositoryInterface
     */
    protected $documentLinkRepository;

    /**
     * ActivityService constructor.
     * @param ActivityRepositoryInterface     $activityRepository
     * @param DocumentLinkRepositoryInterface $documentLinkRepository
     * @param LoggerInterface                 $logger
     */
    public function __construct(ActivityRepositoryInterface $activityRepository, DocumentLinkRepositoryInterface $documentLinkRepository, LoggerInterface $logger)
    {
        $this->activityRepository     = $activityRepository;
        $this->logger                 = $logger;
        $this->documentLinkRepository = $documentLinkRepository;
    }

    /**
     * Get all Activities for the current Organization.
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function all()
    {
        try {
            return $this->activityRepository->all(session('org_id'));
        } catch (Exception $exception) {
            $this->logger->error(
                sprintf('Error due to %s', $exception->getMessage()),
                $this->getContext($exception)
            );

            return [];
        }
    }

    /**
     * Store the Activity data.
     *
     * @param array $rawData
     * @param       $version
     * @return \App\Models\Activity\Activity|null
     */
    public function store(array $rawData, $version)
    {
        try {
            $mappedData   = $this->transform($this->getMapping($rawData, 'Activity', $version));
            $documentLink = false;

            if (array_key_exists('document_link', $mappedData)) {
                $documentLink = $mappedData['document_link'];
                unset($mappedData['document_link']);
            }

            $activity = $this->activityRepository->save($mappedData);

            if ($documentLink) {
                $this->documentLinkRepository->save($documentLink, $activity->id);
            }

            $this->logger->info('Activity successfully saved.', $this->getContext());

            return $activity;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    /**
     *  Find a Specific Activity.
     *
     * @param $activityId
     * @return \App\Models\Activity\Activity
     */
    public function find($activityId)
    {
        return $this->activityRepository->find($activityId);
    }

    /**
     * Delete a activity.
     *
     * @param $activityId
     * @return mixed|null
     */
    public function delete($activityId)
    {
        try {
            $activity = $this->activityRepository->delete($activityId);

            $this->logger->info('Activity successfully deleted.', $this->getContext());

            return $activity;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    /**
     * Returns reversely mapped activity data to edit.
     *
     * @param $activityId
     * @param $version
     * @return array
     */
    public function edit($activityId, $version)
    {
        $activity     = $this->find($activityId)->toArray();
        $documentLink = $this->documentLinkRepository->all($activity['id'])->toArray();

        if ($documentLink) {
            $activity['document_link'] = $documentLink;
        }

        return $this->transformReverse($this->getMapping($activity, 'Activity', $version));
    }

    /**
     * Update the activity data.
     *
     * @param $activityId
     * @param $rawData
     * @param $version
     * @return mixed|null
     */
    public function update($activityId, $rawData, $version)
    {
        try {
            $mappedData   = $this->transform($this->getMapping($rawData, 'Activity', $version));
            $documentLink = false;

            if (array_key_exists('document_link', $mappedData)) {
                $documentLink = $mappedData['document_link'];
                unset($mappedData['document_link']);
            }

            $this->activityRepository->update($activityId, $mappedData);

            if ($documentLink) {
                $this->documentLinkRepository->update($documentLink, $activityId);
            }

            $this->logger->info('Activity successfully updated.', $this->getContext());

            return true;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }
}
