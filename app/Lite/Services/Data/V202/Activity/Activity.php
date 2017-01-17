<?php namespace App\Lite\Services\Data\V202\Activity;

use App\Lite\Services\Data\Contract\MapperInterface;
use App\Models\Organization\Organization;

/**
 * Class ActivityData
 * @package App\Lite\Services\Data\Activity
 */
class Activity implements MapperInterface
{
    /**
     * Code for funding organisation role
     */
    const FUNDING_ORGANIZATION_ROLE = 1;

    /**
     * Code for implementing organisation role
     */
    const IMPLEMENTING_ORGANIZATION_ROLE = 4;

    /**
     * Code for start date (Actual)
     */
    const START_DATE = 2;

    /**
     * Code for end date (Actual)
     */
    const END_DATE = 4;

    /**
     * Code for target groups type of description.
     */
    const TARGET_GROUPS = 3;

    /**
     * Code for objectives type of description.
     */
    const OBJECTIVES = 2;

    /**
     * Code for general type of description.
     */
    const GENERAL_DESCRIPTION = 1;

    /**
     * Code for outcomes document category.
     */
    const OUTCOMES_DOCUMENT_CODE = 'A08';

    /**
     * Code for annual report document category.
     */
    const ANNUAL_REPORT_CODE = 'B01';

    /*
     * Default Document format,
     */
    const DOCUMENT_FORMAT = 'application/pdf';

    /**
     * Raw data holder for Activity entity.
     *
     * @var array
     */
    protected $rawData = [];

    /**
     * Contains mapped data.
     * @var array
     */
    protected $mappedData = [];

    /**
     * @var int
     */
    protected $index = 0;

    /**
     * Data template for Activity.
     *
     * @var array
     */
    protected $template = [];

    /**
     * Path to template.
     */
    const BASE_TEMPLATE_PATH = 'Services/XmlImporter/Foundation/Support/Templates/V202.json';

    /**
     * @var array
     */
    protected $mappedFields = [
        'activity_identifier'        => 'identifier',
        'activity_title'             => 'title',
        'general_description'        => 'description',
        'objectives'                 => 'description',
        'target_groups'              => 'description',
        'activity_status'            => 'activity_status',
        'sector'                     => 'sector',
        'start_date'                 => 'activity_date',
        'end_date'                   => 'activity_date',
        'country'                    => 'recipient_country',
        'funding_organisations'      => 'participating_organization',
        'implementing_organisations' => 'participating_organization',
        'outcomes_document'          => 'document_link',
        'annual_report'              => 'document_link'
    ];

    /**
     * ActivityData constructor.
     * @param array $rawData
     */
    public function __construct(array $rawData)
    {
        $this->rawData  = $rawData;
        $this->template = $this->loadTemplate();
    }

    /**
     * Map the raw data to element template.
     *
     * @return array
     */
    public function map()
    {
        foreach ($this->rawData as $key => $value) {
            if (!empty($value)) {
                $this->resetIndex($this->mappedFields[$key]);
                $this->{$this->mappedFields[$key]}($key, $value, $this->getTemplateOf($key));
            }
        }

        return $this->mappedData;
    }

    /**
     * Map database data into frontend compatible format.
     *
     * @return mixed
     */
    public function reverseMap()
    {
        $this->mappedData['activity_identifier'] = getVal($this->rawData, ['identifier', 'activity_identifier']);
        $this->mappedData['activity_title']      = getVal($this->rawData, ['title', 0, 'narrative']);
        $this->mappedData['activity_status']     = getVal($this->rawData, ['activity_status']);
        $this->mappedData['sector']              = getVal($this->rawData, ['sector', 0, 'sector_code']);

        $this->reverseMapDescription()
             ->reverseMapActivityDate()
             ->reverseMapRecipientCountry()
             ->reverseMapParticipatingOrganisation()
             ->reverseMapDocumentLink();

        return $this->mappedData;
    }

    /**
     * Reset the index value to 0.
     * @param $key
     */
    protected function resetIndex($key)
    {
        if (!array_key_exists($key, $this->mappedData)) {
            $this->index = 0;
        }
    }

    /**
     * Map the data to identifier template.
     *
     * @param $key
     * @param $value
     * @param $template
     */
    protected function identifier($key, $value, $template)
    {
        $organization             = Organization::findOrFail(session('org_id'));
        $reporting_org_identifier = getVal($organization->reporting_org, [0, 'reporting_organization_identifier'], '');

        $template[$key]                              = $value;
        $template['iati_identifier_text']            = sprintf('%s-%s', $reporting_org_identifier, $value);
        $this->mappedData[$this->mappedFields[$key]] = $template;
    }

