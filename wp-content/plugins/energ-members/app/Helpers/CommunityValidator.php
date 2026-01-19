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
        $community = is_string($community) ? trim($community) : '';
        $sub       = is_string($sub) ? trim($sub) : '';

        if ($community === '' || $sub === '') {
            return false;
        }

        // 1) First try slug-based validation (your frontend sends slugs)
        $slugs = self::slugList();
        if (isset($slugs[$community])) {
            return in_array($sub, $slugs[$community], true);
        }

        // 2) Backward compatibility: label-based validation
        // e.g. "Oil & Gas" + "Upstream"
        $labels = self::list();
        if (isset($labels[$community])) {
            return in_array($sub, $labels[$community], true);
        }

        // 3) Extra safety: if someone sends label community but slug sub (or vice versa)
        // Try mapping label->slug community and validate again
        $communitySlug = self::communityLabelToSlug($community);
        if ($communitySlug && isset($slugs[$communitySlug])) {
            // If sub looks like label, convert it; if already slug, keep it.
            $subSlug = self::subLabelToSlug($communitySlug, $sub) ?: $sub;
            return in_array($subSlug, $slugs[$communitySlug], true);
        }

        return false;
    }

    /**
     * Convert label community => slug community
     * Example: "Oil & Gas" => "oil-gas"
     */
    private static function communityLabelToSlug(string $label): ?string
    {
        $map = [
            'Oil & Gas' => 'oil-gas',
            'Power Generation' => 'power-generation',
            'Renewables' => 'renewables',
            'Transmission' => 'transmission',
            'Distribution' => 'distribution',
            'Electricity Markets' => 'electricity-markets',
            'New Energies' => 'new-energies',
            'Energy Storage Systems' => 'energy-storage-systems',
            'Sustainability' => 'sustainability',
        ];
        return $map[$label] ?? null;
    }

    /**
     * Convert label sub-community => slug sub-community within a given community slug
     * Example: ("oil-gas","Upstream") => "upstream"
     */
    private static function subLabelToSlug(string $communitySlug, string $subLabel): ?string
    {
        $labelToSlug = [
            'oil-gas' => [
                'Upstream' => 'upstream',
                'Pipelines' => 'pipelines',
                'Refining' => 'refining',
                'Petrochemicals' => 'petrochemicals',
                'CGD' => 'cgd',
                'LPG' => 'lpg',
                'Retail' => 'retail',
                'Oil Markets' => 'oil-markets',
            ],
            'power-generation' => [
                'Thermal' => 'thermal',
                'Nuclear' => 'nuclear',
            ],
            'renewables' => [
                'Solar' => 'solar',
                'Wind' => 'wind',
                'Hydro' => 'hydro',
                'Biopower' => 'biopower',
                'Cogeneration' => 'cogeneration',
                'Waste-to-Energy' => 'waste-to-energy',
            ],
            'transmission' => [
                'Smart Grid' => 'smart-grid',
            ],
            'distribution' => [
                'Smart Meters & AMI' => 'smart-meters-ami',
                'EV Charging' => 'ev-charging',
                'Data Centres' => 'data-centres',
                'Smart Cities' => 'smart-cities',
                'Railways & Metros' => 'railways-metros',
            ],
            'electricity-markets' => [
                'Power Markets' => 'power-markets',
                'Carbon Markets' => 'carbon-markets',
                'RCO' => 'rco',
            ],
            'new-energies' => [
                'Green Hydrogen' => 'green-hydrogen',
                'E-Fuels' => 'e-fuels',
            ],
            'energy-storage-systems' => [
                'BESS' => 'bess',
                'Pumped Hydro' => 'pumped-hydro',
                'CAES' => 'caes',
                'Thermal' => 'thermal',
                'Flywheel' => 'flywheel',
            ],
            'sustainability' => [
                'Energy Efficiency' => 'energy-efficiency',
                'Occupational Health' => 'occupational-health',
                'Industrial & Process Safety' => 'industrial-process-safety',
                'Environment' => 'environment',
            ],
        ];

        return $labelToSlug[$communitySlug][$subLabel] ?? null;
    }
}
