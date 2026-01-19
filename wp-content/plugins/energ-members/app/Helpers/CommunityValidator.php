<?php

namespace Energ\Helpers;

class CommunityValidator
{
    /**
     * Canonical SLUG list (must match frontend values exactly)
     */
    public static function slugList()
    {
        return [
            'oil-gas' => [
                'upstream',
                'pipelines',
                'refining',
                'petrochemicals',
                'cgd',
                'lpg',
                'retail',
                'oil-markets',
            ],
            'power-generation' => [
                'thermal',
                'nuclear',
            ],
            'renewables' => [
                'solar',
                'wind',
                'hydro',
                'biopower',
                'cogeneration',
                'waste-to-energy',
            ],
            'transmission' => [
                'smart-grid',
            ],
            'distribution' => [
                'smart-meters-ami',
                'ev-charging',
                'data-centres',
                'smart-cities',
                'railways-metros',
            ],
            'electricity-markets' => [
                'power-markets',
                'carbon-markets',
                'rco',
            ],
            'new-energies' => [
                'green-hydrogen',
                'e-fuels',
            ],
            'energy-storage-systems' => [
                'bess',
                'pumped-hydro',
                'caes',
                'thermal',
                'flywheel',
            ],
            'sustainability' => [
                'energy-efficiency',
                'occupational-health',
                'industrial-process-safety',
                'environment',
            ],
        ];
    }

    /**
     * Backward compatible LABEL list (optional; keep if used elsewhere)
     */
    public static function list()
    {
        return [
            'Oil & Gas' => [
                'Upstream',
                'Pipelines',
                'Refining',
                'Petrochemicals',
                'CGD',
                'LPG',
                'Retail',
                'Oil Markets',
            ],
            'Power Generation' => [
                'Thermal',
                'Nuclear',
            ],
            'Renewables' => [
                'Solar',
                'Wind',
                'Hydro',
                'Biopower',
                'Cogeneration',
                'Waste-to-Energy',
            ],
            'Transmission' => [
                'Smart Grid',
            ],
            'Distribution' => [
                'Smart Meters & AMI',
                'EV Charging',
                'Data Centres',
                'Smart Cities',
                'Railways & Metros',
            ],
            'Electricity Markets' => [
                'Power Markets',
                'Carbon Markets',
                'RCO',
            ],
            'New Energies' => [
                'Green Hydrogen',
                'E-Fuels',
            ],
            'Energy Storage Systems' => [
                'BESS',
                'Pumped Hydro',
                'CAES',
                'Thermal',
                'Flywheel',
            ],
            'Sustainability' => [
                'Energy Efficiency',
                'Occupational Health',
                'Industrial & Process Safety',
                'Environment',
            ],
        ];
    }

    private static function slugify($s)
    {
        $s = strtolower(trim((string)$s));

        // normalize '&' and punctuation
        $s = str_replace('&', '', $s);

        // spaces -> hyphen
        $s = preg_replace('/\s+/', '-', $s);

        // remove invalid chars
        $s = preg_replace('/[^a-z0-9\-]/', '', $s);

        // collapse hyphens
        $s = preg_replace('/-+/', '-', $s);

        return trim($s, '-');
    }

    /**
     * ✅ MAIN VALIDATOR:
     * 1) First try SLUG validation (exact match with frontend)
     * 2) If not found, fallback to label validation (older payloads)
     */
    public static function isValid($community, $sub)
    {
        $community = (string)$community;
        $sub = (string)$sub;

        // 1) slug-first
        $slugs = self::slugList();
        if (isset($slugs[$community])) {
            return in_array($sub, $slugs[$community], true);
        }

        // 2) fallback: label-based (older clients)
        $list = self::list();

        // match label by slugify
        $cNeedle = self::slugify($community);
        foreach ($list as $label => $subs) {
            if (self::slugify($label) !== $cNeedle) continue;

            $sNeedle = self::slugify($sub);
            foreach ($subs as $subLabel) {
                if (self::slugify($subLabel) === $sNeedle) return true;
            }
            return false;
        }

        return false;
    }
}
