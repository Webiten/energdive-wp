<?php
namespace Energ\Frontend;

class EnergClubData
{
    // Communities → Sub-communities
    public static function communities(): array
    {
        return [
            'oil-gas' => [
                'label' => 'Oil & Gas',
                'subs'  => [
                    'upstream' => 'Upstream',
                    'midstream' => 'Midstream',
                    'downstream' => 'Downstream',
                    'petrochemicals' => 'Petrochemicals',
                    'drilling-services' => 'Drilling & Services',
                    'cgd' => 'CGD',
                ],
            ],
            'power-utility' => [
                'label' => 'Power & Utilities',
                'subs'  => [
                    'thermal' => 'Thermal',
                    'hydro' => 'Hydro',
                    'nuclear' => 'Nuclear',
                    'transmission' => 'Transmission',
                    'distribution' => 'Distribution',
                    'renewables-grid' => 'Renewables Integration',
                ],
            ],
            'safety-environment' => [
                'label' => 'Safety & Environment',
                'subs'  => [
                    'hse-management' => 'HSE Management',
                    'process-safety' => 'Process Safety',
                    'fire-emergency' => 'Fire & Emergency',
                    'sustainability' => 'Sustainability',
                ],
            ],
        ];
    }

    // Industry → Sub-industry
    public static function industries(): array
    {
        return [
            'oil-gas' => [
                'label' => 'Oil & Gas',
                'subs' => [
                    'upstream' => 'Upstream',
                    'midstream' => 'Midstream',
                    'downstream' => 'Downstream',
                ],
            ],
            'power-utilities' => [
                'label' => 'Power & Utilities',
                'subs' => [
                    'generation' => 'Generation',
                    't-d' => 'Transmission & Distribution',
                    'retail' => 'Retail & Supply',
                ],
            ],
            'renewables' => [
                'label' => 'Renewables',
                'subs' => [
                    'solar' => 'Solar',
                    'wind' => 'Wind',
                    'storage' => 'Storage',
                    'hydrogen' => 'Hydrogen',
                ],
            ],
            'chemicals' => [
                'label' => 'Chemicals',
                'subs' => [
                    'basic' => 'Basic Chemicals',
                    'specialty' => 'Specialty Chemicals',
                ],
            ],
        ];
    }

    /**
     * Auto-map Area of Industry (dashboard intelligence modules)
     * based on community + sub-community (and optionally industry).
     */
    public static function areaOfIndustry(string $community, string $subCommunity): string
    {
        $map = [
            'oil-gas|upstream' => 'Intelligence: Upstream (Exploration, Production)',
            'oil-gas|midstream' => 'Intelligence: Midstream (Pipelines, Storage, LNG)',
            'oil-gas|downstream' => 'Intelligence: Downstream (Refining, Marketing)',
            'oil-gas|cgd' => 'Intelligence: City Gas Distribution',
            'power-utility|thermal' => 'Intelligence: Thermal Power',
            'power-utility|transmission' => 'Intelligence: Transmission Networks',
            'safety-environment|process-safety' => 'Intelligence: Process Safety',
        ];

        $key = $community . '|' . $subCommunity;
        return $map[$key] ?? 'Intelligence: General';
    }
}
