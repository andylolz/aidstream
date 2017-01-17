<?php namespace App\Lite\Services\Validation\Rules\V202;

use App\Core\V201\Traits\GetCodes;
use App\Models\Activity\Activity as ActivityModel;

/**
 * Class Activity
 * @package App\Lite\Services\Validation\Rules\V202
 */
class Activity
{
    use GetCodes;

    /**
     * Contains rules for the Activity.
     *
     * @var array
     */
    protected $activityRules = [];

    /**
     * List of the methods to set rules and messages.
     *
     * @var array
     */
    protected $methods = [
        'ActivityIdentifier',
        'ActivityTitle',
        'GeneralDescription',
        'ActivityStatus',
        'Sector',
        'StartDate',
        'EndDate',
        'Country',
        'OrganisationName',
        'OrganisationType',
        'DocumentUrl'
    ];

    /**
     * Contains messages for the Activity.
     *
     * @var array
     */
    protected $activityMessages = [];

    /**
     * Calls and sets $this->activityRules from $this->methods.
     *
     * @return array
     */
    public function rules()
    {
        foreach ($this->methods() as $method) {
            $methodName = sprintf('rulesFor%s', $method);

            if (method_exists($this, $methodName)) {
                $this->{$methodName}();
            }
        }

        return $this->activityRules;
    }

    /**
     * Calls and sets $this->activityMessages from $this->methods.
     *
     * @return array
     */
    public function messages()
    {
        foreach ($this->methods() as $method) {
            $methodName = sprintf('messagesFor%s', $method);

            if (method_exists($this, $methodName)) {
                $this->{$methodName}();
            }
        }

        return $this->activityMessages;
    }

    /**
     * Returns available methods.
     *
     * @return array
     */
    protected function methods()
    {
        return $this->methods;
    }

    /**
     * Sets rules for the activity identifier.
     */
    protected function rulesForActivityIdentifier()
    {
        $this->activityRules['activity_identifier'] = sprintf('required|not_in:%s', $this->getActivityIdentifiers());
    }

