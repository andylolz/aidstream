<?php namespace App\Lite\Forms\V202;


use App\Lite\Forms\LiteBaseForm;
use App\Lite\Forms\FormPathProvider;

/**
 * Class Activity
 * @package App\Lite\Forms\V202
 */
class Activity extends LiteBaseForm
{
    use FormPathProvider;

    /**
     * Form structure for the Activity.
     */
    public function buildForm()
    {
        $participatingOrganisationFormPath = $this->getFormPath('ParticipatingOrganisation');

        $this->addText('activity_identifier', trans('lite/elementForm.activity_identifier'))
             ->addText('activity_title', trans('lite/elementForm.activity_title'))
             ->addSelect(
                 'activity_status',
                 $this->getCodeList('ActivityStatus', 'Activity'),
                 trans('lite/elementForm.activity_status'),
                 null,
                 null,
                 true,
                 ['wrapper' => ['class' => 'form-group col-sm-6']]
             )
             ->addSelect(
                 'sector',
                 $this->getCodeList('Sector', 'Activity'),
                 trans('lite/elementForm.sector'),
                 null,
                 null,
                 true,
                 ['wrapper' => ['class' => 'form-group col-sm-6']]
             )
             ->add('start_date', 'date', ['label' => trans('lite/elementForm.start_date'), 'required' => true, 'wrapper' => ['class' => 'form-group col-sm-6']])
             ->add('end_date', 'date', ['label' => trans('lite/elementForm.end_date'), 'wrapper' => ['class' => 'form-group col-sm-6']])
             ->addText('general_description', trans('lite/elementForm.general_description'))
             ->addText('objectives', trans('lite/elementForm.objectives'), false)
             ->addText('target_groups', trans('lite/elementForm.target_groups'), false)
             ->addSelect(
                 'country',
                 $this->getCodeList('Country', 'Organization'),
                 trans('lite/elementForm.country'),
                 null,
                 null,
                 true,
                 ['wrapper' => ['class' => 'form-group col-sm-6']]
             )
             ->addToCollection('funding_organisations', ' ', $participatingOrganisationFormPath, 'collection_form funding_organisations')
             ->addButton('add_more_funding', trans('lite/elementForm.add_another_funding_organisation'), 'funding_organisations', 'add_more')
             ->addToCollection('implementing_organisations', ' ', $participatingOrganisationFormPath, 'collection_form implementing_organisations')
             ->addButton('add_more_implementing', trans('lite/elementForm.add_another_implementing_organisation'), 'implementing_organisations', 'add_more');
    }
}

