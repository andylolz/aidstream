<?php namespace App\Core\V201\Forms\Activity;

use App\Core\Form\BaseForm;

/**
 * Class PolicyMaker
 * @package App\Core\V201\Forms\Activity
 */
class PolicyMaker extends BaseForm
{

    public function buildForm()
    {
        $this
            ->addSelect('vocabulary', $this->getCodeList('PolicyMarkerVocabulary', 'Activity'))
            ->addSelect('significance', $this->getCodeList('PolicySignificance', 'Activity'))
            ->addSelect('policy_marker', $this->getCodeList('PolicyMarker', 'Activity'))
            ->addNarrative('narrative')
            ->addAddMoreButton('add_narrative', 'narrative')
            ->addRemoveThisButton('remove_policy_maker');
    }
}