    /**
     * Sets messages for the activity identifier.
     */
    protected function messagesForActivityIdentifier()
    {
        $this->activityMessages['activity_identifier.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.activity_identifier')]);
        $this->activityMessages['activity_identifier.not_in']   = trans('validation.not_in', ['attribute' => trans('lite/elementForm.activity_identifier')]);
    }

    /**
     * Sets rules for the activity title.
     */
    protected function rulesForActivityTitle()
    {
        $this->activityRules['activity_title'] = 'required';
    }

    /**
     * Sets messages for the activity title.
     */
    protected function messagesForActivityTitle()
    {
        $this->activityMessages['activity_title.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.activity_title')]);
    }

    /**
     * Sets rules for general description.
     */
    protected function rulesForGeneralDescription()
    {
        $this->activityRules['general_description'] = 'required';
    }

    /**
     * Sets messages for general description.
     */
    protected function messagesForGeneralDescription()
    {
        $this->activityMessages['general_description.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.general_description')]);
    }

    /**
     * Sets rules for activity status.
     */
    protected function rulesForActivityStatus()
    {
        $this->activityRules['activity_status'] = sprintf('required|in:%s', $this->getStringFormatCode('ActivityStatus', 'Activity'));
    }

    /**
     * Sets messages for activity status.
     */
    protected function messagesForActivityStatus()
    {
        $this->activityMessages['activity_status.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.general_description')]);
        $this->activityMessages['activity_status.in']       = trans('validation.code_list', ['attribute' => trans('lite/elementForm.general_description')]);
    }

    /**
     * Sets rules for sector.
     */
    protected function rulesForSector()
    {
        $this->activityRules['sector'] = sprintf('required|in:%s', $this->getStringFormatCode('Sector', 'Activity'));
    }

    /**
     * Sets messages for sector.
     */
    protected function messagesForSector()
    {
        $this->activityMessages['sector.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.sector')]);
        $this->activityMessages['sector.in']       = trans('validation.code_list', ['attribute' => trans('lite/elementForm.sector')]);
    }

    /**
     * Sets rules for start date.
     */
    protected function rulesForStartDate()
    {
        $this->activityRules['start_date'] = 'required|date';
    }

    /**
     * Sets messages for start date.
     */
    protected function messagesForStartDate()
    {
        $this->activityMessages['start_date.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.start_date')]);
        $this->activityMessages['start_date.date']     = trans('validation.date', ['attribute' => trans('lite/elementForm.start_date')]);
    }

    /**
     * Sets rules for end date.
     */
    protected function rulesForEndDate()
    {
        $this->activityRules['end_date'] = 'date|after:start_date';
    }

    /**
     * Sets messages for end date.
     */
    protected function messagesForEndDate()
    {
        $this->activityMessages['end_date.date']  = trans('validation.date', ['attribute' => trans('lite/elementForm.end_date')]);
        $this->activityMessages['end_date.after'] = trans('validation.after', ['attribute' => trans('lite/elementForm.end_date'), 'date' => trans('lite/elementForm.start_date')]);
    }

    /**
     * Sets rules for country.
     */
    protected function rulesForCountry()
    {
        $this->activityRules['country'] = sprintf('required|in:%s', $this->getStringFormatCode('Country', 'Organization'));
    }

    /**
     * Sets messages for country.
     */
    protected function messagesForCountry()
    {
        $this->activityMessages['country.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.country')]);
        $this->activityMessages['country.in']       = trans('validation.code_list', ['attribute' => trans('lite/elementForm.country')]);
    }

    /**
     * Sets rules for organisation name.
     */
    protected function rulesForOrganisationName()
    {
        $this->activityRules['implementing_organisations.*.organisation_name'] = 'required';
    }

    /**
     * Sets messages for organisation name.
     */
    protected function messagesForOrganisationName()
    {
        $this->activityMessages['implementing_organisations.*.organisation_name.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.implementing_organisation_name')]);
    }

    /**
     * Sets rules for organisation type.
     */
    protected function rulesForOrganisationType()
    {
        $this->activityRules['funding_organisations.*.organisation_type']      = sprintf('in:%s', $this->getStringFormatCode('OrganizationType', 'Organization'));
        $this->activityRules['implementing_organisations.*.organisation_type'] = sprintf('required|in:%s', $this->getStringFormatCode('OrganizationType', 'Organization'));
    }

    /**
     * Sets messages for organisation type.
     */
    protected function messagesForOrganisationType()
    {
        $this->activityMessages['funding_organisations.*.organisation_type.in']            = trans('validation.code_list', ['attribute' => trans('lite/elementForm.funding_organisation_type')]);
        $this->activityMessages['implementing_organisations.*.organisation_type.required'] = trans('validation.required', ['attribute' => trans('lite/elementForm.implementing_organisation_type')]);
        $this->activityMessages['implementing_organisations.*.organisation_type.in']       = trans('validation.code_list', ['attribute' => trans('lite/elementForm.implementing_organisation_type')]);
    }

    protected function rulesForDocumentUrl()
    {
        $this->activityRules['outcomes_document_url'] = 'url';
        $this->activityRules['annual_report_url']     = 'url';
    }

    protected function messagesForDocumentUrl()
    {
        $this->activityRules['outcomes_document_url.url'] = trans('validation.url', ['attribute' => trans('lite/elementForm.outcomes_document_url')]);
        $this->activityRules['annual_report_url.url']     = trans('validation.url', ['attribute' => trans('lite/elementForm.annual_report_url')]);
    }

    /**
     *  Return list of activity Identifiers of the organisation.
     *  If activityId is present in url then query is further filtered by the activity id.
     *
     * @return string
     */
    protected function getActivityIdentifiers()
    {
        if ($activityId = request()->route()->activity) {
            $activities = ActivityModel::where('organization_id', session('org_id'))->where('id', '<>', $activityId)->get();
        } else {
            $activities = ActivityModel::where('organization_id', session('org_id'))->get();
        }

        $activityIdentifiers = [];

        foreach ($activities as $activity) {
            $activityIdentifiers[] = getVal($activity->identifier, ['activity_identifier']);
        }

        return implode(",", $activityIdentifiers);
    }
}