    /**
     * Map the data to title template.
     *
     * @param $key
     * @param $value
     * @param $template
     */
    protected function title($key, $value, $template)
    {
        $template['narrative'] = $value;

        $this->mappedData[$this->mappedFields[$key]][$this->index] = $template;
    }

    /**
     * Map the data to description template.
     *
     * @param $key
     * @param $value
     * @param $template
     */
    protected function description($key, $value, $template)
    {
        $this->mappedData['description'][$this->index]              = $template['type'];
        $this->mappedData['description'][$this->index]['type']      = $this->getDescriptionType($key);
        $this->mappedData['description'][$this->index]['narrative'] = [['narrative' => $value, 'language' => '']];
        $this->index ++;
    }

    /**
     * Map the data to activity status template.
     *
     * @param $key
     * @param $value
     * @param $template
     */
    protected function activity_status($key, $value, $template)
    {
        $this->mappedData[$key] = $value;
    }

    /**
     * Map the data to sector template.
     *
     * @param $key
     * @param $value
     * @param $template
     */
    protected function sector($key, $value, $template)
    {
        $template['sector_vocabulary']            = '1';
        $template['sector_code']                  = $value;
        $this->mappedData['sector'][$this->index] = $template;
    }

    /**
     * Map the data to activity date template.
     *
     * @param $key
     * @param $value
     * @param $template
     */
    protected function activity_date($key, $value, $template)
    {
        $this->mappedData['activity_date'][$this->index]         = $template;
        $this->mappedData['activity_date'][$this->index]['date'] = $value;
        $this->mappedData['activity_date'][$this->index]['type'] = $this->getActivityDateType($key);
        $this->index ++;
    }

    /**
     *  Map the data to recipient country template.
     *
     * @param $key
     * @param $value
     * @param $template
     */
    protected function recipient_country($key, $value, $template)
    {
        $this->mappedData['recipient_country'][$this->index]                 = $template;
        $this->mappedData['recipient_country'][$this->index]['country_code'] = $value;
    }

    /**
     * Map the data to participating organisation template.
     *
     * @param $key
     * @param $value
     * @param $template
     */
    protected function participating_organization($key, $value, $template)
    {
        $organizationRole = $this->getOrganizationRole($key);
        foreach ($value as $index => $field) {
            $organizationType = getVal($field, ['organisation_type'], '');
            $organizationName = getVal($field, ['organisation_name'], '');

            if ($organizationName != "" || $organizationType != "") {
                $this->mappedData['participating_organization'][$this->index]                              = $template;
                $this->mappedData['participating_organization'][$this->index]['organization_role']         = $organizationRole;
                $this->mappedData['participating_organization'][$this->index]['organization_type']         = $organizationType;
                $this->mappedData['participating_organization'][$this->index]['narrative'][0]['narrative'] = $organizationName;
                $this->index ++;
            }
        }
    }

    /**
     * Map the data to document link template.
     *
     * @param $key
     * @param $value
     * @param $template
     */
    protected function document_link($key, $value, $template)
    {
        $documentCategory = $this->getDocumentCategory($key);

        foreach ($value as $index => $field) {
            $documentTitle = getVal($field, ['document_title']);
            $documentUrl   = getVal($field, ['document_url']);
            $documentId    = getVal($field, ['document_link_id']);
            
            if ($documentTitle != "" || $documentUrl != "" || $documentId != "") {
                $this->mappedData['document_link'][$this->index] = $template;

                if ($documentId != "") {
                    $this->mappedData['document_link'][$this->index]['id'] = $documentId;
                }

                $this->mappedData['document_link'][$this->index]['url']                      = $documentUrl;
                $this->mappedData['document_link'][$this->index]['title'][0]['narrative'][0] = ['narrative' => $documentTitle, 'language' => ''];
                $this->mappedData['document_link'][$this->index]['category'][0]['code']      = $documentCategory;
                $this->mappedData['document_link'][$this->index]['document_date'][0]['date'] = date('Y-m-d');
                $this->mappedData['document_link'][$this->index]['format']                   = self::DOCUMENT_FORMAT;
                $this->index ++;
            }
        }
    }

    /**
     * Reverse map description for form.
     *
     * @return $this
     */
    protected function reverseMapDescription()
    {
        foreach (getVal($this->rawData, ['description'], []) as $descriptions) {
            $descriptionType                    = $this->getDescriptionType(getVal($descriptions, ['type']), true);
            $description                        = getVal($descriptions, ['narrative', 0, 'narrative']);
            $this->mappedData[$descriptionType] = $description;
        }

        return $this;
    }

