<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\Brand as BrandResource;
use App\Http\Resources\Color as ColorResource;
use App\Http\Resources\OnlineCategory as OnlineCategoryResource;
use App\Http\Resources\OnlineTag as OnlineTagResource;
use App\Http\Resources\Pref;
use App\Http\Resources\SalesType as SalesTypeResource;
use App\Repositories\BrandRepository;
use App\Repositories\ColorRepository;
use App\Repositories\Front\EnumMasterRepositoryConstant;
use App\Repositories\OnlineCategoryRepository;
use App\Repositories\OnlineTagRepository;
use App\Repositories\PrefRepository;
use App\Repositories\SalesTypeRepository;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    /**
     * @var EnumMasterRepositoryConstant
     */
    private $enumRepository;

    /**
     * @var OnlineCategoryRepository
     */
    protected $onlineCategoryRepository;

    /**
     * @var ColorRepository
     */
    protected $colorRepository;

    /**
     * @var BrandRepository
     */
    protected $brandRepository;

    /**
     * @var OnlineTagRepository
     */
    protected $onlineTagRepository;

    /**
     * @var SalesTypeRepository
     */
    protected $salesTypeRepository;

    /** @var PrefRepository */
    protected $prefRepository;

    public function __construct(
        EnumMasterRepositoryConstant $enumRepository,
        OnlineCategoryRepository $onlineCateRepository,
        ColorRepository $colorRepository,
        BrandRepository $brandRepository,
        OnlineTagRepository $onlineTagRepository,
        SalesTypeRepository $salesTypeRepository,
        PrefRepository $prefRepository
    ) {
        $this->enumRepository = $enumRepository;
        $this->onlineCategoryRepository = $onlineCateRepository;
        $this->colorRepository = $colorRepository;
        $this->brandRepository = $brandRepository;
        $this->onlineTagRepository = $onlineTagRepository;
        $this->salesTypeRepository = $salesTypeRepository;
        $this->prefRepository = $prefRepository;
    }

    public function index(Request $request)
    {
        $onlineCategories = $this->onlineCategoryRepository->all();
        $colors = $this->colorRepository->all();
        $brands = $this->brandRepository->all();
        $onlineTags = $this->onlineTagRepository->all();
        $salesType = $this->salesTypeRepository->all();
        $prefs = $this->prefRepository->all();

        return [
            'enum' => $this->enumRepository->all(),
            'online_categories' => OnlineCategoryResource::collection($onlineCategories),
            'colors' => ColorResource::collection($colors),
            'brands' => BrandResource::collection($brands),
            'online_tags' => OnlineTagResource::collection($onlineTags),
            'sales_type' => SalesTypeResource::collection($salesType),
            'prefs' => Pref::collection($prefs),
            'constants' => [
                'f_regi' => config('constants.f_regi'),
                'order' => config('constants.order'),
            ],
        ];
    }
}
