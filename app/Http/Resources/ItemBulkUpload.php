<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemBulkUpload extends JsonResource
{
    /**
     * @var bool
     */
    private $withErrors;

    public function __construct($resorce, $withErrors = false)
    {
        parent::__construct($resorce);

        $this->withErrors = $withErrors;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'file_name' => $this->file_name,
            'format' => $this->format,
            'status' => $this->status,
            'success' => $this->success,
            'failure' => $this->failure,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'errors' => $this->when($this->withErrors, $this->errors),
        ];
    }
}