    /**
     * Reverse map activity date for form.
     *
     * @return $this
     */
    protected function reverseMapActivityDate()
    {
        foreach (getVal($this->rawData, ['activity_date'], []) as $activityDate) {
            $activityDateType                    = $this->getActivityDateType(getVal($activityDate, ['type']), true);
            $date                                = getVal($activityDate, ['date']);
            $this->mappedData[$activityDateType] = $date;
        }

        return $this;
    }

    /**
     * Reverse map recipient country for form.
     *
     * @return $this
     */
    protected function reverseMapRecipientCountry()
    {
        foreach (getVal($this->rawData, ['recipient_country'], []) as $index => $country) {
            $this->mappedData['country'] = getVal($country, ['country_code']);
        }

        return $this;
    }

    /**
     * Reverse map participating organisations for form.
     *
     * @return $this
     */
    protected function reverseMapParticipatingOrganisation()
    {
        foreach (getVal($this->rawData, ['participating_organization'], []) as $index => $organization) {
            $organizationRole = $this->getOrganizationRole(getVal($organization, ['organization_role']), true);
            if (!array_key_exists($organizationRole, $this->mappedData)) {
                $index = 0;
            }
            $organizationType                                                 = getVal($organization, ['organization_type']);
            $organizationName                                                 = getVal($organization, ['narrative', 0, 'narrative']);
            $this->mappedData[$organizationRole][$index]['organisation_name'] = $organizationName;
            $this->mappedData[$organizationRole][$index]['organisation_type'] = $organizationType;
        }

        return $this;
    }

    protected function reverseMapDocumentLink()
    {
        foreach (getVal($this->rawData, ['document_link'], []) as $index => $documentLink) {
            $documentCategory = $this->getDocumentCategory(getVal($documentLink, ['document_link', 'category', 0, 'code']), true);
            if (!array_key_exists($documentCategory, $this->mappedData)) {
                $index = 0;
            }
            $this->mappedData[$documentCategory][$index]['document_title']   = getVal($documentLink, ['document_link', 'title', 0, 'narrative', 0, 'narrative']);
            $this->mappedData[$documentCategory][$index]['document_url']     = getVal($documentLink, ['document_link', 'url']);
            $this->mappedData[$documentCategory][$index]['document_link_id'] = getVal($documentLink, ['id']);
        }

        return $this;
    }

    /**
     * Returns the organisation role code.
     * If reversed is true, then key is returned.
     *
     * @param      $key
     * @param bool $reversed
     * @return mixed|string
     */
    protected function getOrganizationRole($key, $reversed = false)
    {
        $organizationRole = [
            'funding_organisations'      => self::FUNDING_ORGANIZATION_ROLE,
            'implementing_organisations' => self::IMPLEMENTING_ORGANIZATION_ROLE
        ];
        if ($reversed) {
            return getVal(array_flip($organizationRole), [$key]);
        }

        return $organizationRole[$key];
    }

    /**
     * Returns the type of activity date code.
     * If reversed is true, then key is returned.
     *
     * @param      $key
     * @param bool $reversed
     * @return mixed|string
     */
    protected function getActivityDateType($key, $reversed = false)
    {
        $activityDates = [
            'start_date' => self::START_DATE,
            'end_date'   => self::END_DATE
        ];

        if ($reversed) {
            return getVal(array_flip($activityDates), [$key]);
        }

        return $activityDates[$key];
    }

    /**
     * Returns the description type code.
     * If reversed is true, then key is returned.
     *
     * @param      $key
     * @param bool $reversed
     * @return mixed|string
     */
    protected function getDescriptionType($key, $reversed = false)
    {
        $description = [
            'general_description' => self::GENERAL_DESCRIPTION,
            'objectives'          => self::OBJECTIVES,
            'target_groups'       => self::TARGET_GROUPS
        ];

        if ($reversed) {
            return getVal(array_flip($description), [$key]);
        }

        return $description[$key];
    }

    /**
     * Returns the document category.
     * If reversed is true, then key is returned.
     *
     * @param      $key
     * @param bool $reversed
     * @return mixed|string
     */
    protected function getDocumentCategory($key, $reversed = false)
    {
        $documentCategory = [
            'outcomes_document' => self::OUTCOMES_DOCUMENT_CODE,
            'annual_report'     => self::ANNUAL_REPORT_CODE
        ];

        if ($reversed) {
            return getVal(array_flip($documentCategory), [$key]);
        }

        return $documentCategory[$key];
    }

    /**
     * Returns specific template of an element.
     *
     * @param $key
     * @return mixed
     */
    public function getTemplateOf($key)
    {
        return $this->template[$this->mappedFields[$key]];
    }

    /**
     * Returns template of the elements.
     *
     * @return mixed
     */
    public function loadTemplate()
    {
        return json_decode(file_get_contents(app_path(self::BASE_TEMPLATE_PATH)), true);
    }
}

