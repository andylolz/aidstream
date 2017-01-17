<?php namespace App\Lite\Repositories\DocumentLinks;


use App\Lite\Contracts\DocumentLinkRepositoryInterface;
use App\Models\Activity\ActivityDocumentLink;

/**
 * Class DocumentLinksRepository
 * @package App\Lite\Repositories\DocumentLinks
 */
class DocumentLinksRepository implements DocumentLinkRepositoryInterface
{
    /**
     * @var ActivityDocumentLink
     */
    private $documentLink;

    /**
     * DocumentLinksRepository constructor.
     * @param ActivityDocumentLink $documentLink
     */
    public function __construct(ActivityDocumentLink $documentLink)
    {
        $this->documentLink = $documentLink;
    }

    /**
     * Returns all documentLinks of provided activity.
     *
     * @param $activityId
     * @return mixed
     */
    public function all($activityId)
    {
        return $this->documentLink->where('activity_id', $activityId)->get();
    }

    /**
     * Saves the document link data into database.
     *
     * @param array $data
     * @param       $activityId
     * @return boolean
     */
    public function save(array $data, $activityId)
    {
        foreach ($data as $index => $documentLink) {
            $this->documentLink->create(['activity_id' => $activityId, 'document_link' => $documentLink]);
        }

        return true;
    }

    /**
     * Find the details of the provided id of document link.
     *
     * @param $documentLinkId
     * @return mixed
     */
    public function find($documentLinkId)
    {
        return $this->documentLink->findOrFail($documentLinkId);
    }

    /**
     * Update the details of the document link
     *
     * @param array $data
     * @param       $activityId
     * @return boolean
     */
    public function update(array $data, $activityId)
    {
        foreach ($data as $dataIndex => $documentLink) {
            $id = getVal($documentLink, ['id']);
            unset($documentLink['id']);

            $this->documentLink->updateOrCreate(['id' => $id, 'activity_id' => $activityId], ['document_link' => $documentLink]);
        }

        return true;
    }
}

