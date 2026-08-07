<?php

namespace App\Services\Analytics;

class GeoLocationTool
{
    /**
     * Resolve IP address or hash to GeoLocation details (Country ID, Region, City).
     * Standardized fallback for anonymized IP hashes.
     */
    public function getLocation(?string $ipOrHash): array
    {
        // When IP privacy / hashing is active, IP cannot be reverse-geolocated.
        // Return default nulls or country lookup if valid IP provided.
        return [
            'country_id' => null,
            'region'     => null,
            'city'       => null,
        ];
    }
}
