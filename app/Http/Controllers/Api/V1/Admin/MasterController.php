<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Domain\Utils\AdminLog;
use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\Master\IndexEnumsRequest;
use App\Http\Resources\Department as DepartmentResource;
use App\Http\Resources\Division as DivisionResource;
use App\Http\Resources\Pref as PrefResource;
use App\Http\Resources\Term as TermResource;
use App\Repositories\Admin\EnumMasterRepositoryConstantInterface;
use App\Repositories\DepartmentRepository;
use App\Repositories\DivisionRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\PrefRepository;
use App\Repositories\TermRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterController extends ApiAdminController
{
    /**
     * @var EnumMasterRepositoryConstantInterface
     */
    private $enumMasterRepository;

    /**
     * @var DepartmentRepository
     */
    private $departmentRepository;

    /**
     * @var DivisionRepository
     */
    private $divisionRepository;

    /**
     * @var TermRepository
     */
    private $termRepository;

    /**
     * @var PrefRepository
     */
    private $prefRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @param EnumMasterRepositoryConstantInterface $enumMasterRepository
     * @param OrganizationRepository $organizationRepository
     * @param DepartmentRepository $departmentRepository
     * @param DivisionRepository $divisionRepository
     * @param TermRepository $termRepository
     * @param PrefRepository $prefRepository
     */
    public function __construct(
        EnumMasterRepositoryConstantInterface $enumMasterRepository,
        OrganizationRepository $organizationRepository,
        DepartmentRepository $departmentRepository,
        DivisionRepository $divisionRepository,
        TermRepository $termRepository,
        PrefRepository $prefRepository
    ) {
        $this->organizationRepository = $organizationRepository;
        $this->enumMasterRepository = $enumMasterRepository;
        $this->departmentRepository = $departmentRepository;
        $this->divisionRepository = $divisionRepository;
        $this->termRepository = $termRepository;
        $this->prefRepository = $prefRepository;
    }

    /**
     * @param IndexEnumsRequest $request
     *
     * @return array
     */
    public function indexEnums(IndexEnumsRequest $request)
    {
        $enums = $this->enumMasterRepository->all();

        return form_response_array($enums);
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function indexDepartments()
    {
        $departments = $this->departmentRepository->all();

        return DepartmentResource::collection($departments);
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function indexDivisions()
    {
        $divisions = $this->divisionRepository->all();

        return DivisionResource::collection($divisions);
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function indexTerms()
    {
        $terms = $this->termRepository->all();

        return TermResource::collection($terms);
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function indexOrganizations()
    {
        $organizations = $this->organizationRepository->all();

        return JsonResource::collection($organizations);
    }

    /**
     * @return array
     */
    public function indexActionNames()
    {
        return form_response_array(AdminLog::getTitles());
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function indexPrefs()
    {
        $prefs = $this->prefRepository->all();

        return PrefResource::collection($prefs);
    }
}
