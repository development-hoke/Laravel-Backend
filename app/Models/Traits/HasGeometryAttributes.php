<?php

namespace App\Models\Traits;

use App\Utils\Geometry;
use Illuminate\Support\Facades\DB;

trait HasGeometryAttributes
{
    /**
     * @param mixed $value
     *
     * @return float
     */
    public function sanitizeLongitudeValue($value)
    {
        return max(min(Geometry::LONGITUDE_SIZE, (float) $value), -Geometry::LONGITUDE_SIZE);
    }

    /**
     * @param mixed $value
     *
     * @return float
     */
    public function sanitizeLatitudeValue($value)
    {
        return max(min(Geometry::LATITUDE_SIZE, (float) $value), -Geometry::LATITUDE_SIZE);
    }

    /**
     * @param string $name
     * @param array $value [経度, 緯度]
     *
     * @return void
     */
    public function setAsGeometry($name, $value)
    {
        $longitude = $value['longitude'];
        $latitude = $value['latitude'];

        $longitude = $this->sanitizeLongitudeValue($longitude);
        $latitude = $this->sanitizeLatitudeValue($latitude);

        $this->attributes[$name] = DB::raw("PointFromText('POINT({$longitude} {$latitude})')");
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getFromGeometryColumn($name)
    {
        if (!isset($this->attributes[$name])) {
            return null;
        }

        $location = $this->extractLocationFromPointFromTextExpression($this->attributes[$name]);

        return $location;
    }

    /**
     * @param \Illuminate\Database\Query\Expression|string $expression
     *
     * @return int[]|null
     */
    private function extractLocationFromPointFromTextExpression($expression)
    {
        if (preg_match("/POINT\((-?[0-9\.]+) (-?[0-9\.]+)\)/i", (string) $expression, $matchs)) {
            $longitude = $matchs[1];
            $latitude = $matchs[2];

            return ['longitude' => $longitude, 'latitude' => $latitude];
        }

        return null;
    }

    /**
     * 距離でソートする
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $location
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByDistance($query, array $location, string $order = 'asc', string $columnName = 'location')
    {
        $columnName = $this->getTable().'.'.$columnName;

        return $query
            ->select(['*'])
            ->selectRaw("ST_DISTANCE({$columnName}, POINT(?, ?)) AS distance", $location)
            ->orderBy('distance', $order);
    }
}
